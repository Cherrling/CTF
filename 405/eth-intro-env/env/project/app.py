import json
import logging
from ast import Dict, List
from typing import Any, Optional

import aiohttp
import asyncio
import aiofiles
from fastapi import FastAPI, Request, WebSocket
import websockets

from contextlib import asynccontextmanager

import subprocess
from functools import lru_cache

from web3 import Web3
from eth_account import Account
from eth_abi import encode
from web3.contract import Contract
from web3.types import RPCResponse
from eth_account.hdaccount import key_from_seed, seed_from_mnemonic, generate_mnemonic

import os

ALLOWED_NAMESPACES = ["web3", "eth", "net"]
DISALLOWED_METHODS = [
    "eth_sign",
    "eth_signTransaction",
    "eth_signTypedData",
    "eth_signTypedData_v3",
    "eth_signTypedData_v4",
    "eth_sendTransaction",
    "eth_sendUnsignedTransaction",
]

DEFAULT_DERIVATION_PATH = "m/44'/60'/0'/0/"
DEFAULT_MNEMONIC = "test test test test test test test test test test test junk"

ANVIL_HOST, ANVIL_PORT = ("127.0.0.1", 8545)
PROJECT_DIR = "./chall"

@asynccontextmanager
async def lifespan(app: FastAPI):
    global session, mnemonic, anvil_process, chall_addr, privileged_w3
    session = aiohttp.ClientSession()

    # start anvil
    anvil_process = subprocess.Popen([
        "/usr/local/bin/anvil",
        "--accounts", "0",
        "--state-interval", "5",
        "--chain-id", "405",
        "--host", ANVIL_HOST,
        "--port", str(ANVIL_PORT),
    ])
    # wait for initialize
    await asyncio.sleep(2)

    # gen mnemonic
    mnemonic = generate_mnemonic(12, lang="english")

    privileged_w3 = Web3(Web3.HTTPProvider(f'http://{ANVIL_HOST}:{ANVIL_PORT}'))
    user_addr, user_key = derive_account(0, mnemonic)
    admin_addr, admin_key = derive_account(1, mnemonic)

    anvil_setBalance(privileged_w3, user_addr, Web3.to_wei(1, 'ether'))
    anvil_setBalance(privileged_w3, admin_addr, Web3.to_wei(100, 'ether'))

    # build challenge

    subprocess.run([
        "/usr/local/bin/forge", "build"
    ], cwd=PROJECT_DIR)

    # deploy challenge

    secret1 = "0x" + os.urandom(32).hex()
    secret2 = "0x" + os.urandom(32).hex()

    subprocess.run([
        "/usr/local/bin/forge", "script",
        "--rpc-url", f"http://{ANVIL_HOST}:{ANVIL_PORT}",
        "--private-key", admin_key,
        "--broadcast",
        "script/Deploy.s.sol:Deploy"
    ], cwd=PROJECT_DIR, env={
        "MNEMONIC": mnemonic,
        "secret1": secret1,
        "secret2": secret2
    })

    # read challenge address
    async with aiofiles.open(f"{PROJECT_DIR}/out/deploy.txt", "r") as f:
        chall_addr = Web3.to_checksum_address(await f.read())

    yield

    anvil_process.terminate()
    await session.close()

app = FastAPI(lifespan=lifespan)


@app.get("/")
async def root():
    return {
        "msg": "challenge is running, please visit /info to get user private key and challenge address, /getflag to get flag"
    }


@app.get("/info")
async def info():
    return {
        "rpc_uri": "/main_rpc",
        "challenge": chall_addr,
        "deployer": derive_account(1, mnemonic)[0],
        "user_privkey": derive_account(0, mnemonic)[1]
    }


@app.get("/getflag")
def getflag():
    chall_abi = get_contract("Challenge.sol", "Challenge")[0]
    chall = privileged_w3.eth.contract(abi=chall_abi, address=chall_addr)
    isSolved1 = chall.functions.isSolved1().call()
    isSolved2 = chall.functions.isSolved2().call()
    return {
        "flag1": get_flag(1) if isSolved1 else "not solved yet.",
        "flag2": get_flag(2) if isSolved2 else "not solved yet."
    }


def jsonrpc_fail(id: Any, code: int, message: str) -> Dict:
    return {
        "jsonrpc": "2.0",
        "id": id,
        "error": {
            "code": code,
            "message": message,
        },
    }


def validate_request(request: Any) -> Optional[Dict]:
    if not isinstance(request, dict):
        return jsonrpc_fail(None, -32600, "expected json object")

    request_id = request.get("id")
    request_method = request.get("method")

    if request_id is None:
        return jsonrpc_fail(None, -32600, "invalid jsonrpc id")

    if not isinstance(request_method, str):
        return jsonrpc_fail(request["id"], -32600, "invalid jsonrpc method")

    if (
        request_method.split("_")[0] not in ALLOWED_NAMESPACES
        or request_method in DISALLOWED_METHODS
    ):
        return jsonrpc_fail(request["id"], -32600, "forbidden jsonrpc method")

    return None


async def proxy_request(
    request_id: Optional[str], body: Any
) -> Optional[Any]:
    try:
        async with session.post(f"http://{ANVIL_HOST}:{ANVIL_PORT}", json=body) as resp:
            return await resp.json()
    except Exception as e:
        logging.error(
            "failed to proxy anvil request", exc_info=e
        )
        return jsonrpc_fail(request_id, -32602, str(e))


@app.post("/main_rpc")
async def rpc(request: Request):
    try:
        body = await request.json()
    except json.JSONDecodeError:
        return jsonrpc_fail(None, -32600, "expected json body")

    # special handling for batch requests
    if isinstance(body, list):
        responses = []
        for idx, req in enumerate(body):
            validation_error = validate_request(req)
            responses.append(validation_error)

            if validation_error is not None:
                # neuter the request
                body[idx] = {
                    "jsonrpc": "2.0",
                    "id": idx,
                    "method": "web3_clientVersion",
                }

        upstream_responses = await proxy_request(None, body)

        for idx in range(len(responses)):
            if responses[idx] is None:
                if isinstance(upstream_responses, List):
                    responses[idx] = upstream_responses[idx]
                else:
                    responses[idx] = upstream_responses

        return responses

    validation_resp = validate_request(body)
    if validation_resp is not None:
        return validation_resp

    return await proxy_request(body["id"], body)

async def forward_message(client_to_remote: bool, client_ws: WebSocket, remote_ws: websockets):
    if client_to_remote:
        async for message in client_ws.iter_text():
            try:
                json_msg = json.loads(message)
            except json.JSONDecodeError:
                await client_ws.send_json(jsonrpc_fail(None, -32600, "expected json body"))

            validation = validate_request(json_msg)
            if validation is not None:
                await client_ws.send_json(validation)
            else:
                await remote_ws.send(message)
    else:
        async for message in remote_ws:
            await client_ws.send_text(message)


@app.websocket("/main_rpc/ws")
async def ws_rpc(client_ws: WebSocket):
    instance_host = f"ws://{ANVIL_HOST}:{ANVIL_PORT}"

    async with websockets.connect(instance_host) as remote_ws:
        await client_ws.accept()
        task_a = asyncio.create_task(forward_message(True, client_ws, remote_ws))
        task_b = asyncio.create_task(forward_message(False, client_ws, remote_ws))

        try:
            await asyncio.wait([task_a, task_b], return_when=asyncio.FIRST_COMPLETED)
            task_a.cancel()
            task_b.cancel()
        except:
            pass

def check_error(resp: RPCResponse):
    if "error" in resp:
        raise Exception("rpc exception", resp["error"])

def int2hex(v):
    if isinstance(v, int):
        return hex(v)
    return v


def anvil_autoImpersonateAccount(web3: Web3, enabled: bool):
    check_error(web3.provider.make_request("anvil_autoImpersonateAccount", [enabled]))


def anvil_setCode(web3: Web3, addr: str, bytecode: str):
    check_error(web3.provider.make_request("anvil_setCode", [addr, bytecode]))


def anvil_setStorageAt(
    web3: Web3,
    addr: str,
    slot: str | int,
    value: str,
):
    check_error(web3.provider.make_request("anvil_setStorageAt", [addr, int2hex(slot), value]))


def anvil_setBalance(
    web3: Web3,
    addr: str,
    balance: str | int,
):
    if isinstance(balance, int):
        balance = hex(balance)
    check_error(web3.provider.make_request("anvil_setBalance", [addr, int2hex(balance)]))


def sendTx(w3: Web3, tx, private_key):
    signed_txn = w3.eth.account.sign_transaction(tx, private_key=private_key)
    send = w3.eth.send_raw_transaction(signed_txn.rawTransaction)
    txid = send.hex()
    res = w3.eth.wait_for_transaction_receipt(txid)
    # assert res["status"] == 1
    return (txid, res)

@lru_cache
def get_contract(file: str, contract: str) -> tuple[str, str]:
    with open(f"{PROJECT_DIR}/out/{file}/{contract}.json", "r") as f:
        cache = json.load(f)
        abi = json.dumps(cache["abi"])
        bytecode = cache["bytecode"]["object"]
        return (abi, bytecode)

@lru_cache
def calc_create_addr(sender: str, nonce: int):
    import rlp
    return Web3.to_checksum_address(
        Web3.keccak(rlp.encode([
            bytes.fromhex(sender.removeprefix("0x")),
            nonce
        ]))[-20:]
    )

@lru_cache
def derive_account(index: int, mnemonic: str = DEFAULT_MNEMONIC, derivation_path: str = DEFAULT_DERIVATION_PATH):
    seed = seed_from_mnemonic(mnemonic, "")
    private_key = key_from_seed(seed, f"{derivation_path}{index}")
    return Web3.to_checksum_address(Account.from_key(private_key).address), private_key.hex()

@lru_cache
def get_flag(idx: int):
    name = f"FLAG{idx}"
    if name in os.environ:
        return os.environ[name]
    return f"flag{{flag{idx}_unset_yet}}"
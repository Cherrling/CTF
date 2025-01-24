# sample solve script to interface with the server
import pwn
import hashlib

from solders.pubkey import Pubkey


def calc_discriminator(namespace: bytes, name: bytes):
    preimage = namespace + b':' + name
    return hashlib.sha256(preimage).digest()[:8]

# if you don't know what this is doing, look at server code and also sol-ctf-framework read_instruction:
# https://github.com/otter-sec/sol-ctf-framework/blob/rewrite-v2/src/lib.rs#L237
# feel free to change the accounts and ix data etc. to whatever you want
account_metas = [
    ("user",            "sw"), # signer + writable
    ("secured_payment", "-r"), # read only
    ("system_program",  "-r"), # read only
]

HOST, PORT = "localhost", 1337
p = pwn.remote(HOST, PORT)

with open("solve/target/deploy/solve.so", "rb") as f:
    solve = f.read()

p.sendlineafter(b"program pubkey: \n", b"BzyxyvKY5JybTPWZ3a91FyAXpPrjJUXvyNgHASkP7vHD")
p.sendlineafter(b"program len: \n", str(len(solve)).encode())
p.send(solve)

accounts = {
    "system_program": "11111111111111111111111111111111",
}

p.recvuntil(b"program: ")
accounts["secured_payment"] = p.recvline().decode().strip()
p.recvuntil(b"user: ")
accounts["user"] = p.recvline().decode().strip()
p.recvuntil(b"admin: ")
accounts["admin"] = p.recvline().decode().strip()
p.recvuntil(b"payment: ")
accounts["payment"] = p.recvline().decode().strip()

# an example for calc PDA address
accounts["config"] = str(Pubkey.find_program_address(
    [b"config"],
    Pubkey.from_string(accounts["secured_payment"])
)[0])

print(accounts)

# solve is the instruction you want to call
instruction_data = calc_discriminator(b"global", b"solve")

p.sendline(str(len(account_metas)).encode())
for name, perms in account_metas:
    p.sendline(f"{perms} {accounts[name]}".encode())

p.sendlineafter(b"ix len: \n", str(len(instruction_data)).encode())
p.send(instruction_data)

p.interactive()

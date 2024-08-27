import asyncio
import functools
import logging
import os
import struct
import more_itertools as mit

from pathlib import Path

from bumble.core import AdvertisingData
from bumble.device import Advertisement, Connection, Device, DeviceConfiguration, Peer
from bumble.att import Attribute
from bumble.gatt import Characteristic, CharacteristicValue, Service
from bumble.hci import Address, OwnAddressType
from bumble.host import Host
from bumble.transport import open_transport_or_link
import time
from Crypto.Util.Padding import pad
from Crypto.PublicKey import ECC
from Crypto.Cipher import AES
import secrets
from collections import namedtuple
import hashlib
from cryptography import x509
from cryptography.hazmat.primitives import hashes,serialization
from cryptography.hazmat.primitives.asymmetric import padding
from cryptography.hazmat.backends import default_backend


Connect_Confirm = namedtuple('Connect_Confirm',['nseq','mtimestamp','InitState','DKid','Digest','PhoneId'])
Digital_Key = namedtuple('Digital_Key',['DKid','acert','vcert','digest','sign'])
vin = b"XPENGTESTVIN0001"
iv = b"\x1d\x0a\x5d\xba\x3c\xa5\xd8\x07\x53\x1d\x61\x22\xA6\x6c\x26\x87"
flag = open("flag.txt","rb").read()
DKid = open("digital_key.txt","rb").read()[:4]
Phoneid = open("phone_id.txt","rb").read()

def gen_manufacturer():
    random_bytes = secrets.token_bytes(8)
    return random_bytes
        
def gen_connectkey(bArr,bArr2):
    bArr3 = [48,49,50,51,52,53,54,55,56,57,65,66,67,68,69,70]
    bArr4 = []
    bArr5 = []
    for i in range(8):       
        bArr4.append(bArr3[(bArr2[i]>>4)&15])
        bArr4.append(bArr3[bArr2[i]&15])
    for i in range(16):
        bArr5.append(bArr[i]^bArr4[i])
    return bArr5

def right8(result):
    result = result & 0xffffffff
    result = result >> 8
    result = result & 0xff
    return result

def gen_crc(req):
    crclist = [0, 49345, 49537, 320, 49921, 960, 640, 49729, 50689, 1728, 1920, 51009, 1280, 50625, 50305, 1088, 52225, 3264, 3456, 52545, 3840, 53185, 52865, 3648, 2560, 51905, 52097, 2880, 51457, 2496, 2176, 51265, 55297, 6336, 6528, 55617, 6912, 56257, 55937, 6720, 7680, 57025, 57217, 8000, 56577, 7616, 7296, 56385, 5120, 54465, 54657, 5440, 55041, 6080, 5760, 54849, 53761, 4800, 4992, 54081, 4352, 53697, 53377, 4160, 61441, 12480, 12672, 61761, 13056, 62401, 62081, 12864, 13824, 63169, 63361, 14144, 62721, 13760, 13440, 62529, 15360, 64705, 64897, 15680, 65281, 16320, 16000, 65089, 64001, 15040, 15232, 64321, 14592, 63937, 63617, 14400, 10240, 59585, 59777, 10560, 60161, 11200, 10880, 59969, 60929, 11968, 12160, 61249, 11520, 60865, 60545, 11328, 58369, 9408, 9600, 58689, 9984, 59329, 59009, 9792, 8704, 58049, 58241, 9024, 57601, 8640, 8320, 57409, 40961, 24768, 24960, 41281, 25344, 41921, 41601, 25152, 26112, 42689, 42881, 26432, 42241, 26048, 25728, 42049, 27648, 44225, 44417, 27968, 44801, 28608, 28288, 44609, 43521, 27328, 27520, 43841, 26880, 43457, 43137, 26688, 30720, 47297, 47489, 31040, 47873, 31680, 31360, 47681, 48641, 32448, 32640, 48961, 32000, 48577, 48257, 31808, 46081, 29888, 30080, 46401, 30464, 47041, 46721, 30272, 29184, 45761, 45953, 29504, 45313, 29120, 28800, 45121, 20480, 37057, 37249, 20800, 37633, 21440, 21120, 37441, 38401, 22208, 22400, 38721, 21760, 38337, 38017, 21568, 39937, 23744, 23936, 40257, 24320, 40897, 40577, 24128, 23040, 39617, 39809, 23360, 39169, 22976, 22656, 38977, 34817, 18624, 18816, 35137, 19200, 35777, 35457, 19008, 19968, 36545, 36737, 20288, 36097, 19904, 19584, 35905, 17408, 33985, 34177, 17728, 34561, 18368, 18048, 34369, 33281, 17088, 17280, 33601, 16640, 33217, 32897, 16448]
    result = 0
    for i in req:
        result = crclist[(result^i)&255]^right8(result)
    return (result.to_bytes(2,'big'))

def check_crc(req):
    crc_value = req[-2:]
    need_crc = gen_crc(req[:-2])
    if(crc_value != need_crc):
        return False
    else:
        return True

def encrypt_content(manufacturer,req):
    connectkey = bytes(gen_connectkey(vin,manufacturer))
    nseq = b"\x00\x01"
    mtimestamp = int(time.time()).to_bytes(4,'big')
    req = pad(nseq+mtimestamp+req,16)
    encrypted_bytes = AES.new(connectkey,AES.MODE_CBC,iv).encrypt(req)
    req_header = b"\xfe\x02"+(9+len(encrypted_bytes)).to_bytes(2,'big')+b"\x01\x02\x02"
    crc = gen_crc(req_header+encrypted_bytes)
    return req_header+encrypted_bytes+crc

def decrypt_content(connectKey,resp):
    if len(resp) < 9:
        return None
    content_len = resp[2:4] 
    if int.from_bytes(content_len,"big") != len(resp):
        return None
    if resp[1]!=b'\x01':
        try:
            encrypted_content = resp[7:-2]                     
            decrypted_bytes = AES.new(connectKey,AES.MODE_CBC,iv).decrypt(encrypted_content)
            if len(decrypted_bytes) < 26:
                return None
            connector_connect_confirm = Connect_Confirm(decrypted_bytes[0:2],decrypted_bytes[2:6],decrypted_bytes[6],decrypted_bytes[7:11],decrypted_bytes[11:19],decrypted_bytes[19:])
            return connector_connect_confirm            
        except:
            logger.error("decrypt error!!")
            return None
    else:
        logger.error("type error!!")
        return None

def connect_confirm_verify(manufacturer,value):  
    try:
        assert len(value) > 9
        assert len(value) < 244
        connectkey = bytes(gen_connectkey(vin,manufacturer))
        sha_val = hashlib.sha256(manufacturer).digest()
        connect_confirm = Connect_Confirm(b'\x00\x01',int(time.time()).to_bytes(4,'big'),b'\x00',DKid,sha_val[sha_val[0]&15:(sha_val[0]&15)+8],Phoneid)
        if not check_crc(value):
            logger.error("crc error!!")
            raise
        decrypted_content = decrypt_content(connectkey,value)
        if decrypted_content == None:
            logger.error("cannot decrypt!!")
            raise
        else:
            if (decrypted_content.nseq != connect_confirm.nseq):
                logger.error("nseq not match!!")
                raise
            if (decrypted_content.DKid != connect_confirm.DKid):
                logger.error("DKid not match!!")
                raise
            if (decrypted_content.PhoneId[:7] != connect_confirm.PhoneId):
                logger.error("PhoneId not match!!"+str(decrypted_content.PhoneId))
                raise       
            if (decrypted_content.Digest != connect_confirm.Digest):
                logger.error("Digest not match!!")
                raise
            return True
    except:
        return False

transport_name = "usb:[driver=rtk]0"
config = DeviceConfiguration()
config.load_from_dict(
    {
        "name": "Bumble",
        "advertising_interval": 1600,
    }
)
class CcService(Service):
    def __init__(self,manufacturer):
        self.write_1 = Characteristic(
            "00000001-DA58-11E1-4A69-0002A5D5C51B",
            Characteristic.Properties.WRITE_WITHOUT_RESPONSE,
            Attribute.Permissions.WRITEABLE,
            CharacteristicValue(
                write=lambda *args, **kwargs: asyncio.create_task(
                    self._on_write_1(*args, **kwargs)
                )
            ),
        )
        self.notify_1 = Characteristic(
            "00000002-DA58-11E1-4A69-0002A5D5C51B",
            Characteristic.Properties.NOTIFY,
            Attribute.Permissions.READABLE,
        )
        self.write_2 = Characteristic(
            "00000003-DA58-11E1-4A69-0002A5D5C51B",
            Characteristic.Properties.WRITE_WITHOUT_RESPONSE,
            Attribute.Permissions.WRITEABLE,
            CharacteristicValue(
                write=lambda *args, **kwargs: asyncio.create_task(
                    self._on_write_2(*args, **kwargs)
                )
            ),
        )
        self.notify_2 = Characteristic(
            "00000004-DA58-11E1-4A69-0002A5D5C51B",
            Characteristic.Properties.NOTIFY,
            Attribute.Permissions.READABLE,
        )
        characteristics = [self.write_1, self.notify_1, self.write_2, self.notify_2]
        super().__init__(
            "00000000-DA58-11E1-4A69-0002A5D5C51B", characteristics, primary=True
        )
        self.manufacturer = manufacturer
        self.init_state = 0
        self.step_state = 0
        self.authed = False
 
    
    def clean(self):
        self.init_state = 0
        self.step_state = 0
        self.authed = False
        
    async def _on_write_1(self, conn: Connection, value: bytes):
        if conn.att_mtu != 247:
            await conn.disconnect()  
            return
        logger.info("write 1: %s", value)
        logger.info("current_step: %d", self.step_state)
        match self.step_state:
            case 0:
                try:
                    if(connect_confirm_verify(self.manufacturer,value)):
                        confirm_ok = encrypt_content(self.manufacturer,b"ok")
                        self.step_state = 1
                        await conn.device.notify_subscriber(
                            conn,
                            self.notify_1,
                            confirm_ok,                   
                        )
                        self.authed = True
                    else:
                        confirm_fail = encrypt_content(self.manufacturer,b"failed")
                        await conn.device.notify_subscriber(
                            conn,
                            self.notify_1,
                            confirm_fail,                       
                        )
                        raise
                except:
                    await conn.disconnect()        
            case 1:
                try:
                    if self.authed:
                        content = encrypt_content(self.manufacturer,flag)
                        await conn.device.notify_subscriber(
                            conn,
                            self.notify_1,
                            content,                       
                        )
                    else:
                        content = encrypt_content(self.manufacturer,b"not authed!!")
                        await conn.device.notify_subscriber(
                            conn,
                            self.notify_1,
                            content,                       
                        )
                        raise
                except:
                    await conn.disconnect()                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        

    async def _on_write_2(self, conn: Connection, value: bytes):
        logger.info("write 2: %s", value)

logger = logging.getLogger(__name__)

manufacturer = gen_manufacturer()
ccs = CcService(manufacturer)

async def _main():
    async with await open_transport_or_link(transport_name) as (hci_source, hci_sink):
        device = Device.from_config_with_hci(config, hci_source, hci_sink)
        bumble_host_log_level = logging.getLogger("bumble.host").level
        logging.getLogger("bumble.host").setLevel("INFO")
        logger.info("manufacturer is %s",manufacturer)
        manufacturer_data = struct.pack("<H", 0x01FE) + b"DARKNARY"+manufacturer
        logging.getLogger("bumble.host").setLevel(bumble_host_log_level)
        device.add_service(ccs)
        device.advertising_data = bytes(
            AdvertisingData(
                [
                    (
                        AdvertisingData.MANUFACTURER_SPECIFIC_DATA,
                        manufacturer_data,
                    ),
                ]
            )
        )
        device.scan_response_data = bytes(
            AdvertisingData(
                [
                    (AdvertisingData.COMPLETE_LOCAL_NAME, b"DARKNAVY_BLE"),
                ]
            )
        )

        @device.on("connection")
        def on_connection(conn: Connection):
            logger.info("New connection: %s", conn.peer_address)
            conn.on("disconnection",on_disconnect)
        
   

        await device.power_on()
        await device.start_advertising(
            auto_restart=True, own_address_type=OwnAddressType.PUBLIC
        )
        await asyncio.to_thread(input)

def on_disconnect(reason):
    logger.info("clean everything")
    ccs.clean()

if __name__ == "__main__":
    logging.basicConfig(level="INFO")
    logging.getLogger(__name__).setLevel("DEBUG")
    logging.getLogger("bumble").setLevel("DEBUG")
    logging.getLogger("bumble.transport").setLevel("INFO")
    logging.getLogger("bumble.host").setLevel("INFO")
    
    logging.getLogger("bumble.drivers").setLevel("INFO")
    rtk_firmware_dir = Path.cwd() / "data" / "firmware" / "realtek"
    os.environ["BUMBLE_RTK_FIRMWARE_DIR"] = str(rtk_firmware_dir)
    asyncio.run(_main())
import time
import random
from randcrack import RandCrack
from Crypto.Util.number import *

# 时间为2019.6.15 4.04pm
stream1 = random.Random(1560557040) # Guess what time it is :P (year=2019， UTC+8)
# 从msg文件读取msg
msg = open('msg', 'rb').read()
# 以二进制形式读取ciphertext的内容
ciphertext = open('ciphertext', 'rb').read()

print(f"len(msg) = {len(msg)}")
print(f"len(ciphertext) = {len(ciphertext)}")

rc = RandCrack()


flag=b''
for i in range (len(ciphertext)//4):
    if i < 624:
        tmp= (bytes_to_long(msg[i*4:i*4+4]) ^ stream1.getrandbits(32) ^ bytes_to_long(ciphertext[i*4:i*4+4]))
        rc.submit(tmp)
    else:
        flag += long_to_bytes(rc.predict_getrandbits(32) ^ bytes_to_long(ciphertext[i*4:i*4+4])^stream1.getrandbits(32))
    
    
print(f'flag = {flag}')
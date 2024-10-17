import time
import random
from Crypto.Util.number import *
from secret import flag, msg


stream1 = random.Random(int(time.time())) # Guess what time it is :P (year=2019， UTC+8)
stream2 = random.Random(flag)

open('msg','wb').write(msg)

msg += flag
c = b''

assert len(msg) % 4 == 0

for i in range(len(msg)//4):
    c += long_to_bytes(bytes_to_long(msg[i*4:i*4+4]) ^ stream1.getrandbits(32) ^ stream2.getrandbits(32)).rjust(4, b'\x00')

open('ciphertext','wb').write(c)
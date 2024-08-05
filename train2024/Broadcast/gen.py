from Crypto.Util.number import *
import gmpy2
from secret import flag

m = bytes_to_long(flag)
assert m.bit_length() < 240

e = 11
n = [getPrime(256) * getPrime(256) for _ in range(12)]

def enc(i):
    return gmpy2.powmod(m, e, n[i])

with open('enc.txt', 'w') as f:
    for x in range(12):
        f.write(f'{enc(x)} {n[x]}\n')

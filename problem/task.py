from Crypto.Util.number import *
from sympy import nextprime
from secret import flag

m = bytes_to_long(flag.encode())

p = getPrime(512)
q = p
for i in range(getRandomNBitInteger(10)):
  q = nextprime(q)

N = p * q
phi = (p-1) * (q-1)
e = 65537
d = inverse(e, phi)
c = pow(m, e, N)

print(f"{N=}")
print(f"{c=}")

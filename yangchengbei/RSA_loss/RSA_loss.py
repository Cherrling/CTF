from Crypto.Util.number import *
from gmpy2 import *
from sympy import invert
# p = getPrime(100)
# q = getPrime(100)
p = 898278915648707936019913202333
q = 814090608763917394723955024893
n = p * q
e = 65537
message = b"DASCTF{00000000-0000-0000-0000-000000000001}"
m = bytes_to_long(message)
print(f'm = {m}')
print(f'n = {n}')
# c = pow(m, e, n)
c = 356435791209686635044593929546092486613929446770721636839137

print(f'c = {c}')
print(f'p = {p}')
print(f'q = {q}')
d =int(invert(e,(p-1)*(q-1))) 
print(f'd = {d}')
newm = pow(c, d, n)
print(long_to_bytes(newm))
#c = 356435791209686635044593929546092486613929446770721636839137
#p = 898278915648707936019913202333
#q = 814090608763917394723955024893
#b'X\xee\x1ey\x88\x01dX\xf6i\x91\x80h\xf4\x1f!\xa7"\x0c\x9a\x06\xc8\x06\x81\x15'
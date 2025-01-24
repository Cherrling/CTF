import gmpy2
import random
import binascii
from hashlib import sha256
from sympy import nextprime
from Crypto.Cipher import AES
from Crypto.Util.Padding import pad
from Crypto.Util.number import long_to_bytes
import binascii as binaccii


victory_key = "WANGDINGCUP"

p = 0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffefffffc2f
a = 0
b = 7
xG = 0x79be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798
yG = 0x483ada7726a3c4655da4fbfc0e1108a8fd17b448a68554199c47d08ffb10d4b8
G = (xG, yG)
n = 0xfffffffffffffffffffffffffffffffebaaedce6af48a03bbfd25e8cd0364141
h = 1
zero = (0,0)
c='507be52e0721bba75c9dc23180a78de9af0cf21842dd27e16ae27b43de0e99b5a6bf661ef5ebf56d9d7bf9c6ed207a9d753aa94adfa51cbc3d4cb234eb3f177d'
r1 = 68097957214892959097808634206491566533643222966619139257346810421081585723930
r2 = 68097957214892959097808634206491566533643222966619139257346810421081585723930
s1 = 67249409472936638291163196053877339220525780634461864450594029472181758074828
s2 = 73373457041635669316699455116837076942349235043285345596412317434825715497785
z1 = 80195815881183635999191380380485539364733792579032143197993380599095251892429
z2 = 3634916156299659718916511729962752254806535817011719083804498430258130628828

c=binaccii.unhexlify(c)
iv,c= c[:16],c[16:]

s_gap=(s1-s2)%n
z_gap=(z1-z2)%n

s_gap_inverse=gmpy2.invert(s_gap,n)
da=((s1*z_gap*s_gap_inverse-z1)*gmpy2.invert(r1,n))%n

print(long_to_bytes(da))

key=sha256(long_to_bytes(da)).digest()
cipher=AES.new(key,AES.MODE_CBC,iv)
vc=cipher.decrypt(c)
print(vc)
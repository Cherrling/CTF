import os
from Crypto.Util.number import *
# from secret import flag

def genkey(bits):
    p = getPrime(bits)
    q = getPrime(bits)
    while (p-1) % 7 == 0 or (q-1) % 7 == 0:
        p = getPrime(bits)
        q = getPrime(bits)
    n = p * q
    e = 0x10001
    d = inverse(e, (p-1)*(q-1))
    return (e, n), (p, q, d)

def flip_bit(num, idx):
    return num ^ (1 << idx)

def signature(m, sk):
    p, q, d = sk
    sig = pow(m, d, p*q)
    return sig

def fault_signature(m, sk, flip_idx):
    p, q, d = sk
    dd = flip_bit(d, flip_idx)
    sig = pow(m, dd, p*q)
    return sig

def pad(msg, length):
    pad_length = length - len(msg) - 1
    pad_data = os.urandom(pad_length)
    return msg + b'\x00' + pad_data

def unpad(msg):
    return msg.split(b"\x00")[0]

bits = 512
pk1, sk1 = genkey(bits)

e1, n = pk1
p, q, d1 = sk1
e2 = 7
d2 = inverse(e2, (p-1)*(q-1))

pk2 = (e2, n)
sk2 = (p, q, d2)

# 两组密钥共用pq，其中一组密钥的e=0x10001，另一组密钥的e=7

print(pk2)
print(sk2)


m = bytes_to_long(pad(flag, bits//4-1))
# 随机填充到3倍数长度
msg = bytes_to_long(b'ddddhm')

c = pow(m, e1, n)
msg_sig = signature(msg, sk2)

msg_fault_sigs = []
for idx in range(0, d2.bit_length()*2//3):
    fault_sig = fault_signature(msg, sk2, idx)
    msg_fault_sigs.append(fault_sig)

print(f'n = {n}')
print(f'd2_nbits = {d2.bit_length()}')
print(f'c = {c}')
print(f'msg_sig = {msg_sig}')
print(f'msg_fault_sigs = {msg_fault_sigs}')
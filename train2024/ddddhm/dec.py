n = 129796898134024157099156452709058687368221438176030502692200747714575571701027659861801365516981802018997712595009646737912670988500232545577601552315965176230630625733276337162755295073834150653688782811860047196091202071789102378530279205691266477048001888471022116120453815115157747843722970514365474705361
e=7

for i in range(e+1):
    d_estimate = (n*i+1) // e
    print(hex(d_estimate))
    
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
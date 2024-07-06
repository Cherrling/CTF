import os
import hashlib
from Crypto.Util.number import *
from Crypto.PublicKey import DSA
import random
def gen_proof_key():
    password = 'happy_the_year_of_loong'
    getin = ''
    for i in password:
        if random.randint(0, 1):
            getin += i.lower()
        else:
            getin += i.upper()
    ans = hashlib.sha256(getin.encode()).hexdigest()
    return getin, ans



def gen_key():
    pri = random.randint(2,q - 2)
    pub = pow(g,pri,p)
    return pri,pub

def sign(m,pri):
    k = int(hashlib.md5(os.urandom(20)).hexdigest(),16)
    H = int(hashlib.sha256(m).hexdigest(),16)
    r = pow(g,k,p) % q
    s = pow(k,-1,q) * (H + pri * r) % q
    return r,s

def verify(pub,m,signature):
    r,s = signature
    if r <= 0 or r >= q or s <= 0 or s >= q:
        return False
    w = pow(s,-1,q)
    H = int(hashlib.sha256(m).hexdigest(),16)
    u1 = H * w % q
    u2 = r * w % q
    v = (pow(g,u1,p) * pow(pub,u2,p) % p) % q
    return v == r
    
def login():
    print('Hello sir,Plz login first')
    menu = '''
    1.sign
    2.verify
    3.get my key
    '''
    times = 8
    while True:
        print(menu)
        if times < 0:
            print('Timeout!')
            return False
        choice = int(input('>'))
        if choice == 1:
            name = input('Username:').encode()
            if b'admin' in name:
                print('Get out!')
                return False
            r,s = sign(name,pri)
            print(f'This is your signature -- > {r},{s}')
            times -= 1
        elif choice == 2:
            print('Sure,Plz input your signature')
            print(pri)
            r = int(input('r:'))
            s = int(input('s:'))
            if verify(pub,b'admin',(r,s)) == True:
                print('login success!')
                return True
            else:
                print('you are not admin')
                return False
        elif choice == 3:
            print(f'Oh,your key is {(p,q,g)}')
getin,ans = gen_proof_key()
print(f'Your gift --> {ans[:6]}')
your_token = input('Plz input your token\n>')
if your_token != getin:
    print('Get out!')
    exit(0)

key = DSA.generate(1024)
p, q, g = key.p, key.q, key.g
pri, pub = gen_key()
if login() == False:
    exit(0)
print(open('/flag','r').read())
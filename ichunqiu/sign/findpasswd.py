import hashlib
from Crypto.Util.number import *

def find_password(prefix):
    password = 'happy_the_year_of_loong'
    for i in range(2 ** len(password)):
        binary = bin(i)[2:].zfill(len(password))
        guess = ''.join([password[j].lower() if binary[j] == '0' else password[j].upper() for j in range(len(password))])
        hashed = hashlib.sha256(guess.encode()).hexdigest()
        if hashed.startswith(prefix):
            return guess
    return None

prefix = 'e5d73c'

password = find_password(prefix)
if password is not None:
    print(f'Found password: {password}')
else:
    print('Password not found.')
from param import *
import socket

def string_to_bit_array(text):
    array = list()
    for char in text:
        binval = binvalue(char, 8)
        array.extend([int(x) for x in list(binval)])
    return array

def bit_array_to_string(array):
    res = ''.join([chr(int(y,2)) for y in [''.join([str(x) for x in bytes]) for bytes in  nsplit(array,8)]])   
    return res

def binvalue(val, bitsize):
    binval = bin(val)[2:] if isinstance(val, int) else bin(ord(val))[2:]
    if len(binval) > bitsize:
        binval = binval[-bitsize:]
    while len(binval) < bitsize:
        binval = "0"+binval
    return binval

def nsplit(s, n):
    return [s[k:k+n] for k in range(0, len(s), n)]

class thisishash():
    def __init__(self):
        self.msg = None
        self.text = None
        self.keys = list()
        
    def run(self, msg, text):
        self.msg = msg
        self.text = text
        
        self.generatekeys()
        text_blocks = nsplit(self.text, 8)
        result = list()
        for block in text_blocks:
            block = string_to_bit_array(block)
            block = self.permut(block,PI)
            g, d = nsplit(block, 32)
            tmp = None
            for i in range(16):
                d_e = self.expand(d, E)
                tmp = self.xor(self.keys[i], d_e)
                tmp = self.substitute(tmp)
                tmp = self.permut(tmp, P)
                tmp = self.xor(g, tmp)
                g = d
                d = tmp
            result += self.permut(d+g, PI_1)
        final_res = bit_array_to_string(result)
        return final_res
    
    def substitute(self, d_e):
        subblocks = nsplit(d_e, 6)
        result = list()
        for i in range(len(subblocks)):
            block = subblocks[i]
            row = int(str(block[0])+str(block[5]),2)
            column = int(''.join([str(x) for x in block[1:][:-1]]),2)
            val = S_BOX[i][row][column]
            bin = binvalue(val, 4)
            result += [int(x) for x in bin]
        return result
        
    def permut(self, block, table):
        return [block[x-1] for x in table]
    
    def expand(self, block, table):
        return [block[x-1] for x in table]
    
    def xor(self, t1, t2):
        return [x^y for x,y in zip(t1,t2)]
    
    def generatekeys(self):
        self.keys = []
        key = string_to_bit_array(self.msg)
        key = self.permut(key, CP_1)
        g, d = nsplit(key, 28)
        for i in range(16):
            g, d = self.shift(g, d, SHIFT[i])
            tmp = g + d
            self.keys.append(self.permut(tmp, CP_2))

    def shift(self, g, d, n):
        return g[n:] + g[:n], d[n:] + d[:n]
    
    def hash(self, msg):
        msg += len(msg).to_bytes()
        while len(msg)%8 != 0:
            msg += b'\x00'
        text = b'\x00'*8
        for i in range(0, len(msg), 8):
            text = self.run(msg[i:i+8], text)
        return text.encode('latin-1')


def dohash(msg):
    h = thisishash()
    res = h.hash(msg)
    return res

from Crypto.Util.number import *
from tqdm import tqdm
msg1 = b'Cra2y_4_V_mE_S0'

h1 = dohash(msg1)

for i in tqdm(range(2**32)):
    msg2 = long_to_bytes(i)
    h2 = dohash(msg2)
    
    if h1 == h2:
        print(f"Collision found: {msg1} and {msg2}")
        break
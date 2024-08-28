#!/usr/bin/env python
# -*- coding: UTF-8 -*-
import os
import hashlib
from sage.all import *
from Crypto.Cipher import AES
from Crypto.Util.Padding import pad
from secret import c, b, key, FLAG


def add_curve(P, Q, K):
    a, d, p = K
    if P == (0, 0):
        return Q
    if Q == (0, 0):
        return P
    x1, y1 = P
    x2, y2 = Q
    x3 = (x1 * y2 + y1 * x2) * pow(1 - d * x1 ** 2 * x2 ** 2, -1, p) % p
    y3 = ((y1 * y2 + 2 * a * x1 * x2) * (1 + d * x1 ** 2 * x2 ** 2) + 2 * d * x1 * x2 * (x1 ** 2 + x2 ** 2)) * pow(
        (1 - d * x1 ** 2 * x2 ** 2) ** 2, -1, p) % p
    return x3, y3


def mul_curve(n, P, K):
    R = (0, 0)
    while n > 0:
        if n % 2 == 1:
            R = add_curve(R, P, K)
        P = add_curve(P, P, K)
        n = n // 2
    return R


def AES_encrypt(k):
    key = hashlib.sha256(str(k).encode()).digest()[:16]
    iv = os.urandom(16)
    cipher = AES.new(key, AES.MODE_CBC, iv)
    cipher = cipher.encrypt(pad(FLAG, 16))
    data = {}
    data["iv"] = iv.hex()
    data["cipher"] = cipher.hex()
    return data


a = 46
d = 20
p1 = 826100030683243954408990060837
K1 = (a, d, p1)
G1 = (560766116033078013304693968735, 756416322956623525864568772142)
P1 = mul_curve(c, G1, K1)
Q1 = mul_curve(b, G1, K1)
print("P1 =", P1)
print("Q1 =", Q1)
# P1 = (528578510004630596855654721810, 639541632629313772609548040620)
# Q1 = (819520958411405887240280598475, 76906957256966244725924513645)

p = 770311352827455849356512448287
E = EllipticCurve(GF(p), [-c, b])
G = E.gens()[0]
P = G * key
data = AES_encrypt(key)
print("G =", G)
print("P =", P)
print("data =",data)
# G = (584273268656071313022845392380 : 105970580903682721429154563816 : 1)
# P = (401055814681171318348566474726 : 293186309252428491012795616690 : 1)
# data = {'iv': 'bae1b42f174443d009c8d3a1576f07d6', 'cipher': 'ff34da7a65854ed75342fd4ad178bf577bd622df9850a24fd63e1da557b4b8a4'}

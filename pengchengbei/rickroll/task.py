from Crypto.Util.number import *
from Crypto.Util.Padding import pad
from pwn import xor
import os
from hashlib import *
from random import *


HINT = b"111"
FLAG = "flag{xxxxxxxx}"
EFFECTIVE_ROW = 6


def rickroll_loader():
    with open("rickroll", "r") as file:
        lines = [line.strip().encode() for line in file if line.strip()]
    return lines


def find_ezprime(size):
    while True:
        prime = getPrime(size)
        if isPrime(prime // 2):
            return prime


def secure_encrypt(message_parts, hint_value):
    modulus = find_ezprime(260)
    
    key_material = os.urandom(32)
    print("key_material =", key_material)
    multipliers = [getrandbits(256) for _ in range(EFFECTIVE_ROW)]

    encrypted_parts = [
        int((multiplier * hint_value + bytes_to_long(xor(pad(chunk, 32)
            [::-1], key_material))) % (modulus - 1))
        for multiplier, chunk in zip(multipliers, message_parts)
    ]
    for multiplier, chunk in zip(multipliers, message_parts):
        print("chunk =", chunk)
        print("multiplier =", multiplier)
        print(f"pad(chunk, 32) = {pad(chunk, 32)}")
        print("pad(chunk, 32)[] = ", pad(chunk, 32)[::-1])

    return encrypted_parts, multipliers, modulus


if __name__ == "__main__":
    rickroll = rickroll_loader()
    m = bytes_to_long(HINT)

    encrypted_lyrics, multipliers, modulus = secure_encrypt(
        rickroll[:EFFECTIVE_ROW], m)
    # print(f"Rickroll lyrics: {rickroll[:EFFECTIVE_ROW]}")
    print("S =", encrypted_lyrics)
    print("V =", multipliers)
    print("n =", modulus)


'''
S = [624073892368439332713131144655355187273652775732037030273908973687487472640419, 1129513550732743550887354593625951854836036688324123410864182971141396110133306, 1117643028354341949186759218964558582164677605237787761003042032239935547551873, 151619055620013230556169740951169935393567570823439146992800622058967940011364, 596106506159944398847755500086869373163910176213091804211992440336880292610397, 685472210701608040945173323626153641749419080165879222271110177606156013942182]
V = [100024809269721744282017864103544473542698741247649693420201028956644193231147, 85493218764912449360009112267171851264674952927507787108286827385372626006804, 75451455656190167222034904545925816909383290106210237096763781707294423744719, 1864420400658866895837249178680154965580281261003086054650703872439476331244, 111069754111223622246512532174936637994215526100226395068812327641951277359169, 88031405587803201423744918486788030404029698214504194443110805396831023823738]
n = 1497114501625523578039715607844306226528709444454126120151416887663514076507099
'''
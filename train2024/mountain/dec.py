from sympy import symbols, Eq, solve
import numpy as np
from Crypto.Util.Padding import pad
from tqdm import tqdm

flag = b'We choose to go to the Moon. We choose to go to the Moon... We choose to go to the Moon in this decade and do the other things, not because they are easy, but because they are hard'

crypt = "28452235270529613e707821653f74021f236f753434435d023372514d4362690b5f760958252d4973331f1f367e37691e226a23530a032f20776c356f1e23496a7348743c5439551c6c452670051c70260e09190a0a05382448031c2909331363544b0035251775707d407e3e09243b55631f6f58234e6a1f170035634f2f3c7e102b7e74645c502a376f1e0f5a3d751d79146322367568385d1a5545712b1e486e2e2d6e5b7e644c175b2e7b103a29613e793c70600e41396c69034c38787411483c220e0e0a136c7b432f60085a21660f07515e25680f17531d3d447b6d302f7615592e606a7a4a01100137402a1c7d0b443d2a0c4515385f3b2d483c6b0b360e5e19083f08026876122e3414457e1759664b60064b33214f084327126201116725556b31625f5b357d6d20472b356b5b0b03076b225547502b6b703e716b326f1918324766652072694f0e5268324f2b3359763b714e73205e036e626d1e4734555a0365126c6453610763661f1611597d460b18281f5106033d417602113904173d7e054f5f08156e596d5e3f32397762714f7d015046480a721f2311384010016a2b321b69016f46545d3236066651190758675978"
crypt_array = [int(crypt[i:i+2], 16) for i in range(0, len(crypt), 2)]


def try_dec_block(plain,cry,arr):
    a,b,c,d,e,f,g,h = symbols('a b c d e f g h')
    eq1 = Eq(a*plain[0]+b*plain[1]+c*plain[2]+d*plain[3]+e*plain[4]+f*plain[5]+g*plain[6]+h*plain[7], cry[0]+arr[0]*8)
    eq2 = Eq(a*plain[8]+b*plain[9]+c*plain[10]+d*plain[11]+e*plain[12]+f*plain[13]+g*plain[14]+h*plain[15], cry[8]+arr[1]*8)
    eq3 = Eq(a*plain[16]+b*plain[17]+c*plain[18]+d*plain[19]+e*plain[20]+f*plain[21]+g*plain[22]+h*plain[23], cry[16]+arr[2]*8)
    eq4 = Eq(a*plain[24]+b*plain[25]+c*plain[26]+d*plain[27]+e*plain[28]+f*plain[29]+g*plain[30]+h*plain[31], cry[24]+arr[3]*8)
    eq5 = Eq(a*plain[32]+b*plain[33]+c*plain[34]+d*plain[35]+e*plain[36]+f*plain[37]+g*plain[38]+h*plain[39], cry[32]+arr[4]*8)
    eq6 = Eq(a*plain[40]+b*plain[41]+c*plain[42]+d*plain[43]+e*plain[44]+f*plain[45]+g*plain[46]+h*plain[47], cry[40]+arr[5]*8)
    eq7 = Eq(a*plain[48]+b*plain[49]+c*plain[50]+d*plain[51]+e*plain[52]+f*plain[53]+g*plain[54]+h*plain[55], cry[48]+arr[6]*8)
    eq8 = Eq(a*plain[56]+b*plain[57]+c*plain[58]+d*plain[59]+e*plain[60]+f*plain[61]+g*plain[62]+h*plain[63], cry[56]+arr[7]*8)
    # 已知上面8个方程组结果分别是c[0] c[8] c[16] c[24] c[32] c[40] c[48] c[56]
    # 求解a b c d e f g h
    res=solve((eq1,eq2,eq3,eq4,eq5,eq6,eq7,eq8),(a,b,c,d,e,f,g,h))
    # 判断是否是整数
    if isinstance(res[a],int):
        print(res)

def dec(plain):
    # 180/8=22.5
    plain = plain[:176]
    x=0
    cry= list(crypt_array)
    # 要计算的K的第一列
    # 理论上是求解线性方程组问题
    # 不能用暴力
    # python sympy库可以解线性方程组
    # 使用sympy库解线性方程组
    # 考虑相乘后取模127
    # 8个方程组
    for i in tqdm(range(8)):
        for j in tqdm(range(8)):
            for k in tqdm(range(8)):
                for l in tqdm(range(8)):
                    for m in tqdm(range(8)):
                        for n in tqdm(range(8)):
                            for o in tqdm(range(8)):
                                for p in tqdm(range(8)):
                                    # 计算次数
                                    x+=1
                                    print(x)
                                    try_dec_block(plain,cry,[i,j,k,l,m,n,o,p])

print(dec(flag))
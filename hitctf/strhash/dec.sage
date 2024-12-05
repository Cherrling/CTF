from sage.all import *

# 已知参数
n = 28  # 假设 flag 长度为 28

# 从本地文件读取哈希值和模数
def read_hashes_and_mods(filename):
    with open(filename, 'r') as f:
        lines = f.readlines()
        hashes = [int(line.split()[0]) for line in lines]
        mods = [int(line.split()[1]) for line in lines
    return hashes, mods

hashes, mods = read_hashes_and_mods('res.txt')

M = prod(mods)
res_totals = crt(hashes, mods)

# 构造格基
dim = n + 1
B = Matrix(ZZ, dim, dim)

for i in range(n):
    B[i, i] = 233^(n - i - 1)
    B[i, n] = -1

B[n, n] = M

# 目标向量
t = vector([0]*n + [res_totals])

# LLL 约减
B = B.LLL()

# 尝试从约减后的基中提取短向量
for row in B.rows():
    possible_flag_nums = row[:n]
    # 检查 ASCII 范围
    if all(32 <= abs(c) <= 126 for c in possible_flag_nums):
        flag = ''.join([chr(abs(c)) for c in possible_flag_nums])
        # 验证哈希值
        if verify_flag(flag, hashes, mods):
            print("Found flag:", flag)
            break
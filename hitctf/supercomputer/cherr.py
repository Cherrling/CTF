import numpy as np

def f(x):
    x ^= ((x >> 11) & 0xb114514bb114514b)
    x ^= ((x << 13) & 0x114514cc114514cc)
    x ^= ((x << 17) & 0xaa191981aa191981)
    x ^= ((x >> 19) & 0xb1919810b1919810)
    x ^= ((x << 23) & 0x114514cd114514cd)
    x ^= ((x >> 29) & 0xb114514ab114514a)
    x ^= ((x << 31) & 0x114514cb114514cb)
    x ^= ((x << 37) & 0xaa19198caa19198c)
    x ^= ((x >> 41) & 0xb191981db191981d)
    x ^= ((x << 43) & 0x114514ce114514ce)
    x &= 0xFFFFFFFFFFFFFFFF  # 保证 x 为 64 位
    return x

def build_matrix():
    size = 64
    M = np.zeros((size, size), dtype=np.uint8)
    for i in range(size):
        x = 1 << i
        y = f(x)
        for j in range(size):
            if (y >> j) & 1:
                M[j, i] = 1
    return M

def matrix_pow(mat, power):
    result = np.identity(mat.shape[0], dtype=np.uint8)
    while power > 0:
        if power % 2 == 1:
            result = np.mod(np.dot(result, mat), 2)
        mat = np.mod(np.dot(mat, mat), 2)
        power //= 2  # 修正此处，使用整除 2
    return result

def vector_to_int(vec):
    x = 0
    for i in range(len(vec)):
        if vec[i] % 2 == 1:
            x |= (1 << i)
    return x

def int_to_vector(x):
    return np.array([(x >> i) & 1 for i in range(64)], dtype=np.uint8)

def calc(n):
    x0 = 0x1122334455667788
    x0_vec = int_to_vector(x0)
    M = build_matrix()
    Mn = matrix_pow(M, n)
    xn_vec = np.mod(np.dot(Mn, x0_vec), 2)
    xn = vector_to_int(xn_vec)
    return xn

# 示例运行
n = 10000000000000000
result = calc(n)
print('calc(%d) = 0x%x' % (n, result))
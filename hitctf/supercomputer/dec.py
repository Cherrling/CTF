def f(x):
    x ^= ((x >> 11) & 0xB114514BB114514B)
    x ^= ((x << 13) & 0x114514CC114514CC)
    x ^= ((x << 17) & 0xAA191981AA191981)
    x ^= ((x >> 19) & 0xB1919810B1919810)
    x ^= ((x << 23) & 0x114514CD114514CD)
    x ^= ((x >> 29) & 0xB114514AB114514A)
    x ^= ((x << 31) & 0x114514CB114514CB)
    x ^= ((x << 37) & 0xAA19198CAA19198C)
    x ^= ((x >> 41) & 0xB191981DB191981D)
    x ^= ((x << 43) & 0x114514CE114514CE)
    return x

def build_matrix():
    M = []
    for i in range(64):
        x = 1 << i
        y = f(x)
        row = 0
        for j in range(64):
            if (y >> j) & 1:
                row |= 1 << j
        M.append(row)
    return M

def mat_mul(A, B):
    C = [0]*64
    for i in range(64):
        for k in range(64):
            if (A[i] >> k) & 1:
                C[i] ^= B[k]
    return C

def mat_pow(mat, power):
    result = [1 << i for i in range(64)]
    while power > 0:
        if power % 2 == 1:
            result = mat_mul(result, mat)
        mat = mat_mul(mat, mat)
        power //= 2
    return result

def mat_vec_mul(mat, vec):
    result = 0
    for i in range(64):
        if (vec >> i) & 1:
            result ^= mat[i]
    return result

def calc(n):
    x0 = 0x1122334455667788
    M = build_matrix()
    M_pow = mat_pow(M, n)
    return mat_vec_mul(M_pow, x0)

print('flag{%s}' % hex(calc(10000000000000000)))

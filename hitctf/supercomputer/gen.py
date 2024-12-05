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
    return x

def calc(n):
    x = 0x1122334455667788
    for _ in range(n):
        x = f(x)
    return x

assert calc(1) == 0xaa732acf4eb6a79d
assert calc(10) == 0x28262ecbd673b7d7
assert calc(100) == 0x23a6b8be477b7c3
assert calc(1000) == 0x93737600f66aa785
assert calc(100000) == 0x8a762387c4e3aed8
assert calc(1000000) == 0x1267270a756bf7df
assert calc(10000000) == 0x39377648f4e367d7
assert calc(100000000) == 0x82232f8777773e8a
assert calc(1000000000) == 0x8762b83d6faa783
assert calc(10000000000) == 0x27237cdd6726788
assert calc(100000000000) == 0x97f6b44e7fe66de
assert calc(1000000000000) == 0x88723b8666ff27ca
assert calc(10000000000000) == 0x8a32670ee5ef6ec0
assert calc(100000000000000) == 0x993b3b4265ea6edb
assert calc(1000000000000000) == 0x8b732ec065e7e795
assert calc(10000000000000000) == 0x8a7623c8c6e3e7d8
print('flag{%s}' % hex(calc(10000000000000000)))


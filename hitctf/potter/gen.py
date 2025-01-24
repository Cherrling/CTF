from secret import key, flag
import string, random

assert len(key) == 20
assert all(map(lambda x: x in '0123456789abcdef', key))
assert flag == "flag{%s}" % key
assert int(key.encode().hex(), base=23) % 998244353 == 333660883

text = "".join(
    filter(lambda x: x in string.ascii_letters, open("harry-potter.txt", encoding='utf8').read().lower())
)
assert len(text) >= 100000


class Machine:
    def __init__(self, key):
        self.key = list(key.encode())

    @staticmethod
    def next_rand(x):
        return (x * 114514123 + 1919810) % 998244353

    @staticmethod
    def next_many_rands(x, n):
        for _ in range(n):
            x = Machine.next_rand(x)
            yield x

    @staticmethod
    def char_add(x, y):
        z = sum(map(string.ascii_lowercase.index, (x, y))) % 26
        return string.ascii_lowercase[z]

    def enc_char(self, c):
        w = list(Machine.next_many_rands(self.key.pop(0), 26))
        self.key.append(Machine.next_rand(w[-1]))
        return Machine.char_add(c, random.choices(string.ascii_lowercase, weights=w)[0])

    def enc(self, s):
        return "".join(map(self.enc_char, s))


open("cipher.txt", "w").write(Machine(key).enc(text))

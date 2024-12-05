nbit = 128

class LFSR:
    def __init__(self, seed: int, mask: int):
        self.state = seed & (2**nbit - 1)
        self.mask = mask & (2**nbit - 1)

    def __next__(self):
        b = (self.state & self.mask).bit_count() & 1
        self.state = ((self.state << 1) | b) & (2**nbit - 1)
        return b
    
    def __call__(self, bits: int):
        out = 0
        for _ in range(bits):
            out = (out << 1) | next(self)
        return out

class NFSR:
    def __init__(self, lfsr0: LFSR, lfsr1: LFSR, noise: iter):
        self.lfsr0 = lfsr0
        self.lfsr1 = lfsr1
        self.noise = noise
    
    def __next__(self):
        b0, b1 = self.lfsr0(1), self.lfsr1(1)
        b = b0 ^ b1 ^ next(self.noise)
        return b
    
    def __call__(self, bits: int):
        out = 0
        for _ in range(bits):
            out = (out << 1) | next(self)
        return out

    def encrypt(self, msg: bytes):
        return bytes([m ^ self(8) for m in msg])

def NOISE(lfsr: LFSR, p: float=2/3, prec: int=256):
    import secrets
    
    t = 2**prec
    while True:
        if lfsr(1):
            yield int(secrets.randbelow(t) / t > p)
        else:
            yield int(secrets.randbelow(t) / t <= p)
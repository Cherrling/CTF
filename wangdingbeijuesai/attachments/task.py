import secrets, signal
from utils import nbit, LFSR, NFSR, NOISE


def proof_of_work():
    import random, string, hashlib

    ss = ''.join(random.choices(string.ascii_letters + string.digits, k=20))
    sh = hashlib.sha256(ss.encode()).hexdigest()
    print(f"|    sha256(XXXX + {ss[4:]}) == {sh}")
    print(ss[:4])
    prefix = input("|    XXXX>")
    return prefix == ss[:4]


if __name__ == "__main__":
    try:
        assert proof_of_work()
        print('a')
        # signal.alarm(666)
        
        seed, mask = [secrets.randbits(12) | 2**(12-1) for _ in range(2)]
        lfsr = LFSR(seed, mask)
        noise = NOISE(lfsr)
        seeds = [secrets.randbits(nbit) | 2**(nbit-1) for _ in range(2)]
        masks = [secrets.randbits(nbit) | 2**(nbit-1) for _ in range(2)]
        print(f"|  {masks = }")
        
        args = [(512, 128), (256, 256), (128, 512)]
        n, m = args[int(input('|  args>')) % len(args)]

        print("|  Good luck")
        for _ in range(n):
            print("|  Menu:\n|    [H]it\n|    [S]tand\n|    [Q]uit")
            inp = input("|  inp>").lower()
            if inp == 'h':
                lfsrs = [LFSR(seed, mask) for seed, mask in zip(seeds, masks)]
                nfsr = NFSR(*lfsrs, noise=noise)
                bits = nfsr.encrypt(b'\x00'*m)
                print(f"|  {bits.hex() = }")
            else:
                if inp == 's' and all(int(input('|  seed>')) == seed for seed in seeds):
                    print('|  🏁', open('flag', 'r').read())
                break
        print("|  Bye")
    except:
        print("|  Nah")
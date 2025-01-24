import sys
def nx(idx, s):
    return s[idx:] + s[:idx]
def main(p, t1, t2):
    s = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz_{}"
    t = [[nx((i+j) % len(s), s) for j in range(len(s))] for i in range(len(s))]
    f1 = 0
    f2 = 0
    c = ""
    for a in p:
        c += t[s.find(a)][s.find(t1[f1])][s.find(t2[f2])]
        f1 = (f1 + 1) % len(t1)
        f2 = (f2 + 1) % len(t2)
    return c
print(main(sys.argv[1], sys.argv[2], sys.argv[3]))


"""
python test.py flag{xxxxxx(32位)} xxxx(4位) xxxx(4位)
output:
S0vAeRotMHuuMPqwMHvoGSltRQlwHRx}JOzoOh
"""
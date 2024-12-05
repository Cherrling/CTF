files = [[], []]
index = 0
flag = []
with open('candump.log', 'r') as r:
    for i in range(1):
        line = r.readline()
    line = r.readline().strip()
    while line is not None and len(line) != 0:
        if '7D1#' in line:
            index = 1
            line = r.readline().strip()
            continue
        line = line.split('#')
        files[index].append(line[1])
        line = r.readline().strip()
        
for i in range(len(files[0])):
    tmpa = bytes.fromhex(files[0][i])
    tmpb = bytes.fromhex(files[1][i])
    tmpc = b''
    for j in range(len(tmpa)):
        tmpc += bytes.fromhex(hex(tmpa[j] ^ tmpb[j])[2:].rjust(2, '0'))
    flag.append(tmpc)
for i in flag:
    print(i.hex().upper())
passwordList = [0 for _ in range(8)]
with open('flag.zip', 'wb') as w:
    for i in range(1, len(flag)):
        w.write(flag[i][3:])
        num = flag[i][1]
        if num != 0xff:
            tmp = flag[i][2]
            for j in range(8):
                passwordList[j] = passwordList[j] | (((tmp >> (7 - j)) & 1) <<(num))
print('password:')
for i in passwordList:
    print(chr(i), end = '')
print('\n')
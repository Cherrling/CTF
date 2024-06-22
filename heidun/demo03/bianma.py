#将字符串转为2进制，3进制，4进制等不同编码

# 2进制
a = 'hello'
b = bytes(a, encoding='utf-8')
print(b)

# 3进制

a = 'hello'
b = bytearray(a, encoding='utf-8')
print(b)



# 16进制
a = 'hello'
b = bytes.fromhex(a)
print(b)

# 32进制
import base64
a = 'hello'
b = base64.b32encode(a.encode('utf-8'))
print(b)

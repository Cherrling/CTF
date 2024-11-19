import json

from gmssl.sm4 import CryptSM4, SM4_ENCRYPT, SM4_DECRYPT

    
def decsm4(value):
    key = b'FD1AN2B1B5HM9VAW'
    iv = b'\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00' #  bytes类型

    
    encrypt_value=bytes.fromhex(value) #  bytes类型

    crypt_sm4 = CryptSM4()    # 默认填充为”3-PKCS7“

    try:
        
        crypt_sm4.set_key(key, SM4_DECRYPT)
        decrypt_value = crypt_sm4.crypt_cbc(iv , encrypt_value) #  bytes类型
        return decrypt_value
    except:
        return ''


# 从 JSON 文件读取数据
with open('sm4.json', 'r', encoding='utf-8') as file:
    # 使用 json.load() 将文件内容解析为 Python 对象
    data = json.load(file)

cnt=0
for item in data:
    # print(item['password'])
    dec = decsm4(item['password'])
    print(dec)
    item['enc'] = 1
    if dec != '':
        cnt+=1
        item['password'] = dec.decode('utf-8', errors='ignore')
        item['enc'] = 0
print(data)
print(cnt)
# 将 JSON 数据存储到另一个文件
with open('smfin.json', 'w', encoding='utf-8') as json_file:
    json.dump(data, json_file, ensure_ascii=False, indent=4)
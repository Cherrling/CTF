import json
from gmssl import *
from Crypto.Util.number import *

# 打开 JSON 文件并读取内容
with open('smfin.json', 'r', encoding='utf-8') as file:
    data = json.load(file)

# 打印读取的数据
print(len(data))

cnt = 0

master_key = Sm9EncMasterKey()
master_key.import_encrypted_master_key_info_pem('sm9.pem', '123456')
# print(data[169])



# 遍历每个对象并修改指定字段
# for item in data:
for i in range(0, len(data)):
    item = data[i]
    if 'password' in item and item['enc'] == 0:
        receiver_id = item['username']
        plaintext = hex(bytes_to_long(item['password'].encode()))[2:]
        # print(receiver_id)
        # print(plaintext)
        ciphertext = master_key.encrypt(plaintext.encode(), receiver_id)
        print(cnt)
        cnt = cnt+1
        item['password'] = hex(bytes_to_long(ciphertext))[2:]
    # if cnt > 5:
    #     break
    item['password'] = item['password'][:6] + '*'*6
    if 'username' in item:
        item['username'] = item['username'][0] + (len(item['username'])-2)*'*' + item['username'][-1]
    if 'phone' in item:
        item['phone'] = item['phone'][0:3] + '****' + item['phone'][7:12]
    if 'nickname' in item:
        if len(item['nickname']) == 2:
            item['nickname'] = item['nickname'][0] + '*'
        elif len(item['nickname']) == 3:
            item['nickname'] = item['nickname'][0] + '*' + item['nickname'][2]
        elif len(item['nickname']) == 4:
            item['nickname'] = item['nickname'][0] + '**' + item['nickname'][2]
    if 'birthday' in item:
        item['birthday'] = item['birthday'][0:4] + '****'
    if 'sex' in item:
        pass
    if 'card_id' in item:
        item['card_id'] = '*'*6 + item['card_id'][6:10] +'*'*8
    if 'bank_id' in item:
        item['bank_id'] = item['bank_id'][0:2] + '*'*(len(item['bank_id'])-4)+item['bank_id'][-2:]
    if 'email' in item:
        a = item['email'].split('@')
        item['email'] = a[0][0]+'*'*(len(a[0])-1) + '@' + a[1]
        # item['email'] = a[0][0]+'****' + '@' + a[1]
    if 'address' in item: 
        item['address'] = item['address'][:6]+'*'*(len(item['address'])-6)
    # print(item)
    # exit()
        
    # 可以继续修改其他字段...
# 用户名,密码,手机号,姓名,出生日期,性别,身份证,银行卡,邮箱,地址
# 打印修改后的数据
# print(data)


data = [item for item in data if item['enc'] == 1]


# 将 JSON 数据存储到另一个文件
with open('aaaa.json', 'w', encoding='utf-8') as json_file:
    json.dump(data, json_file, ensure_ascii=False, indent=4)

    
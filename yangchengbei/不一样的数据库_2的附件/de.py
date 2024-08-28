#测试解压加密的zip文件
import zipfile
import os
import shutil

def extract_zip(zip_path, password):
    with zipfile.ZipFile(zip_path) as zf:
        #测试密码是否正确
        try:
            zf.extractall(pwd=password.encode())
        except Exception as e:
            return False
    return True

def decrypt_zip(zip_path, password):
    extract_path = os.path.splitext(zip_path)[0]
    if not os.path.exists(extract_path):
        os.makedirs(extract_path)
    if extract_zip(zip_path, password):
        return extract_path
    return False
#main
zip_path = 'flag.zip'
password = '0xB54BFB67'

#循环测试不同的大小写组合
for i in range(2**len(password)):
    binary = bin(i)[2:].zfill(len(password))
    guess = ''.join([password[j].lower() if binary[j] == '0' else password[j].upper() for j in range(len(password))])
    print(f"Trying password: {guess}")
    extract_path = decrypt_zip(zip_path, guess)
    if extract_path:
        print(f"Successfully extracted to {extract_path}")
        break

    
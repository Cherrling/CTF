from gmssl import sm3, func

# 要进行杂凑的字符串
input_string = "heidun2024"

# 将字符串转换为字节
input_bytes = input_string.encode()

# 计算SM3杂凑值
hash_value = sm3.sm3_hash(func.bytes_to_list(input_bytes))

print("原字符串: ", input_string)
print("SM3杂凑值: ", hash_value)

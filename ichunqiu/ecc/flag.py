result_left = 149691910197772493630846090710751498787161757266225
result_right = 148194990859991056906456851498975219214210159620733

# 将大整数转换为字节并解码为字符串，忽略解码错误
left_part = int(result_left).to_bytes((result_left.bit_length() + 7) // 8, 'big').decode(errors='ignore')
right_part = int(result_right).to_bytes((result_right.bit_length() + 7) // 8, 'big').decode(errors='ignore')

# 将两个部分组合成flag
flag = left_part + right_part
print(flag)

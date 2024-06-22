def string_to_base18(s):
    # 将字符串转换为字节表示
    byte_array = s.encode('utf-8')
    
    # 将字节数组转换为整数表示
    integer_representation = int.from_bytes(byte_array, byteorder='big')
    
    # 定义18进制字符
    base18_chars = '0123456789ABCDEFGH'
    
    # 将整数转换为18进制字符串
    if integer_representation == 0:
        return base18_chars[0]
    
    base18_string = ''
    while integer_representation > 0:
        integer_representation, remainder = divmod(integer_representation, 18)
        base18_string = base18_chars[remainder] + base18_string
    
    return base18_string

# 示例用法
input_string = "ergdgjboglfpgcbpbofmgafhfngpfoflfpfkgjgccndcfqfpgcgofofpdadadagr"
base18_string = string_to_base18(input_string)
print(f"The string '{input_string}' in base18 is: {base18_string}")

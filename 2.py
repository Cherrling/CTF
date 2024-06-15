
def hex_string_decrypt(hex_str):
    # 确保输入的十六进制字符串长度为偶数
    if len(hex_str) % 2 != 0:
        raise ValueError("Invalid hex string length")

    # 转换十六进制字符串为字节数组
    byte_array = bytes.fromhex(hex_str)

    # 减去33并确保结果在合法字节范围内
    decrypted_bytes = [(byte - 33) & 0xFF for byte in byte_array]

    # 将结果转换为十六进制字符串
    decrypted_hex_str = ''.join(format(byte, '02x') for byte in decrypted_bytes)

    return decrypted_hex_str

if __name__ == "__main__":
    # 输入一个十六进制字符串
    hex_input = "2b2e273d3b797e75272326766a6f7222716f3a727e7c6f362e28746d697d2c2272752876752172776b9e"

    try:
        decrypted_output = hex_string_decrypt(hex_input)
        print("Decrypted hex string:", decrypted_output)
    except ValueError as e:
        print("Error:", e)

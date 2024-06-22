import requests

# 发送GET请求
url = 'http://localhost:8080/my_encode.php?key=111&data=123'  # 将此URL替换为实际的目标URL
response = requests.get(url)

# 获取响应内容
response_content = response.content

# 将响应内容转换为十六进制格式
hex_output = response_content.hex()

# 打印十六进制格式的响应内容
print(hex_output)

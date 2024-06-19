
import requests

url = "http://train2024.hitctf.cn:26557/?spell=viwo50"

payload = b"\xaa\xbb\xcc\xdd"
headers = {
   'To': 'Doraemon',
   'Cookie': 'icecream=good;friedchicken=nice',
   'User-Agent': 'Apifox/1.0.0 (https://apifox.com)',
   'Content-Type': 'raw',
   'Accept': '*/*',
   'Host': '8.130.75.113:25430',
   'Connection': 'keep-alive'
}

response = requests.request("POST", url, headers=headers, data=payload)

print(response.text)
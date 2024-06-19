import requests
import re

url = "http://train2024.hitctf.cn:26387/"

def extract_span_content(response_text):
    # Use regex to extract the content inside the span tag
    match = re.search(r'<span id="msg" class="ui purple text">(.+?)<\/span>', response_text)
    if match:
        content = match.group(1)
        return content
    return "No matching content found"

def main():
    payload={}
    headers = {
    'User-Agent': 'Apifox/1.0.0 (https://apifox.com)',
    'Accept': '*/*',
    'Host': 'train2024.hitctf.cn:26387',
    'Connection': 'keep-alive'
    }

    response = requests.request("GET", url, headers=headers, data=payload)

    print(response.text)
    if response.status_code == 200:
        response_text = response.text
        content = extract_span_content(response_text)
        print(f"Extracted span content: {content}")
    else:
        print(f"Request failed with status code {response.status_code}")

if __name__ == "__main__":
    main()
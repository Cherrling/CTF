import requests
import re

from selenium import webdriver

from selenium.webdriver import ActionChains

from selenium.webdriver.common.by import By 

from selenium.webdriver.edge.service import Service

from selenium.webdriver.common.keys import Keys

url = "http://train2024.hitctf.cn:26876/"

def main():
    ser = Service()
    ser.path = r'C:\\Code\\CTF\\train2024\\chromedriver-win64\\chromedriver.exe'
    driver = webdriver.Chrome(service=ser)
    driver.get(url)
    # Find the element by id
    while True:

        msg = driver.find_element(By.ID, "msg")
        # Extract the content of the element
        print(msg.text)

        input=driver.find_element(By.ID, "gameinput")
        if msg.text == "Are you ready?":
            input.send_keys("ready")
            # 发送回车
            input.send_keys(Keys.RETURN)
        else:
            # 提取算式<span id="msg" class="ui purple text">0 * 1 (Problem 1/100)</span>
            calc=msg.text.split("(")[0]
            # 计算结果
            result=eval(calc)
            input.send_keys(result)
            input.send_keys(Keys.RETURN)






if __name__ == "__main__":
    main()
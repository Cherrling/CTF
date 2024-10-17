# 山东省赛 Cherrling
# 签到题

直接关注公众号获取即可

# ezrsa
 
注意到 ```hint = pow(A, n-p-q, n)``` ，`hint` 与 `n` 互质，即 `hint = A^(-1)mod(n)`。对 `A` 求模逆即可得到 `hint`。

```A = pow(h + p * 2024, e, n)``` ， 即 ```(h+P*2024)^(e) = k*p*q + A```。

两边同时对 p 取模，即 ```h^e mod(p) = A mod (p)```

即  ```h^e - A = k*p```，将 ```(h^e-A)``` 与 ```n``` 求 gcd 即可

```python
A = gmpy2.invert(hint, n)

he= pow(h, e, n)

kp=A-he
# 求 kp 和 n 的gcd
gcd = gmpy2.gcd(kp, n)
print(gcd)
p=gcd
q=n//p
assert p*q == n
d = gmpy2.invert(e, (p-1)*(q-1))
m=pow(c, d, n)
print(long_to_bytes(m))
```

# ezcalc

用脚本向网站发起post,传参 answer, 即可拿到网站源码

```python 
import uuid,json,os,hashlib,time
from flask import Flask, request, session
from config import key,get_calc

app = Flask(__name__)

app.secret_key = str(uuid.uuid4())

black_list=['__init__']
s='123456789+-'
def check(data):
    for i in black_list:
        if i in data:
            return False
    return True

def merge(src, dst):
    for k, v in src.items():
        if hasattr(dst, '__getitem__'):
            if dst.get(k) and type(v) == dict:
                merge(v, dst.get(k))
            else:
                dst[k] = v
        elif hasattr(dst, k) and type(v) == dict:
            merge(v, getattr(dst, k))
        else:
            setattr(dst, k, v)

class user():
    def __init__(self):
        self.username = ""
        self.password = ""
        pass
    def check(self, data):
        if self.username == data['username'] and self.password == data['password']:
            return True
        return False

Users = []
usernames=[]

@app.route('/admin/register',methods=['POST'])
def register():
    if request.data:
        try:
            data = json.loads(request.data.decode())
            if "username" not in data or "password" not in data:
                return "Register Failed"
            usernames.append(data['username'])
        except Exception:
            return "Register Failed"
        return "Register Success"
    else:
        return "Register Failed"


@app.route('/admin/login',methods=['POST'])
def login():
    if request.data:
        try:
            data = json.loads(request.data)
            if "username" not in data or "password" not in data:
                return "Login Failed"
            if data["username"] in usernames:
                session["username"] = data["username"]
                session["role"] = "guest"
                return "Login Success"
        except Exception:
            return "Login Failed"
    return "Login Failed"


@app.route('/admin/admin', methods=['GET', 'POST'])
def admin():
    username = session.get('username')
    role = session.get('role')
    if not username or role != 'admin':
        return "no admin"
    if request.data:
        if not check(request.data.decode()):
            return "No No No"
        User = user()
        merge(data, User)
        Users.append(User)
        return "Welcome admin"
    else:
        return "whoami"


@app.route('/',methods=['GET','POST'])
def index():
    if request.method != 'POST':
        c1,x1=get_calc()
        session['x1']=x1
        session['time']=int(time.time())
        return c1+' = ? <br><br>plz give me answer'

    answer = request.form.get("answer")
    t = session.get('time')
    x1= session.get('x1')
    if answer == None or x1 == None or t == None:
        return "something error"
    else:
        if int(time.time())-t>2:
            return "time too long"
        else:
            if hashlib.md5(answer.encode()).hexdigest() == x1:
                return open(__file__, "r").read()
            else:
                return "calc failed"


@app.route('/admin/calc',methods=['POST'])
def calc():
    if request.data:
        try:
            data = json.loads(request.data)
            print(data)
            if "calc" not in data or "answer" not in data:
                return "Failed"
            for i in data["calc"]:
                if i not in s:
                    return "no rce , only math"
            if eval(data["calc"]) == data["answer"]:
                return key
        except Exception:
            return "Failed"
    return "Failed"

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
```

主要关注其中涉及到 key 的部分 
```python
@app.route('/admin/calc',methods=['POST'])
def calc():
    if request.data:
        try:
            data = json.loads(request.data)
            print(data)
            if "calc" not in data or "answer" not in data:
                return "Failed"
            for i in data["calc"]:
                if i not in s:
                    return "no rce , only math"
            if eval(data["calc"]) == data["answer"]:
                return key
        except Exception:
            return "Failed"
    return "Failed"
```

调用 /admin/calc 接口即可拿到 key
```python
import requests
import json

def fetch_calculation():
    # Create a session
    session = requests.Session()
    
    # Fetch data from the server
    response = session.get(url="http://119.45.42.24:20025/")
    
    # Check if the request was successful
    if response.status_code == 200:
        return response.text
    else:
        print(f"Error fetching data: {response.status_code}")
        return None

def parse_calculation(response_text):
    # Split the response text to extract the calculation
    try:
        calculation_str = response_text.split("=")[0]
        return eval(calculation_str)  # Evaluate the calculation
    except Exception as e:
        print(f"Error parsing calculation: {e}")
        return None

def send_calculation_result(session, calculation_result):
    # Prepare the data to send
    data = {
        "calc": str(calculation_result),
        "answer": calculation_result
    }
    
    json_data = json.dumps(data)
    
    # Check if the calculation is correct
    if eval(data["calc"]) == data["answer"]:
        print("Calculation is correct.")
    else:
        print("Calculation is incorrect.")
    
    # Send the result back to the server
    response = session.post(url="http://119.45.42.24:20025/admin/calc", data=json_data)
    
    # Check if the request was successful
    if response.status_code == 200:
        print("Response from server:", response.text)
    else:
        print(f"Error sending data: {response.status_code}")

def main():
    response_text = fetch_calculation()
    if response_text:
        calculation_result = parse_calculation(response_text)
        if calculation_result is not None:
            send_calculation_result(requests.Session(), calculation_result)

if __name__ == "__main__":
    main()

```
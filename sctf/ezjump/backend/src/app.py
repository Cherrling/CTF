import os
import subprocess
import urllib.request

from flask import Flask, request, session, render_template

from Utils.utils import *

app = Flask(__name__)
app.secret_key = os.urandom(32)

@app.route('/', methods=['GET'])
def hello():
    return "Welcome to SCTF 2024! Have a Good Time!"


@app.route('/login', methods=['GET'])
def login():
    username = request.args.get("username")
    password = request.args.get("password")
    user =get_user(username)
    if user:
            if password == user['password']:
                if user['role']=="admin":
                    cmd=request.args.get("cmd")
                    if not cmd:
                        return "No command provided", 400
                    if waf(cmd):
                        return "nonono"
                    try:
                        result = subprocess.run(['curl', cmd], stdout=subprocess.PIPE, stderr=subprocess.PIPE,text=True,encoding='utf-8')
                        return result.stdout
                    except Exception as e:
                        return f"Error: {str(e)}", 500
                else:
                    session['username'] = username
                    session['role'] = user['role']
                return render_template('index.html', username=session['username'], role=session['role'])
            else:
                session['username'] = 'guest'
                session['role'] = 'noBody'
                return render_template('index.html', username=session['username'], role=session['role'])
    else:
            add_user(username, password, 'n0B0dy')
            user = get_user(username)
            if user:
                session['username'] = username
                session['role'] = 'noBody'
            else:
                session['username'] = 'guest'
                session['role'] = 'noBody'
            return render_template('index.html', username=session['username'], role=session['role'])
    return "Please give me username and password!"




if __name__ == '__main__':
    app.run(debug=False, host='0.0.0.0', port=5000)

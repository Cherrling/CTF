from flask import Flask, request, render_template, jsonify 
import subprocess 
import os 
import base64 
app = Flask(__name__) 
FLAG = os.getenv("FLAG", "flag{}") 
flag_base64 = base64.urlsafe_b64encode(FLAG.encode()).decode() 
print(f"FLAG: {flag_base64}")
@app.route("/") 
def index(): 
    with open(__file__, "r") as file: 
        source_code = file.read() 
        return source_code 
        
@app.route("/execute", methods=["POST"]) 
def execute(): 
    auth = request.form.get("auth") 
    if not auth: 
        return jsonify({"error": "auth is required"}), 401 
    # if any(char in "`!@#$%&*()-=+;.[]{}<>\\|;'\"?/" for char in auth): 
    #     return jsonify({"error": "Hacker!"}), 400 
    try: 
        command = ["wget", f"{auth}@127.0.0.1:5000/{flag_base64}"] 
        print(command)
        subprocess.run(command, check=True) 
        return jsonify({"message": "Command executed successfully"}) 
    except Exception as e: 
        return jsonify({"error": "Command failed"}), 500 
        
if __name__ == "__main__": app.run(host="0.0.0.0", port=5000)
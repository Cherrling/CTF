
classes = dict()
caller_map = dict()

with open("./class", "r") as f:
    f.readline()
    f.readline()
    f.readline()

    while line1 := f.readline():
        class_name = line1.split("class ")[1].split("():")[0]
        func_name = f.readline().split("def ")[1].split("(self):")[0]

        assert class_name not in classes, f"duplicate class: {classes}"
        classes[class_name] = func_name

        cond = f.readline().split("if ")[1].split(":")[0]

        assert eval(cond), f"cond not true: {class_name}"

        callee = f.readline().strip().split("(")[0]

        # if callee == "os.system":
        #     root = class_name

        caller_map[callee] = f"{class_name}.{func_name}"

        f.readline()
        f.readline()

cur = "os.system"

class_str = ""
func_str = ""
all_str = ""
cnt = 0

while cur in caller_map:
    cur = caller_map[cur]
    class_str = class_str + cur.split(".")[0][1:]
    func_str = func_str + cur.split(".")[1][1:]
    cnt += 1

    all_str = all_str + cur.split(".")[0][1:] + cur.split(".")[1][1:]

print(cnt)

letters = set(all_str)
print("".join(sorted(list(letters))))

class_str = class_str.replace("O", "0")
func_str = func_str.replace("O", "0")
all_str = all_str.replace("O", "0")

with open("class_str", "w") as f:
    f.write(class_str)
with open("func_str", "w") as f:
    f.write(func_str)

with open("all_str", "w") as f:
    f.write(all_str)

with open("res.7z", "wb") as f:
    f.write(bytes.fromhex(class_str + func_str))
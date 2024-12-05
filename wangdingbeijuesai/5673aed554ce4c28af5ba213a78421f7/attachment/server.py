from Cryptodome.Cipher import AES
from Cryptodome.Util.Padding import pad, unpad
import os
import subprocess
import base64

def readuntil(process, until_text):
	while True:
		output = readline(process)
		if any(substring in output for substring in until_text):
			break
			
	return output

def readline(process):
	return process.stdout.readline()

def process_qemu(code, start_script):
	process = subprocess.Popen([start_script], stdin=subprocess.PIPE, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, bufsize=0)
	
	readuntil(process, ["Input ur b64_code: \n", "Power down"])
	
	process.stdin.write(code+"\n")
	
	result = readuntil(process, ["result", "Power down"])
	
	if "result" in result:
		return result.split("result: ", 1)[1]
	else:
		return "dummydummydummy\n"
	
if __name__ == '__main__':
	print("Welcome to Generic Linux kernel shellcode Challenge")
	code = input("Input ur b64_code: \n")
	
	print("Virtual 1 Working......")
	
	os.chdir("./vir1")
	key = process_qemu(code, "./vir1_start.sh").replace('\n', '').encode("ascii")
	print("Virtual 1 Finished")
	
	print("Virtual 2 Working......")
	os.chdir("../vir2")
	cipher_b64 = process_qemu(code, "./vir2_start.sh").replace('\n', '').encode("ascii")
	print("Virtual 2 Finished")
	
	print("Calc ur result......")
	
	if cipher_b64.find(b"dummy")!=-1 or key.find(b"dummy")!=-1:
		print("Bad kernel shellcode")
	else:
		cipher_raw = base64.b64decode(cipher_b64)
		cipher = AES.new(key, AES.MODE_ECB)
		decrypted = cipher.decrypt(cipher_raw).rstrip(b'\x00').decode("ascii")
		
		print("Nice kernel shellcode")
		print(decrypted)

import socket


REDIS_HOST = '172.11.0.4'
REDIS_PORT = 6379


def pack_command(*args):
    # 构建 RESP 请求
    command = f"*{len(args)}\r\n"
    for arg in args:
        arg_str = str(arg)
        command += f"${len(arg_str)}\r\n{arg_str}\r\n"
    return command.encode('utf-8')



def connect_redis():
    redis_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    redis_socket.connect((REDIS_HOST, REDIS_PORT))
    return redis_socket



def GET(key):
    redis_socket = connect_redis()
    try:
        # 发送命令
        command = pack_command('GET', key)
        redis_socket.sendall(command)

        # 接收响应
        response = b''
        while True:
            chunk = redis_socket.recv(1024)
            response += chunk
            if response.endswith(b'\r\n'):
                break
    finally:
        redis_socket.close()
    if "$-1\r\n" in response.decode('utf-8'):
        return None
        # 提取真实内容
    result_start_idx = response.index(b'\r\n') + 2  # 跳过第一行响应
    result_end_idx = response.index(b'\r\n', result_start_idx)  # 找到第二个\r\n
    real_content = response[result_start_idx:result_end_idx]
    return real_content



def SET(key, value):
    redis_socket = connect_redis()
    try:
        # 发送命令
        command = pack_command('SET', key, value)
        command = WAF(command)
        redis_socket.sendall(command)

        # 接收响应
        response = b''
        while True:
            chunk = redis_socket.recv(1024)
            response += chunk
            if response.endswith(b'\r\n'):
                break
    finally:
        redis_socket.close()

    return response.decode('utf-8')


def WAF(key):
    if b'admin' in key:
        key = key.replace(b'admin', b'hacker')
    return key
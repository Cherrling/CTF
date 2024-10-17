from Crypto.Util.number import *
import socket
from hashlib import md5
from pubkey import *

A = Matrix(GF(P), A)
Y = Matrix(GF(P), Y)

def H(m, R):
    con = m + b''.join([long_to_bytes(int(R[i,j])) for i in range(R.nrows()) for j in range(R.ncols())])
    print(f"Hashing: {con}")
    print(f"Hashed: {md5(con).digest()}")
    print(f"Hashed: {bytes_to_long(md5(con).digest())}")    
    return bytes_to_long(md5(con).digest())

def verify(msg, sign):
    R, s = sign
    return pow(A, s) * pow(Y, H(msg, R)) == R

def start_server(host='0.0.0.0', port=8000):
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.bind((host, port))
    server_socket.listen(1)
    print(f"Listening on {host}:{port}")
    while True:
        try:
            client_socket, client_address = server_socket.accept()
            print(f"Connection from {client_address}")
            try:
                client_socket.sendall(b'Show me the order: ')
                msg = client_socket.recv(1024)[:-1]
                # q 退出
                if msg == b'q':
                    client_socket.sendall(b'Bye')
                    break
                
                client_socket.sendall(b'Now sign here \n')
                client_socket.send(b'R : ')
                R = MatrixSpace(GF(P), 2, 2)(list(map(int, client_socket.recv(4096).decode().split(','))))
                client_socket.send(b's : ')
                s = int(client_socket.recv(1024).decode())
                print(f"Received: {msg}, {R}, {s}")
                
                if verify(msg, (R, s)):
                    client_socket.sendall(b'Order Accepted. \n')
                    if b"Durian Pizza" in msg:
                        client_socket.sendall(b'Nice choice, here is the gift for you: ' + open('/flag','rb').read())
                    else:
                        client_socket.sendall(b'OK. Just a simple order...')
                else:
                    client_socket.sendall(b'You bad hacker, get out of here!')
            except Exception as e:
                client_socket.sendall(b'Error')
                print(f"Error: {e}")
            finally:
                client_socket.close()
                print(f"Connection with {client_address} closed")
        except:
            client_socket.close()

if __name__ == '__main__':
    start_server()
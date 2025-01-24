import time
import os
import socket

from cryptography.hazmat.primitives import serialization
from cryptography.hazmat.primitives.asymmetric import padding

import constants
import tls
import collections

from certificates import get_certificate, load
from cipher_suites import CIPHER_SUITES, CipherSuite
from packer import prepend_length, pack, record
from reader import read

from string import printable
from Crypto.Cipher import AES

class Server:
    def __init__(self, conn, tls_version, ciphers, cert, key):
        self.tls_version = tls_version
        self.conn = conn
        self.server_random = int(time.time()).to_bytes(4, 'big') + os.urandom(28)
        self.messages = []
        self.session_id = os.urandom(32)
        ciphers = ciphers if isinstance(ciphers, collections.abc.Iterable) else tuple(ciphers)
        self.ciphers = tuple(CIPHER_SUITES[cipher] for cipher in ciphers if cipher in CIPHER_SUITES)
        self.cipher_suite: CipherSuite = None
        self.server_certificate = load(open(cert).read())
        self.private_key = serialization.load_pem_private_key(open(key, "rb").read(), password=None)
        self.will = ''
        self.iv = os.urandom(16)
        self.key = os.urandom(16)

    def encrypt(self, data, key, iv):
        cipher = AES.new(key, AES.MODE_CFB, iv=iv)
        ct_bytes = cipher.encrypt(data.encode('utf-8'))
        return ct_bytes.hex()

    def decrypt(self, ct, key, iv):
        ct_bytes = bytes.fromhex(ct)
        cipher = AES.new(key, AES.MODE_CFB, iv=iv)
        data = cipher.decrypt(ct_bytes[:])
        return data
    
    def decode_words(self, words):
        decoded_strings = []
        error_count = 0
        
        for word in words:
            try:
                decoded_strings.append(word.decode())
            except (UnicodeDecodeError, AttributeError):
                error_count += 1
                if error_count > 1:
                    return None
        
        return decoded_strings

    def record(self, content_type, data, *, tls_version=None):
        return record(content_type, tls_version or self.tls_version, data)

    def pack(self, header_type, data, *, tls_version=None):
        return pack(header_type, tls_version or self.tls_version, data, len_byte_size=3)

    def read(self, conn, return_record=False):
        record, content = read(conn)
        if return_record:
            return record, content
        return content

    def serve(self):
        self.client_hello(self.conn)
        self.server_hello(self.conn)
        self.client_flag(self.conn)
        self.server_flag(self.conn)
        self.conn.close()

    def client_hello(self, conn):
        record_bytes, hello_bytes = self.read(conn, return_record=True)
        self.messages.append(hello_bytes)
        assert len(hello_bytes) > 0
        assert hello_bytes[:1] == b'\x01'
        tls_version = hello_bytes[4:6]

        self.client_random, hello_bytes = hello_bytes[6:6 + 32], hello_bytes[6 + 32:]
        session_id_length = int.from_bytes(hello_bytes[:1], 'big')
        session_id, hello_bytes = hello_bytes[:session_id_length + 1], hello_bytes[session_id_length + 1:]
        client_cipher_suites_length, hello_bytes = int.from_bytes(hello_bytes[:2], 'big'), hello_bytes[2:]
        client_cipher_suites, hello_bytes = hello_bytes[:client_cipher_suites_length], hello_bytes[client_cipher_suites_length:]
        compression_method_length, hello_bytes = int.from_bytes(hello_bytes[:1], 'big'), hello_bytes[1:]
        compression_method, hello_bytes = int.from_bytes(hello_bytes[:compression_method_length], 'big'), hello_bytes[compression_method_length:]

        server_cipher_suite = client_cipher_suites[:2]
        self.cipher_suite = CipherSuite.get_from_id(self.tls_version, self.client_random, self.server_random,
                                                    self.server_certificate, server_cipher_suite)

        extensions_length, hello_bytes = int.from_bytes(hello_bytes[:2], 'big'), hello_bytes[2:]
        extensions, hello_bytes = hello_bytes[:extensions_length], hello_bytes[extensions_length:]
        msg_length, hello_bytes = int.from_bytes(hello_bytes[:2], 'big'), hello_bytes[2:]
        msg = hello_bytes[:msg_length]
        if not set(msg) <= set(printable.encode()):
            self.will = "What can I say"
        elif not msg.startswith(b'dont'):
            self.will = "I cant hear clearly."
        else:
            self.will = f"I {msg.decode()} flag [signed]"


    def server_hello(self, conn):
        cipher_suites_bytes = int(CIPHER_SUITES[self.cipher_suite.__str__()]['id'], 16).to_bytes(2, "big")
        session_id_bytes = prepend_length(self.session_id, len_byte_size=1)
        compression_method_bytes = b"\x00"

        signed_msg_bytes = bytes.fromhex(self.encrypt(self.will, self.key, self.iv))
        signed_msg_bytes = prepend_length(bytes.fromhex(self.encrypt(self.will, self.key, self.iv)), len_byte_size=2)
        server_hello_bytes = self.pack(constants.PROTOCOL_SERVER_HELLO,
                                       self.server_random +
                                       session_id_bytes +
                                       cipher_suites_bytes +
                                       compression_method_bytes +
                                       signed_msg_bytes
                                       )

        message = self.record(constants.CONTENT_TYPE_HANDSHAKE, server_hello_bytes, tls_version=self.tls_version)
        self.messages.append(server_hello_bytes)

        cert = self.server_certificate.public_bytes(encoding=serialization.Encoding.DER)
        cert_bytes = len(cert).to_bytes(3, "big") + cert
        cert_bytes = len(cert_bytes).to_bytes(3, "big") + cert_bytes
        cert_bytes = constants.PROTOCOL_SERVER_CERTIFICATE + len(cert_bytes).to_bytes(3, "big") + cert_bytes
        message += self.record(constants.CONTENT_TYPE_HANDSHAKE, cert_bytes, tls_version=self.tls_version)

        server_hello_done = self.record(constants.CONTENT_TYPE_HANDSHAKE, b"\x0e\x00\x00\x00")
        message += server_hello_done
        conn.send(message)
        self.messages.append(message)

    def client_flag(self, conn):
        record_bytes, flag_bytes = self.read(conn, return_record=True)
        self.want_flag = self.decrypt(flag_bytes.hex(), self.key, self.iv)
    
    def server_flag(self, conn):
        words = self.want_flag.split(b' ')
        decoded_strings = self.decode_words(words)
        if decoded_strings is not None:
            msg = ' '.join(decoded_strings)
        else:
            msg = 'Mamba out!'
        if msg == "I do want flag [signed]":
            FLAG = open("/flag").read()
            server_flag_bytes = FLAG.encode()
        else:
            server_flag_bytes = b"You dont want, I will never force you"
        message = self.record(constants.CONTENT_TYPE_HANDSHAKE, server_flag_bytes)
        conn.send(message)

if __name__ == "__main__":
    host = "0.0.0.0"
    port = 23333
    tls_version = tls.TLSV1_2()
    cipher_suites = ('AES256-GCM-SHA384',
                     'AES256-SHA256',
                     'AES256-SHA',
                     'AES128-SHA',)
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.bind((host, port))
    s.listen(10)
    while True:
        try:
            conn, addr = s.accept()
            # print(addr)
            server = Server(conn, tls_version, cipher_suites, cert="cert.pem", key="key.pem")
            server.serve()
        except:
            continue
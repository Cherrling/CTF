CONTENT_TYPE_HANDSHAKE = b'\x16'
CONTENT_TYPE_DATA = b'\x17'
CONTENT_TYPE_ALERT = b'\x15'

PROTOCOL_CLIENT_HELLO = b'\x01'
PROTOCOL_SERVER_HELLO = b'\x02'
PROTOCOL_CLIENT_KEY_EXCHANGE = b'\x10'
PROTOCOL_CHANGE_CIPHER_SPEC = b'\x14'
PROTOCOL_CLIENT_FINISH = b'\x14'
PROTOCOL_SERVER_FINISH = b'\x14'
PROTOCOL_SERVER_CERTIFICATE = b'\x0b'
PROTOCOL_SERVER_HELLO_DONE = b'\x0e'

PROTOCOL_NEW_SESSION_TICKET = b'\x04'

ERROR_FATAL = b'\x02'
ERROR_CODE_BAD_RECORD_MAC = b'\x14'

LABEL_CLIENT_FINISHED = b'client finished'
LABEL_SERVER_FINISHED = b'server finished'

EXTENSION_SERVER_NAME_TYPE_HOSTNAME = b'\x00'

EXTENSION_ALPN_HTTP_2 = b'\x68\x32'
EXTENSION_ALPN_HTTP_1_1 = b'http/1.1'

EXTENSION_EC_POINT_FORMAT_UNCOMPRESSED = b'\x00'

EXTENSION_HEARTBEAT_MODE_PEER_ALLOWED_TO_SEND_REQUESTS = b'\x01'
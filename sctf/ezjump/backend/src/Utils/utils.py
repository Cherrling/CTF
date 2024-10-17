import base64,json
from Utils import redis as r

def get_user(username):
    user_info_serialized = r.GET(f'user:{username}')
    if user_info_serialized:
        user_info = json.loads(base64.b64decode(user_info_serialized).decode())
        return user_info
    else:
        return None
    return None


def add_user(username, password, role):
    user_info = {'password': password, 'role': role}
    user_info_json = json.dumps(user_info)
    user_info_serialized = base64.b64encode(user_info_json.encode()).decode()
    r.SET(f'user:{username}', user_info_serialized)


def waf(url):
    if url.startswith(('file://', 'gopher://')):
        return True
    else:
        return False

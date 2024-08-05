from PIL import Image
from secret import key
from os import system

img = Image.open('flag.bmp')
assert img.mode == 'RGB'
assert img.width == 1046
assert img.height == 374

assert open('flag.bmp', 'rb').read(54) == b'BM\xc6\xe0\x17\x00\x00\x00\x00\x006\x00\x00\x00(\x00\x00\x00\x16\x04\x00\x00v\x01\x00\x00\x01\x00 \x00\x00\x00\x00\x00\x90\xe0\x17\x00\xc4\x0e\x00\x00\xc4\x0e\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00'

system(f'openssl sm4-ecb -in flag.bmp -out flag.enc -k {key}')
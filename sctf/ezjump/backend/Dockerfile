FROM python:3.9-slim

RUN apt-get update
RUN apt-get install -y curl

RUN mkdir /app
COPY src/ /app/
WORKDIR /app/

RUN pip install Flask==2.2.2 -i https://pypi.tuna.tsinghua.edu.cn/simple
RUN pip install Werkzeug==2.2.2 -i https://pypi.tuna.tsinghua.edu.cn/simple
WORKDIR /app
COPY init.sh /
RUN chmod 777 /init.sh

EXPOSE 5000

ENTRYPOINT ["bash","/init.sh"]
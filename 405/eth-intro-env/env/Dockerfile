FROM ghcr.io/foundry-rs/foundry:latest

COPY ./entrypoint.sh /

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories && \
    apk add python3 py3-pip curl && \
    pip config set global.index-url https://mirrors.ustc.edu.cn/pypi/simple && \
    pip install web3 "fastapi[standard]" aiofiles && \
    adduser ctf -D && \
    chmod +x /entrypoint.sh


ADD ./project /home/ctf/
RUN chown -R ctf:ctf /home/ctf/chall

USER ctf
WORKDIR /home/ctf/

ENTRYPOINT [ "/entrypoint.sh" ]

EXPOSE 8000

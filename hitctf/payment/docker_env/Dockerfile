FROM ubuntu:22.04

RUN useradd -m ctf

COPY ./app /home/ctf/
COPY ./entrypoint.sh /

RUN chown -R root:root /home/ctf \
    && chmod 755 /home/ctf

USER ctf
ENTRYPOINT [ "/entrypoint.sh" ]
EXPOSE 1337

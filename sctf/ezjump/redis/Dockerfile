FROM redis:5.0
COPY redis.conf /usr/local/etc/redis/redis.conf
COPY flag /flag

CMD [ "redis-server", "/usr/local/etc/redis/redis.conf" ]
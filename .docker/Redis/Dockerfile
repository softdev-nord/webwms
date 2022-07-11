FROM redis:latest

LABEL maintainer="Rene Irrgang <info@softdev-nord.de>"

## For security settings uncomment, make the dir, copy conf, and also start with the conf, to use it
#RUN mkdir -p /usr/local/etc/redis
#COPY redis.conf /usr/local/etc/redis/redis.conf

VOLUME /data

CMD ["redis-server"]

EXPOSE 6379

#CMD ["redis-server", "/usr/local/etc/redis/redis.conf"]

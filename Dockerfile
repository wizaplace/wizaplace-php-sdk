FROM composer:1.4

RUN apk --update --no-cache add make

VOLUME /app
WORKDIR /app

RUN chmod -R 777 /composer

ENTRYPOINT []
CMD ["/usr/bin/make", "all"]

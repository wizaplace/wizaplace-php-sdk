FROM composer:1.4

RUN apk --update --no-cache add make

RUN mkdir -p /app
VOLUME /app
WORKDIR /app

ENTRYPOINT []
CMD ["/usr/bin/make", "all"]

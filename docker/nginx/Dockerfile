FROM alpine:3.4

RUN apk add --update nginx tzdata && rm -rf /var/cache/apk/* && rm -rf /tmp/*

# Set timezone
RUN rm /etc/localtime
RUN cp /usr/share/zoneinfo/America/Guatemala /etc/localtime
RUN echo "America/Guatemala" >  /etc/timezone
RUN "date"

RUN apk del tzdata
RUN mkdir -p /home/www-data/super-speed-nginx/var/nginx
COPY nginx.conf /etc/nginx/

RUN adduser -D -g '' -G www-data www-data

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log

CMD ["nginx"]

EXPOSE 80

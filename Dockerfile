FROM centos:centos7
MAINTAINER AIZAWA Hina <hina@bouhime.com>

ADD docker/nginx/nginx.repo /etc/yum.repos.d/
RUN yum update -y && \
    yum install -y \
        ImageMagick \
        curl \
        nginx \
        patch \
        pngcrush \
        scl-utils \
        sqlite \
        tar \
        unzip \
        wget \
        http://ftp.tsukuba.wide.ad.jp/Linux/fedora/epel/7/x86_64/e/epel-release-7-5.noarch.rpm \
        https://www.softwarecollections.org/en/scls/remi/php56more/epel-7-x86_64/download/remi-php56more-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/git19/epel-7-x86_64/download/rhscl-git19-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/nodejs010/epel-7-x86_64/download/rhscl-nodejs010-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/rh-php56/epel-7-x86_64/download/rhscl-rh-php56-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/v8314/epel-7-x86_64/download/rhscl-v8314-epel-7-x86_64.noarch.rpm \
            && \
    yum install -y \
        git19-git \
        more-php56-php-mcrypt \
        nodejs010-npm \
        rh-php56-php-cli \
        rh-php56-php-fpm \
        rh-php56-php-intl \
        rh-php56-php-mbstring \
        rh-php56-php-opcache \
        rh-php56-php-pdo \
        rh-php56-php-pecl-jsonc \
        rh-php56-php-xml \
        supervisor \
            && \
    yum clean all && \
    useradd festink && \
    chmod 701 /home/festink

ADD docker/env/scl-env.sh /etc/profile.d/
ADD docker/supervisor/* /etc/supervisord.d/
ADD . /home/festink/fest.ink
RUN chown -R festink:festink /home/festink/fest.ink

USER festink
RUN cd ~festink/fest.ink && bash -c 'source /etc/profile.d/scl-env.sh && make clean && rm -rf runtime/* && make'

USER root
ADD docker/php/php-config.diff /tmp/
RUN patch -p1 -d /etc/opt/rh/rh-php56 < /tmp/php-config.diff && rm /tmp/php-config.diff

ADD docker/nginx/default.conf /etc/nginx/conf.d/

CMD /usr/bin/supervisord
EXPOSE 80

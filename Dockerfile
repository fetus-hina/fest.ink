FROM centos:centos7
MAINTAINER AIZAWA Hina <hina@bouhime.com>

ADD docker/nginx/nginx.repo /etc/yum.repos.d/
ADD docker/rpm-gpg/ /etc/pki/rpm-gpg/

RUN rpm --import \
    /etc/pki/rpm-gpg/RPM-GPG-KEY-CentOS-7 \
    /etc/pki/rpm-gpg/RPM-GPG-KEY-CentOS-SIG-SCLo \
    /etc/pki/rpm-gpg/RPM-GPG-KEY-EPEL-7 \
    /etc/pki/rpm-gpg/RPM-GPG-KEY-remi \
        && \
    yum update -y && \
    yum install -y \
        ImageMagick \
        centos-release-scl-rh \
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
        http://rpms.famillecollet.com/enterprise/7/safe/x86_64/remi-release-7.1-3.el7.remi.noarch.rpm  \
            && \
    yum install -y \
        git19-git \
        nodejs010-npm \
        php70-php-cli \
        php70-php-fpm \
        php70-php-intl \
        php70-php-json \
        php70-php-mbstring \
        php70-php-mcrypt \
        php70-php-opcache \
        php70-php-pdo \
        php70-php-pecl-zip \
        php70-php-xml \
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
RUN patch -p1 -d /etc/opt/remi/php70 < /tmp/php-config.diff && rm /tmp/php-config.diff

ADD docker/nginx/default.conf /etc/nginx/conf.d/

CMD /usr/bin/supervisord
EXPOSE 80

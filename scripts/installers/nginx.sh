#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

. $DIR/../../config

#deb http://nginx.org/packages/ubuntu/ $RELEASE nginx
#deb-src http://nginx.org/packages/ubuntu/ $RELEASE nginx

#apt-get update
#apt-get install nginx

if [[ $NGINX_INSTALL = 1 ]]; then
   exit 1
fi

nginx=stable # use nginx=development for latest development version
add-apt-repository ppa:nginx/$nginx
apt-get update
apt-get install nginx
vsm config NGINX_INSTALL 1

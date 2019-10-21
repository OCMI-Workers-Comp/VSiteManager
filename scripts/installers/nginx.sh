#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

. $DIR/../../config

if [[ $NGINX_INSTALL = 1 ]]; then
   exit 1
fi

nginx=stable # use nginx=development for latest development version
add-apt-repository ppa:nginx/$nginx
apt-get update

if service --status-all | grep -Fq 'apache2'; then    
  sudo service apache2 restart    
fi

apt-get install nginx
vsm config NGINX_INSTALL 1

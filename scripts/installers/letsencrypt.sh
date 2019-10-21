#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

. $DIR/../../config

if [[ $CERTBOT_INSTALL = 1 ]]; then
   exit 1
fi

add-apt-repository ppa:certbot/certbot
apt install python-certbot-nginx
vsm config CERTBOT_INSTALL 1
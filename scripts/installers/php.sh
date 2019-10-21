#/bin/bash

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 
   exit 1
fi

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

. $DIR/../../config

if [[ $PHP_INSTALL == 1 ]]; then
    exit 1;
fi

apt-get update && apt-get upgrade
apt-get install software-properties-common
add-apt-repository ppa:ondrej/php
apt-get update
apt-get install php$PHPVER
apt-get install php-curl php-dev php-gd php-mbstring php-zip php-mysql php-pdo php-xml php-pdo php-mysql
apt-get install php-fpm


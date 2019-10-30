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
apt-get install php$PHPVER-curl php-$PHPVERdev php$PHPVER-gd php$PHPVER-mbstring php$PHPVER-zip php$PHPVER-mysql php$PHPVER-pdo php$PHPVER-xml php$PHPVER-pdo php$PHPVER-mysql php$PHPVER-imagick unzip
apt-get install php-fpm


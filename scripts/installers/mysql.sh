#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

. $DIR/../../config

if [[ $MYSQL_INSTALL = 1 ]]; then
   exit 1;
fi

wget https://dev.mysql.com/get/$MYSQL_DEB
dpkg -i $MYSQL_DEB
apt-get update
apt-get install mysql-server
vsm config MYSQL_INSTALL 1
rm $MYSQL_DEB

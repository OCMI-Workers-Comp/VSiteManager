#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

. $DIR/../../config

if [[ $USERS_INSTALL = 1 ]]; then
   exit 1
fi

groupadd vsm-web
useradd -G vsm-web vsm -b /home
vsm config USERS_INSTALL 1

#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

. $DIR/../../config

if [[ $VSM_INSTALL == 1 ]]; then
    exit 1
fi

ln -s $PWD/../../bin/vsm /usr/bin/vsm
vsm update PHP_INSTALL 1
vsm update VSM_INSTALL 1
#export PATH=$PWD/../../bin/:$PATH
#source ~/.bashrc

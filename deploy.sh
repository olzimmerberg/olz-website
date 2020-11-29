#!/bin/sh

FTP_COMMANDS_FILE="./ci/ftp-deploy"
if [ "$2" != "" ]; then
    FTP_COMMANDS_FILE="$2"
fi

if [ "$1" = "hoststar-prod" ]; then
    USERNAME="deploy.olzimmerberg.ch"
    HOST="lx7.hoststar.hosting"
    PORT="5544"
    URL="https://olzimmerberg.ch"
    RESET=0
elif [ "$1" = "hoststar-test" ]; then
    USERNAME="deploytest.olzimmerberg.ch"
    HOST="lx7.hoststar.hosting"
    PORT="5544"
    URL="https://test.olzimmerberg.ch"
    RESET=1
else
    echo "Usage: deploy.sh env"
    echo "env must be hoststar-prod or hoststar-test"
    exit 1
fi

if [ "$SSHPASS" = "" ]; then
    echo -n "Password for $USERNAME@$HOST: "
    read -s PASSWORD
    export SSHPASS="$PASSWORD"
fi
sshpass -e sftp -o StrictHostKeyChecking=no -P "$PORT" "$USERNAME@$HOST" < $FTP_COMMANDS_FILE
wget -q -O - "$URL/deploy/deploy.php"
if [ $RESET -eq 1 ]; then
    wget -q -O - "$URL/tools.php/reset"
fi

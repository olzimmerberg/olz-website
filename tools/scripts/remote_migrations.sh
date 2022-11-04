#!/bin/sh

set -e

cd ./tools/remote_config

echo -n "Remote Database Password (for olz): "
read -s PASSWORD
echo

export DOCTRINE_CONNECTION_PASSWORD="$PASSWORD"

./vendor/bin/doctrine-migrations "$@"
exit $?

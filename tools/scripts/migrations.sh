#!/bin/sh

set -e

cd src/config
./vendor/bin/doctrine-migrations "$@"
cd ../../
exit $?

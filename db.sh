#!/bin/sh

cd src/config
./vendor/bin/doctrine-migrations "$@"
exit $?

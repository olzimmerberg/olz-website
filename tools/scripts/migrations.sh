#!/bin/sh

set -e

cd public/_/config
./vendor/bin/doctrine-migrations "$@"
cd ../../
exit $?

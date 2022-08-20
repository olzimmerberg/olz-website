#!/bin/sh

set -e

bin/console doctrine:migrations:"$@"
exit $?

#!/bin/sh

set -e

./vendor/bin/doctrine "$@"
exit $?

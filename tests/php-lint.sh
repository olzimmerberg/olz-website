#!/bin/sh

EXIT_CODE=0
for entry in ../src/*.php
do
    php -l "$entry" > /dev/null 2>&1
    PHP_EXIT_CODE=$?
    if [ $PHP_EXIT_CODE -ne 0 ]; then
        echo "ERROR in $entry"
        EXIT_CODE=$PHP_EXIT_CODE
    fi
done
exit $EXIT_CODE

#!/bin/sh

EXIT_CODE=0
DID_RUN=0
for ENTRY in ../src/*.php
do
    php -l "$ENTRY" > /dev/null 2>&1
    PHP_EXIT_CODE=$?
    DID_RUN=1
    if [ $PHP_EXIT_CODE -ne 0 ]; then
        echo "ERROR in $ENTRY"
        EXIT_CODE=$PHP_EXIT_CODE
    fi
done
if [ $DID_RUN -eq 0 ]; then
    echo "did not run for any file"
    EXIT_CODE=255
fi
exit $EXIT_CODE

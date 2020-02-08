#!/bin/sh

EXIT_CODE=0
DID_RUN=0
for ENTRY in ../src/*.php
do
    RESULT=$(cat "$ENTRY" | grep -n -E "^[ ]*$(printf '\t')")
    DID_RUN=1
    if [ -n "$RESULT" ]; then
        echo "ERROR in $ENTRY: $RESULT"
        EXIT_CODE=255
    fi
done
if [ $DID_RUN -eq 0 ]; then
    echo "did not run for any file"
    EXIT_CODE=255
fi
exit $EXIT_CODE

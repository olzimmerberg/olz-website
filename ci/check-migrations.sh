#!/bin/sh

EXIT_CODE=0
cd ../src/config
# Create migration, but if there is one, remove it again, immediately.
./vendor/bin/doctrine-migrations migrations:diff --editor-cmd=rm
EXIT_CODE=$?
if [ $EXIT_CODE -eq 1 ]; then # Could not create migration, as diff is empty.
    echo "Migrations are complete."
    exit 0
else
    echo "Migrations are incomplete. Please run doctrine migration builder."
    exit 1
fi

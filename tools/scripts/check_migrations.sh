#!/bin/sh

EXIT_CODE=0
cd ./_/config/
./vendor/bin/doctrine-migrations migrations:diff
EXIT_CODE=$?
cd ../../
if [ $EXIT_CODE -eq 1 ]; then # Could not create migration, as diff is empty.
    echo "Migrations are complete."
    exit 0
else
    echo "Migrations are incomplete. Please run doctrine migration builder."
    echo "Exit code: $EXIT_CODE"
    exit 1
fi

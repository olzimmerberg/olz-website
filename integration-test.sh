#!/bin/sh

DOMAIN='127.0.0.1:30270'

rm -Rf ./screenshots
mkdir -p ./screenshots

# Run gecko (Firefox) driver
geckodriver &
GECKODRIVER_PID=$!

# Configure dev server
if [ ! -z DB_PORT ] && [ ! -f ./dev-server/config.php ]; then
    cp ./dev-server/config.template.php ./dev-server/config.php
    sed -i "s/localhost:3306/127.0.0.1:$DB_PORT/g" ./dev-server/config.php
    echo "Dev server configured."
else
    echo "Dev server configuration preserved."
fi

# Build JavaScript code
npm run webpack-build

# Run dev server
php -S "$DOMAIN" -t ./dev-server/ &
DEVSERVER_PID=$!

# Load dev data
WGET_RESULT=""
ITERATION=0
while [ "$WGET_RESULT" != "reset:SUCCESS" ]
do
    sleep 0.5
    WGET_RESULT=$(wget --no-verbose -O - "http://$DOMAIN/tools.php/reset")
    ITERATION=$((ITERATION+1))
    if [ $ITERATION -gt 50 ]; then
        exit 1;
    fi
done

# Run test
EXIT_CODE=0
php tests/integration_tests/firefox_test.php
EXIT_CODE=$?

# Clean up
kill -9 $GECKODRIVER_PID
kill -9 $DEVSERVER_PID

exit $EXIT_CODE

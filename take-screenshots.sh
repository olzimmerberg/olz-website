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
    sed -i "s/3306/$DB_PORT/g" ./dev-server/config.php
    echo "Dev server configured."
else
    echo "Dev server configuration preserved."
fi

# Build JavaScript code
npm run webpack-build

# Run dev server
php -S "$DOMAIN" -t ./dev-server/ &
DEVSERVER_PID=$!

# Run test
EXIT_CODE=0
php tests/screenshot_tests/firefox_test.php $@
EXIT_CODE=$?

# Clean up
kill -9 $GECKODRIVER_PID
kill -9 $DEVSERVER_PID

exit $EXIT_CODE

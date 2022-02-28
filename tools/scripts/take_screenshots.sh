#!/bin/sh

set -e

DOMAIN='127.0.0.1:30270'

BROWSER='firefox'
SET_INDEX=''

while [ ! -z "$1" ]; do
    case "$1" in
        --firefox)
            BROWSER='firefox'
            ;;
        --chrome)
            BROWSER='chrome'
            ;;
        --help|-h)
            echo "Usage: $(basename $0) [--firefox|--chrome] [set_index]" 2>&1
            exit 1
            ;;
        *)
            SET_INDEX="$1"
            ;;
    esac
    shift
done

rm -Rf ./screenshots
mkdir -p ./screenshots

# Run gecko (Firefox) driver or Chrome driver
if [ "$BROWSER" = "firefox" ]; then
    geckodriver &
elif [ "$BROWSER" = "chrome" ]; then
    chromedriver --port=4444 &
else
    echo "Invalid browser: $BROWSER"
    exit 1
fi
BROWSER_DRIVER_PID=$!

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
mkdir -p ./dev-server/logs
php -S "$DOMAIN" -t ./dev-server/ > ./dev-server/logs/take-screenshots.log 2>&1 &
DEVSERVER_PID=$!

# Run test, allow aborting
set +e
EXIT_CODE=0
php tests/screenshot_tests/take_screenshots.php "$BROWSER" "$SET_INDEX"
EXIT_CODE=$?

# Clean up
kill -9 $BROWSER_DRIVER_PID
kill -9 $DEVSERVER_PID

exit $EXIT_CODE

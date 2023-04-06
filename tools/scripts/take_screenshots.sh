#!/bin/sh

set -e

BROWSER='firefox'
NO_BUILD=0
SET_INDEX=''

while [ ! -z "$1" ]; do
    case "$1" in
        --firefox)
            BROWSER='firefox'
            ;;
        --chrome)
            BROWSER='chrome'
            ;;
        --no-build)
            NO_BUILD=1
            ;;
        --help|-h)
            echo "Usage: $(basename $0) [--firefox|--chrome] [--no-build] [set_index]" 2>&1
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

# Configure env
if [ ! -z DB_PORT ] && [ ! -f ./tests/IntegrationTests/document-root/config.php ]; then
    cp ./public/config.template.php ./public/config.php
    sed -i "s/3306/$DB_PORT/g" ./public/config.php
    echo "Dev server env configured."
else
    echo "Dev server env configuration preserved."
fi

# Configure symfony
if [ ! -z DB_PORT ] && [ ! -f .env.local ]; then
    cp .env.local.template .env.local
    sed -i "s/:3306/:$DB_PORT/g" .env.local
    echo "Dev server symfony configured."
else
    echo "Dev server symfony configuration preserved."
fi

# Build JavaScript code
if [ "$NO_BUILD" = "0" ]; then
    export NODE_OPTIONS="--max-old-space-size=4096"
    npm run webpack-build
fi

# Run dev server
mkdir -p ./public/logs
# php -S "127.0.0.1:30270" -t ./public/ > ./public/logs/take-screenshots.log 2>&1 &
symfony server:start --port=30270 > ./public/logs/take-screenshots.log 2>&1 &
DEVSERVER_PID=$!
sleep 1

# Run test, allow aborting
set +e
EXIT_CODE=0
php tests/screenshot_tests/take_screenshots.php "$BROWSER" "$SET_INDEX"
EXIT_CODE=$?

# Clean up
kill -9 $BROWSER_DRIVER_PID
kill -9 $DEVSERVER_PID

exit $EXIT_CODE

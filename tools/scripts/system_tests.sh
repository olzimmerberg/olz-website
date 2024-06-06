#!/bin/sh

set -e

BROWSER='firefox'
SYSTEM_TEST_MODE='dev'

while [ ! -z "$1" ]; do
    case "$1" in
        --dev)
            SYSTEM_TEST_MODE='dev'
            ;;
        --dev_rw)
            SYSTEM_TEST_MODE='dev_rw'
            ;;
        --staging)
            SYSTEM_TEST_MODE='staging'
            ;;
        --staging_rw)
            SYSTEM_TEST_MODE='staging_rw'
            ;;
        --prod)
            SYSTEM_TEST_MODE='prod'
            ;;
        --meta)
            SYSTEM_TEST_MODE='meta'
            ;;
        --firefox)
            BROWSER='firefox'
            ;;
        --chrome)
            BROWSER='chrome'
            ;;
        --no-build)
            NO_BUILD=1
            ;;
        --help|-h|*)
            echo "Usage: $(basename $0) [--dev|--dev_rw|--staging|--staging_rw|--prod|--meta] [--firefox|--chrome]" 2>&1
            exit 1
            ;;
    esac
    shift
done

rm -Rf ./screenshots
mkdir -p ./screenshots
mkdir -p ./public/logs

echo "BROWSER = $BROWSER"
echo "SYSTEM_TEST_MODE = $SYSTEM_TEST_MODE"

# Run gecko (Firefox) driver or Chrome driver
if [ "$BROWSER" = "firefox" ]; then
    geckodriver --port 4444 &
elif [ "$BROWSER" = "chrome" ]; then
    chromedriver --port=4444 &
else
    echo "Invalid browser: $BROWSER"
    exit 1
fi
BROWSER_DRIVER_PID=$!

if [ "$SYSTEM_TEST_MODE" = "dev" ] || [ "$SYSTEM_TEST_MODE" = "dev_rw" ]; then
    # Configure env
    if [ ! -z DB_PORT ] && [ ! -f ./config/olz.dev.php ]; then
        cp ./config/olz.dev.template.php ./config/olz.dev.php
        sed -i "s/3306/$DB_PORT/g" ./config/olz.dev.php
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

    # Reset the environment
    APP_ENV=test php bin/console olz:db-reset full > ./public/logs/take-screenshots.log 2>&1 &

    # Build JavaScript code
    if [ "$NO_BUILD" = "0" ]; then
        export NODE_OPTIONS="--max-old-space-size=4096"
        npm run webpack-build
    else
        sleep 15 # wait until DB reset is finished
    fi

    # Run dev server
    APP_ENV=test symfony server:start --port=30270 > ./public/logs/take-screenshots.log 2>&1 &
    DEVSERVER_PID=$!
    sleep 3
fi

# Run test, allow aborting
set +e
EXIT_CODE=0
APP_ENV=test SYSTEM_TEST_MODE="$SYSTEM_TEST_MODE" SYMFONY_DEPRECATIONS_HELPER='max[direct]=0' php ./bin/phpunit -c ./phpunit.xml.dist $@ ./tests/SystemTests
EXIT_CODE=$?

# Display logs
# for file in ./public/logs/*; do
#     if [ -f "$file" ]; then
#         echo "$file"
#         cat "$file"
#     fi
# done

# Clean up
kill -9 $BROWSER_DRIVER_PID
if [ "$SYSTEM_TEST_MODE" = "dev" ] || [ "$SYSTEM_TEST_MODE" = "dev_rw" ]; then
    kill -9 $DEVSERVER_PID
fi

exit $EXIT_CODE

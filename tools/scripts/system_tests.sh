#!/bin/sh

set -e

BROWSER='firefox'
MODE='dev'
NO_BUILD=0
SLICE='1/1'
REST=''

while [ ! -z "$1" ]; do
    case "$1" in
        --dev)
            MODE='dev'
            ;;
        --dev_rw)
            MODE='dev_rw'
            ;;
        --staging)
            MODE='staging'
            ;;
        --staging_rw)
            MODE='staging_rw'
            ;;
        --prod)
            MODE='prod'
            ;;
        --meta)
            MODE='meta'
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
        */*)
            SLICE="$1"
            ;;
        --help|-h)
            echo "Usage: $(basename $0) [--dev|--dev_rw|--staging|--staging_rw|--prod|--meta] [--firefox|--chrome]" 2>&1
            exit 1
            ;;
        *)
            REST="$REST $1"
            ;;
    esac
    shift
done

rm -Rf ./screenshots
mkdir -p ./screenshots
mkdir -p ./public/logs

echo "BROWSER = $BROWSER"
echo "MODE = $MODE"
echo "SLICE = $SLICE"
echo "REST = $REST"

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

if [ "$MODE" = "dev" ] || [ "$MODE" = "dev_rw" ]; then
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
    APP_ENV=dev php bin/console olz:db-reset full

    # Build JavaScript code
    if [ "$NO_BUILD" = "0" ]; then
        export NODE_OPTIONS="--max-old-space-size=4096"
        npm run webpack-build
    fi

    # Run dev server
    APP_ENV=dev symfony server:start --port=30270 > ./public/logs/system-tests.log 2>&1 &
    DEVSERVER_PID=$!
fi
sleep 3

# Run test, allow aborting
set +e
EXIT_CODE=0
APP_ENV=dev SYSTEM_TEST_MODE="$MODE" SYSTEM_TEST_SLICE="$SLICE" SYMFONY_DEPRECATIONS_HELPER='max[direct]=0' php ./bin/phpunit -c ./phpunit.xml.dist $REST ./tests/SystemTests
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
if [ "$MODE" = "dev" ] || [ "$MODE" = "dev_rw" ]; then
    kill -9 $DEVSERVER_PID
fi

exit $EXIT_CODE

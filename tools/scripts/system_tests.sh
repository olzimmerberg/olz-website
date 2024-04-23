#!/bin/sh

set -e

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
    geckodriver --port 4444 &
elif [ "$BROWSER" = "chrome" ]; then
    chromedriver --port=4444 &
else
    echo "Invalid browser: $BROWSER"
    exit 1
fi
BROWSER_DRIVER_PID=$!

# Run test, allow aborting
set +e
EXIT_CODE=0
SYMFONY_DEPRECATIONS_HELPER='max[direct]=0' php ./bin/phpunit -c ./phpunit.xml.dist $@ ./tests/SystemTests
EXIT_CODE=$?

# Clean up
kill -9 $BROWSER_DRIVER_PID

exit $EXIT_CODE

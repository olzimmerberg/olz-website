#!/bin/sh

set -e

SYMFONY_DEPRECATIONS_HELPER='max[direct]=0' XDEBUG_MODE=coverage php ./bin/phpunit -c ./phpunit.xml.dist $@ ./tests/UnitTests
echo "Ran the tests. Gathering coverage..."
rm -Rf "$(pwd)/docs/coverage/php/UnitTests"
mkdir -p "$(pwd)/docs/coverage/php/UnitTests"
mv "$(pwd)/php-coverage/html-coverage" "$(pwd)/docs/coverage/php/UnitTests/html"

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/docs/coverage/php/UnitTests/html/index.html"
echo ""

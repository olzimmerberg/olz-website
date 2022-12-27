#!/bin/sh

set -e

php ./bin/phpunit -c ./phpunit.xml $@ ./tests/UnitTests
rm -Rf "$(pwd)/docs/coverage/php/UnitTests"
mkdir -p "$(pwd)/docs/coverage/php/UnitTests"
mv "$(pwd)/php-coverage/html-coverage" "$(pwd)/docs/coverage/php/UnitTests/html"

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/docs/coverage/php/UnitTests/html/index.html"
echo ""

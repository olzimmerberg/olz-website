#!/bin/sh

set -e

php ./bin/phpunit -c ./phpunit.xml.dist $@ ./tests

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/php-coverage/html-coverage/index.html"
echo ""

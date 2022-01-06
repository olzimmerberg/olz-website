#!/bin/sh

set -e

./vendor/bin/phpunit -c ./phpunit.xml --bootstrap ./vendor/autoload.php $@ ./tests/unit_tests

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/php-coverage/html-coverage/index.html"
echo ""

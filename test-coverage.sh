#!/bin/sh

./vendor/bin/phpunit -c ./phpunit.xml $@ tests

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "file://$(pwd)/php-coverage/html-coverage/index.html"
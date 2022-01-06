#!/bin/sh

set -e

# Configure database
if [ ! -z DB_PORT ] && [ ! -f ./tests/integration_tests/document-root/config.php ]; then
    cp ./tests/integration_tests/document-root/config.template.php ./tests/integration_tests/document-root/config.php
    sed -i "s/'3306'/'$DB_PORT'/g" ./tests/integration_tests/document-root/config.php
    echo "Integration test server configured."
else
    echo "Integration test server configuration preserved."
fi

./vendor/bin/phpunit -c ./phpunit.xml --bootstrap ./vendor/autoload.php $@ ./tests/integration_tests

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/php-coverage/html-coverage/index.html"
echo ""

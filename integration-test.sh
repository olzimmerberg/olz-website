#!/bin/sh

# Configure database
if [ ! -z DB_PORT ] && [ ! -f ./tests/integration_tests/document-root/config.php ]; then
    cp ./tests/integration_tests/document-root/config.template.php ./tests/integration_tests/document-root/config.php
    sed -i "s/'3306'/'$DB_PORT'/g" ./tests/integration_tests/document-root/config.php
    echo "Dev server configured."
else
    echo "Dev server configuration preserved."
fi

./vendor/bin/phpunit -c ./phpunit.xml --bootstrap vendor/autoload.php $@ tests/integration_tests

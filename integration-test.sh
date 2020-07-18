#!/bin/sh

# Configure database
if [ ! -z DB_PORT ] && [ ! -f ./tests/integration_tests/document-root/config.php ]; then
    cp ./tests/integration_tests/document-root/config.template.php ./tests/integration_tests/document-root/config.php
    sed -i "s/localhost:3306/127.0.0.1:$DB_PORT/g" ./tests/integration_tests/document-root/config.php
    echo "Dev server configured."
else
    echo "Dev server configuration preserved."
fi

./vendor/bin/phpunit --bootstrap vendor/autoload.php --test-suffix '_test.php,Test.php' tests/integration_tests

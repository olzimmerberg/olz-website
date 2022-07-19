#!/bin/sh

set -e

# Configure env
if [ ! -z DB_PORT ] && [ ! -f ./tests/IntegrationTests/document-root/config.php ]; then
    cp ./tests/IntegrationTests/document-root/config.template.php ./tests/IntegrationTests/document-root/config.php
    sed -i "s/'3306'/'$DB_PORT'/g" ./tests/IntegrationTests/document-root/config.php
    echo "Integration test server env configured."
else
    echo "Integration test server env configuration preserved."
fi

# Configure symfony
if [ ! -z DB_PORT ] && [ ! -f .env.test.local ]; then
    cp .env.test .env.test.local
    sed -i "s/:3306/:$DB_PORT/g" .env.test.local
    echo "Integration test server symfony configured."
else
    echo "Integration test server symfony configuration preserved."
fi

# Configure dev server symfony
if [ ! -z DB_PORT ] && [ ! -f .env.local ]; then
    cp .env .env.local
    sed -i "s/:3306/:$DB_PORT/g" .env.local
    echo "Dev server symfony configured."
else
    echo "Dev server symfony configuration preserved."
fi

./vendor/bin/phpunit -c ./phpunit.xml $@ ./tests/IntegrationTests

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/php-coverage/html-coverage/index.html"
echo ""

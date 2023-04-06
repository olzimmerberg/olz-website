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
    cp .env.test.local.template .env.test.local
    sed -i "s/:3306/:$DB_PORT/g" .env.test.local
    echo "Integration test server symfony configured."
else
    echo "Integration test server symfony configuration preserved."
fi

# Configure dev server symfony
if [ ! -z DB_PORT ] && [ ! -f .env.local ]; then
    cp .env.local.template .env.local
    sed -i "s/:3306/:$DB_PORT/g" .env.local
    echo "Dev server symfony configured."
else
    echo "Dev server symfony configuration preserved."
fi

php ./bin/phpunit -c ./phpunit.xml $@ ./tests/IntegrationTests
rm -Rf "$(pwd)/docs/coverage/php/IntegrationTests"
mkdir -p "$(pwd)/docs/coverage/php/IntegrationTests"
mv "$(pwd)/php-coverage/html-coverage" "$(pwd)/docs/coverage/php/IntegrationTests/html"

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/docs/coverage/php/IntegrationTests/html/index.html"
echo ""

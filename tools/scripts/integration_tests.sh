#!/bin/sh

set -e

# Configure env
if [ ! -z DB_PORT ] && [ ! -f ./config/olz.test.php ]; then
    cp ./config/olz.test.template.php ./config/olz.test.php
    sed -i "s/'3306'/'$DB_PORT'/g" ./config/olz.test.php
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

APP_ENV=test SYMFONY_DEPRECATIONS_HELPER='max[direct]=0' php ./bin/phpunit -c ./phpunit.xml.dist $@ ./tests/IntegrationTests
rm -Rf "$(pwd)/docs/coverage/php/IntegrationTests"
mkdir -p "$(pwd)/docs/coverage/php/IntegrationTests"
mv "$(pwd)/php-coverage/html-coverage" "$(pwd)/docs/coverage/php/IntegrationTests/html"

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/docs/coverage/php/IntegrationTests/html/index.html"
echo ""

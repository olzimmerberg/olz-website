#!/bin/sh

EXIT_CODE=0
../vendor/bin/php-cs-fixer fix --config=../.php_cs_config.php -v --dry-run --diff
EXIT_CODE=$?
exit $EXIT_CODE

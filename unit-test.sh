#!/bin/sh

set -e

./vendor/bin/phpunit -c ./phpunit.xml --bootstrap vendor/autoload.php $@ tests/unit_tests

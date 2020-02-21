#!/bin/sh

./vendor/bin/phpunit --bootstrap vendor/autoload.php --test-suffix '_test.php,Test.php' tests

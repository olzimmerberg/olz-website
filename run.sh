#!/bin/sh

npm run webpack-watch &

php -S 127.0.0.1:30270 -t ./dev-server/

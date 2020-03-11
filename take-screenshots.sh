#!/bin/sh

DOMAIN='127.0.0.1:30270'

rm -Rf ./screenshots
mkdir -p ./screenshots

# Configure dev server
if [ ! -z DB_PORT ] && [ ! -f ./dev-server/config.php ]; then
    cp ./dev-server/config.template.php ./dev-server/config.php
    sed -i "s/localhost:3306/127.0.0.1:$DB_PORT/g" ./dev-server/config.php
    echo "Dev server configured."
else
    echo "Dev server configuration preserved."
fi

# Build JavaScript code
npm run webpack-build

# Run dev server
php -S "$DOMAIN" -t ./dev-server/ &

# Load dev data
WGET_RESULT=""
ITERATION=0
while [ "$WGET_RESULT" != "RESET:SUCCESS" ]
do
    sleep 0.5
    WGET_RESULT=$(wget --no-verbose -O - "http://$DOMAIN/reset.php")
    ITERATION=$((ITERATION+1))
    if [ $ITERATION -gt 50 ]; then
        exit 1;
    fi
done

# Figure out firefox command
firefox --screenshot ./screenshots/.test.png "http://$DOMAIN/_/"
if [ -f ./screenshots/.test.png ]; then # e.g. in GitHub Actions
    FIREFOX_CMD='firefox'
    rm ./screenshots/.test.png
else # e.g. on Simon's computer
    firefox -CreateProfile olz-screenshots
    FIREFOX_CMD='firefox -P olz-screenshots'
fi

# Take screenshots
$FIREFOX_CMD --screenshot ./screenshots/startseite.png "http://$DOMAIN/_/?test=1&page=1"
$FIREFOX_CMD --screenshot ./screenshots/aktuell.png "http://$DOMAIN/_/?test=1&page=2&id=1"
$FIREFOX_CMD --screenshot ./screenshots/leistungssport.png "http://$DOMAIN/_/?test=1&page=7"
$FIREFOX_CMD --screenshot ./screenshots/termine.png "http://$DOMAIN/_/?test=1&page=3"
$FIREFOX_CMD --screenshot ./screenshots/galerie.png "http://$DOMAIN/_/?test=1&page=4&id=2"
$FIREFOX_CMD --screenshot ./screenshots/forum.png "http://$DOMAIN/_/?test=1&page=5"
$FIREFOX_CMD --screenshot ./screenshots/karten.png "http://$DOMAIN/_/?test=1&page=12"
$FIREFOX_CMD --screenshot ./screenshots/material.png "http://$DOMAIN/_/?test=1&page=21"
$FIREFOX_CMD --screenshot ./screenshots/service.png "http://$DOMAIN/_/?test=1&page=8"
$FIREFOX_CMD --screenshot ./screenshots/kontakt.png "http://$DOMAIN/_/?test=1&page=6"
$FIREFOX_CMD --screenshot ./screenshots/trophy.png "http://$DOMAIN/_/?test=1&page=20"
$FIREFOX_CMD --screenshot ./screenshots/error.png "http://$DOMAIN/_/?test=1&page=0"
$FIREFOX_CMD --screenshot ./screenshots/search.png "http://$DOMAIN/_/?test=1&page=9"
$FIREFOX_CMD --screenshot ./screenshots/fuer_einsteiger.png "http://$DOMAIN/_/?test=1&page=18"
chmod +r ./screenshots/*

# Kill dev server
killall php

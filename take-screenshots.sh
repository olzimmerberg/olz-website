#!/bin/sh

DOMAIN='127.0.0.1:30270'

rm -Rf ./screenshots
mkdir -p ./screenshots

# Configure dev server
cp ./dev-server/config.template.php ./dev-server/config.php
if [ ! -z DB_PORT ]; then
    sed -i "s/localhost:3306/127.0.0.1:$DB_PORT/g" ./dev-server/config.php
fi

# Run dev server
php -S "$DOMAIN" -t ./dev-server/ &

# Load dev data
WGET_RESULT=""
while [ "$WGET_RESULT" != "RESET:SUCCESS" ]
do
    sleep 0.1
    WGET_RESULT=$(wget --no-verbose -O - "http://$DOMAIN/reset.php")
done

# Take screenshots
firefox --screenshot ./screenshots/startseite.png "http://$DOMAIN/_/?page=1"
firefox --screenshot ./screenshots/aktuell.png "http://$DOMAIN/_/?page=2&id=1"
firefox --screenshot ./screenshots/leistungssport.png "http://$DOMAIN/_/?page=7"
firefox --screenshot ./screenshots/termine.png "http://$DOMAIN/_/?page=3"
firefox --screenshot ./screenshots/galerie.png "http://$DOMAIN/_/?page=4&id=2"
firefox --screenshot ./screenshots/forum.png "http://$DOMAIN/_/?page=5"
firefox --screenshot ./screenshots/karten.png "http://$DOMAIN/_/?page=12"
firefox --screenshot ./screenshots/material.png "http://$DOMAIN/_/?page=21"
firefox --screenshot ./screenshots/service.png "http://$DOMAIN/_/?page=8"
firefox --screenshot ./screenshots/kontakt.png "http://$DOMAIN/_/?page=6"
firefox --screenshot ./screenshots/trophy.png "http://$DOMAIN/_/?page=20"
firefox --screenshot ./screenshots/error.png "http://$DOMAIN/_/?page=0"
firefox --screenshot ./screenshots/search.png "http://$DOMAIN/_/?page=9"
firefox --screenshot ./screenshots/fuer_einsteiger.png "http://$DOMAIN/_/?page=18"
chmod +r ./screenshots/*

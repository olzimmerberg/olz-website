#!/bin/sh

rm -Rf ./deploy/
mkdir -p ./deploy/
cp -R ./src/* ./deploy/
mkdir -p ./deploy/screenshots/generated
zip -r ./deploy.zip ./deploy/

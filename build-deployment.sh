#!/bin/sh

# DEPLOYMENT_NAME=$(date '+%Y-%m-%dT%H_%M_%S')
DEPLOYMENT_NAME='current'

rm -Rf ./deploy/$DEPLOYMENT_NAME
mkdir -p ./deploy/$DEPLOYMENT_NAME
cp -R ./src/* ./deploy/$DEPLOYMENT_NAME/
rm -f ./deploy/current
echo $DEPLOYMENT_NAME >> ./deploy/CURRENT_NAME

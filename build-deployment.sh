#!/bin/sh

rm -Rf ./deploy/
mkdir -p ./deploy/
rm -Rf ./src/jsbuild/
npm run webpack-build
cp -R ./src/* ./deploy/
mkdir -p ./deploy/screenshots/generated
zip -r ./deploy.zip ./deploy/

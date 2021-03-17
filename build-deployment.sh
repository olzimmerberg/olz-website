#!/bin/sh

set -e

rm -Rf ./deploy/
mkdir -p ./deploy/
rm -Rf ./src/jsbuild/
npm run webpack-build
cp -R ./src/* ./deploy/
rm ./deploy/config/vendor
cp -R ./vendor/ ./deploy/config/vendor/
mkdir -p ./deploy/screenshots/generated
cd ./deploy/resultate/
zip -r ./live_uploader.zip ./live_uploader/
cd ../../
zip -r ./deploy.zip ./deploy/

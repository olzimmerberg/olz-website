#!/bin/sh

set -e

npm run jest -- $@
rm -Rf "$(pwd)/docs/coverage/ts/UnitTests"
mkdir -p "$(pwd)/docs/coverage/ts/UnitTests"
mv "$(pwd)/coverage/lcov-report" "$(pwd)/docs/coverage/ts/UnitTests/html"

echo ""
echo "Open the HTML test coverage in a web browser:"
echo "    file://$(pwd)/docs/coverage/ts/UnitTests/html/index.html"
echo ""

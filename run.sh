#!/bin/sh

set -e

# Start webpack file watcher
npm run webpack-watch &
WEBPACK_WATCH_PID=$!
echo "Webpack Watch PID: $WEBPACK_WATCH_PID"

# Start Telegram webhook simulator
php ./tools/notify/telegram_webhook_simulator.php 127.0.0.1:30270 &
TELEGRAM_WEBHOOK_SIMULATOR_PID=$!
echo "Telegram Webhook Simulator PID: $TELEGRAM_WEBHOOK_SIMULATOR_PID"

# Run dev server, allow aborting
set +e
php -S 127.0.0.1:30270 -t ./dev-server/

# Clean up
kill -9 $TELEGRAM_WEBHOOK_SIMULATOR_PID
kill -9 $WEBPACK_WATCH_PID

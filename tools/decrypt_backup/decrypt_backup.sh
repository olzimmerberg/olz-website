#!/bin/sh

set -e

echo -n "Database Backup Decryption Password (for prod): "
read -s PASSWORD
echo

php "${0%.sh}.php" $1 $PASSWORD
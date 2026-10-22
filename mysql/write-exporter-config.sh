#!/bin/sh
set -eu
umask 077

printf '[client]\nuser=%s\npassword=%s\nhost=mysql\nport=3306\n' \
    "$MYSQL_EXPORTER_USER" "$MYSQL_EXPORTER_PASSWORD" > /config/.my.cnf
chmod 0444 /config/.my.cnf
#!/bin/sh

echo "Starting services..."

/usr/bin/supervisord -n -c /etc/supervisord.conf

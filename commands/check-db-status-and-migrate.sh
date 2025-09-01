#!/bin/sh
#until php -r "new PDO('pgsql:host=timescaledb;port=5432;dbname=gps_data','postgres','postgres');"; do
#  echo "Waiting for TimescaleDB..."
#  sleep 2
#done

until pg_isready -h timescaledb -p 5432 -U postgres >/dev/null 2>&1; do
  echo "Waiting for TimescaleDB..."
  sleep 2
done

php artisan migrate --force

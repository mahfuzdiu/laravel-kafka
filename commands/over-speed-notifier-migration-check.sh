#!/bin/bash

# Wait until the database is accepting connections
until php artisan migrate:status >/dev/null 2>&1; do
  echo "Waiting for DB..."
  sleep 2
done

# Start Kafka consumer
php artisan kafka:speed-notifier

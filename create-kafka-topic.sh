#!/bin/sh

# Wait a few seconds for Kafka to be ready
echo "⏳ Waiting for Kafka to start..."
sleep 10

# Create topic
kafka-topics.sh --bootstrap-server kafka:9092 \
  --create --topic gps-tracker \
  --partitions 1 --replication-factor 1

echo "✅ Topic gps-tracking created"

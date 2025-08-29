<?php

return [
    'broker' => env('KAFKA_BROKER', 'kafka:9092'),
    'topic' => env('KAFKA_TOPIC', 'gps-tracker'),
    'group' => env('KAFKA_GROUP_ID', 'over-speed-notifier'),
];

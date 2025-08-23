<?php

return [
    'brokers' => env('KAFKA_BROKER', 'kafka:9092'),
    'topics' => [
        'gps-tracking' => [
            'partitions' => 1,
            'replication_factor' => 1,
        ],
    ],

    'default_producer' => [
        'acks' => 1,
        'compression' => null,
    ],
];
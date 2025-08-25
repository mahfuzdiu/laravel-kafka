<?php

return [
    'broker' => env('KAFKA_BROKER', 'kafka:9092'),
    'topic' => "gps-tracking",
];

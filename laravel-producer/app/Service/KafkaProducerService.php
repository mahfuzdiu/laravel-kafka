<?php

namespace App\Service;

use Junges\Kafka\Facades\Kafka;

class KafkaProducerService
{
    /*
    *RdKafka\Producer directly | The underlying librdkafka library supports
    *true batching and asynchronous delivery. 
    */
    public function send(array $payload)
    {
        Kafka::publish()
        ->onTopic("gps-tracking")
        ->withBodyKey("message", $payload)
        ->send();
    }
}

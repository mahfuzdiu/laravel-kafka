<?php

namespace App\Service;

use RdKafka\Producer;
use RdKafka\Conf;

class KafkaProducerService
{
    protected Producer $producer;
    protected string $topic;

    public function __construct()
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', config('kafka.broker'));

        $this->producer = new Producer($conf);
        $this->topic = config('kafka.topic');
    }

    public function load(array $payload)
    {
        $topic = $this->producer->newTopic($this->topic);
        $message = json_encode($payload);

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        // Poll for delivery report callbacks
        $this->producer->poll(0);

        // Flush with error checking
        $result = $this->producer->flush(1000);
        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            error_log("Kafka flush failed with code: $result");
        } else {
            error_log("Message sent to Kafka topic {$this->topic}");
        }

        return $result;
    }

    public function send()
    {
        $this->producer->flush(1000);
    }
}

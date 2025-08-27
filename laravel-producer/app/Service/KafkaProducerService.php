<?php

namespace App\Service;

use RdKafka\Producer;
use RdKafka\ProducerTopic;
use RdKafka\Conf;

class KafkaProducerService
{
    private Producer $producer;
    private ProducerTopic $topic;

    public function __construct(string $clientId = 'php-producer')
    {
        $conf = new Conf();
        $conf->set('client.id', $clientId);

        $this->producer = new Producer($conf);

        if ($this->producer->addBrokers(config("kafka.broker")) === 0) {
            throw new RuntimeException("Could not connect to broker");
        }

        $this->topic = $this->producer->newTopic(config("kafka.topic"));
    }

    public function load(string $message): void
    {
        $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        // Poll handles delivery reports, must be called regularly
        $this->producer->poll(0);
    }

    public function flush(int $timeoutMs = 10000)
    {
        $result = $this->producer->flush($timeoutMs);

        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            throw new RuntimeException("Unable to flush messages, code: $result");
        }

        return $result;
    }
}

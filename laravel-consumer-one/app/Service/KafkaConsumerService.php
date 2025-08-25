<?php

namespace App\Service;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;

class KafkaConsumerService
{

    private KafkaConsumer $consumer;
    private string $topicName;

    public function __construct(string $groupId = 'laravel-group')
    {
        $this->topicName = config("kafka.topic");

        $conf = new Conf();

        // Consumer group id (all consumers with the same group share partitions)
        $conf->set('group.id', $groupId);

        // Kafka broker
        $conf->set('metadata.broker.list', config("kafka.broker"));

        // Enable partition assignment callbacks
        $conf->setRebalanceCb(function (KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    $kafka->assign(NULL);
                    break;

                default:
                    throw new \Exception($err);
            }
        });

        $this->consumer = new KafkaConsumer($conf);

        // Subscribe to topic (Kafka assigns partitions automatically)
        $this->consumer->subscribe([$this->topicName]);
    }

    public function listen(int $timeoutMs = 1000): void
    {
        echo "Listening to topic: {$this->topicName}\n";

        while (true) {
            $message = $this->consumer->consume($timeoutMs);

            if ($message === null) {
                continue;
            }

            $this->handleMessage($message);
        }
    }

    private function handleMessage(Message $message): void
    {
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                echo "Received message: {$message->payload}\n";
                $this->processMessage($message->payload);
                break;

            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                // No more messages in partition, safe to ignore
                break;

            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                // Poll timeout, no message
                break;

            default:
                echo "Kafka error: {$message->errstr()}\n";
                break;
        }
    }

    private function processMessage(string $payload): void
    {
        // TODO: handle your message
        echo "Processing: $payload\n";
    }
}

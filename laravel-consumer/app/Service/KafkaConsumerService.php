<?php

namespace App\Service;

use App\Models\CarSpeed;
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

        $batch = [];
        $batchSize = 1000;   // how many messages per batch

        while (true) {
            $message = $this->consumer->consume($timeoutMs);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $batch[] = $message;
                    if (count($batch) >= $batchSize) {
                        $this->processBatch($batch);
                        $batch = []; // reset
                    }
                    break;

                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    // End of partition, not an error, just continue
                    break;

                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    // If timeout and we already collected some messages, process them
                    if (!empty($batch)) {
                        $this->processBatch($batch);
                        $batch = [];
                    }
                    break;

                default:
                    throw new \Exception($message->errstr(), $message->err);
            }
        }
    }

    private function processBatch($messages): void
    {
        $totalSpeed = 0;
        foreach ($messages as $message) {
            if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
                $data = json_decode($message->payload, true);

                $totalSpeed = $totalSpeed + $data["speed"];
            } elseif ($message->err !== RD_KAFKA_RESP_ERR__PARTITION_EOF &&
                $message->err !== RD_KAFKA_RESP_ERR__TIMED_OUT) {
                // log error
            }
        }

        //insert avg speed per second
        $messageCount = count($messages);
        $averageSpeed = $totalSpeed / $messageCount;
        if ($messageCount > 0) {
            //Inserting avg speed/s
            CarSpeed::create([
                "avg_speed" => $averageSpeed
            ]);
        }
    }
}

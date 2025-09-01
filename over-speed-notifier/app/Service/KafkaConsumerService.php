<?php

namespace App\Service;

use App\Models\SpeedAlert;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;

class KafkaConsumerService
{
    private KafkaConsumer $consumer;
    private string $topicName;
    private $overSpeedLimit = 80;

    public function __construct()
    {
        $this->topicName = config("kafka.topic");
        $conf = new Conf();

        // Consumer group id (all consumers with the same group share partitions)
        $conf->set('group.id', config("kafka.group"));

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

    /**
     * @param int $timeoutMs
     * @throws \Exception
     */
    public function listen(int $timeoutMs = 1000): void
    {
        echo "Listening to topic: {$this->topicName}\n";

        $batch = [];
        $batchSize = 100;   // how many messages per batch

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

    /**
     * @param $messages
     */
    private function processBatch($messages): void
    {
        foreach ($messages as $message) {
            $data = json_decode($message->payload, true);
            if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
                if($data["speed"] > $this->overSpeedLimit){
                    //send notification
                    SpeedAlert::create(["speed" => $data["speed"]]);
                    break;
                }
            } elseif ($message->err !== RD_KAFKA_RESP_ERR__PARTITION_EOF &&
                $message->err !== RD_KAFKA_RESP_ERR__TIMED_OUT) {
                // log error
            }
        }
    }
}

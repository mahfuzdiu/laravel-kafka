<?php

namespace App\Console\Commands;

use App\Service\KafkaConsumerService;
use Illuminate\Console\Command;

class KafkaListen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kafka-listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to kafka topic';

    /**
     * Execute the console command.
     */
    public function handle(KafkaConsumerService $kcs)
    {
        $kcs->listen();
    }
}

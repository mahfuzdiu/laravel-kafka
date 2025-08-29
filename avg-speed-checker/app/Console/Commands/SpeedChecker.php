<?php

namespace App\Console\Commands;

use App\Service\KafkaConsumerService;
use Illuminate\Console\Command;

class SpeedChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:speed-checker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to topic gps-tracker';

    /**
     * Execute the console command.
     */
    public function handle(KafkaConsumerService $kcs)
    {
        $kcs->listen();
    }
}

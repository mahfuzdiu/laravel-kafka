<?php

namespace App\Console\Commands;

use App\Service\KafkaConsumerService;
use Illuminate\Console\Command;

class SpeedNotifier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:speed-notifier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to topic gps-tracker to send over speed limit notifier';

    /**
     * Execute the console command.
     */
    public function handle(KafkaConsumerService $kcs)
    {
        $kcs->listen();
    }
}

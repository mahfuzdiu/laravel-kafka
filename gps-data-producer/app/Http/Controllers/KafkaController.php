<?php

namespace App\Http\Controllers;

use App\Service\KafkaProducerService;

class KafkaController
{
    public function sendDataToKafkaTopic(KafkaProducerService $kps)
    {
       try {
           //delivers 30,000 messages for 5min
           for ($i = 0; $i < 30000; $i++) {
               $kps->load(json_encode($this->generateGpsPayload()));
               $kps->flush();
               usleep(10 * 1000); // 10 ms simulated delay
           }

           return response()->json("Messages are send to kafka");
       } catch (\Exception $e){
           return $e->getMessage();
       }
    }

    private function generateGpsPayload(): array
    {
        return [
            'device_id' => 'device_' . rand(1, 100),
            'latitude' => round(23.7 + lcg_value() * 0.1, 6),    // ~Dhaka area
            'longitude' => round(90.3 + lcg_value() * 0.1, 6),
            'speed' => round(mt_rand(0, 120) + lcg_value(), 2),  // km/h
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

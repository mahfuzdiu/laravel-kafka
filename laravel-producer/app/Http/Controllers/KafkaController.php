<?php

namespace App\Http\Controllers;

use App\Service\KafkaProducerService;

class KafkaController
{
    public function sendDataToKafkaTopic(KafkaProducerService $kps)
    {
        return $kps->load($this->generateGpsPayload());
//        try {
//            for ($i = 0; $i < 10; $i++)
//            {
//                $kps->load($this->generateGpsPayload());
//            }
//
//            $kps->send();
//        } catch (\Exception $e){
//            return $e->getMessage();
//        }
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

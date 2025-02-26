<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SensorData;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class CheckGasLevel extends Command
{
    protected $signature = 'check:gas-level';
    protected $description = 'Cek level gas dan kirim peringatan jika melebihi ambang batas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $latestSensorData = SensorData::latest()->first();
        if (!$latestSensorData) {
            Log::warning("Tidak ada data sensor untuk dicek.");
            return;
        }

        $thresholds = [
            'mq4_value' => 500,
            'mq6_value' => 400,
            'mq8_value' => 300
        ];

        $whatsappService = new WhatsAppService();
        $alerts = [];

        foreach ($thresholds as $sensor => $threshold) {
            $sensorValue = $latestSensorData->$sensor ?? 0;
            if ($sensorValue >= $threshold) {
                $alerts[] = "⚠️ *Peringatan!* Sensor *{$sensor}* mendeteksi gas berbahaya! \n🔥 Level: *{$sensorValue}* ppm 🚨";
            }
        }

        if (!empty($alerts)) {
            $message = implode("\n\n", $alerts);
            Log::info("🚨 Mengirim peringatan WhatsApp: $message");
            $whatsappService->sendGasAlert($message);
        }
    }
}


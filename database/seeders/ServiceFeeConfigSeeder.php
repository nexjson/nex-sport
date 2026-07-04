<?php

namespace Database\Seeders;

use App\Models\ServiceFeeConfig;
use Illuminate\Database\Seeder;

class ServiceFeeConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'min_reward' => 0,
                'max_reward' => 5000000,
                'service_fee' => 100000,
            ],
            [
                'min_reward' => 5000001,
                'max_reward' => 20000000,
                'service_fee' => 250000,
            ],
            [
                'min_reward' => 20000001,
                'max_reward' => 100000000,
                'service_fee' => 500000,
            ],
        ];

        foreach ($configs as $config) {
            ServiceFeeConfig::firstOrCreate(
                ['min_reward' => $config['min_reward'], 'max_reward' => $config['max_reward']],
                ['service_fee' => $config['service_fee']]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\DeliveryCharge;
use Illuminate\Database\Seeder;

class DeliveryChargeSeeder extends Seeder
{
    public function run(): void
    {
        $charges = [
            ['from_city' => 'Lahore', 'to_city' => 'Lahore', 'charge' => 150],
            ['from_city' => 'Lahore', 'to_city' => 'Karachi', 'charge' => 350],
            ['from_city' => 'Lahore', 'to_city' => 'Islamabad', 'charge' => 300],
            ['from_city' => 'Lahore', 'to_city' => 'Rawalpindi', 'charge' => 300],
            ['from_city' => 'Lahore', 'to_city' => 'Faisalabad', 'charge' => 250],
            ['from_city' => 'Lahore', 'to_city' => 'Multan', 'charge' => 300],
            ['from_city' => 'Lahore', 'to_city' => 'Peshawar', 'charge' => 400],
            ['from_city' => 'Karachi', 'to_city' => 'Karachi', 'charge' => 150],
            ['from_city' => 'Karachi', 'to_city' => 'Lahore', 'charge' => 350],
            ['from_city' => 'Karachi', 'to_city' => 'Islamabad', 'charge' => 400],
        ];

        foreach ($charges as $charge) {
            DeliveryCharge::updateOrCreate(
                [
                    'from_city' => $charge['from_city'],
                    'to_city' => $charge['to_city'],
                ],
                [
                    'charge' => $charge['charge'],
                    'is_active' => true,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'delivery.origin_city',
                'value' => 'Lahore',
                'type' => 'string',
                'group' => 'delivery',
                'description' => 'Default dispatch city used when calculating city-to-city delivery charges.',
            ],
            [
                'key' => 'delivery.default_charge',
                'value' => '250',
                'type' => 'integer',
                'group' => 'delivery',
                'description' => 'Fallback delivery charge when no city-to-city delivery charge is configured.',
            ],
            [
                'key' => 'delivery.free_delivery_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'delivery',
                'description' => 'Enable free delivery rules for eligible orders.',
            ],
            [
                'key' => 'delivery.free_delivery_min_order_amount',
                'value' => '5000',
                'type' => 'integer',
                'group' => 'delivery',
                'description' => 'Minimum order subtotal that qualifies for free delivery.',
            ],
            [
                'key' => 'delivery.free_delivery_min_item_quantity',
                'value' => '0',
                'type' => 'integer',
                'group' => 'delivery',
                'description' => 'Minimum item quantity that qualifies for free delivery. Set to 0 to disable this rule.',
            ],
            [
                'key' => 'payments.available_methods',
                'value' => json_encode(['cash_on_delivery']),
                'type' => 'json',
                'group' => 'payments',
                'description' => 'Enabled checkout payment methods. Only cash on delivery is available.',
            ],
            [
                'key' => 'payments.default_method',
                'value' => 'cash_on_delivery',
                'type' => 'string',
                'group' => 'payments',
                'description' => 'Default checkout payment method.',
            ],
            [
                'key' => 'payments.gateway_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'payments',
                'description' => 'External payment gateways are disabled until integration is added.',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

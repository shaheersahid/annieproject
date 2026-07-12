<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCharge;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class SettingController extends Controller
{
    private const SETTING_FIELDS = [
        'delivery_origin_city' => ['key' => 'delivery.origin_city', 'default' => 'Lahore', 'type' => 'string'],
        'delivery_default_charge' => ['key' => 'delivery.default_charge', 'default' => '250', 'type' => 'integer'],
        'free_delivery_enabled' => ['key' => 'delivery.free_delivery_enabled', 'default' => '1', 'type' => 'boolean'],
        'free_delivery_min_order_amount' => ['key' => 'delivery.free_delivery_min_order_amount', 'default' => '5000', 'type' => 'integer'],
        'free_delivery_min_item_quantity' => ['key' => 'delivery.free_delivery_min_item_quantity', 'default' => '0', 'type' => 'integer'],
    ];

    public function edit(): View
    {
        $settings = collect(self::SETTING_FIELDS)
            ->mapWithKeys(fn (array $config, string $field) => [
                $field => Setting::get($config['key'], $config['default']),
            ]);

        $deliveryCharges = DeliveryCharge::query()
            ->orderBy('from_city')
            ->orderBy('to_city')
            ->get();

        return view('admin.content.settings.edit', compact('settings', 'deliveryCharges'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_origin_city' => ['required', 'string', 'max:255'],
            'delivery_default_charge' => ['required', 'integer', 'min:0'],
            'free_delivery_enabled' => ['nullable', 'boolean'],
            'free_delivery_min_order_amount' => ['required', 'integer', 'min:0'],
            'free_delivery_min_item_quantity' => ['required', 'integer', 'min:0'],
            'delivery_charges' => ['nullable', 'array'],
            'delivery_charges.*.id' => ['nullable', 'integer', 'exists:delivery_charges,id'],
            'delivery_charges.*.from_city' => ['nullable', 'string', 'max:255'],
            'delivery_charges.*.to_city' => ['nullable', 'string', 'max:255'],
            'delivery_charges.*.charge' => ['nullable', 'integer', 'min:0'],
            'delivery_charges.*.is_active' => ['nullable', 'boolean'],
            'delivery_charges.*.delete' => ['nullable', 'boolean'],
        ]);

        foreach (self::SETTING_FIELDS as $field => $config) {
            Setting::set(
                $config['key'],
                $validated[$field] ?? 0,
                $config['type'],
                'delivery'
            );
        }

        foreach ($validated['delivery_charges'] ?? [] as $chargeData) {
            $id = $chargeData['id'] ?? null;

            if (! empty($chargeData['delete'])) {
                if ($id) {
                    DeliveryCharge::whereKey($id)->delete();
                }

                continue;
            }

            $fromCity = trim($chargeData['from_city'] ?? '');
            $toCity = trim($chargeData['to_city'] ?? '');

            if ($fromCity === '' || $toCity === '') {
                continue;
            }

            $attributes = [
                'from_city' => $fromCity,
                'to_city' => $toCity,
                'charge' => $chargeData['charge'] ?? 0,
                'is_active' => (bool) ($chargeData['is_active'] ?? false),
            ];

            $id
                ? DeliveryCharge::whereKey($id)->update($attributes)
                : DeliveryCharge::updateOrCreate(
                    ['from_city' => $fromCity, 'to_city' => $toCity],
                    $attributes
                );
        }

        return back()->with('success', 'Settings saved.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class AffiliateController extends Controller
{
    public function redirect(Product $product, string $platform): RedirectResponse
    {
        $url = match ($platform) {
            'amazon' => $product->amazon_url,
            'temu' => $product->temu_url,
            'aliexpress' => $product->aliexpress_url,
            default => null,
        };

        abort_unless(filled($url), 404);

        AffiliateClick::query()->create([
            'product_id' => $product->id,
            'platform' => $platform,
            'clicked_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->headers->get('referer'),
        ]);

        return redirect()->away($url);
    }
}

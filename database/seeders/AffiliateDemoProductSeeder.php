<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AffiliateDemoProductSeeder extends Seeder
{
    public function run(): void
    {
        $seller = Seller::firstOrCreate(
            ['username' => 'smart-comfort-finds'],
            [
                'store_name' => 'Smart Comfort Finds',
                'owner_name' => 'Annie Admin',
                'email' => 'admin@smartcomfortfinds.com',
                'phone' => '+1 555 010 2026',
                'country' => 'United States',
                'zip_code' => '00000',
                'location' => 'United States',
                'short_description' => 'Affiliate picks for eyewear, comfort, and daily-use accessories.',
                'is_active' => true,
            ]
        );

        $categories = collect([
            'Blue Light Glasses',
            'Sunglasses',
            'Reading Glasses',
            'Eyeglass Frames',
            'Lens Accessories',
            'Cleaning Kits',
        ])->map(fn (string $name) => Category::firstOrCreate(
            ['slug' => Str::slug($name)],
            [
                'name' => $name,
                'description' => 'Affiliate deals for '.$name.'.',
                'is_active' => true,
                'show_on_home' => true,
            ]
        ));

        $brands = collect([
            'Smart Comfort Finds',
            'Annie Eyewear',
            'Astra',
            'Peachmart',
            'RayBan',
            'Police',
        ])->map(fn (string $name) => Brand::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'is_active' => true]
        ));

        $products = [
            ['Blue Light Blocking Glasses for Computer Work', 'Blue Light Glasses', 'Astra', 'lens', 'both', 'astra-hexagonal-metal-frame.png', 4.6, ['Lightweight daily frame', 'Good for laptop and mobile use'], ['Confirm size on retailer page']],
            ['Anti Glare Reading Glasses 3 Pack', 'Reading Glasses', 'Smart Comfort Finds', 'lens', 'amazon', 'peachmart-blue-cut-glasses.png', 4.4, ['Useful multi-pack', 'Simple everyday design'], ['Strength options vary by listing']],
            ['Polarized Driving Sunglasses for Men', 'Sunglasses', 'Police', 'frame', 'both', 'police-polarized-sunglasses.png', 4.7, ['Polarized lenses', 'Good outdoor pick'], ['Fit may vary by face shape']],
            ['Women Oversized UV400 Sunglasses', 'Sunglasses', 'Annie Eyewear', 'frame', 'temu', 'cartier-premium-half-frame.png', 4.3, ['Fashion-forward shape', 'UV400 style listing'], ['Retailer color can vary']],
            ['Flexible TR90 Eyeglass Frames', 'Eyeglass Frames', 'Astra', 'frame', 'amazon', 'tomford-men-eyewear.png', 4.5, ['Flexible frame material', 'Prescription-ready style'], ['Lens fitting handled by retailer or optician']],
            ['Rimless Lightweight Reading Glasses', 'Reading Glasses', 'Peachmart', 'lens', 'temu', 'alpha-bluecut-glasses.png', 4.2, ['Minimal frame look', 'Good spare pair option'], ['Check magnification before buying']],
            ['Kids Blue Light Glasses', 'Blue Light Glasses', 'Smart Comfort Finds', 'lens', 'both', 'rayban-premium-eyewear-frame.png', 4.4, ['Child-friendly style', 'Screen-time focused'], ['Sizing needs careful checking']],
            ['Magnetic Clip On Sunglasses Frame', 'Eyeglass Frames', 'Annie Eyewear', 'frame', 'amazon', 'cartier-transition-glasses.png', 4.1, ['Two-in-one style', 'Useful for indoor and outdoor use'], ['Magnet alignment depends on model']],
            ['Eyeglass Repair Kit with Screws', 'Lens Accessories', 'Smart Comfort Finds', 'accessory', 'temu', 'eyewear-cleaning-kit.png', 4.3, ['Handy home repair kit', 'Small and easy to store'], ['Tiny parts require careful handling']],
            ['Microfiber Lens Cleaning Cloth Set', 'Cleaning Kits', 'Annie Eyewear', 'accessory', 'both', 'eyewear-cleaning-kit.png', 4.8, ['Useful for glasses and screens', 'Low-cost add-on item'], ['Wash before reuse for best results']],
            ['Portable Hard Shell Glasses Case', 'Lens Accessories', 'Smart Comfort Finds', 'accessory', 'amazon', 'custom-prescription-lens-package.png', 4.5, ['Protective travel case', 'Works for most standard frames'], ['Large sunglasses may not fit']],
            ['Photochromic Transition Style Glasses', 'Sunglasses', 'RayBan', 'lens', 'both', 'cartier-transition-glasses.png', 4.6, ['Indoor/outdoor convenience', 'Popular lens style'], ['Transition speed depends on conditions']],
        ];

        foreach ($products as $index => [$name, $categoryName, $brandName, $type, $platform, $image, $rating, $pros, $cons]) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'brand_id' => $brands->firstWhere('name', $brandName)?->id,
                    'seller_id' => $seller->id,
                    'name' => $name,
                    'sku' => 'SCF-AFF-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'product_type' => $type,
                    'affiliate_platform' => $platform,
                    'amazon_url' => in_array($platform, ['amazon', 'both'], true) ? 'https://www.amazon.com/?tag=yourtag-20' : null,
                    'temu_url' => in_array($platform, ['temu', 'both'], true) ? 'https://www.temu.com/' : null,
                    'external_product_id' => 'SCF-DEMO-'.($index + 1),
                    'has_variants' => false,
                    'status' => 'published',
                    'review_status' => 'approved',
                    'base_price' => 0,
                    'sale_price' => null,
                    'price_note' => 'Check latest price',
                    'affiliate_rating' => $rating,
                    'stock' => 0,
                    'low_stock_threshold' => 0,
                    'short_description' => $this->shortDescription($categoryName),
                    'description' => '<p>This is a demo affiliate product for Smart Comfort Finds. Replace the placeholder retailer URL with your real Amazon or Temu affiliate link from the admin panel.</p>',
                    'pros' => $pros,
                    'cons' => $cons,
                    'is_draft' => false,
                    'is_active' => true,
                    'is_featured' => $index < 6,
                    'out_of_stock' => false,
                ]
            );

            $category = $categories->firstWhere('name', $categoryName);
            $product->categories()->sync([$category->id]);

            Image::updateOrCreate(
                [
                    'imageable_id' => $product->id,
                    'imageable_type' => Product::class,
                    'type' => 'primary',
                ],
                [
                    'path' => 'assets/images/optical/products/'.$image,
                    'order' => 0,
                ]
            );
        }
    }

    private function shortDescription(string $category): string
    {
        return match ($category) {
            'Blue Light Glasses' => 'Affiliate pick for screen use, remote work, and everyday device time.',
            'Sunglasses' => 'Affiliate pick for outdoor use, driving, and casual eyewear.',
            'Reading Glasses' => 'Affiliate pick for reading, office use, and spare everyday glasses.',
            'Eyeglass Frames' => 'Affiliate pick for frame comparison before retailer checkout.',
            'Lens Accessories', 'Cleaning Kits' => 'Affiliate pick for eyewear care, storage, and maintenance.',
            default => 'Affiliate eyewear pick for Smart Comfort Finds.',
        };
    }
}

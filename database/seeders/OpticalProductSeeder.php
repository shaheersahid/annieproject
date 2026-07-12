<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OpticalProductSeeder extends Seeder
{
    public function run(): void
    {
        $customers = $this->seedCustomers();
        $seller = $this->seedSeller();
        $categories = $this->seedCategories();
        $brands = $this->seedBrands();
        $tags = $this->seedTags();
        $attributes = $this->seedAttributes($categories);
        $products = $this->seedProducts($seller, $categories, $brands, $tags, $attributes);

        $this->seedOrders($customers, $seller, $products->where('status', 'published')->values());
    }

    protected function seedCustomers()
    {
        return collect([
            ['name' => 'Ayesha Khan', 'email' => 'ayesha.khan@example.com', 'phone' => '+92 300 1112233'],
            ['name' => 'Hassan Raza', 'email' => 'hassan.raza@example.com', 'phone' => '+92 301 2223344'],
            ['name' => 'Sana Qureshi', 'email' => 'sana.qureshi@example.com', 'phone' => '+92 302 3334455'],
            ['name' => 'Bilal Ahmed', 'email' => 'bilal.ahmed@example.com', 'phone' => '+92 303 4445566'],
        ])->map(fn ($customer) => User::updateOrCreate(
            ['email' => $customer['email']],
            $customer + [
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        ));
    }

    protected function seedSeller(): Seller
    {
        return Seller::updateOrCreate(
            ['username' => 'qadir-optics'],
            [
                'store_name' => 'Qadir Optics',
                'owner_name' => 'Qadir Optics Team',
                'email' => 'sales@qadiroptics.test',
                'phone' => '+92 321 1001001',
                'country' => 'Pakistan',
                'zip_code' => '54000',
                'location' => 'Lahore, Pakistan',
                'short_description' => 'Custom prescription glasses, optical frames, blue cut lenses, transition lenses, sunglasses, and eyewear accessories.',
                'is_active' => true,
            ]
        );
    }

    protected function seedCategories()
    {
        $parents = collect([
            ['name' => 'Frames', 'description' => 'Prescription-ready optical frames by size, shape, and fit.', 'sort_order' => 10, 'show_on_home' => true],
            ['name' => 'Lenses', 'description' => 'Blue cut, transition, powered, and non-powered lens services.', 'sort_order' => 20, 'show_on_home' => true],
            ['name' => 'Sunglasses', 'description' => 'Outdoor and prescription-ready sunglasses.', 'sort_order' => 30, 'show_on_home' => true],
            ['name' => 'Accessories', 'description' => 'Cases, cleaning kits, and daily eyewear care items.', 'sort_order' => 40, 'show_on_home' => true],
        ])->map(fn ($category) => Category::updateOrCreate(
            ['slug' => Str::slug($category['name'])],
            $category + ['is_active' => true]
        ));

        $children = collect([
            ['name' => 'Luxury Frames', 'parent' => 'Frames', 'description' => 'Premium frames similar to Peachmart luxury-frame collections.', 'sort_order' => 11],
            ['name' => 'Men Frames', 'parent' => 'Frames', 'description' => 'Wide and medium fits for men.', 'sort_order' => 12],
            ['name' => 'Women Frames', 'parent' => 'Frames', 'description' => 'Everyday and fashion-forward women frames.', 'sort_order' => 13],
            ['name' => 'Power Free Fashion Frames', 'parent' => 'Frames', 'description' => 'Non-prescription frames for daily styling.', 'sort_order' => 14],
            ['name' => 'Blue Cut Glasses', 'parent' => 'Lenses', 'description' => 'Screen-protection glasses and blue light lens options.', 'sort_order' => 21],
            ['name' => 'Transition Glasses', 'parent' => 'Lenses', 'description' => 'Photochromic transition lenses and complete glasses.', 'sort_order' => 22],
            ['name' => 'Prescription Lenses', 'parent' => 'Lenses', 'description' => 'Custom lenses based on customer prescription.', 'sort_order' => 23],
            ['name' => 'Intelligent Glasses', 'parent' => 'Lenses', 'description' => 'Smart lens packages for indoor and outdoor use.', 'sort_order' => 24],
            ['name' => 'Cleaning Kits', 'parent' => 'Accessories', 'description' => 'Spray, cloth, and care kits.', 'sort_order' => 41],
        ])->map(function ($category) use ($parents) {
            $parent = $parents->firstWhere('name', $category['parent']);

            return Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'parent_id' => $parent?->id,
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                    'show_on_home' => false,
                ]
            );
        });

        return $parents->merge($children)->values();
    }

    protected function seedBrands()
    {
        return collect([
            'RayBan',
            'Cartier',
            'Prada',
            'TomFord',
            'BOSS',
            'Police',
            'Lacoste',
            'Astra',
            'Peachmart',
            'Qadir Optics',
        ])->map(fn ($name) => Brand::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'is_active' => true]
        ));
    }

    protected function seedTags()
    {
        return collect([
            ['name' => 'New Arrival', 'type' => 'product', 'option' => 'new_arrival'],
            ['name' => 'Trending', 'type' => 'product', 'option' => 'trending'],
            ['name' => 'On Sale', 'type' => 'product', 'option' => 'on_sale'],
            ['name' => 'Bestseller', 'type' => 'product', 'option' => 'bestseller'],
        ])->map(fn ($tag) => ProductTag::updateOrCreate(
            ['slug' => Str::slug($tag['name'])],
            $tag + ['is_active' => true]
        ));
    }

    protected function seedAttributes($categories)
    {
        $attributes = collect([
            ['name' => 'Frame Size', 'value' => "Narrow\nMedium\nWide", 'input_type' => 'dropdown', 'categories' => ['Frames', 'Luxury Frames', 'Men Frames', 'Women Frames', 'Power Free Fashion Frames', 'Sunglasses']],
            ['name' => 'Frame Color', 'value' => "Black\nGold\nSilver\nGrey Transparent\nMatte White\nMaroon\nTortoise", 'input_type' => 'color_switch', 'categories' => ['Frames', 'Luxury Frames', 'Men Frames', 'Women Frames', 'Power Free Fashion Frames', 'Sunglasses']],
            ['name' => 'Lens Type', 'value' => "Blue Cut\nTransition\nPrescription\nPower Free\nPolarized", 'input_type' => 'dropdown', 'categories' => ['Lenses', 'Blue Cut Glasses', 'Transition Glasses', 'Prescription Lenses', 'Intelligent Glasses', 'Sunglasses']],
            ['name' => 'Prescription Range', 'value' => "Power Free\nSingle Vision\nBifocal\nProgressive", 'input_type' => 'dropdown', 'categories' => ['Lenses', 'Prescription Lenses', 'Transition Glasses']],
            ['name' => 'Accessory Pack', 'value' => "Case Only\nCleaning Kit\nCase + Cloth\nComplete Care Kit", 'input_type' => 'dropdown', 'categories' => ['Accessories', 'Cleaning Kits']],
        ])->map(function ($attribute, $index) use ($categories) {
            $categoryNames = $attribute['categories'];
            unset($attribute['categories']);

            $model = ProductAttribute::updateOrCreate(
                ['slug' => Str::slug($attribute['name'])],
                $attribute + ['is_active' => true, 'sort_order' => ($index + 1) * 10]
            );

            $model->categories()->sync($categories->whereIn('name', $categoryNames)->pluck('id')->all());

            return $model;
        });

        return $attributes;
    }

    protected function seedProducts(Seller $seller, $categories, $brands, $tags, $attributes)
    {
        $catalog = collect([
            ['name' => 'RayBan Premium Eyewear Frame L-328', 'type' => 'frame', 'brand' => 'RayBan', 'price' => 6500, 'sale_price' => 5200, 'stock' => 36, 'category' => 'Luxury Frames', 'image' => 'rayban-premium-eyewear-frame.png', 'attributes' => ['Frame Size' => ['Medium', 'Wide'], 'Frame Color' => ['Black', 'Gold']]],
            ['name' => 'Cartier Premium Half Frame L-340', 'type' => 'frame', 'brand' => 'Cartier', 'price' => 7200, 'sale_price' => 5900, 'stock' => 22, 'category' => 'Luxury Frames', 'image' => 'cartier-premium-half-frame.png', 'attributes' => ['Frame Size' => ['Wide'], 'Frame Color' => ['Gold', 'Silver']]],
            ['name' => 'Astra Hexagonal Metal Frame L-160 Grey Transparent', 'type' => 'frame', 'brand' => 'Astra', 'price' => 4800, 'sale_price' => 3600, 'stock' => 41, 'category' => 'Power Free Fashion Frames', 'image' => 'astra-hexagonal-metal-frame.png', 'attributes' => ['Frame Size' => ['Medium', 'Wide'], 'Frame Color' => ['Grey Transparent', 'Matte White']]],
            ['name' => 'TomFord High Quality Men Eyewear L-325', 'type' => 'frame', 'brand' => 'TomFord', 'price' => 6800, 'sale_price' => null, 'stock' => 18, 'category' => 'Men Frames', 'image' => 'tomford-men-eyewear.png', 'attributes' => ['Frame Size' => ['Medium'], 'Frame Color' => ['Black', 'Tortoise']]],
            ['name' => 'Peachmart Aero Screen Blue Cut Glasses 1786', 'type' => 'lens', 'brand' => 'Peachmart', 'price' => 3200, 'sale_price' => 2600, 'stock' => 55, 'category' => 'Blue Cut Glasses', 'image' => 'peachmart-blue-cut-glasses.png', 'attributes' => ['Lens Type' => ['Blue Cut'], 'Frame Size' => ['Medium', 'Wide']]],
            ['name' => 'Alpha Screen Protection BlueCut Glasses T-009', 'type' => 'lens', 'brand' => 'Qadir Optics', 'price' => 2900, 'sale_price' => 2400, 'stock' => 48, 'category' => 'Blue Cut Glasses', 'image' => 'alpha-bluecut-glasses.png', 'attributes' => ['Lens Type' => ['Blue Cut'], 'Frame Size' => ['Narrow', 'Medium']]],
            ['name' => 'Cartier Transition Glasses PC-237', 'type' => 'lens', 'brand' => 'Cartier', 'price' => 8500, 'sale_price' => 7000, 'stock' => 24, 'category' => 'Transition Glasses', 'image' => 'cartier-transition-glasses.png', 'attributes' => ['Lens Type' => ['Transition'], 'Prescription Range' => ['Power Free', 'Single Vision']]],
            ['name' => 'Qadir Custom Prescription Lens Package', 'type' => 'service', 'brand' => 'Qadir Optics', 'price' => 4500, 'sale_price' => null, 'stock' => 100, 'category' => 'Prescription Lenses', 'image' => 'custom-prescription-lens-package.png', 'attributes' => ['Lens Type' => ['Prescription'], 'Prescription Range' => ['Single Vision', 'Bifocal', 'Progressive']]],
            ['name' => 'Police Polarized Sunglasses Gold Black L-195', 'type' => 'frame', 'brand' => 'Police', 'price' => 7800, 'sale_price' => 6700, 'stock' => 17, 'category' => 'Sunglasses', 'image' => 'police-polarized-sunglasses.png', 'attributes' => ['Lens Type' => ['Polarized'], 'Frame Color' => ['Gold', 'Black']]],
            ['name' => 'Qadir Complete Eyewear Cleaning Kit', 'type' => 'accessory', 'brand' => 'Qadir Optics', 'price' => 850, 'sale_price' => null, 'stock' => 120, 'category' => 'Cleaning Kits', 'image' => 'eyewear-cleaning-kit.png', 'attributes' => ['Accessory Pack' => ['Complete Care Kit', 'Cleaning Kit']]],
        ]);

        return $catalog->map(function ($item, $index) use ($seller, $categories, $brands, $tags, $attributes) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'brand_id' => $brands->firstWhere('name', $item['brand'])?->id,
                    'seller_id' => $seller->id,
                    'name' => $item['name'],
                    'sku' => 'QO-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'product_type' => $item['type'],
                    'affiliate_platform' => $index % 3 === 0 ? 'both' : ($index % 2 === 0 ? 'temu' : 'amazon'),
                    'amazon_url' => $index % 3 === 0 || $index % 2 === 1 ? 'https://www.amazon.com/?tag=yourtag-20' : null,
                    'temu_url' => $index % 3 === 0 || $index % 2 === 0 ? 'https://www.temu.com/' : null,
                    'external_product_id' => 'QO-DEMO-'.$index,
                    'has_variants' => count($item['attributes']) > 0,
                    'status' => 'published',
                    'review_status' => 'approved',
                    'is_draft' => false,
                    'is_active' => true,
                    'out_of_stock' => false,
                    'base_price' => $item['price'],
                    'sale_price' => $item['sale_price'],
                    'price_note' => 'Check latest price',
                    'affiliate_rating' => 4.1 + (($index % 5) / 10),
                    'deal_enabled' => $item['sale_price'] !== null,
                    'deal_type' => $item['sale_price'] !== null ? 'fixed' : null,
                    'deal_value' => $item['sale_price'] !== null ? $item['price'] - $item['sale_price'] : null,
                    'stock' => $item['stock'],
                    'sold_out' => 0,
                    'short_description' => $this->shortDescription($item['type']),
                    'description' => 'Seeded optical catalog item for inventory, dashboard categorization, variants, and cash-on-delivery demo orders.',
                    'pros' => ['Good everyday value', 'Relevant eyewear category', 'Easy retailer checkout'],
                    'cons' => ['Retailer price can change', 'Availability depends on seller'],
                    'is_featured' => $index < 4,
                ]
            );

            $category = $categories->firstWhere('name', $item['category']);
            $product->categories()->sync([$category->id]);

            foreach ($tags->pluck('id')->take(2) as $tagId) {
                DB::table('product_tag_product')->updateOrInsert([
                    'product_id' => $product->id,
                    'product_tag_id' => $tagId,
                ]);
            }

            Image::updateOrCreate(
                [
                    'imageable_id' => $product->id,
                    'imageable_type' => Product::class,
                    'type' => 'primary',
                ],
                [
                    'path' => asset('assets/images/optical/products/'.$item['image']),
                    'order' => 0,
                ]
            );

            $this->syncVariants($product, $item, $attributes);

            return $product;
        });
    }

    protected function syncVariants(Product $product, array $item, $attributes): void
    {
        $position = 0;
        $variantIds = [];
        $categoryId = $product->categories()->value('categories.id');

        foreach ($item['attributes'] as $attributeName => $values) {
            foreach ($values as $value) {
                $attribute = $attributes->firstWhere('name', $attributeName);
                $payload = [$attributeName => $value];
                $sku = $product->sku.'-'.Str::upper(Str::slug($value, ''));

                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'product_id' => $product->id,
                        'category_id' => $categoryId,
                        'attributes' => $payload,
                        'price' => (float) $product->base_price + ($position * 150),
                        'sale_price' => $product->sale_price ? (float) $product->sale_price + ($position * 100) : null,
                        'deal_enabled' => (bool) $product->deal_enabled,
                        'deal_type' => $product->deal_type,
                        'deal_value' => $product->deal_value,
                        'stock' => max(5, (int) floor($product->stock / 3) - $position),
                        'is_active' => true,
                        'position' => $position,
                        'combination_hash' => hash('sha256', $product->id.'|'.$attribute?->id.'|'.$value),
                    ]
                );

                $variantIds[] = $variant->id;
                $position++;
            }
        }

        $product->variants()->whereNotIn('id', $variantIds)->delete();
    }

    protected function seedOrders($customers, Seller $seller, $products): void
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        for ($i = 1; $i <= 12; $i++) {
            $customer = $customers[($i - 1) % $customers->count()];
            $status = $statuses[$i % count($statuses)];
            $picked = $products->slice($i % max(1, $products->count()), 2);
            $picked = $picked->isEmpty() ? $products->take(2) : $picked;
            $subTotal = 0;

            $order = Order::updateOrCreate(
                ['order_number' => 'QO-COD-'.str_pad((string) $i, 5, '0', STR_PAD_LEFT)],
                [
                    'customer_id' => $customer->id,
                    'seller_id' => $seller->id,
                    'status' => $status,
                    'payment_status' => $status === 'cancelled' ? 'cancelled' : 'pending',
                    'shipping_address' => $customer->name.', Lahore, Pakistan',
                    'notes' => 'Cash on delivery order. No online payment gateway is enabled.',
                    'ordered_at' => now()->subDays(12 - $i),
                    'created_at' => now()->subDays(12 - $i),
                    'updated_at' => now()->subDays(12 - $i),
                ]
            );

            $order->items()->delete();

            foreach ($picked as $product) {
                $quantity = 1 + ($i % 2);
                $unitPrice = (float) ($product->sale_price ?? $product->base_price);
                $lineTotal = $quantity * $unitPrice;
                $subTotal += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $product->variants()->orderBy('position')->value('id'),
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $shipping = $subTotal >= 5000 ? 0 : 250;
            $grand = $subTotal + $shipping;

            $order->update([
                'sub_total' => $subTotal,
                'vat_total' => 0,
                'tax_total' => 0,
                'shipping_total' => $shipping,
                'discount_total' => 0,
                'grand_total' => $grand,
            ]);

            Transaction::updateOrCreate(
                ['transaction_number' => 'QO-COD-TXN-'.str_pad((string) $i, 5, '0', STR_PAD_LEFT)],
                [
                    'order_id' => $order->id,
                    'payment_method' => 'cash_on_delivery',
                    'status' => 'pending',
                    'amount' => $grand,
                    'gateway_reference' => null,
                    'paid_at' => null,
                ]
            );
        }
    }

    protected function shortDescription(string $type): string
    {
        return match ($type) {
            'lens' => 'Custom lens option for screen protection, prescription, or transition use.',
            'accessory' => 'Practical eyewear accessory for everyday care and storage.',
            'service' => 'Optical service item for custom prescription handling.',
            default => 'Prescription-ready optical frame with managed sizes, colors, and inventory.',
        };
    }
}

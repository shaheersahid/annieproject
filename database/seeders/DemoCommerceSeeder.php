<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\Seller;
use App\Models\SizeChart;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoCommerceSeeder extends Seeder
{
    public function run(): void
    {
        $customers = $this->seedCustomers();
        $sellers = $this->seedSellers();
        $categories = $this->seedCategories();
        $brands = $this->seedBrands();
        $sizeCharts = $this->seedSizeCharts();
        $tags = $this->seedTags();
        $attributes = $this->seedAttributes();
        $products = $this->seedProducts($sellers, $categories, $brands, $sizeCharts, $tags, $attributes);

        $this->seedOrders($customers, $sellers, $products->where('status', 'published')->values());
    }

    protected function seedCustomers()
    {
        $customers = collect([
            ['name' => 'Ayesha Khan', 'email' => 'ayesha.khan@example.com', 'phone' => '+92 300 1112233'],
            ['name' => 'Hira Malik', 'email' => 'hira.malik@example.com', 'phone' => '+92 301 2223344'],
            ['name' => 'Sana Qureshi', 'email' => 'sana.qureshi@example.com', 'phone' => '+92 302 3334455'],
            ['name' => 'Fatima Raza', 'email' => 'fatima.raza@example.com', 'phone' => '+92 303 4445566'],
            ['name' => 'Noor Ahmed', 'email' => 'noor.ahmed@example.com', 'phone' => '+92 304 5556677'],
            ['name' => 'Mariam Sheikh', 'email' => 'mariam.sheikh@example.com', 'phone' => '+92 305 6667788'],
            ['name' => 'Zara Ali', 'email' => 'zara.ali@example.com', 'phone' => '+92 306 7778899'],
            ['name' => 'Alina Butt', 'email' => 'alina.butt@example.com', 'phone' => '+92 307 8889900'],
        ]);

        return $customers->map(fn ($customer) => User::updateOrCreate(
            ['email' => $customer['email']],
            $customer + [
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        ));
    }

    protected function seedSellers()
    {
        return collect([
            ['username' => 'nishat-atelier', 'store_name' => 'Nishat Atelier', 'owner_name' => 'Maha Farooq', 'email' => 'seller.nishat@example.com', 'phone' => '+92 321 1001001', 'location' => 'Gulberg, Lahore'],
            ['username' => 'karachi-pret', 'store_name' => 'Karachi Pret Studio', 'owner_name' => 'Danish Mirza', 'email' => 'seller.pret@example.com', 'phone' => '+92 321 1001002', 'location' => 'Clifton, Karachi'],
            ['username' => 'rawalpindi-couture', 'store_name' => 'Rawalpindi Couture', 'owner_name' => 'Nimra Shah', 'email' => 'seller.couture@example.com', 'phone' => '+92 321 1001003', 'location' => 'Saddar, Rawalpindi'],
        ])->map(fn ($seller) => Seller::updateOrCreate(
            ['username' => $seller['username']],
            $seller + [
                'country' => 'Pakistan',
                'zip_code' => '54000',
                'short_description' => 'Curated women fashion, stitched suits, unstitched collections, and seasonal embroidery.',
                'is_active' => true,
            ]
        ));
    }

    protected function seedCategories()
    {
        $parents = collect(['Women Clothing', 'Luxury Pret', 'Festive Wear'])->map(fn ($name) => Category::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'is_active' => true]
        ));

        $children = collect([
            ['name' => 'Stitched Suits', 'parent' => 'Women Clothing'],
            ['name' => 'Unstitched Lawn', 'parent' => 'Women Clothing'],
            ['name' => 'Embroidered Kurtis', 'parent' => 'Luxury Pret'],
            ['name' => 'Formal Dresses', 'parent' => 'Festive Wear'],
        ])->map(function ($category) use ($parents) {
            $parent = $parents->firstWhere('name', $category['parent']);

            return Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                ['name' => $category['name'], 'parent_id' => $parent?->id, 'is_active' => true]
            );
        });

        return $parents->merge($children)->values();
    }

    protected function seedBrands()
    {
        return collect(['Raimall Signature', 'Lawn Luxe', 'Urban Modesty', 'Thread Theory'])->map(fn ($name) => Brand::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'is_active' => true]
        ));
    }

    protected function seedSizeCharts()
    {
        return collect([
            ['name' => 'Standard Women Size Chart', 'description' => 'XS to XL measurements for ready-to-wear products.'],
            ['name' => 'Unstitched Fabric Guide', 'description' => 'Fabric length and dupatta measurements for unstitched suits.'],
        ])->map(fn ($chart) => SizeChart::updateOrCreate(
            ['name' => $chart['name']],
            $chart + ['measurements' => ['small' => '36', 'medium' => '40', 'large' => '44']]
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

    protected function seedAttributes()
    {
        return collect([
            ['name' => 'Size', 'value' => "XS\nS\nM\nL\nXL", 'input_type' => 'dropdown'],
            ['name' => 'Color', 'value' => "Ivory\nBlack\nMaroon\nSage\nNavy", 'input_type' => 'color_switch'],
            ['name' => 'Fabric', 'value' => "Lawn\nCotton\nChiffon\nJacquard\nKhaddar", 'input_type' => 'dropdown'],
        ])->map(fn ($attribute) => ProductAttribute::updateOrCreate(
            ['slug' => Str::slug($attribute['name'])],
            $attribute + ['is_active' => true, 'sort_order' => 0]
        ));
    }

    protected function seedProducts($sellers, $categories, $brands, $sizeCharts, $tags, $attributes)
    {
        $catalog = collect([
            ['name' => 'Ivory Embroidered Lawn Suit', 'type' => 'unstitched', 'price' => 8500, 'stock' => 64, 'category' => 'Unstitched Lawn', 'status' => 'published', 'image' => 'assets/images/demos/demo-7/products/product-1-1.jpg'],
            ['name' => 'Maroon Formal Chiffon Dress', 'type' => 'stitched', 'price' => 14500, 'stock' => 28, 'category' => 'Formal Dresses', 'status' => 'published', 'image' => 'assets/images/demos/demo-7/products/product-2-1.jpg'],
            ['name' => 'Sage Green Pret Kurti', 'type' => 'stitched', 'price' => 5200, 'stock' => 42, 'category' => 'Stitched Suits', 'status' => 'published', 'image' => 'assets/images/demos/demo-7/products/product-3-1.jpg'],
            ['name' => 'Black Threadwork Kurti', 'type' => 'embroidery', 'price' => 6900, 'stock' => 36, 'category' => 'Embroidered Kurtis', 'status' => 'published', 'image' => 'assets/images/demos/demo-7/products/product-4-1.jpg'],
            ['name' => 'Navy Jacquard Two Piece', 'type' => 'stitched', 'price' => 7800, 'stock' => 33, 'category' => 'Luxury Pret', 'status' => 'published', 'image' => 'assets/images/demos/demo-7/products/product-5-1.jpg'],
            ['name' => 'Pastel Eid Unstitched Set', 'type' => 'unstitched', 'price' => 9800, 'stock' => 51, 'category' => 'Festive Wear', 'status' => 'published', 'image' => 'assets/images/demos/demo-7/products/product-6-1.jpg'],
            ['name' => 'Draft Pearl Organza Dupatta', 'type' => 'unstitched', 'price' => 4200, 'stock' => 18, 'category' => 'Unstitched Lawn', 'status' => 'draft', 'image' => 'assets/images/demos/demo-7/products/product-7-1.jpg'],
            ['name' => 'Draft Minimal Cotton Co-ord', 'type' => 'stitched', 'price' => 6400, 'stock' => 22, 'category' => 'Stitched Suits', 'status' => 'draft', 'image' => 'assets/images/demos/demo-7/products/product-8-1.jpg'],
            ['name' => 'Draft Gold Motif Kurti', 'type' => 'embroidery', 'price' => 5900, 'stock' => 15, 'category' => 'Embroidered Kurtis', 'status' => 'draft', 'image' => 'assets/images/demos/demo-7/products/product-9-1.jpg'],
        ]);

        return $catalog->map(function ($item, $index) use ($sellers, $categories, $brands, $sizeCharts, $tags, $attributes) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'brand_id' => $brands[$index % $brands->count()]->id,
                    'seller_id' => $sellers[$index % $sellers->count()]->id,
                    'size_chart_id' => $item['type'] === 'unstitched' ? $sizeCharts->last()->id : null,
                    'name' => $item['name'],
                    'sku' => 'RM-'.$index.sprintf('%03d', $index + 1),
                    'product_type' => $item['type'],
                    'has_variants' => true,
                    'status' => $item['status'],
                    'review_status' => $item['status'] === 'published' ? 'approved' : 'pending',
                    'base_price' => $item['price'],
                    'sale_price' => $index % 2 === 0 ? $item['price'] - 700 : null,
                    'deal_enabled' => $index % 2 === 0,
                    'deal_type' => $index % 2 === 0 ? 'fixed' : null,
                    'deal_value' => $index % 2 === 0 ? 700 : null,
                    'stock' => $item['stock'],
                    'sold_out' => rand(8, 45),
                    'short_description' => 'Premium '.$item['name'].' crafted for daily elegance and seasonal occasions.',
                    'description' => 'A realistic demo product with curated fabric, seller, category, pricing, stock, and order history.',
                ]
            );

            $category = $categories->firstWhere('name', $item['category']) ?? $categories->first();
            $product->categories()->syncWithoutDetaching([$category->id]);
            foreach ($tags->pluck('id')->random(min(2, $tags->count()))->all() as $tagId) {
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
                    'path' => asset($item['image']),
                    'order' => 0,
                ]
            );

            Image::updateOrCreate(
                [
                    'imageable_id' => $product->id,
                    'imageable_type' => Product::class,
                    'type' => 'gallery',
                    'order' => 1,
                ],
                [
                    'path' => asset(str_replace('-1.jpg', '-2.jpg', $item['image'])),
                ]
            );

            foreach (['S', 'M', 'L'] as $position => $size) {
                ProductVariant::updateOrCreate(
                    ['sku' => $product->sku.'-'.$size],
                    [
                        'product_id' => $product->id,
                        'attributes' => ['Size' => $size],
                        'price' => $item['price'] + ($position * 250),
                        'stock' => max(5, (int) floor($item['stock'] / 3) - $position),
                        'is_active' => true,
                        'position' => $position,
                    ]
                );
            }

            return $product;
        });
    }

    protected function seedOrders($customers, $sellers, $products): void
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'];

        for ($i = 1; $i <= 42; $i++) {
            $seller = $sellers[($i - 1) % $sellers->count()];
            $customer = $customers[($i - 1) % $customers->count()];
            $orderProducts = $products->where('seller_id', $seller->id)->values();
            if ($orderProducts->isEmpty()) {
                $orderProducts = $products;
            }

            $picked = $orderProducts->random(min(rand(1, 3), $orderProducts->count()));
            $picked = $picked instanceof Product ? collect([$picked]) : $picked;
            $subTotal = 0;

            $order = Order::updateOrCreate(
                ['order_number' => 'RM-ORD-'.str_pad((string) $i, 5, '0', STR_PAD_LEFT)],
                [
                    'customer_id' => $customer->id,
                    'seller_id' => $seller->id,
                    'status' => $statuses[$i % count($statuses)],
                    'payment_status' => in_array($statuses[$i % count($statuses)], ['cancelled', 'failed']) ? 'failed' : 'paid',
                    'shipping_address' => $customer->name.', Lahore, Pakistan',
                    'notes' => 'Demo order seeded for management and reports.',
                    'ordered_at' => now()->subDays(42 - $i),
                    'created_at' => now()->subDays(42 - $i),
                    'updated_at' => now()->subDays(42 - $i),
                ]
            );

            $order->items()->delete();

            foreach ($picked as $product) {
                $quantity = rand(1, 3);
                $unitPrice = (float) $product->base_price;
                $lineTotal = $quantity * $unitPrice;
                $subTotal += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $product->variants()->inRandomOrder()->value('id'),
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $tax = round($subTotal * 0.05, 2);
            $shipping = $subTotal >= 10000 ? 0 : 350;
            $discount = $i % 5 === 0 ? 500 : 0;
            $grand = $subTotal + $tax + $shipping - $discount;
            $order->update([
                'sub_total' => $subTotal,
                'vat_total' => $tax,
                'tax_total' => $tax,
                'shipping_total' => $shipping,
                'discount_total' => $discount,
                'grand_total' => $grand,
            ]);

            Transaction::updateOrCreate(
                ['transaction_number' => 'TXN-'.str_pad((string) $i, 6, '0', STR_PAD_LEFT)],
                [
                    'order_id' => $order->id,
                    'payment_method' => $i % 3 === 0 ? 'bank_transfer' : 'card',
                    'status' => $order->payment_status === 'paid' ? 'paid' : 'failed',
                    'amount' => $grand,
                    'gateway_reference' => 'GW-'.$order->order_number,
                    'paid_at' => $order->payment_status === 'paid' ? $order->created_at : null,
                ]
            );

            if ($order->status === 'returned') {
                OrderReturn::updateOrCreate(
                    ['return_number' => 'RET-'.str_pad((string) $i, 5, '0', STR_PAD_LEFT)],
                    [
                        'order_id' => $order->id,
                        'status' => 'refunded',
                        'refund_amount' => round($grand * 0.8, 2),
                        'reason' => 'Customer requested size exchange; refund processed for demo data.',
                        'requested_at' => $order->created_at->copy()->addDays(3),
                    ]
                );
            }
        }
    }
}

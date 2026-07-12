<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Seller;
use App\Models\Image;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ExtendedProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellers = Seller::all();
        $brands = Brand::all();
        $categories = Category::all();

        if ($sellers->isEmpty() || $brands->isEmpty() || $categories->isEmpty()) {
            $this->command->info('Please run DemoCommerceSeeder first to create sellers, brands, and categories.');
            return;
        }

        // Get main categories and their product data
        $categoryProducts = $this->getProductDataByCategory();

        $productCount = 0;
        foreach ($categoryProducts as $categoryName => $products) {
            $category = $categories->firstWhere('name', $categoryName);
            if (!$category) {
                continue;
            }

            // Create 50 products for each category
            foreach ($products as $index => $productData) {
                $productCount++;

                $product = Product::updateOrCreate(
                    ['slug' => Str::slug($productData['name'] . ' ' . ($index + 1))],
                    [
                        'brand_id' => $brands->random()->id,
                        'seller_id' => $sellers->random()->id,
                        'name' => $productData['name'],
                        'sku' => 'RM-' . $categoryName . '-' . sprintf('%05d', $index + 1),
                        'product_type' => $productData['type'],
                        'has_variants' => true,
                        'status' => $productData['status'],
                        'review_status' => $productData['status'] === 'published' ? 'approved' : 'pending',
                        'base_price' => $productData['base_price'],
                        'sale_price' => $productData['sale_price'] ?? null,
                        'deal_enabled' => isset($productData['deal_enabled']) ? $productData['deal_enabled'] : ($index % 3 === 0),
                        'deal_type' => ($index % 3 === 0) ? 'percentage' : null,
                        'deal_value' => ($index % 3 === 0) ? rand(5, 25) : null,
                        'stock' => rand(15, 150),
                        'sold_out' => rand(5, 50),
                        'short_description' => $productData['short_description'],
                        'description' => $productData['description'],
                    ]
                );

                // Assign category
                $product->categories()->syncWithoutDetaching([$category->id]);

                // Add primary image and gallery images
                $imagePaths = $this->getImagePathsForProduct($categoryName, $index);

                if (!empty($imagePaths)) {
                    Image::updateOrCreate(
                        [
                            'imageable_id' => $product->id,
                            'imageable_type' => Product::class,
                            'type' => 'primary',
                        ],
                        [
                            'path' => $imagePaths[0],
                            'order' => 1,
                        ]
                    );

                    foreach (array_slice($imagePaths, 1) as $order => $path) {
                        Image::firstOrCreate(
                            [
                                'imageable_id' => $product->id,
                                'imageable_type' => Product::class,
                                'type' => 'gallery',
                                'path' => $path,
                            ],
                            [
                                'order' => $order + 2,
                            ]
                        );
                    }
                }

                // Create product variants (3-5 per product)
                $this->createProductVariants($product, $productData, $index);

                if ($productCount % 10 === 0) {
                    $this->command->info("Created {$productCount} products...");
                }
            }
        }

        $this->command->info("Successfully created {$productCount} products across all categories!");
    }

    /**
     * Create product variants (3-5 per product with different sizes and colors)
     */
    protected function createProductVariants(Product $product, array $productData, int $index): void
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL'];
        $colors = ['Ivory', 'Black', 'Maroon', 'Sage', 'Navy', 'Teal', 'Rose Gold', 'Burgundy'];

        // Create 3-5 variants per product
        $variantCount = rand(3, 5);
        $basePrice = $productData['base_price'];
        $salePrice = $productData['sale_price'];

        for ($i = 0; $i < $variantCount; $i++) {
            $size = $sizes[$i % count($sizes)];
            $color = $colors[($i + $index) % count($colors)];
            $variantSku = $product->sku . '-' . strtoupper(substr($size, 0, 1)) . strtoupper(substr($color, 0, 1));

            // Vary price slightly for each variant
            $variantPrice = $basePrice + (rand(-500, 1000) / 100);
            $variantSalePrice = $salePrice ? $salePrice + (rand(-500, 500) / 100) : null;

            ProductVariant::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'sku' => $variantSku,
                ],
                [
                    'attributes' => [
                        'Size' => $size,
                        'Color' => $color,
                    ],
                    'price' => max(100, $variantPrice),
                    'sale_price' => $variantSalePrice ? max(100, $variantSalePrice) : null,
                    'deal_enabled' => $index % 3 === 0,
                    'deal_type' => ($index % 3 === 0) ? 'percentage' : null,
                    'deal_value' => ($index % 3 === 0) ? rand(5, 20) : null,
                    'stock' => rand(5, 40),
                    'sold_out' => rand(0, 15),
                    'is_active' => true,
                    'position' => $i,
                ]
            );
        }
    }

    /**
     * Get image paths for a product with 1 primary + 4 gallery images
     */
    protected function getImagePathsForProduct(string $category, int $index): array
    {
        $productImages = glob(public_path('assets/images/products/product-*.jpg')) ?: [];
        if (empty($productImages)) {
            return [];
        }

        sort($productImages, SORT_NATURAL);

        $totalImages = count($productImages);
        $startIndex = $index % $totalImages;
        $selectedImages = [];

        for ($i = 0; $i < 5; $i++) {
            $path = $productImages[($startIndex + $i) % $totalImages];
            $selectedImages[] = ltrim(str_replace(public_path(), '', $path), '/');
        }

        return array_values(array_unique($selectedImages));
    }

    /**
     * Get product data organized by category
     */
    protected function getProductDataByCategory(): array
    {
        return [
            'Stitched Suits' => $this->getStitchedSuitsProducts(),
            'Unstitched Lawn' => $this->getUnstitchedLawnProducts(),
            'Embroidered Kurtis' => $this->getEmbroideredKurtisProducts(),
            'Formal Dresses' => $this->getFormalDressesProducts(),
            'Luxury Pret' => $this->getLuxuryPretProducts(),
        ];
    }

    protected function getStitchedSuitsProducts(): array
    {
        $names = [
            'Classic Ivory Stitched Suit',
            'Navy Blue Formal Suit',
            'Sage Green Casual Suit',
            'Maroon Wine Stitched Suit',
            'Black Elegant Suit',
            'Cream Embellished Suit',
            'Teal Modern Suit',
            'Burgundy Premium Suit',
            'Mint Green Fresh Suit',
            'Charcoal Grey Suit',
            'Blush Pink Romantic Suit',
            'Olive Green Sophisticated Suit',
            'Pearl White Pristine Suit',
            'Chocolate Brown Rich Suit',
            'Lavender Dreamy Suit',
            'Rose Gold Luxury Suit',
            'Deep Purple Regal Suit',
            'Mustard Yellow Vibrant Suit',
            'Coral Peach Warm Suit',
            'Steel Grey Modern Suit',
            'Emerald Green Precious Suit',
            'Apricot Soft Suit',
            'Indigo Traditional Suit',
            'Taupe Elegant Suit',
            'Forest Green Classic Suit',
            'Peachy Pink Delicate Suit',
            'Deep Teal Bold Suit',
            'Ash Grey Neutral Suit',
            'Marigold Golden Suit',
            'Plum Purple Rich Suit',
            'Seafoam Green Cool Suit',
            'Rust Orange Warm Suit',
            'Silver Blue Modern Suit',
            'Honey Gold Radiant Suit',
            'Slate Blue Professional Suit',
            'Terracotta Earthy Suit',
            'Aquamarine Fresh Suit',
            'Ochre Golden Suit',
            'Dusty Rose Vintage Suit',
            'Teal Blue Trendy Suit',
            'Camel Beige Classic Suit',
            'Eggplant Purple Royal Suit',
            'Seafoam Mint Suit',
            'Wine Red Bold Suit',
            'Stone Grey Minimalist Suit',
            'Saffron Yellow Festival Suit',
            'Periwinkle Blue Soft Suit',
            'Rust Brown Heritage Suit',
        ];

        return $this->generateProducts($names, 'stitched', 3500, 8500, 'cotton');
    }

    protected function getUnstitchedLawnProducts(): array
    {
        $names = [
            'Ivory Embroidered Unstitched Lawn',
            'Pastel Pink Lawn Suit',
            'Mint Green Lawn Collection',
            'Lavender Floral Unstitched Lawn',
            'Cream Banarsi Lawn',
            'Aqua Blue Printed Lawn',
            'Blush Rose Embroidered Lawn',
            'Sage Green Traditional Lawn',
            'Peach Blossom Lawn Suit',
            'White Threadwork Lawn',
            'Light Yellow Casual Lawn',
            'Pale Blue Sky Lawn',
            'Dusty Pink Vintage Lawn',
            'Seafoam Tropical Lawn',
            'Lemon Yellow Fresh Lawn',
            'Lilac Purple Dreamy Lawn',
            'Cream Jacquard Unstitched',
            'Rose Pink Border Lawn',
            'Pistachio Green Printed Lawn',
            'Champagne Gold Luxury Lawn',
            'Sky Blue Classic Lawn',
            'Peach Melba Embroidered Lawn',
            'Mint Mojito Fresh Lawn',
            'Cream Canvas Lawn',
            'Blush Mauve Soft Lawn',
            'Turquoise Tropical Lawn',
            'Vanilla Cream Lawn',
            'Rose Quartz Pink Lawn',
            'Seafoam Breeze Lawn',
            'Butter Yellow Sunny Lawn',
            'Lavender Fields Lawn',
            'Cloud White Pure Lawn',
            'Coral Pink Vibrant Lawn',
            'Pigeon Blue Gentle Lawn',
            'Apricot Dream Lawn',
            'Mint Chocolate Lawn',
            'Blush Gold Glamorous Lawn',
            'Powder Blue Delicate Lawn',
            'Peach Nectar Lawn',
            'Sage Whisper Soft Lawn',
            'Cream Dream Elegant Lawn',
            'Rose Petal Romantic Lawn',
            'Teal Tide Trendy Lawn',
            'Buttercream Sweet Lawn',
            'Twilight Purple Evening Lawn',
            'Pearl Oyster Luxury Lawn',
            'Mint Tea Refreshing Lawn',
        ];

        return $this->generateProducts($names, 'unstitched', 2500, 6500, 'lawn');
    }

    protected function getEmbroideredKurtisProducts(): array
    {
        $names = [
            'Black Threadwork Embroidered Kurti',
            'Gold Zari Kurti',
            'Silver Sequence Kurti',
            'Maroon Beaded Luxury Kurti',
            'Navy Embellished Kurti',
            'Cream Mirror Work Kurti',
            'Teal Pearl Embroidered Kurti',
            'Burgundy Brocade Kurti',
            'Emerald Stones Kurti',
            'Deep Purple Royal Kurti',
            'Rose Gold Glamorous Kurti',
            'Chocolate Beads Kurti',
            'Plum Embroidered Premium Kurti',
            'Bronze Gold Thread Kurti',
            'Indigo Traditional Kurti',
            'Sapphire Blue Luxury Kurti',
            'Copper Metallic Kurti',
            'Blush Pearl Kurti',
            'Forest Green Embellished Kurti',
            'Coral Embroidered Kurti',
            'Olive Stones Kurti',
            'Mauve Mirror Work Kurti',
            'Charcoal Beaded Kurti',
            'Rust Zari Kurti',
            'Slate Blue Sequins Kurti',
            'Taupe Gold Embroidered Kurti',
            'Magenta Threadwork Kurti',
            'Cobalt Beads Kurti',
            'Ochre Embellished Kurti',
            'Petrol Blue Luxury Kurti',
            'Sand Gold Thread Kurti',
            'Eggplant Pearls Kurti',
            'Teal Metallic Kurti',
            'Crimson Zari Embroidered Kurti',
            'Pewter Stones Kurti',
            'Garnet Mirror Work Kurti',
            'Slate Beaded Premium Kurti',
            'Nutmeg Brown Threadwork Kurti',
            'Cerulean Sequence Kurti',
            'Caramel Gold Embellished Kurti',
            'Violet Royal Kurti',
            'Espresso Beads Kurti',
            'Turquoise Pearl Luxury Kurti',
            'Sienna Orange Zari Kurti',
            'Navy Mirror Stones Kurti',
            'Bordeaux Premium Embroidered Kurti',
            'Pistachio Green Metallic Kurti',
        ];

        return $this->generateProducts($names, 'embroidery', 5000, 12000, 'silk');
    }

    protected function getFormalDressesProducts(): array
    {
        $names = [
            'Maroon Formal Chiffon Dress',
            'Black Evening Gown',
            'Navy Formal Dress',
            'Emerald Green Cocktail Dress',
            'Deep Purple Evening Gown',
            'Burgundy Formal Dress',
            'Gold Sequined Evening Dress',
            'Silver Embellished Gown',
            'Red Carpet Dress',
            'Sapphire Blue Formal Dress',
            'Rose Gold Glamorous Dress',
            'Charcoal Grey Gown',
            'Teal Formal Dress',
            'Plum Evening Gown',
            'Bronze Metallic Dress',
            'Indigo Formal Dress',
            'Coral Evening Gown',
            'Blush Pink Formal Dress',
            'Forest Green Cocktail Dress',
            'Champagne Gold Gown',
            'Slate Blue Evening Dress',
            'Mauve Formal Dress',
            'Chocolate Brown Gown',
            'Rust Orange Evening Dress',
            'Eggplant Purple Formal Dress',
            'Copper Metallic Gown',
            'Dusty Rose Evening Dress',
            'Petrol Blue Formal Dress',
            'Taupe Elegant Gown',
            'Magenta Cocktail Dress',
            'Garnet Red Formal Dress',
            'Pewter Silver Evening Gown',
            'Olive Green Formal Dress',
            'Caramel Gold Elegant Dress',
            'Violet Royal Evening Gown',
            'Cream Lace Formal Dress',
            'Steel Blue Gown',
            'Terracotta Warm Dress',
            'Aquamarine Formal Dress',
            'Ochre Golden Evening Gown',
            'Sienna Orange Formal Dress',
            'Midnight Blue Gown',
            'Claret Wine Evening Dress',
            'Sage Green Formal Dress',
            'Pearlescent White Gown',
            'Ruby Red Glamorous Dress',
            'Charcoal Black Elegant Evening Gown',
        ];

        return $this->generateProducts($names, 'stitched', 8500, 18000, 'chiffon');
    }

    protected function getLuxuryPretProducts(): array
    {
        $names = [
            'Navy Jacquard Two Piece',
            'Cream Silk Two Piece Set',
            'Maroon Brocade Suit',
            'Gold Jacquard Premium Set',
            'Black Velvet Luxury Two Piece',
            'Teal Silk Formal Set',
            'Emerald Green Premium Suit',
            'Burgundy Jacquard Two Piece',
            'Rose Gold Glamorous Set',
            'Sapphire Blue Silk Two Piece',
            'Deep Purple Royal Suit',
            'Chocolate Brown Velvet Set',
            'Silver Metallic Premium Two Piece',
            'Coral Pink Luxury Suit',
            'Olive Green Silk Set',
            'Plum Velvet Formal Two Piece',
            'Bronze Gold Jacquard Suit',
            'Blush Pink Premium Set',
            'Forest Green Silk Luxury Suit',
            'Charcoal Grey Premium Two Piece',
            'Mauve Brocade Formal Set',
            'Indigo Traditional Silk Suit',
            'Rust Orange Warm Two Piece',
            'Eggplant Purple Royal Set',
            'Slate Blue Professional Suit',
            'Taupe Elegant Premium Two Piece',
            'Magenta Silk Formal Set',
            'Garnet Red Luxury Two Piece',
            'Pewter Silver Glamorous Suit',
            'Caramel Gold Elegant Set',
            'Violet Royal Formal Two Piece',
            'Espresso Brown Premium Suit',
            'Turquoise Silk Luxury Set',
            'Sienna Orange Warm Two Piece',
            'Cream Silk Premium Formal Suit',
            'Copper Metallic Jacquard Set',
            'Dusty Rose Vintage Luxury Two Piece',
            'Terracotta Warm Premium Suit',
            'Aquamarine Silk Formal Set',
            'Ochre Golden Elegant Two Piece',
            'Seafoam Green Tropical Premium Suit',
            'Stone Grey Minimalist Set',
            'Saffron Yellow Festival Luxury Two Piece',
            'Periwinkle Blue Soft Formal Suit',
            'Rust Brown Heritage Premium Set',
        ];

        return $this->generateProducts($names, 'stitched', 7500, 16000, 'silk');
    }

    protected function generateProducts(array $names, string $type, int $minPrice, int $maxPrice, string $fabric): array
    {
        $descriptions = [
            'Premium crafted piece perfect for everyday elegance and special occasions.',
            'Exquisitely designed with attention to every detail and quality materials.',
            'A must-have addition to your wardrobe for style and comfort combined.',
            'Artfully created to bring out your natural beauty and confidence.',
            'Beautifully tailored for a perfect fit and timeless appeal.',
            'Carefully selected fabric and intricate designs for a luxurious feel.',
            'Modern design meets traditional craftsmanship in this stunning piece.',
            'Versatile and elegant, perfect for any occasion and season.',
            'Impeccably finished with premium quality and attention to detail.',
            'A sophisticated choice for those who appreciate fine fashion.',
        ];

        return collect($names)->map(function ($name, $index) use ($type, $minPrice, $maxPrice, $fabric, $descriptions) {
            $basePrice = rand($minPrice, $maxPrice);
            $discount = rand(0, 3) * 500; // 0%, 500, 1000, 1500 discount

            return [
                'name' => $name,
                'type' => $type,
                'base_price' => $basePrice,
                'sale_price' => $discount > 0 ? $basePrice - $discount : null,
                'deal_enabled' => rand(0, 1) === 1,
                'status' => rand(0, 4) > 0 ? 'published' : 'draft', // 80% published, 20% draft
                'short_description' => 'Beautiful ' . $name . ' in premium ' . $fabric . ' fabric.',
                'description' => $descriptions[array_rand($descriptions)] . ' Made from finest ' . $fabric . '.',
            ];
        })->toArray();
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->cleanTable('brands', ['name']);
        $this->cleanTable('sellers', ['short_description']);
        $this->cleanTable('categories', ['name', 'description']);
        $this->cleanTable('products', ['name', 'short_description', 'description', 'pros', 'cons']);
        $this->cleanTable('order_items', ['product_name']);
        $this->cleanTable('images', ['path']);
    }

    public function down(): void
    {
        // Content cleanup is intentionally permanent.
    }

    private function cleanTable(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $columns = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn($table, $column)
        ));

        if ($columns === []) {
            return;
        }

        DB::table($table)
            ->select(array_merge(['id'], $columns))
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($table, $columns): void {
                foreach ($rows as $row) {
                    $updates = [];

                    foreach ($columns as $column) {
                        $value = $row->{$column};

                        if (! is_string($value) || stripos($value, 'eyewear') === false) {
                            continue;
                        }

                        $updates[$column] = $this->cleanText($value);
                    }

                    if ($updates !== []) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                }
            }, 'id');
    }

    private function cleanText(string $value): string
    {
        return str_ireplace(
            [
                'Annie Eyewear',
                'rayban-premium-eyewear-frame.png',
                'tomford-men-eyewear.png',
                'eyewear-cleaning-kit.png',
                'Premium Eyewear Frame',
                'Men Eyewear',
                'Complete Eyewear Cleaning',
                'eyewear accessories',
                'eyewear care',
                'eyewear category',
                'casual eyewear',
                'Affiliate eyewear pick',
                'eyewear deal',
                'eyewear options',
                'eyewear',
            ],
            [
                'Annie Finds',
                'rayban-premium-frame.png',
                'tomford-men-frame.png',
                'glasses-cleaning-kit.png',
                'Premium Frame',
                'Men Frame',
                'Complete Glasses Cleaning',
                'vision accessories',
                'glasses care',
                'product category',
                'casual wear',
                'Affiliate product pick',
                'product deal',
                'product options',
                'product',
            ],
            $value
        );
    }
};

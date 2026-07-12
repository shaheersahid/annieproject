<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('products')
            ->whereNotIn('product_type', ['frame', 'lens', 'accessory', 'service'])
            ->update(['product_type' => 'frame']);

        DB::statement("ALTER TABLE products MODIFY product_type ENUM('frame', 'lens', 'accessory', 'service') NOT NULL DEFAULT 'frame'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('products')
            ->whereNotIn('product_type', ['physical', 'digital'])
            ->update(['product_type' => 'physical']);

        DB::statement("ALTER TABLE products MODIFY product_type ENUM('physical', 'digital') NOT NULL DEFAULT 'physical'");
    }
};

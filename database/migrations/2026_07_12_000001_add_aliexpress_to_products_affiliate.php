<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'aliexpress_url')) {
                $table->text('aliexpress_url')->nullable()->after('temu_url');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::table('products')
                ->whereNotIn('affiliate_platform', ['none', 'amazon', 'temu', 'aliexpress', 'both', 'all'])
                ->update(['affiliate_platform' => 'none']);

            DB::statement("ALTER TABLE products MODIFY affiliate_platform VARCHAR(20) NOT NULL DEFAULT 'none'");
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'aliexpress_url')) {
                $table->dropColumn('aliexpress_url');
            }
        });
    }
};

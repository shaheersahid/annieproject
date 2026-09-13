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
            if (! Schema::hasColumn('products', 'featured_sort_order')) {
                $table->unsignedInteger('featured_sort_order')->default(0)->after('is_featured');
            }
        });

        $order = 1;
        DB::table('products')
            ->where('is_featured', true)
            ->orderByDesc('updated_at')
            ->orderBy('id')
            ->pluck('id')
            ->each(function ($productId) use (&$order): void {
                DB::table('products')->where('id', $productId)->update([
                    'featured_sort_order' => $order++,
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'featured_sort_order')) {
                $table->dropColumn('featured_sort_order');
            }
        });
    }
};

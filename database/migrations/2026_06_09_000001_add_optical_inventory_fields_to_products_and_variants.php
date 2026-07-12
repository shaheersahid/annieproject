<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->unsignedInteger('low_stock_threshold')->default(5)->after('stock');
            }

            if (! Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('description');
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('product_variants', 'low_stock_threshold')) {
                $table->unsignedInteger('low_stock_threshold')->default(5)->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'low_stock_threshold')) {
                $table->dropColumn('low_stock_threshold');
            }

            if (Schema::hasColumn('product_variants', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('products', 'low_stock_threshold')) {
                $columns[] = 'low_stock_threshold';
            }

            if (Schema::hasColumn('products', 'specifications')) {
                $columns[] = 'specifications';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};

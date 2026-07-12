<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_tags')) {
            return;
        }

        Schema::table('product_tags', function (Blueprint $table) {
            if (! Schema::hasColumn('product_tags', 'type')) {
                $table->enum('type', ['product', 'category', 'brand', 'collection'])->default('product')->after('slug');
            }

            if (! Schema::hasColumn('product_tags', 'option')) {
                $table->enum('option', ['featured', 'trending', 'new_arrival', 'on_sale', 'bestseller'])->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_tags')) {
            return;
        }

        Schema::table('product_tags', function (Blueprint $table) {
            if (Schema::hasColumn('product_tags', 'option')) {
                $table->dropColumn('option');
            }

            if (Schema::hasColumn('product_tags', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};

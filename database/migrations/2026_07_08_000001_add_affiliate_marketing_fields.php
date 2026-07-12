<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'affiliate_platform')) {
                $table->string('affiliate_platform', 20)->default('none')->after('product_type');
            }

            if (! Schema::hasColumn('products', 'amazon_url')) {
                $table->text('amazon_url')->nullable()->after('affiliate_platform');
            }

            if (! Schema::hasColumn('products', 'temu_url')) {
                $table->text('temu_url')->nullable()->after('amazon_url');
            }

            if (! Schema::hasColumn('products', 'external_product_id')) {
                $table->string('external_product_id')->nullable()->after('temu_url');
            }

            if (! Schema::hasColumn('products', 'price_note')) {
                $table->string('price_note')->nullable()->after('sale_price');
            }

            if (! Schema::hasColumn('products', 'affiliate_rating')) {
                $table->decimal('affiliate_rating', 3, 2)->nullable()->after('price_note');
            }

            if (! Schema::hasColumn('products', 'pros')) {
                $table->json('pros')->nullable()->after('specifications');
            }

            if (! Schema::hasColumn('products', 'cons')) {
                $table->json('cons')->nullable()->after('pros');
            }

            if (! Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }

            if (! Schema::hasColumn('products', 'click_count')) {
                $table->unsignedBigInteger('click_count')->default(0)->after('is_featured');
            }
        });

        if (! Schema::hasTable('affiliate_clicks')) {
            Schema::create('affiliate_clicks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('platform', 20);
                $table->string('ip_hash', 64)->nullable();
                $table->text('user_agent')->nullable();
                $table->text('referrer')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'platform']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');

        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'affiliate_platform',
                'amazon_url',
                'temu_url',
                'external_product_id',
                'price_note',
                'affiliate_rating',
                'pros',
                'cons',
                'is_featured',
                'click_count',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

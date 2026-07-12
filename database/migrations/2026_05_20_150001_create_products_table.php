<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('sellers')->nullOnDelete();
            $table->foreignId('size_chart_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->enum('product_type', ['frame', 'lens', 'accessory', 'service'])->default('frame');
            $table->boolean('has_variants')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('review_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->boolean('deal_enabled')->default(false);
            $table->enum('deal_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('deal_value', 12, 2)->nullable();
            $table->dateTime('deal_start_at')->nullable();
            $table->dateTime('deal_end_at')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->unsignedInteger('sold_out')->default(0);
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('specifications')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'review_status']);
            $table->index(['brand_id', 'seller_id']);
        });

        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['category_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('products');
    }
};

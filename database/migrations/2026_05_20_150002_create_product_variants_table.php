<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('product_variants');

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->unique();
            $table->json('attributes')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->boolean('deal_enabled')->default(false);
            $table->enum('deal_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('deal_value', 12, 2)->nullable();
            $table->dateTime('deal_start_at')->nullable();
            $table->dateTime('deal_end_at')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->unsignedInteger('sold_out')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('image_path')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('combination_hash')->nullable()->unique();
            $table->timestamps();
            $table->index(['product_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};

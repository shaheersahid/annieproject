<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('value')->nullable();
            $table->enum('input_type', ['dropdown', 'radio', 'checkbox', 'color_switch', 'textinput'])->default('dropdown');
            $table->text('short_description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('category_product_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['category_id', 'product_attribute_id'], 'category_product_attribute_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product_attribute');
        Schema::dropIfExists('product_attributes');
    }
};

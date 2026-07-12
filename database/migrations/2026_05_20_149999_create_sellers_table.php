<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('store_logo')->nullable();
            $table->string('cover_photo')->nullable();
            $table->string('username')->unique();
            $table->string('store_name');
            $table->string('owner_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('country')->default('Pakistan');
            $table->string('zip_code')->nullable();
            $table->string('location')->nullable();
            $table->text('short_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};

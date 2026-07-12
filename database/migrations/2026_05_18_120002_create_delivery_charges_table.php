<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_charges', function (Blueprint $table) {
            $table->id();
            $table->string('from_city');
            $table->string('to_city');
            $table->decimal('charge', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['from_city', 'to_city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_charges');
    }
};

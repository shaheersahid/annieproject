<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('abandoned_carts');

        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->json('items')->nullable();
            $table->decimal('cart_total', 12, 2)->default(0);
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->timestamps();
            $table->index(['abandoned_at', 'recovered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};


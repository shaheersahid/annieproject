<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('inventory_snapshots');

        Schema::create('inventory_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('sold_out')->default(0);
            $table->date('snapshot_date');
            $table->timestamps();
            $table->unique(['product_id', 'variant_id', 'snapshot_date'], 'inv_snapshots_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_snapshots');
    }
};


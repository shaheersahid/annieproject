<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_draft')->default(false)->after('status');
            $table->boolean('is_active')->default(true)->after('is_draft');
            $table->boolean('out_of_stock')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_draft', 'is_active', 'out_of_stock']);
        });
    }
};

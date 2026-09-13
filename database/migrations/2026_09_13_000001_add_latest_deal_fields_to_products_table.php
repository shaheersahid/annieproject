<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_latest')) {
                $table->boolean('is_latest')->default(false)->after('is_featured');
            }

            if (! Schema::hasColumn('products', 'latest_sort_order')) {
                $table->unsignedInteger('latest_sort_order')->default(0)->after('is_latest');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'latest_sort_order')) {
                $table->dropColumn('latest_sort_order');
            }

            if (Schema::hasColumn('products', 'is_latest')) {
                $table->dropColumn('is_latest');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_reel')) {
                $table->boolean('is_reel')->default(false)->after('is_latest');
            }

            if (! Schema::hasColumn('products', 'tiktok_url')) {
                $table->text('tiktok_url')->nullable()->after('aliexpress_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'tiktok_url')) {
                $table->dropColumn('tiktok_url');
            }

            if (Schema::hasColumn('products', 'is_reel')) {
                $table->dropColumn('is_reel');
            }
        });
    }
};

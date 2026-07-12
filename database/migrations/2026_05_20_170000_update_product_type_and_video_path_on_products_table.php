<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('products')->whereNotIn('product_type', ['frame', 'lens', 'accessory', 'service'])->update(['product_type' => 'frame']);
            DB::statement("ALTER TABLE products MODIFY product_type ENUM('frame', 'lens', 'accessory', 'service') NOT NULL DEFAULT 'frame'");
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'video_path')) {
                $table->string('video_path')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'video_path')) {
                $table->dropColumn('video_path');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::table('products')->whereNotIn('product_type', ['frame', 'lens', 'accessory', 'service'])->update(['product_type' => 'frame']);
            DB::statement("ALTER TABLE products MODIFY product_type ENUM('frame', 'lens', 'accessory', 'service') NOT NULL DEFAULT 'frame'");
        }
    }
};

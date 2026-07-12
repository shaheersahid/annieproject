<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['products', 'orders'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'seller_id')) {
                continue;
            }

            try {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['seller_id']);
                });
            } catch (Throwable) {
                // Some local databases may not have the old users foreign key.
            }

            DB::table($tableName)
                ->whereNotNull('seller_id')
                ->whereNotExists(function ($query) use ($tableName) {
                    $query->selectRaw('1')
                        ->from('sellers')
                        ->whereColumn('sellers.id', "{$tableName}.seller_id");
                })
                ->update(['seller_id' => null]);

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('seller_id')->references('id')->on('sellers')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['products', 'orders'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'seller_id')) {
                continue;
            }

            try {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['seller_id']);
                });
            } catch (Throwable) {
            }

            DB::table($tableName)->update(['seller_id' => null]);

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('seller_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('menus', 'stok')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->integer('stok')->default(0)->after('kategori');
            });
        }

        if (Schema::hasColumn('menus', 'tersedia')) {
            DB::table('menus')->where('tersedia', 1)->update(['stok' => 1]);
            DB::table('menus')->where(function ($query) {
                $query->where('tersedia', 0)->orWhereNull('tersedia');
            })->update(['stok' => 0]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('menus', 'stok')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('stok');
            });
        }
    }
};
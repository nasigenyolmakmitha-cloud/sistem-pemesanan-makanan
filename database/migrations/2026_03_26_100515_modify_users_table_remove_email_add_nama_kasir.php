<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Drop kolom yang tidak dipakai
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email', 'email_verified_at', 'remember_token']);
        });

        // Step 2: Rename name → nama_kasir
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'nama_kasir');
        });

        // Step 3: Ubah tipe kolom
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_kasir', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_kasir', 255)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nama_kasir', 'name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->rememberToken()->after('password');
        });
    }
};

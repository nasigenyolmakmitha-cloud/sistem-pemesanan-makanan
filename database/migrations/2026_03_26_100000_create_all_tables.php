<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_meja', 20);
            $table->string('qr_token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('sesi_pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meja_id')->constrained('mejas')->onDelete('cascade');
            $table->string('kode_sesi', 64)->unique();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamp('dibuka_pada');
            $table->timestamp('ditutup_pada')->nullable();
            $table->timestamps();
        });

        Schema::create('pemesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi_pemesanans')->onDelete('cascade');
            $table->string('nama', 100);
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 10, 2);
            $table->string('foto', 255)->nullable();
            $table->string('kategori', 50);
            $table->integer('stok')->default(0);
            $table->timestamps();
        });

        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesan_id')->constrained('pemesans')->onDelete('cascade');
            $table->foreignId('sesi_id')->constrained('sesi_pemesanans')->onDelete('cascade');
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibayar'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->onDelete('cascade');
            $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('set null');
            $table->integer('jumlah');
            $table->decimal('harga_saat_pesan', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanans');
        Schema::dropIfExists('pesanans');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('pemesans');
        Schema::dropIfExists('sesi_pemesanans');
        Schema::dropIfExists('mejas');
    }
};

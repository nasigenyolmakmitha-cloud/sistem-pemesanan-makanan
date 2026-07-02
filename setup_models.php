<?php

$models = [
    'Meja' => 'protected $fillable = ["nomor_meja", "qr_token"];',
    'SesiPemesanan' => 'protected $fillable = ["meja_id", "kode_sesi", "status", "dibuka_pada", "ditutup_pada"];',
    'Pemesan' => 'protected $fillable = ["sesi_id", "nama"];'. "\n    public function pesanan() { return \$this->hasMany(Pesanan::class); }",
    'Menu' => 'protected $fillable = ["nama", "deskripsi", "harga", "foto", "kategori", "stok"];',
    'Pesanan' => 'protected $fillable = ["pemesan_id", "sesi_id", "status", "catatan"];',
    'DetailPesanan' => 'protected $fillable = ["pesanan_id", "menu_id", "jumlah", "harga_saat_pesan", "subtotal"];'
];

foreach ($models as $name => $content) {
    $path = __DIR__ . "/app/Models/$name.php";
    $code = <<<EOT
<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
class $name extends Model
{
    use HasFactory;
    $content
}
EOT;
    file_put_contents($path, $code);
    echo "Updated $name\n";
}

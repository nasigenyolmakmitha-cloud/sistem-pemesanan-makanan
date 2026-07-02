<?php

$commands = [
    'php artisan make:model Meja -m',
    'php artisan make:model SesiPemesanan -m',
    'php artisan make:model Pemesan -m',
    'php artisan make:model Menu -m',
    'php artisan make:model Pesanan -m',
    'php artisan make:model DetailPesanan -m',
    'php artisan make:controller Pelanggan/PemesananController',
    'php artisan make:controller Pelanggan/KeranjangController',
    'php artisan make:controller Admin/DashboardController',
    'php artisan make:controller Admin/SesiController',
    'php artisan make:controller Admin/PesananController',
    'php artisan make:controller Admin/MenuController',
    'php artisan make:controller Admin/MejaController',
    'php artisan make:controller Admin/RiwayatController',
    'composer require simplesoftwareio/simple-qrcode'
];

foreach($commands as $cmd) {
    echo "Running: $cmd\n";
    system($cmd);
}

<?php

// Pastikan direktori ada
$dirs = [
    __DIR__.'/resources/views/layouts',
    __DIR__.'/resources/views/admin/auth',
    __DIR__.'/resources/views/admin/sesi',
    __DIR__.'/resources/views/admin/menu',
    __DIR__.'/resources/views/admin/meja',
    __DIR__.'/resources/views/admin/riwayat',
    __DIR__.'/resources/views/pelanggan',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Layout Utama
$layout = <<<'EOT'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Nasi Be Genyol') }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-bg { background: linear-gradient(135deg, #FF9800 0%, #F44336 100%); color: white; border-radius: 15px; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 12px; }
        .btn-primary { background-color: #F44336; border-color: #F44336; }
        .btn-primary:hover { background-color: #D32F2F; border-color: #D32F2F; }
    </style>
</head>
<body>
    @if(Request::is('admin*') && !Request::is('admin/login'))
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">🥘 Be Genyol Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Manajemen Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Manajemen Meja</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Riwayat</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    @endif

    <div class="container mt-4 mb-5">
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
EOT;
file_put_contents(__DIR__.'/resources/views/layouts/app.blade.php', $layout);

// Login Admin
$login = <<<'EOT'
@extends('layouts.app')
@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-5">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-danger">🍳 Nasi Be Genyol</h3>
                <p class="text-muted">Masuk ke Panel Kasir</p>
            </div>
            <form action="/admin/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@nasibegenyo.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="****" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Masuk Sistem</button>
            </form>
        </div>
    </div>
</div>
@endsection
EOT;
file_put_contents(__DIR__.'/resources/views/admin/auth/login.blade.php', $login);

// Dashboard Admin
$dashboard = <<<'EOT'
@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Dashboard Pesanan</h2>
    <span class="badge bg-success p-2"><i class="bi bi-circle-fill text-white spinner-grow spinner-grow-sm"></i> Real-time Active</span>
</div>

<div class="row">
    @forelse($sesiAktif as $sesi)
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title fw-bold">{{ $sesi->meja->nomor_meja }}</h4>
                    <span class="badge bg-danger rounded-pill">{{ $sesi->pemesan->count() }} Orang</span>
                </div>
                <p class="text-muted small mb-3">Dibuka: {{ $sesi->dibuka_pada }}</p>
                <a href="/admin/sesi/{{ $sesi->id }}" class="btn btn-outline-danger w-100">Lihat Detail Pasukan</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-cup-straw text-muted" style="font-size: 3rem;"></i>
        <h4 class="text-muted mt-3">Belum ada meja yang aktif saat ini</h4>
    </div>
    @endforelse
</div>
@endsection
EOT;
file_put_contents(__DIR__.'/resources/views/admin/dashboard.blade.php', $dashboard);

// Pelanggan Scan QR - Input Nama
$inputNama = <<<'EOT'
@extends('layouts.app')
@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="card hero-bg p-5 text-center mb-4">
            <h1 class="fw-bold mb-0">Selamat Datang!</h1>
            <p class="mb-0 fs-5">di Nasi Be Genyol Mak Mitha</p>
        </div>
        
        <div class="card p-4">
            <div class="text-center mb-4">
                <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill shadow">📍 Anda di {{ $meja->nomor_meja }}</span>
            </div>
            <form action="/pesan/{{ $meja->qr_token }}/nama" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold">Siapa nama panggilan Anda?</label>
                    <input type="text" name="nama" class="form-control form-control-lg text-center" placeholder="Ketik nama Anda di sini..." required autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">Lihat Menu <i class="bi bi-arrow-right"></i></button>
            </form>
        </div>
    </div>
</div>
@endsection
EOT;
file_put_contents(__DIR__.'/resources/views/pelanggan/input-nama.blade.php', $inputNama);

// Pelanggan Menu
$menu = <<<'EOT'
@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Hi, {{ session('nama_pemesan') }} 👋</h3>
        <span class="text-muted">📍 {{ $meja->nomor_meja }}</span>
    </div>
    <a href="#" class="btn btn-warning position-relative px-3 rounded-pill shadow-sm fw-bold">
        <i class="bi bi-cart-fill"></i> Keranjang
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            0
        </span>
    </a>
</div>

<div class="row">
    @foreach($menus as $m)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 d-flex flex-row overflow-hidden">
            <div class="bg-secondary" style="width: 120px; min-height: 120px;">
                <!-- Image placeholder, will use random food icon -->
                <div class="h-100 w-100 d-flex align-items-center justify-content-center bg-light text-muted">
                    <i class="bi bi-image" style="font-size: 2rem;"></i>
                </div>
            </div>
            <div class="card-body py-2 px-3 d-flex flex-column justify-content-center">
                <h6 class="fw-bold mb-1">{{ $m->nama }}</h6>
                <p class="text-muted small mb-2" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $m->deskripsi }}</p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="fw-bold text-danger">Rp {{ number_format($m->harga, 0, ',', '.') }}</span>
                    @if($m->stok > 0)
                        <button class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;"><i class="bi bi-plus"></i></button>
                    @else
                        <span class="badge bg-secondary">Habis</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
EOT;
file_put_contents(__DIR__.'/resources/views/pelanggan/menu.blade.php', $menu);

echo "Views generated successfully.\n";

EOT;

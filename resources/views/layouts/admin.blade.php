<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin — {{ config('app.name', 'Nasi Be Genyol') }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Pro -->
    <link href="https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css" rel="stylesheet">
    
    <!-- Ionic Framework CDN (Bypass Vite chunk loading issues) -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ionic/core/css/ionic.bundle.css" />

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <style>
        :root {
            --ion-font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            --ion-color-primary: #FF5722;
            --ion-color-primary-rgb: 255,87,34;
            --ion-color-primary-contrast: #ffffff;
            --ion-color-primary-shade: #E64A19;
            --ion-color-primary-tint: #FF7043;
            --ion-color-success: #4CAF50;
            --ion-color-warning: #FFC107;
            --ion-color-danger: #F44336;
            --ion-background-color: #F4F6F9;
            --ion-toolbar-background: #ffffff;
            --orange: #FF5722;
            --orange-light: #FFF3E0;
            --orange-dark: #E64A19;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            background: #F4F6F9;
        }

        /* Sidebar Menu Styling */
        ion-menu ion-content {
            --background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%);
        }

        .menu-header {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .menu-header .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-header .brand-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #FF5722, #FF7043);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
            box-shadow: 0 4px 15px rgba(255,87,34,0.3);
        }

        .menu-header .brand-text h3 {
            margin: 0;
            color: #fff;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .menu-header .brand-text span {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .menu-user-card {
            margin: 1.2rem 1rem;
            padding: 1rem;
            background: rgba(255,255,255,0.06);
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.08);
        }

        .menu-user-card .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-user-card .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .menu-user-card .user-name {
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .menu-user-card .user-role {
            color: rgba(255,255,255,0.45);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Nav Items */
        .nav-section-title {
            padding: 1.2rem 1.5rem 0.5rem;
            color: rgba(255,255,255,0.3);
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        ion-menu ion-item {
            --background: transparent;
            --color: rgba(255,255,255,0.65);
            --padding-start: 1.2rem;
            --padding-end: 1rem;
            --min-height: 48px;
            margin: 2px 0.7rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        ion-menu ion-item:hover {
            --background: rgba(255,255,255,0.06);
            --color: #fff;
        }

        ion-menu ion-item.active-nav {
            --background: linear-gradient(135deg, rgba(255,87,34,0.15), rgba(255,112,67,0.1));
            --color: #FF7043;
            border: 1px solid rgba(255,87,34,0.2);
        }

        ion-menu ion-item.active-nav ion-icon {
            color: #FF7043;
        }

        ion-menu ion-item ion-icon {
            color: rgba(255,255,255,0.4);
            font-size: 1.2rem;
            margin-right: 12px;
        }

        .menu-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin-top: auto; /* Mendorong ke bawah jika menu sedikit, tetap ikut scroll jika menu banyak */
            flex-shrink: 0; /* Mencegah kontainer mengecil atau melar tidak beraturan */
        }

        .logout-btn {
            width: 100%;
            height: 46px !important; /* Mengunci tinggi tombol secara mutlak agar tidak melar ke bawah */
            --height: 46px !important; /* Mengunci tinggi internal komponen Ionic */
            --background: rgba(244, 67, 54, 0.12) !important; /* Warna merah gelap transparan sesuai gambar */
            --background-hover: rgba(244, 67, 54, 0.2) !important;
            --color: #EF5350 !important; /* Warna teks & ikon merah cerah sesuai gambar */
            --border-radius: 12px;
            --box-shadow: none;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            margin: 0;
        }
        /* Main Content Area */
        ion-header ion-toolbar {
            --background: #ffffff;
            --border-color: rgba(0,0,0,0.06);
            --padding-start: 8px;
        }

        .page-content {
            padding: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Card Styling */
        ion-card {
            border-radius: 16px !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important;
            margin: 0 0 1rem 0;
            border: 1px solid rgba(0,0,0,0.04);
        }

        ion-card-header {
            padding: 1.2rem 1.5rem 0.5rem;
        }

        ion-card-content {
            padding: 0.5rem 1.5rem 1.5rem;
        }

        /* Stats Card */
        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card .stat-icon {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 5rem;
            opacity: 0.06;
        }

        /* Forms */
        ion-input, ion-textarea, ion-select {
            --border-radius: 12px;
            --padding-start: 16px;
        }

        /* Toggle */
        ion-toggle {
            --track-background: #e0e0e0;
            --track-background-checked: var(--orange);
            --handle-background-checked: #fff;
        }

        /* Badge */
        ion-badge {
            border-radius: 8px;
            padding: 4px 10px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .anim-fade-in {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .page-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <ion-app>
        <ion-split-pane content-id="main-content" when="lg">
            <!-- Sidebar Menu -->
            <ion-menu content-id="main-content" side="start">
                <ion-content>
                    <!-- Brand Header -->
                    <div class="menu-header">
                        <div class="brand">
                            <div class="brand-icon">
                                <i class="fad fa-utensils-alt"></i>
                            </div>
                            <div class="brand-text">
                                <h3>NBG Mak Mitha</h3>
                                <span>Admin Panel</span>
                            </div>
                        </div>
                    </div>

                    <!-- User Card -->
                    <div class="menu-user-card">
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->username ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <div class="user-name">{{ Auth::user()->username ?? 'Admin' }}</div>
                                <div class="user-role">Administrator</div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="nav-section-title">Menu Utama</div>
                    
                    <ion-item button href="/admin/dashboard" class="{{ Request::is('admin/dashboard') ? 'active-nav' : '' }}" lines="none">
                        <ion-icon name="stats-chart-outline" slot="start"></ion-icon>
                        <ion-label>Dashboard</ion-label>
                    </ion-item>

                    <ion-item button href="/admin/meja" class="{{ Request::is('admin/meja*') ? 'active-nav' : '' }}" lines="none">
                        <ion-icon name="grid-outline" slot="start"></ion-icon>
                        <ion-label>Manajemen Meja</ion-label>
                    </ion-item>

                    <ion-item button href="/admin/menu" class="{{ Request::is('admin/menu*') ? 'active-nav' : '' }}" lines="none">
                        <ion-icon name="restaurant-outline" slot="start"></ion-icon>
                        <ion-label>Manajemen Menu</ion-label>
                    </ion-item>

                    <div class="nav-section-title">Laporan</div>

                    <ion-item button href="/admin/pendapatan" class="{{ Request::is('admin/pendapatan*') ? 'active-nav' : '' }}" lines="none">
                        <ion-icon name="wallet-outline" slot="start"></ion-icon>
                        <ion-label>Laporan Pendapatan</ion-label>
                    </ion-item>

                    <!-- Logout -->
                    </ion-content>
                    <ion-footer class="ion-no-border" style="background: #16213E;">
                    <div class="menu-footer">
                        <form action="/admin/logout" method="POST" id="admin-logout-form">
                            @csrf
                            <div style="display: block;">
                                <ion-button expand="block" class="logout-btn" type="button" onclick="confirmLogout()">
                                    <ion-icon name="log-out-outline" style="margin-right: 8px; font-size: 1.2rem;"></ion-icon>
                                    KELUAR
                                </ion-button>
                            </div>
                        </form>
                    </div>
                </ion-footer>
            </ion-menu>

            <!-- Main Content -->
            <div class="ion-page" id="main-content">
                <ion-header>
                    <ion-toolbar>
                        <ion-buttons slot="start">
                            <ion-menu-button color="primary"></ion-menu-button>
                        </ion-buttons>
                        <ion-title style="font-weight: 700; font-size: 1.1rem;">
                            @yield('page-title', 'Dashboard')
                        </ion-title>
                    </ion-toolbar>
                </ion-header>

                <ion-content>
                    <div class="page-content">
                        @yield('content')
                    </div>
                </ion-content>
            </div>
        </ion-split-pane>
    </ion-app>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (typeof Swal !== 'undefined') {
            window.Swal = Swal.mixin({
                heightAuto: false
            });
        }

        function confirmLogout() {
            Swal.fire({
                title: 'Apakah Anda Yakin Ingin keluar Dari Sistem?',
                text: 'Anda harus login kembali untuk mengakses sistem.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF5722',
                cancelButtonColor: '#999',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Check which form exists and submit
                    if (document.getElementById('logout-form')) {
                        document.getElementById('logout-form').submit();
                    } else if (document.getElementById('admin-logout-form')) {
                        document.getElementById('admin-logout-form').submit();
                    }
                }
            });
        }
    </script>
    <style>
        /* Fix SweetAlert2 di atas Ionic Framework */
        html.swal2-height-auto, body.swal2-height-auto {
            height: 100% !important;
        }
        .swal2-container {
            z-index: 99999 !important;
        }
        .swal2-popup {
            z-index: 99999 !important;
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
        }
        .swal2-backdrop-show {
            z-index: 99998 !important;
        }
        /* Hapus kotak putih artifact dari Ionic overlay */
        body.swal2-shown > ion-app {
            filter: none !important;
        }
        .swal2-container.swal2-backdrop-show {
            background: rgba(0,0,0,0.4) !important;
        }
    </style>
    @yield('scripts')
</body>
</html>

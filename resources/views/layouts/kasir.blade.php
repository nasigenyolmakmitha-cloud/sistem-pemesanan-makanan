<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kasir — {{ config('app.name', 'Nasi Be Genyol') }}</title>
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

        /* Top Toolbar */
        ion-header ion-toolbar {
            --background: #ffffff;
            --border-color: rgba(0,0,0,0.06);
        }

        .toolbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toolbar-brand .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #FF5722, #FF7043);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: white;
        }

        .toolbar-brand h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            color: #1A1A2E;
        }

        .toolbar-brand span {
            font-size: 0.7rem;
            color: #999;
            font-weight: 600;
        }

        /* Bottom Tab Bar */
        .bottom-tabs {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid rgba(0,0,0,0.06);
            z-index: 1000;
            padding-bottom: env(safe-area-inset-bottom, 0px);
            box-shadow: 0 -2px 20px rgba(0,0,0,0.06);
        }

        .tab-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 0 10px;
            text-decoration: none;
            color: #999;
            font-size: 0.65rem;
            font-weight: 700;
            transition: all 0.2s ease;
            position: relative;
        }

        .tab-item.active {
            color: var(--orange);
        }

        .tab-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 3px;
            background: var(--orange);
            border-radius: 0 0 4px 4px;
        }

        .tab-item i {
            font-size: 1.3rem;
            margin-bottom: 3px;
            transition: transform 0.2s ease;
        }

        .tab-item.active i {
            transform: scale(1.1);
        }

        .tab-item .tab-badge {
            position: absolute;
            top: 4px;
            right: calc(50% - 18px);
            background: #F44336;
            color: white;
            font-size: 0.6rem;
            font-weight: 800;
            min-width: 16px;
            height: 16px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }

        /* Page Content */
        .page-content {
            padding: 1rem;
            padding-bottom: 90px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Card Styling */
        ion-card {
            border-radius: 16px !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important;
            margin: 0 0 1rem 0;
            border: 1px solid rgba(0,0,0,0.04);
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-menunggu { background: #FFF3E0; color: #F57C00; }
        .status-diproses { background: #E3F2FD; color: #1976D2; }
        .status-selesai { background: #E8F5E9; color: #388E3C; }
        .status-dibayar { background: #F3E5F5; color: #7B1FA2; }

        /* Toggle */
        ion-toggle {
            --track-background: #e0e0e0;
            --track-background-checked: var(--orange);
            --handle-background-checked: #fff;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.15); }
            100% { opacity: 1; transform: scale(1); }
        }

        .anim-fade-in {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        .flash {
            animation: pulse 2s infinite;
        }

        /* Responsive table */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 10px 14px; text-align: left; font-size: 0.85rem; }
        table thead th { color: #999; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f0f0f0; }
        table tbody td { border-bottom: 1px solid #f5f5f5; }
        table tbody tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <ion-app>
        <!-- Top Header -->
        <ion-header>
            <ion-toolbar>
                <div class="toolbar-brand" slot="start" style="padding-left: 12px;">
                    <div class="brand-icon">
                        <i class="fad fa-utensils-alt"></i>
                    </div>
                    <div>
                        <div class="user-info">
                            <h3>NBG Mak Mitha</h3>
                            <span>Panel Kasir — {{ Auth::user()->username ?? 'Kasir' }}</span>
                        </div>
                    </div>
                </div>
                <ion-buttons slot="end" style="padding-right: 8px;">
                    <form action="/admin/logout" method="POST" style="margin:0;" id="logout-form">
                        @csrf
                        <ion-button type="button" onclick="confirmLogout()" fill="clear" color="danger" style="font-weight:700; font-size:0.8rem;">
                            <ion-icon name="log-out-outline" slot="start"></ion-icon>
                            Keluar
                        </ion-button>
                    </form>
                </ion-buttons>
            </ion-toolbar>
        </ion-header>

        <!-- Main Content -->
        <ion-content>
            <div class="page-content">
                @yield('content')
            </div>
        </ion-content>

        <!-- Bottom Tabs -->
        <div class="bottom-tabs">
            <a href="/kasir/dashboard" class="tab-item {{ Request::is('kasir/dashboard') || Request::is('kasir/sesi*') ? 'active' : '' }}">
                <i class="fad fa-bell-concierge"></i>
                <span>Pesanan</span>
            </a>
            <a href="/kasir/stok" class="tab-item {{ Request::is('kasir/stok*') ? 'active' : '' }}">
                <i class="fad fa-boxes-stacked"></i>
                <span>Stok Menu</span>
            </a>
            <a href="/kasir/riwayat-pemesanan" class="tab-item {{ Request::is('kasir/riwayat-pemesanan') ? 'active' : '' }}">
                <i class="fad fa-history"></i>
                <span>Riwayat</span>
            </a>
        </div>
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
                    document.getElementById('logout-form').submit();
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

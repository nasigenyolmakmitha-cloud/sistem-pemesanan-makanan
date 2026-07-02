<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ config('app.name', 'Nasi Be Genyol') }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Pro -->
    <link href="https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Ionic Framework CDN (Bypass Vite chunk loading issues) -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ionic/core/css/ionic.bundle.css" />
    <style>
        :root {
            --primary-orange: #FF7043;
            --secondary-orange: #FF5722;
            --accent-orange: #FFAB91;
            --dark-bg: #1A1A1A;
            --light-bg: #F8F9FA;
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        
        body { 
            background-color: var(--light-bg); 
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif; 
            color: #2D3436;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 0% 0%, rgba(255, 112, 67, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 87, 34, 0.05) 0px, transparent 50%);
            min-height: 100vh;
            font-size: 16px; 
        }

        @media (max-width: 992px) {
            body { font-size: 17px; }
            .btn { padding: 12px 24px !important; font-size: 1rem !important; }
            h2 { font-size: 1.5rem !important; }
            h3 { font-size: 1.3rem !important; }
            .nav-link { font-size: 1.1rem !important; padding: 10px 16px !important; }
            .form-control { font-size: 1.1rem !important; padding: 0.8rem 1.2rem !important; }
        }

        .hero-bg { 
            background: linear-gradient(135deg, #FF7043 0%, #F4511E 100%); 
            color: white; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(244, 81, 30, 0.2);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .text-orange { color: var(--secondary-orange) !important; }
        .bg-orange { background: var(--secondary-orange) !important; color: white; }
        
        .card { 
            border: 1px solid rgba(0,0,0,0.05); 
            box-shadow: 0 4px 20px rgba(0,0,0,0.04); 
            border-radius: 20px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .btn-primary { 
            background-color: var(--secondary-orange);
            border: none;
            border-radius: 12px;
            font-weight: 700;
            padding: 10px 24px;
            box-shadow: 0 4px 15px rgba(255, 87, 34, 0.2);
            transition: all 0.3s ease;
        }

        .btn-primary:hover { 
            background-color: #E64A19;
            transform: translateY(-2px);
        }

        .navbar {
            background: white !important;
            border-bottom: 2px solid #EEE;
            padding: 0.6rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--secondary-orange) !important;
        }

        .nav-link {
            font-weight: 700;
            font-size: 1.05rem;
            color: #444 !important;
            padding: 0.6rem 1.2rem !important;
            border-radius: 10px;
        }

        .nav-link.active {
            color: var(--secondary-orange) !important;
            background: rgba(255, 87, 34, 0.08);
        }

        .form-control {
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            border: 2px solid #EEE;
            font-size: 1rem;
        }

        .badge-orange {
            background-color: rgba(255, 87, 34, 0.1);
            color: var(--secondary-orange);
            font-weight: 700;
            font-size: 0.9rem;
            border-radius: 8px;
            padding: 6px 12px;
        }

        .text-muted { color: #777 !important; }
        .small, small { font-size: 0.85rem !important; font-weight: 600; }
        
        .text-dark { color: #2D3436 !important; }
        
        /* Fix Tailwind CSS collapse conflict */
        .collapse {
            visibility: visible !important;
        }
    </style>
</head>
<body>
    <ion-app>
        @if(Request::is('admin*') && !Request::is('admin/login'))
        <nav class="navbar navbar-expand-lg mb-4">
            <div class="container-xl">
                <a class="navbar-brand d-flex align-items-center" href="/admin/dashboard">
                    <i class="fad fa-utensils-alt me-2"></i> Be Genyol
                </a>
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="fal fa-bars text-orange fs-2"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center gap-2">
                        <li class="nav-item"><a class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" href="/admin/dashboard"><i class="fal fa-columns me-1"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::is('admin/menu*') ? 'active' : '' }}" href="/admin/menu"><i class="fal fa-burger-soda me-1"></i> Menu</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::is('admin/meja*') ? 'active' : '' }}" href="/admin/meja"><i class="fal fa-table me-1"></i> Meja</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::is('admin/riwayat*') ? 'active' : '' }}" href="/admin/riwayat"><i class="fal fa-history me-1"></i> Riwayat</a></li>
                        <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                            <form action="/admin/logout" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-black py-2"><i class="fal fa-sign-out-alt me-1"></i> Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @endif

        <ion-content>
            <div class="container-xl mt-2 mt-md-4 mb-5 pb-5">
                @yield('content')
            </div>
        </ion-content>
    </ion-app>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (typeof Swal !== 'undefined') {
            window.Swal = Swal.mixin({
                heightAuto: false
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

    @vite(['resources/js/app.js', 'resources/css/app.css'])
    @yield('scripts')
    
    <!-- Area untuk Modal diluar ion-app agar tidak tertutup z-index -->
    @yield('modals')
</body>
</html>
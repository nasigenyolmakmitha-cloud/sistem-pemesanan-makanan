<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login — Nasi Be Genyol</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Pro -->
    <link href="https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css" rel="stylesheet">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <style>
        :root {
            --ion-font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            --ion-color-primary: #FF5722;
        }
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #FF5722 0%, #E64A19 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        .brand-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #FF5722, #FF7043);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #fff;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 20px rgba(255,87,34,0.3);
        }
        h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            color: #2D3436;
        }
        p {
            margin: 0.5rem 0 2rem;
            color: #999;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #2D3436;
            margin-bottom: 0.5rem;
        }
        .input-group {
            display: flex;
            align-items: center;
            background: #f5f5f5;
            border-radius: 12px;
            padding: 0 16px;
            border: 2px solid transparent;
            transition: all 0.3s;
        }
        .input-group:focus-within {
            border-color: #FF5722;
            background: #fff;
        }
        .input-group i {
            color: #999;
            font-size: 1.1rem;
        }
        .input-group input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 14px;
            font-size: 1rem;
            font-family: inherit;
            outline: none;
            color: #2D3436;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #FF5722;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255,87,34,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-submit:hover {
            background: #E64A19;
            transform: translateY(-2px);
        }
        .error-msg {
            background: #FFEBEE;
            color: #D32F2F;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
        }
        .footer-note {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #999;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-icon">
        <i class="fad fa-store"></i>
    </div>
    <h2><span style="color:#FF5722;">Be</span>Genyol</h2>
    <p>Masuk ke Panel Sistem</p>

    @if($errors->any())
        <div class="error-msg">
            <i class="fas fa-exclamation-circle" style="font-size:1.2rem;"></i>
            <div>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        </div>
    @endif

    <form action="/admin/login" method="POST">
        @csrf
        <div class="form-group">
            <label>Username</label>
            <div class="input-group">
                <i class="far fa-user"></i>
                <input type="text" name="username" placeholder="Masukkan username" required autofocus>
            </div>
        </div>
        <div class="form-group">
            <label>Password</label>
            <div class="input-group">
                <i class="far fa-lock-alt"></i>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
        </div>
        <button type="submit" class="btn-submit">
            Masuk <i class="fas fa-arrow-right"></i>
        </button>
    </form>
    
    <div class="footer-note">
        <i class="fal fa-shield-check" style="color:#4CAF50;"></i> Secure Access Control
    </div>
</div>

</body>
</html>
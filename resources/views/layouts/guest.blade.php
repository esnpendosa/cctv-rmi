<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CCTV RMI') }} - Authentication</title>

    <!-- Google Fonts: Lexend Deca -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Hope UI Theme CSS -->
    <link href="{{ asset('css/hope-ui.css') }}" rel="stylesheet">
    
    <style>
        .auth-split-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        .auth-split-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-8);
            background-color: var(--color-bg-page);
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            padding: var(--space-8);
            border-radius: var(--radius-md);
            background-color: var(--color-bg-card);
            box-shadow: var(--shadow-hover);
            border: 1px solid var(--color-border);
        }
        .auth-logo-box {
            width: 52px;
            height: 52px;
            background: var(--color-brand);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--space-4);
        }
        .auth-split-media {
            flex: 1.2;
            background-image: linear-gradient(rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.65)), url('/images/auth-bg.png');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: var(--space-8) var(--space-8) 10% var(--space-8);
            color: #ffffff;
        }
        .auth-media-title {
            font-family: 'Lexend Deca', sans-serif;
            font-size: 2.1rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: var(--space-2);
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }
        .auth-media-desc {
            font-size: 1rem;
            opacity: 0.9;
            max-width: 480px;
            line-height: 1.6;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
        }
        @media (max-width: 991px) {
            .auth-split-media { display: none; }
        }
    </style>
</head>
<body>
    <div class="auth-split-container">
        <div class="auth-split-form">
            <div class="auth-card">
                <div class="text-center mb-4">
                    <div class="auth-logo-box">
                        <i class="bi bi-camera-video-fill" style="font-size:1.4rem;color:#fff;"></i>
                    </div>
                    <h2 style="color:var(--color-text-dark);font-weight:700;font-size:1.4rem;">Selamat Datang</h2>
                    <p style="color:var(--color-text-secondary);font-size:var(--font-size-sm);margin-top:4px;">Masuk ke CCTV RMI — Sistem Manajemen CCTV & Keuangan</p>
                </div>
                
                {{ $slot }}
            </div>
        </div>
        <div class="auth-split-media">
            <div>
                <h1 class="auth-media-title">Secure. Connected. Intelligent.</h1>
                <p class="auth-media-desc">Real-time Remote CCTV Monitoring Interface with Integrated Financial & Inventory Reporting.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6Rz8ynS7ogEvDej/m4gW3F2Ff3RLFh6gUJ2R5j71GLsV9M355FTScgb3gFcR1g" crossorigin="anonymous"></script>
</body>
</html>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Kelola Agenda, Tindak Lanjut, dan Administrasi">
    @include('partials.favicon')
    <title>Masuk - SISKALA</title>
    <!-- Fonts - Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-soft: #eff6ff;
            --text-main: #0f172a;
            --text-secondary: #475569;
            --border-color: #e2e8f0;
            --surface-color: #ffffff;
            --bg-color: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            overflow: hidden;
            background-color: var(--surface-color);
        }

        /* Split Layout */
        .split-screen {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* Left Side - Visual/Branding */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 4rem;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(37, 99, 235, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
            z-index: 1;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 480px;
            width: 100%;
        }

        /* Branding Card for Logo Visibility */
        .branding-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 3rem 2rem;
            box-shadow:
                0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease;
        }

        .branding-card:hover {
            transform: translateY(-5px);
        }

        .brand-logo-large {
            height: 100px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
            /* Removed float animation to keep it steady in card */
        }

        .app-title-large {
            font-size: 3rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #1e293b, #334155);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .app-tagline-large {
            font-size: 1rem;
            color: #475569;
            margin-top: 0.75rem;
            line-height: 1.5;
            font-weight: 500;
        }

        /* Right Side - Login Form */
        .right-panel {
            flex: 1;
            /* Both take 50% */
            max-width: 600px;
            /* Limit width on very large screens for better UX */
            background-color: var(--surface-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            position: relative;
        }

        /* Mobile specific adjustments handled in media query below */

        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.01em;
        }

        .form-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--text-main);
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .form-control:focus+.input-icon {
            color: var(--primary-color);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .custom-checkbox {
            appearance: none;
            width: 1.1rem;
            height: 1.1rem;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-right: 0.5rem;
            cursor: pointer;
            position: relative;
            background-color: white;
            transition: all 0.2s;
        }

        .custom-checkbox:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .custom-checkbox:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .forgot-link {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .helper-links {
            margin-top: 1rem;
            display: grid;
            gap: 0.85rem;
        }

        .helper-link-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.9rem 1rem;
            border-radius: 14px;
            text-decoration: none;
            background: #ffffff;
            border: 1px solid var(--border-color);
            box-shadow: 0 14px 26px -24px rgba(15, 23, 42, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .helper-link-card:hover {
            text-decoration: none;
            transform: translateY(-1px);
            border-color: rgba(37, 99, 235, 0.24);
            box-shadow: 0 20px 30px -24px rgba(37, 99, 235, 0.35);
        }

        .helper-link-icon {
            width: 2.4rem;
            height: 2.4rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary-color);
        }

        .helper-link-copy {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            min-width: 0;
        }

        .helper-link-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .helper-link-title {
            color: var(--text-main);
            font-size: 0.92rem;
            font-weight: 700;
        }

        .helper-link-arrow {
            margin-left: auto;
            color: #94a3b8;
        }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(to right, var(--primary-color), var(--primary-hover));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            position: relative;
            /* For loader if needed */
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 12px -1px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .alert {
            padding: 1rem;
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .alert-icon {
            color: #ef4444;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alert-content strong {
            display: block;
            color: #991b1b;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .alert-content ul {
            margin: 0;
            padding-left: 1.25rem;
            color: #b91c1c;
            font-size: 0.85rem;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .split-screen {
                flex-direction: column;
            }

            .left-panel {
                flex: 0 0 30%;
                /* 30% height on mobile */
                padding: 2rem;
            }

            .brand-logo-large {
                height: 60px;
                margin-bottom: 1rem;
            }

            .app-title-large {
                font-size: 2rem;
            }

            .app-tagline-large {
                font-size: 0.9rem;
                display: none;
                /* Hide tagline on very small screens to save space */
            }

            .right-panel {
                flex: 1;
                padding: 2rem;
                max-width: 100%;
                border-top-left-radius: 24px;
                border-top-right-radius: 24px;
                margin-top: -24px;
                /* Slight overlap */
                background-color: var(--surface-color);
                z-index: 10;
            }
        }

        /* ===== PREMIUM LOADER STYLES ===== */
        #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 1;
            visibility: visible;
        }

        .loader-wrapper-hide {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        .loader-content {
            position: relative;
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 3px solid transparent;
            border-top-color: #2563eb;
            /* Primary Blue from root */
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.5, 0.1, 0.4, 0.9) infinite;
        }

        .loader-ring::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 3px solid transparent;
            border-top-color: #C69749;
            /* Gold accent */
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        .loader-logo {
            width: 70px;
            height: 70px;
            z-index: 10;
            filter: drop-shadow(0 0 15px rgba(37, 99, 235, 0.2));
            animation: pulse-logo 2s ease-in-out infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse-logo {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.15);
                opacity: 0.85;
            }
        }

        body.loading-active {
            overflow: hidden !important;
        }
    </style>
</head>

<body class="loading-active">
    <!-- Premium Loader -->
    <div id="loader-wrapper" class="loader-wrapper-hide">
        <div class="loader-content">
            <div class="loader-ring"></div>
            <img src="{{ asset('images/logo.svg') }}" alt="Loading..." class="loader-logo">
        </div>
    </div>

    <div class="split-screen">
        <!-- Left Panel: Branding -->
        <div class="left-panel">
            <div class="visual-content">
                <div class="branding-card">
                    <img src="{{ asset('images/logo.svg') }}" alt="DJSN Logo" class="brand-logo-large">
                    <h1 class="app-title-large">SISKALA</h1>
                    <p class="app-tagline-large">Sistem Kelola Agenda, tindak Lanjut dan Administrasi<br>Dewan Jaminan
                        Sosial Nasional</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="right-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Selamat Datang</h2>
                    <p class="form-subtitle">Masuk untuk mengakses layanan SISKALA</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    @if($errors->any())
                        <div class="alert">
                            <i class="fe fe-alert-circle alert-icon"></i>
                            <div class="alert-content">
                                <strong>Login Gagal</strong>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <i class="fe fe-mail input-icon"></i>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="Masukkan Email" required autofocus value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <i class="fe fe-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <label class="checkbox-container">
                            <input type="checkbox" id="showPassword" class="custom-checkbox">
                            <span class="checkbox-label">Tampilkan Password</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
                    </div>

                    <button type="submit" class="btn-primary">
                        Login
                    </button>
                    <br>
                    <br>
                    <div class="helper-links">
                        <a href="{{ route('developer') }}" class="helper-link-card">
                            <span class="helper-link-icon" aria-hidden="true">
                                <i class="fe fe-user"></i>
                            </span>
                            <span class="helper-link-copy">
                                <span class="helper-link-label">Informasi</span>
                                <span class="helper-link-title">Developer SISKALA</span>
                            </span>
                            <span class="helper-link-arrow" aria-hidden="true">
                                <i class="fe fe-arrow-right"></i>
                            </span>
                        </a>
                    </div>

                    <div class="footer">
                        &copy; {{ date('Y') }} Dewan Jaminan Sosial Nasional
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Loader Elements
            const loader = $('#loader-wrapper');
            const body = $('body');

            // Toggle Password Visibility
            $('#showPassword').change(function () {
                var input = $('#password');
                if ($(this).is(':checked')) {
                    input.attr('type', 'text');
                } else {
                    input.attr('type', 'password');
                }
            });

            // Show loader on form submit
            $('form').on('submit', function () {
                loader.removeClass('loader-wrapper-hide');
                body.addClass('loading-active');
            });
        });
    </script>
</body>

</html>

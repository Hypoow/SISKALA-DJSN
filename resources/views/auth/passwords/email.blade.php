<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Reset Password - SISKALA">
    <link rel="icon" href="{{ asset('images/logo.svg') }}">
    <title>Lupa Password - SISKALA</title>
    <!-- Fonts - Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

        /* Branding Card */
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
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
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

        /* Right Side - Form */
        .right-panel {
            flex: 1;
            max-width: 600px;
            background-color: var(--surface-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            position: relative;
        }

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
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.5rem;
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

        .form-control:focus + .input-icon {
            color: var(--primary-color);
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
            text-align: center;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 12px -1px rgba(37, 99, 235, 0.3);
        }

        .back-link-container {
            margin-top: 1.5rem;
            text-align: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .back-link i {
            margin-right: 0.5rem;
            width: 16px;
            height: 16px;
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

        .footer {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .alert-success {
            background-color: #ecfdf5;
            border: 1px solid #d1fae5;
            color: #065f46;
        }

        .alert-success .alert-icon {
            color: #10b981;
        }

        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
        }

        .alert-danger .alert-icon {
            color: #ef4444;
        }

        .alert-content {
            font-size: 0.9rem;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .split-screen {
                flex-direction: column;
            }
            
            .left-panel {
                flex: 0 0 30%;
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
            }

            .right-panel {
               flex: 1;
               padding: 2rem;
               max-width: 100%;
               border-top-left-radius: 24px;
               border-top-right-radius: 24px;
               margin-top: -24px;
               background-color: var(--surface-color);
               z-index: 10;
            }
        }
    </style>
</head>
<body>
    <div class="split-screen">
        <!-- Left Panel: Branding (Same as Login) -->
        <div class="left-panel">
            <div class="visual-content">
                <div class="branding-card">
                    <img src="{{ asset('images/logo.svg') }}" alt="DJSN Logo" class="brand-logo-large">
                    <h1 class="app-title-large">SISKALA</h1>
                    <p class="app-tagline-large">Sistem Informasi Penjadwalan & Kegiatan<br>Dewan Jaminan Sosial Nasional</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Password Reset Form -->
        <div class="right-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Lupa Password?</h2>
                    <p class="form-subtitle">Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">
                        <i class="fe fe-check-circle alert-icon"></i>
                        <div class="alert-content">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email</label>
                        <div class="input-group">
                            <i class="fe fe-mail input-icon"></i>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan Email" required autofocus value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary">
                        Kirim Link Reset Password
                    </button>
                    
                    <div class="back-link-container">
                        <a href="{{ route('login') }}" class="back-link">
                            <i class="fe fe-arrow-left"></i>
                            Kembali ke Login
                        </a>
                    </div>
                    <br>
                    <div class="helper-links">
                        <br>
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
                </form>

                <div class="footer">
                    &copy; {{ date('Y') }} Dewan Jaminan Sosial Nasional
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
           // No specific JS needed for now
        });
    </script>
</body>
</html>

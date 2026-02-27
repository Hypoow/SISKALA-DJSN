<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Reset Password - SISKALA">
    <link rel="icon" href="{{ asset('images/logo.svg') }}">
    <title>Reset Password - SISKALA</title>
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
            position: relative;
            overflow: hidden;
            margin-top: 1.5rem;
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

        .text-danger {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.25rem;
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
        <!-- Left Panel: Branding -->
        <div class="left-panel">
            <div class="visual-content">
                <div class="branding-card">
                    <img src="{{ asset('images/logo.svg') }}" alt="DJSN Logo" class="brand-logo-large">
                    <h1 class="app-title-large">SISKALA</h1>
                    <p class="app-tagline-large">Sistem Kelola Agenda, tindak Lanjut dan Administrasi<br>Dewan Jaminan Sosial Nasional</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Password Reset Form -->
        <div class="right-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Reset Password</h2>
                    <p class="form-subtitle">Silakan buat password baru untuk akun Anda.</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <i class="fe fe-mail input-icon"></i>
                            <input type="email" id="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" required autofocus>
                        </div>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru</label>
                        <div class="input-group">
                            <i class="fe fe-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <i class="fe fe-lock input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        Reset Password
                    </button>

                    <div class="footer">
                        &copy; {{ date('Y') }} Dewan Jaminan Sosial Nasional
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

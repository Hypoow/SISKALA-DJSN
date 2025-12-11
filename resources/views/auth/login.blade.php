<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Aplikasi Penjadwalan DJSN">
    <link rel="icon" href="{{ asset('images/logo-new.png') }}">
    <title>Masuk - Schedulo DJSN</title>
    <!-- Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            /* Mesh Gradient Background */
            background-color: #f3f4f6;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-size: 200% 200%;
            animation: gradient-animation 15s ease infinite;
        }

        /* Dynamic Mesh Animation */
        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Overlay to lighten/soften for glass effect base */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.4); 
            z-index: -1;
        }

        /* Glassmorphism Card */
        .login-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.05),
                0 8px 10px -6px rgba(0, 0, 0, 0.01);
            width: 100%;
            max-width: 440px;
            padding: 3rem;
            position: relative;
            z-index: 10;
            transform: translateY(0);
            animation: float-up 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes float-up {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo Area */
        .brand-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .brand-logo {
            height: 60px; /* Slightly larger for emphasis */
            margin-bottom: 1.25rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
        }

        .app-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
            margin: 0;
        }
        
        .app-tagline {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-weight: 500;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 2.8rem; /* Space for icon */
            font-size: 0.95rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background-color: #ffffff; /* Pure white input */
            color: var(--text-dark);
            transition: all 0.2s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        /* Checkbox */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .custom-checkbox {
            width: 1rem;
            height: 1rem;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            margin-right: 0.75rem;
            cursor: pointer;
            position: relative;
        }
        
        .checkbox-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        /* Button */
        .btn-submit {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Footer */
        .footer-text {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* Alert Styling */
        .alert {
            padding: 1rem;
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
        }
        .alert-icon {
             color: #ef4444;
             margin-right: 0.75rem;
             margin-top: 2px;
        }
        .alert-content {
            color: #991b1b;
            font-size: 0.9rem;
        }
        .alert ul {
            margin: 0;
            padding-left: 1rem;
        }
    </style>
  </head>
  <body>
    
    <!-- Decorative Floating Blobs -->
    <div style="position: absolute; top: 10%; left: 15%; width: 300px; height: 300px; background: rgba(59, 130, 246, 0.3); border-radius: 50%; filter: blur(80px); z-index: -1;"></div>
    <div style="position: absolute; bottom: 15%; right: 15%; width: 350px; height: 350px; background: rgba(139, 92, 246, 0.25); border-radius: 50%; filter: blur(80px); z-index: -1;"></div>

    <div class="login-card">
        <div class="brand-section">
            <img src="{{ asset('images/logo-new.png') }}" alt="Logo" class="brand-logo">
            <h1 class="app-name">Schedulo DJSN</h1>
            <p class="app-tagline">Silakan masuk ke akun Anda</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            @if($errors->any())
                <div class="alert">
                    <div class="alert-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div class="alert-content">
                        <strong>Login Gagal</strong>
                        <ul style="list-style: none; padding: 0; margin-top: 4px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </span>
                    <input type="email" id="email" name="email" class="form-control" placeholder="example@gmail.com" required autofocus value="{{ old('email') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                </div>
            </div>

            <div class="checkbox-wrapper">
                <input type="checkbox" id="showPassword" class="custom-checkbox_input" style="margin-right: 8px;">
                <label for="showPassword" class="checkbox-label">Tampilkan Password</label>
            </div>

            <button type="submit" class="btn-submit">
                Masuk
            </button>
        </form>

        <div class="footer-text">
            &copy; {{ date('Y') }} Dewan Jaminan Sosial Nasional
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#showPassword').change(function() {
                var input = $('#password');
                if (this.checked) {
                    input.attr('type', 'text');
                } else {
                    input.attr('type', 'password');
                }
            });
        });
    </script>
  </body>
</html>

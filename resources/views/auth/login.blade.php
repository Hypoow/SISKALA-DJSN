<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Aplikasi Penjadwalan DJSN">
    <link rel="icon" href="{{ asset('images/logo-new.png') }}">
    <title>Schedulo DJSN - Masuk</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{ asset('css/simplebar.css') }}">
    <!-- Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{ asset('css/feather.css') }}">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app-light.css') }}" id="lightTheme">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body, .wrapper {
            height: 100vh;
            overflow: hidden;
        }
        .login-sidebar {
            background: url("{{ asset('images/login-bg.png') }}") no-repeat center center;
            background-size: cover;
            position: relative;
        }
        .login-sidebar::before {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background: rgba(0, 41, 107, 0.4); /* Overlay to ensure text contrast if needed */
        }
        .login-form-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #ffffff;
        }
        .form-content {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        .brand-logo-img {
            max-height: 60px;
            margin-bottom: 2rem;
        }
        .form-control-lg {
            border-radius: 8px;
            font-size: 1rem;
            padding: 1.5rem 1rem;
        }
    </style>
  </head>
  <body class="light">
    <div class="row no-gutters h-100">
        <!-- Left Side: Background Image -->
        <div class="col-lg-7 d-none d-lg-block login-sidebar">
            <div class="d-flex align-items-end h-100 p-5 text-white position-relative">
                <div style="z-index: 2;">
                    <h2 class="font-weight-bold mb-3 display-4">Schedulo DJSN</h2>
                    <p class="h5 font-weight-light">Sistem Penjadwalan Terintegrasi & Efisien.</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-lg-5 col-md-12 bg-white">
            <div class="login-form-container">
                <div class="form-content text-center">
                    <!-- Brand Header -->
                    <div class="d-flex align-items-center justify-content-center mb-4">
                        <img src="{{ asset('images/logo-new.png') }}" alt="Schedulo Logo" class="brand-logo-img mr-3 mb-0" style="max-height: 50px;">
                        <h1 class="h3 font-weight-bold text-primary mb-0" style="letter-spacing: -1px;">Schedulo DJSN</h1>
                    </div>

                    <h2 class="h5 mb-3 font-weight-normal text-left text-muted">Selamat Datang</h2>
                    <p class="text-muted text-left mb-4 small">Silakan masuk untuk melanjutkan.</p>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @if($errors->any())
                            <div class="alert alert-danger shadow-sm border-0" role="alert">
                                <ul class="mb-0 text-left small pl-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group mb-3">
                            <label for="inputEmail" class="sr-only">Alamat Email</label>
                            <input type="email" id="inputEmail" name="email" class="form-control form-control-lg border-0 bg-light" placeholder="Alamat Email" required autofocus value="{{ old('email') }}">
                        </div>
                        <div class="form-group mb-4">
                            <label for="inputPassword" class="sr-only">Kata Sandi</label>
                            <input type="password" id="inputPassword" name="password" class="form-control form-control-lg border-0 bg-light" placeholder="Kata Sandi" required>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="showPassword">
                                <label class="custom-control-label small" for="showPassword">Tampilkan Password</label>
                            </div>
                        </div>

                        <button class="btn btn-lg btn-primary btn-block shadow-sm" type="submit">Masuk</button>
                        
                        <p class="mt-5 mb-0 text-muted small">© {{ date('Y') }} Schedulo DJSN. All rights reserved.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  </body>
</html>





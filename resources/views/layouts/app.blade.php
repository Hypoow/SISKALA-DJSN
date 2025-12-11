<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Schedulo DJSN">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('assets/images/djsn.png') }}">
    <title>@yield('title', 'Schedulo DJSN')</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{ asset('tinydash/css/simplebar.css') }}">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{ asset('tinydash/css/feather.css') }}">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{ asset('tinydash/css/daterangepicker.css') }}">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('tinydash/css/app-dark.css') }}" id="darkTheme" disabled>
    <link rel="stylesheet" href="{{ asset('tinydash/css/app-light.css') }}" id="lightTheme">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
    <script>
      // Force Light Mode
      localStorage.setItem("mode", "light");
    </script>
  </head>
  <body class="vertical light">
    <div class="wrapper">
      <nav class="topnav navbar navbar-light">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
          <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>
        <ul class="nav">

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="avatar avatar-sm mt-2">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="..." class="avatar-img rounded-circle">
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="#">Profile</a>
              <a class="dropdown-item" href="#">Settings</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
              </form>
            </div>
          </li>
        </ul>
      </nav>
      <div id="modeSwitcher" style="display: none;"></div>
      
      @include('layouts.sidebar')

      <main role="main" class="main-content">
        <div class="container-fluid">
          @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          @endif

          @if (session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  {{ session('error') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          @endif

          @if ($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
          @endif
          @yield('content')
        </div> <!-- .container-fluid -->
      </main> <!-- main -->
    </div> <!-- .wrapper -->
    <script src="{{ asset('tinydash/js/jquery.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/popper.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/moment.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/simplebar.min.js') }}"></script>
    <script src='{{ asset('tinydash/js/daterangepicker.js') }}'></script>
    <script src='{{ asset('tinydash/js/jquery.stickOnScroll.js') }}'></script>
    <script src="{{ asset('tinydash/js/tinycolor-min.js') }}"></script>
    <script src="{{ asset('tinydash/js/config.js') }}"></script>
    <script src="{{ asset('tinydash/js/apps.js') }}"></script>
    @livewireScripts
    @stack('scripts')
  </body>
</html>


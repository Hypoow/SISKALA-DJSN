<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SISKALA">
    <meta name="author" content="">
    @include('partials.favicon')
    <title>@yield('title', 'SISKALA')</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{ asset('tinydash/css/simplebar.css') }}">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{ asset('tinydash/css/feather.css') }}">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('tinydash/css/app-dark.css') }}" id="darkTheme" disabled>
    <link rel="stylesheet" href="{{ asset('tinydash/css/app-light.css') }}" id="lightTheme">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #f8f9fa;
        }
    </style>
    @stack('styles')
    <script>
      // Force Light Mode
      localStorage.setItem("mode", "light");
    </script>
  </head>
  <body class="light">
    <div class="wrapper vh-100 d-flex justify-content-center align-items-center">
        @yield('content')
    </div>
    <script src="{{ asset('tinydash/js/jquery.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/popper.min.js') }}"></script>
    <script src="{{ asset('tinydash/js/bootstrap.min.js') }}"></script>
    @stack('scripts')
  </body>
</html>

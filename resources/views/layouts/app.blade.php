<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SISKALA">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('assets/images/logo.svg') }}">
    <title>@yield('title', 'SISKALA')</title>
    <!-- Simple bar CSS -->
    <!-- Simple bar CSS removed -->
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
    <link rel="stylesheet" href="{{ asset('tinydash/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('tinydash/css/select2-bootstrap4.css') }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles
    <style>
        /* FIX: Prevent Layout Shift when Modal Opens */
        body.modal-open {
            overflow: auto !important;
            padding-right: 0 !important;
        }

        [x-cloak] { display: none !important; }
        
        /* Custom Badge Colors */
        .badge-ketua {
            background-color: #8B5CF6 !important; /* Violet/Purple */
            color: white !important;
        }
        .badge-komjakum {
            background-color: #007bff !important; /* Blue (Bootstrap Primary) */
            color: white !important;
        }
        .badge-pme {
            background-color: #28a745 !important; /* Green (Bootstrap Success) */
            color: white !important;
        }
        .badge-sekretariat {
            background-color: #F97316 !important; /* Orange */
            color: white !important;
        }
        .badge-djsn {
            background-color: #6c757d !important; /* Grey (Default) */
            color: white !important;
        }
        
        /* Background Utils */
        .bg-ketua { background-color: #8B5CF6 !important; }
        .bg-komjakum { background-color: #007bff !important; }
        .bg-pme { background-color: #28a745 !important; }
        .bg-sekretariat { background-color: #F97316 !important; }

        /* Premium Markdown Styling */
        .markdown-content {
            color: #2d3748;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        .markdown-content p {
            margin-bottom: 0.75rem !important;
        }
        .markdown-content ul, 
        .markdown-content ol {
            padding-left: 0.5rem !important;
            margin-bottom: 0.75rem !important;
            list-style-position: inside !important;
            display: block !important;
        }
        .markdown-content li {
            margin-bottom: 0.25rem !important;
            display: list-item !important;
        }
        .markdown-content ul {
            list-style-type: disc !important;
        }
        .markdown-content ul li {
            list-style-type: disc !important;
        }
        .markdown-content ol {
            list-style-type: decimal !important;
        }
        .markdown-content ol li {
            list-style-type: decimal !important;
        }
        .markdown-content blockquote {
            border-left: 4px solid #3b82f6;
            background-color: #f8fafc;
            padding: 0.75rem 1rem;
            margin-left: 0;
            margin-bottom: 1rem;
            font-style: italic;
            color: #4a5568;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .markdown-content a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }
        .markdown-content a:hover {
            text-decoration: underline;
        }
        /* Premium Form Control (Dropdowns/Inputs) */
        .form-control-premium {
            display: block;
            width: 100%;
            padding: 0.5rem 2.5rem 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            line-height: 1.5;
            color: #4a5568;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23a0aec0' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 9999px; /* Pill shape */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .form-control-premium:hover {
            border-color: #cbd5e0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .form-control-premium:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            color: #2d3748;
        }

        .dropdown-menu-premium {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 10000;
            min-width: 100%;
            padding: 0.5rem 0;
            margin: 0.25rem 0 0;
            font-size: 0.9rem;
            color: #2d3748;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            animation: fadeIn 0.2s ease-out;
        }

        .dropdown-menu-premium.show {
            display: block;
        }

        .dropdown-item-premium {
            display: block;
            width: 100%;
            padding: 0.5rem 1.25rem;
            clear: both;
            font-weight: 500;
            color: #4a5568;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
        }

        .dropdown-item-premium:hover, 
        .dropdown-item-premium:focus {
            color: #3b82f6;
            background-color: #ebf8ff;
            text-decoration: none;
        }

        .dropdown-item-premium.active {
            color: #2b6cb0;
            background-color: #e2e8f0;
            font-weight: 600;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Search input specific */
        .input-group-premium {
            border-radius: 9999px;
            overflow: hidden;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .input-group-premium:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }
        .input-group-premium .form-control {
            border: none;
            box-shadow: none;
        }
        .input-group-premium .input-group-text {
            background: transparent;
            border: none;
        }
        .markdown-content code {
            background-color: #f1f5f9;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            font-size: 0.875em;
            color: #d63384;
        }
        .markdown-content table {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }
        .markdown-content th, 
        .markdown-content td {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
        }
        .markdown-content th {
            background-color: #f8fafc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        /* Fix for sticky positioning: ensure ancestors don't hide overflow */
        body, html, .wrapper, .main-content, .container-fluid,
        .simplebar-content-wrapper, .simplebar-mask, .simplebar-offset {
            overflow: visible !important;
        }
        /* Ensure no transforms break fixed/sticky context */
        .wrapper {
            transform: none !important;
        }

        /* Sticky Action Header */
        .sticky-action-header {
            position: -webkit-sticky; /* Safari */
            position: sticky;
            top: 70px;
            z-index: 999;
            background-color: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-left: -15px;
            margin-right: -15px;
            padding-left: 2rem !important;
            padding-right: 2rem !important;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        /* Premium Alert Styling */
        .alert-premium {
            border: 0;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            position: relative;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(10px);
            margin-bottom: 1.5rem;
        }

        .alert-premium-danger {
            background-color: rgba(254, 226, 226, 0.9); /* Red-100 with opacity */
            border-left: 5px solid #ef4444; /* Red-500 */
            color: #991b1b; /* Red-800 */
        }

        .alert-premium .alert-icon-wrapper {
            margin-right: 1rem;
            padding-top: 2px;
            color: #ef4444;
        }

        .alert-premium .alert-content {
            flex: 1;
        }

        .alert-premium .alert-heading {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .alert-premium .alert-list {
            list-style-type: none;
            padding-left: 0 !important;
        }

        .alert-premium .alert-list li {
            position: relative;
            padding-left: 1.25rem;
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .alert-premium .alert-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #ef4444;
            font-weight: bold;
        }

        .alert-premium .close {
            opacity: 0.6;
            color: #991b1b;
            text-shadow: none;
            padding: 1.2rem;
            transition: opacity 0.2s;
        }

        .alert-premium .close:hover {
            opacity: 1;
        }

        /* SweetAlert2 Custom Styling */
        div:where(.swal2-container) div:where(.swal2-toast) {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            display: flex !important;
            align-items: center !important;
        }
        
        div:where(.swal2-toast) .swal2-icon {
            margin: 0 0.5rem 0 0 !important;
            transform: scale(0.8);
        }

        div:where(.swal2-html-container) {
            margin: 0 !important;
            font-size: 0.95rem !important;
            font-weight: 500 !important;
            color: #1f2937 !important;
        }
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

          
          {{-- Notification Bell --}}
          <livewire:notification-bell />

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="avatar avatar-sm mt-2">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="..." class="avatar-img rounded-circle">
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-lg border-0" aria-labelledby="navbarDropdownMenuLink" style="min-width: 250px; border-radius: 12px;">
              <div class="dropdown-header d-flex align-items-center bg-light border-bottom py-3 px-3">
                <span class="avatar avatar-sm mt-0 mr-3">
                  <img src="{{ asset('assets/images/logo.svg') }}" alt="..." class="avatar-img rounded-circle">
                </span>
                <div class="user-info text-truncate">
                    <h6 class="mb-0 text-dark font-weight-bold">{{ auth()->user()->name ?? 'User' }}</h6>
                    <small class="text-secondary">{{ auth()->user()->role ?? 'Role' }}</small>
                </div>
              </div>
              <div class="list-group list-group-flush">
                  <a class="list-group-item list-group-item-action border-0 py-2 mt-2 px-3" href="{{ route('profile.edit') }}">
                    <i class="fe fe-user mr-2 text-muted"></i> Profile Saya
                  </a>
              </div>
              <div class="dropdown-divider mb-0"></div>
              <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="dropdown-item py-3 px-3 text-danger font-weight-bold">
                    <i class="fe fe-log-out mr-2"></i> Keluar
                </button>
              </form>
            </div>
          </li>
        </ul>
      </nav>
      <div id="modeSwitcher" style="display: none;"></div>
      
      @include('layouts.sidebar')

      <main role="main" class="main-content">
        <div class="container-fluid">


          @if ($errors->any())
              <div class="alert alert-premium alert-premium-danger alert-dismissible fade show" role="alert">
                  <div class="d-flex align-items-start">
                      <div class="alert-icon-wrapper">
                          <span class="fe fe-alert-triangle fe-24"></span>
                      </div>
                      <div class="alert-content">
                          <strong class="d-block mb-1 alert-heading">Perhatian!</strong>
                          <ul class="mb-0 pl-3 alert-list">
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                  </div>
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
    <!-- Simplebar JS removed -->
    <script src='{{ asset('tinydash/js/daterangepicker.js') }}'></script>
    <script src='{{ asset('tinydash/js/jquery.stickOnScroll.js') }}'></script>
    <script src="{{ asset('tinydash/js/tinycolor-min.js') }}"></script>
    <script src="{{ asset('tinydash/js/config.js') }}"></script>
    <script src="{{ asset('tinydash/js/apps.js') }}"></script>
    <script src="{{ asset('tinydash/js/select2.min.js') }}"></script>
    <script>
      window.Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      });

      window.addEventListener('alert', event => { 
        Toast.fire({
          icon: event.detail.type,
          title: event.detail.message
        });
      });

      $(function() {
        @if (session('success'))
          Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
          });
        @endif

        @if (session('error'))
          Toast.fire({
            icon: 'error',
            title: "{{ session('error') }}"
          });
        @endif
      });
    </script>
    @livewireScripts
    <script>
        // Fix Sidebar Responsiveness by ensuring correct class presence
        $(document).ready(function() {
            function checkWidth() {
                var windowSize = $(window).width();
                // 1200px is the standard xl breakpoint where sidebar behavior changes
                if (windowSize < 1200) {
                    $(".vertical").addClass("narrow");
                } else {
                    $(".vertical").removeClass("narrow");
                }
            }

            // Execute on load
            checkWidth();
            
            // Execute on resize
            $(window).resize(checkWidth);
        });
    </script>
    @stack('scripts')
  </body>
</html>



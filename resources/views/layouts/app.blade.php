<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="SISKALA">
  <meta name="author" content="">
  <link rel="icon" href="{{ asset('images/logo.svg') }}">
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

    [x-cloak] {
      display: none !important;
    }

    /* Custom Badge Colors */
    .badge-ketua {
      background-color: #8B5CF6 !important;
      /* Violet/Purple */
      color: white !important;
    }

    .badge-komjakum {
      background-color: #007bff !important;
      /* Blue (Bootstrap Primary) */
      color: white !important;
    }

    .badge-pme {
      background-color: #28a745 !important;
      /* Green (Bootstrap Success) */
      color: white !important;
    }

    .badge-sekretariat {
      background-color: #F97316 !important;
      /* Orange */
      color: white !important;
    }

    .badge-djsn {
      background-color: #6c757d !important;
      /* Grey (Default) */
      color: white !important;
    }

    /* Background Utils */
    .bg-ketua {
      background-color: #8B5CF6 !important;
    }

    .bg-komjakum {
      background-color: #007bff !important;
    }

    .bg-pme {
      background-color: #28a745 !important;
    }

    .bg-sekretariat {
      background-color: #F97316 !important;
    }

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
      border-radius: 9999px;
      /* Pill shape */
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
      from {
        opacity: 0;
        transform: translateY(-5px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
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
    body,
    html,
    .wrapper,
    .main-content,
    .container-fluid,
    .simplebar-content-wrapper,
    .simplebar-mask,
    .simplebar-offset {
      overflow: visible !important;
    }

    /* Ensure no transforms break fixed/sticky context */
    .wrapper {
      transform: none !important;
    }

    /* Sticky Action Header */
    .sticky-action-header {
      position: -webkit-sticky;
      /* Safari */
      position: sticky;
      top: calc(var(--app-topnav-height, 76px) + 0.75rem) !important;
      z-index: 2200 !important;
      background-color: rgba(255, 255, 255, 0.94);
      border-bottom: 1px solid rgba(226, 232, 240, 0.85);
      box-shadow: 0 18px 34px -30px rgba(15, 44, 89, 0.28);
      margin-left: -15px;
      margin-right: -15px;
      padding-left: 2rem !important;
      padding-right: 2rem !important;
      transition: all 0.3s ease;
      backdrop-filter: blur(14px);
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
      background-color: rgba(254, 226, 226, 0.9);
      /* Red-100 with opacity */
      border-left: 5px solid #ef4444;
      /* Red-500 */
      color: #991b1b;
      /* Red-800 */
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
    (function () {
      const storageKey = 'siskala.sidebar.collapsed';
      const cookieName = 'siskala_sidebar_collapsed';
      const cookieMatch = document.cookie.match(new RegExp('(?:^|; )' + cookieName + '=([^;]*)'));
      const cookieValue = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
      const storageValue = localStorage.getItem(storageKey);
      const isDesktop = window.matchMedia('(min-width: 1200px)').matches;
      const isCollapsed = (storageValue ?? cookieValue) === '1';

      localStorage.setItem('mode', 'light');
      localStorage.setItem(storageKey, isCollapsed ? '1' : '0');

      if (isCollapsed) {
        document.cookie = cookieName + '=1; path=/; max-age=31536000; SameSite=Lax';
      } else {
        document.cookie = cookieName + '=0; path=/; max-age=31536000; SameSite=Lax';
      }

      if (isDesktop && isCollapsed) {
        document.documentElement.classList.add('app-sidebar-desktop-collapsed');
      } else {
        document.documentElement.classList.remove('app-sidebar-desktop-collapsed');
      }
    })();
  </script>
</head>
@php
  $sidebarCollapsed = request()->cookie('siskala_sidebar_collapsed') === '1';
@endphp

<body class="vertical light app-shell{{ $sidebarCollapsed ? ' app-sidebar-desktop-collapsed' : '' }}">
  <script>
    (function () {
      if (window.matchMedia('(min-width: 1200px)').matches && document.documentElement.classList.contains('app-sidebar-desktop-collapsed')) {
        document.body.classList.add('app-sidebar-desktop-collapsed');
      } else if (!window.matchMedia('(min-width: 1200px)').matches) {
        document.body.classList.remove('app-sidebar-desktop-collapsed');
      }
    })();
  </script>
  <div class="wrapper">
    <nav class="topnav navbar navbar-light">
      <div class="d-flex align-items-center">
        <button type="button"
          class="navbar-toggler text-muted p-0 mr-3 app-sidebar-trigger d-xl-none border-0 shadow-none bg-transparent"
          aria-label="Buka menu navigasi" aria-controls="leftSidebar" aria-expanded="false">
          <span class="app-shell-hamburger">
            <i class="fe fe-menu"></i>
          </span>
        </button>
        <!-- Desktop Toggle Button -->
        <button type="button"
          class="navbar-toggler text-muted p-0 mr-3 app-sidebar-desktop-toggle d-none d-xl-inline-flex align-items-center border-0 shadow-none bg-transparent"
          aria-label="{{ $sidebarCollapsed ? 'Perluas menu desktop' : 'Ciutkan menu desktop' }}"
          aria-controls="leftSidebar" aria-expanded="{{ $sidebarCollapsed ? 'false' : 'true' }}">
          <span class="app-shell-hamburger">
            <i class="fe fe-menu" style="font-size: 1.2rem; color: var(--primary-color);"></i>
          </span>
        </button>
        <a href="{{ route('dashboard') }}" class="app-shell-brand text-decoration-none">
          <span class="app-shell-brand-mark">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo SISKALA" class="app-shell-brand-image" width="22"
              height="22">
          </span>
          <span class="app-shell-brand-copy">
            <strong>SISKALA</strong>
            <small>Sistem Informasi Kegiatan DJSN</small>
          </span>
        </a>
      </div>
      <ul class="nav align-items-center">


        {{-- Notification Bell --}}
        <livewire:notification-bell />

        <li class="nav-item dropdown app-navbar-item-profile">
          <a
            class="nav-link dropdown-toggle text-muted pr-0 app-navbar-action app-navbar-profile-trigger d-inline-flex align-items-center justify-content-center"
            href="#" id="profileDropdownTrigger" role="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false" aria-label="Buka menu profil">
            <span class="avatar avatar-sm app-navbar-profile-avatar">
              <img src="{{ asset('images/logo.svg') }}" alt="Logo SISKALA"
                class="avatar-img rounded-circle app-avatar-logo">
            </span>
          </a>
          <div
            class="dropdown-menu dropdown-menu-right shadow-lg border-0 app-navbar-dropdown app-navbar-dropdown-profile"
            aria-labelledby="profileDropdownTrigger" style="min-width: 250px; border-radius: 12px;">
            <div class="dropdown-header d-flex align-items-center bg-light border-bottom py-3 px-3">
              <span class="avatar avatar-sm mt-0 mr-3">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo SISKALA"
                  class="avatar-img rounded-circle app-avatar-logo">
              </span>
              <div class="user-info text-truncate">
                <h6 class="mb-0 text-dark font-weight-bold">{{ auth()->user()->name ?? 'User' }}</h6>
                <small class="text-secondary">{{ auth()->user()->role ?? 'Role' }}</small>
              </div>
            </div>
            <div class="list-group list-group-flush">
              <a class="list-group-item list-group-item-action border-0 py-2 mt-2 px-3"
                href="{{ route('profile.edit') }}">
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
    <button type="button" class="app-sidebar-overlay" aria-label="Tutup menu navigasi" aria-hidden="true"></button>

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

    $(function () {
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
    document.addEventListener('DOMContentLoaded', function () {
      const body = document.body;
      const root = document.documentElement;
      const sidebar = document.getElementById('leftSidebar');
      const overlay = document.querySelector('.app-sidebar-overlay');
      const mobileToggle = document.querySelector('.app-sidebar-trigger');
      const desktopToggle = document.querySelector('.app-sidebar-desktop-toggle');
      const closeButtons = document.querySelectorAll('.dashboard-sidebar-close, .app-sidebar-overlay');
      const navLinks = sidebar ? sidebar.querySelectorAll('.nav-link') : [];
      const topnav = document.querySelector('.topnav');
      const desktopQuery = window.matchMedia('(min-width: 1200px)');
      const storageKey = 'siskala.sidebar.collapsed';
      const cookieName = 'siskala_sidebar_collapsed';
      let sidebarLock = false;
      let shellResizeFrame = null;

      if (!sidebar) {
        return;
      }

      if (window.jQuery) {
        window.jQuery('.sidebar-left').off('mouseenter mouseleave');
        window.jQuery('.collapseSidebar').off('click');
      }

      function isDesktop() {
        return desktopQuery.matches;
      }

      function readPersistedCollapsedState() {
        const storageValue = localStorage.getItem(storageKey);
        const cookieMatch = document.cookie.match(new RegExp('(?:^|; )' + cookieName + '=([^;]*)'));
        const cookieValue = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
        return (storageValue ?? cookieValue) === '1';
      }

      function persistCollapsedState(isCollapsed) {
        localStorage.setItem(storageKey, isCollapsed ? '1' : '0');
        document.cookie = cookieName + '=' + (isCollapsed ? '1' : '0') + '; path=/; max-age=31536000; SameSite=Lax';
      }

      function withSidebarLock(callback) {
        if (sidebarLock) {
          return;
        }

        sidebarLock = true;
        callback();

        window.setTimeout(function () {
          sidebarLock = false;
        }, 360);
      }

      function syncShellOffset() {
        const sidebarWidth = isDesktop() ? Math.round(sidebar.getBoundingClientRect().width) : 0;
        body.style.setProperty('--app-shell-offset', sidebarWidth + 'px');
      }

      function queueShellLayoutSync() {
        if (shellResizeFrame !== null) {
          window.cancelAnimationFrame(shellResizeFrame);
        }

        shellResizeFrame = window.requestAnimationFrame(function () {
          syncShellOffset();
          syncNotificationDropdownLayout();
          shellResizeFrame = null;
        });
      }

      function setMobileOpen(isOpen) {
        const shouldOpen = isOpen && !isDesktop();

        body.classList.toggle('app-sidebar-mobile-open', shouldOpen);
        body.classList.remove('hover');

        if (mobileToggle) {
          mobileToggle.setAttribute('aria-expanded', String(shouldOpen));
        }

        if (overlay) {
          overlay.setAttribute('aria-hidden', String(!shouldOpen));
        }

        sidebar.setAttribute('aria-hidden', isDesktop() ? 'false' : String(!shouldOpen));
        queueShellLayoutSync();
      }

      function setDesktopCollapsed(isCollapsed) {
        const shouldCollapse = isCollapsed && isDesktop();

        body.classList.toggle('app-sidebar-desktop-collapsed', shouldCollapse);
        root.classList.toggle('app-sidebar-desktop-collapsed', shouldCollapse);
        body.classList.remove('hover');

        if (desktopToggle) {
          desktopToggle.setAttribute('aria-expanded', String(!shouldCollapse));
          desktopToggle.setAttribute(
            'aria-label',
            shouldCollapse ? 'Perluas menu desktop' : 'Ciutkan menu desktop'
          );
        }

        queueShellLayoutSync();
      }

      function syncSidebarMode() {
        body.classList.remove('collapsed', 'narrow', 'open', 'hover');
        const savedDesktopState = readPersistedCollapsedState();

        if (isDesktop()) {
          setMobileOpen(false);
          setDesktopCollapsed(savedDesktopState);
          sidebar.setAttribute('aria-hidden', 'false');
          queueShellLayoutSync();
          return;
        }

        body.classList.remove('app-sidebar-desktop-collapsed');
        root.classList.remove('app-sidebar-desktop-collapsed');
        setMobileOpen(false);

        if (desktopToggle) {
          desktopToggle.setAttribute('aria-expanded', 'true');
          desktopToggle.setAttribute('aria-label', 'Ciutkan menu desktop');
        }

        queueShellLayoutSync();
      }

      function isMobileViewport() {
        return window.matchMedia('(max-width: 767.98px)').matches;
      }

      function resetNotificationDropdownLayout(menu) {
        if (!menu) {
          return;
        }

        menu.style.position = '';
        menu.style.top = '';
        menu.style.right = '';
        menu.style.bottom = '';
        menu.style.left = '';
        menu.style.width = '';
        menu.style.maxWidth = '';
        menu.style.minWidth = '';
        menu.style.marginTop = '';
        menu.style.transform = '';
        menu.style.webkitTransform = '';
        menu.style.willChange = '';
        menu.removeAttribute('x-placement');
        menu.removeAttribute('data-popper-placement');
      }

      function pinNotificationDropdown(menu) {
        if (!menu || !isMobileViewport()) {
          return;
        }

        const topnavBottom = topnav ? Math.round(topnav.getBoundingClientRect().bottom) : 0;
        const viewportPadding = 12;
        const availableWidth = Math.max(window.innerWidth - (viewportPadding * 2), 220);

        menu.style.position = 'fixed';
        menu.style.top = (topnavBottom + 8) + 'px';
        menu.style.left = viewportPadding + 'px';
        menu.style.right = viewportPadding + 'px';
        menu.style.bottom = 'auto';
        menu.style.width = availableWidth + 'px';
        menu.style.maxWidth = availableWidth + 'px';
        menu.style.minWidth = '0';
        menu.style.marginTop = '0';
        menu.style.transform = 'none';
        menu.style.webkitTransform = 'none';
        menu.style.willChange = 'auto';
        menu.removeAttribute('x-placement');
        menu.removeAttribute('data-popper-placement');
      }

      function syncNotificationDropdownLayout() {
        const activeNotificationMenu = document.querySelector('.app-navbar-item-notif .app-navbar-dropdown-notif.show');

        if (!activeNotificationMenu) {
          return;
        }

        if (isMobileViewport()) {
          pinNotificationDropdown(activeNotificationMenu);
          return;
        }

        resetNotificationDropdownLayout(activeNotificationMenu);
      }

      if (mobileToggle) {
        mobileToggle.addEventListener('click', function (event) {
          if (isDesktop()) {
            return;
          }

          event.preventDefault();
          event.stopImmediatePropagation();

          withSidebarLock(function () {
            setMobileOpen(!body.classList.contains('app-sidebar-mobile-open'));
          });
        });
      }

      if (desktopToggle) {
        desktopToggle.addEventListener('click', function (event) {
          if (!isDesktop()) {
            return;
          }

          event.preventDefault();
          event.stopImmediatePropagation();

          withSidebarLock(function () {
            const nextState = !body.classList.contains('app-sidebar-desktop-collapsed');
            setDesktopCollapsed(nextState);
            persistCollapsedState(nextState);
          });
        });
      }

      closeButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
          event.preventDefault();
          event.stopImmediatePropagation();

          withSidebarLock(function () {
            setMobileOpen(false);
          });
        });
      });

      navLinks.forEach(function (link) {
        link.addEventListener('click', function () {
          if (!isDesktop()) {
            setMobileOpen(false);
          }
        });
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          setMobileOpen(false);
        }
      });

      if (typeof desktopQuery.addEventListener === 'function') {
        desktopQuery.addEventListener('change', syncSidebarMode);
      } else if (typeof desktopQuery.addListener === 'function') {
        desktopQuery.addListener(syncSidebarMode);
      }

      window.addEventListener('resize', queueShellLayoutSync);

      sidebar.addEventListener('transitionend', function (event) {
        if (['width', 'min-width', 'max-width', 'transform'].includes(event.propertyName)) {
          queueShellLayoutSync();
        }
      });

      if (typeof ResizeObserver === 'function') {
        const sidebarResizeObserver = new ResizeObserver(function () {
          queueShellLayoutSync();
        });

        sidebarResizeObserver.observe(sidebar);
      }

      if (window.jQuery) {
        const $document = window.jQuery(document);

        $document.off('.appNavbarNotif');
        $document.on('show.bs.dropdown.appNavbarNotif shown.bs.dropdown.appNavbarNotif', '.app-navbar-item-notif', function () {
          const menu = this.querySelector('.app-navbar-dropdown-notif');

          window.requestAnimationFrame(function () {
            pinNotificationDropdown(menu);
          });

          window.setTimeout(function () {
            pinNotificationDropdown(menu);
          }, 0);
        });

        $document.on('hide.bs.dropdown.appNavbarNotif hidden.bs.dropdown.appNavbarNotif', '.app-navbar-item-notif', function () {
          resetNotificationDropdownLayout(this.querySelector('.app-navbar-dropdown-notif'));
        });
      }

      syncSidebarMode();
      queueShellLayoutSync();
    });
  </script>
  @stack('scripts')
</body>

</html>

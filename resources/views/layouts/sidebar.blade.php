<aside class="sidebar-left border-right shadow" id="leftSidebar">
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-white ml-2 mt-3" data-toggle="toggle">
      <i class="fe fe-x"><span class="sr-only"></span></i>
    </a>
    <nav class="vertnav navbar navbar-light">
      <!-- nav bar -->
      <div class="w-100 mb-3 d-flex">
        <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ route('dashboard') }}">
          <svg version="1.1" id="logo" class="navbar-brand-img brand-sm" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 120 120" xml:space="preserve">
            <g>
              <polygon class="st0" points="78,105 15,105 24,87 87,87 	" />
              <polygon class="st0" points="96,69 33,69 42,51 105,51 	" />
              <polygon class="st0" points="78,33 15,33 24,15 87,15 	" />
            </g>
          </svg>
        </a>
      </div>
      <ul class="navbar-nav flex-fill w-100 mb-2">
        <li class="nav-item w-100 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fe fe-calendar fe-16"></i>
            <span class="ml-3 item-text">Dashboard</span>
          </a>
        </li>
      </ul>

      <p class="text-muted nav-heading mt-2 mb-1 px-3">
        <span>Reminder H-1</span>
      </p>
      <ul class="navbar-nav flex-fill w-100 mb-2">
        <li class="nav-item w-100 {{ request()->routeIs('report.h1-visual') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('report.h1-visual') }}">
              <i class="fe fe-monitor fe-16"></i>
              <span class="ml-3 item-text">Visualisasi H-1</span>
            </a>
        </li>
        @if(auth()->check() && auth()->user()->isAdmin())
        <li class="nav-item w-100 {{ request()->routeIs('report.h1') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('report.h1') }}">
              <i class="fe fe-file-text fe-16"></i>
              <span class="ml-3 item-text">Pelaporan H-1</span>
            </a>
        </li>
        @endif
      </ul>

      <p class="text-muted nav-heading mt-2 mb-1 px-3">
        <span>Kegiatan</span>
      </p>
      <ul class="navbar-nav flex-fill w-100 mb-2">
        @if(auth()->check() && auth()->user()->isAdmin())
        <li class="nav-item w-100 {{ request()->routeIs('activities.create') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('activities.create') }}">
              <i class="fe fe-plus-circle fe-16"></i>
              <span class="ml-3 item-text">Kegiatan Baru</span>
            </a>
        </li>
        @endif
        <li class="nav-item w-100 {{ request()->routeIs('activities.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('activities.index') }}">
              <i class="fe fe-list fe-16"></i>
              <span class="ml-3 item-text">Daftar Kegiatan</span>
            </a>
        </li>
        <li class="nav-item w-100 {{ request()->routeIs('activities.past') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('activities.past') }}">
              <i class="fe fe-clock fe-15"></i>
              <span class="ml-3 item-text">Kegiatan Selesai</span>
            </a>
        </li>
        <li class="nav-item w-100 {{ request()->routeIs('followup.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('followup.dashboard') }}">
              <i class="fe fe-check-square fe-16"></i>
              <span class="ml-3 item-text">Tindak Lanjut</span>
            </a>
        </li>
      </ul>

      @if(auth()->check() && auth()->user()->role === 'admin')
      <p class="text-muted nav-heading mt-2 mb-1 px-3">
        <span>Admin</span>
      </p>
      <ul class="navbar-nav flex-fill w-100 mb-2">
        <li class="nav-item w-100 {{ request()->routeIs('master-data.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('master-data.index') }}">
              <i class="fe fe-users fe-16"></i>
              <span class="ml-3 item-text">Master User</span>
            </a>
        </li>
        <li class="nav-item w-100 {{ request()->routeIs('master-data.topics') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('master-data.topics') }}">
              <i class="fe fe-tag fe-16"></i>
              <span class="ml-3 item-text">Master Topik</span>
            </a>
        </li>
      </ul>
      @endif

      <p class="text-muted nav-heading mt-2 mb-1 px-3">
        <span>Akun</span>
      </p>
      <ul class="navbar-nav flex-fill w-100 mb-2">
        <li class="nav-item w-100 {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('profile.edit') }}">
              <i class="fe fe-user fe-16"></i>
              <span class="ml-3 item-text">Profil Saya</span>
            </a>
        </li>
        <li class="nav-item w-100">
            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="fe fe-log-out fe-16"></i>
              <span class="ml-3 item-text">Keluar</span>
            </a>
        </li>
      </ul>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
      </form>
    </nav>
  </aside>

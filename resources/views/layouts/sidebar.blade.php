<aside class="sidebar-left border-right shadow app-sidebar" id="leftSidebar" aria-hidden="true">
  <div class="sidebar-mobile-head d-xl-none">
    <a class="sidebar-brand-link sidebar-brand-link--mobile" href="{{ route('dashboard') }}"
      aria-label="Dashboard SISKALA">
      <span class="sidebar-brand-mark sidebar-brand-mark--expanded">
        <img src="{{ asset('images/logo-siskala-sidebar.png') }}" alt="Logo SISKALA"
          class="sidebar-brand-image sidebar-brand-image--expanded">
      </span>
    </a>
    <button type="button" class="btn dashboard-sidebar-close sidebar-mobile-close" aria-label="Tutup menu navigasi">
      <i class="fe fe-x"></i>
    </button>
  </div>

  <nav class="vertnav navbar navbar-light app-sidebar-nav" aria-label="Navigasi utama">
    <div class="sidebar-brand-shell d-flex justify-content-center align-items-center w-100">
      <a href="{{ route('dashboard') }}" aria-label="Dashboard SISKALA"
        class="app-sidebar-brand-link d-inline-flex align-items-center justify-content-center text-decoration-none">
        <span class="app-sidebar-brand-expanded" aria-hidden="true">
          <img src="{{ asset('images/logo-siskala-sidebar.png') }}" alt=""
            class="app-sidebar-brand-expanded-image">
        </span>
        <span class="app-sidebar-brand-collapsed" aria-hidden="true">
          <img src="{{ asset('images/siskala-logo.svg') }}" alt=""
            class="app-sidebar-brand-collapsed-image">
        </span>
      </a>
    </div>

    <div class="app-sidebar-content">
      <section class="app-sidebar-group">
        <p class="text-muted nav-heading mt-0 mb-1 px-3">
          <span>Utama</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
          <li class="nav-item w-100 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}" data-sidebar-label="Dashboard" aria-label="Dashboard">
              <i class="fe fe-calendar fe-16"></i>
              <span class="ml-3 item-text">Dashboard</span>
            </a>
          </li>
        </ul>
      </section>

      <section class="app-sidebar-group">
        <p class="text-muted nav-heading mt-2 mb-1 px-3">
          <span>Laporan Rekapan</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
          <li class="nav-item w-100 {{ request()->routeIs('report.h1-visual') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('report.h1-visual') }}" data-sidebar-label="Visualisasi Rekapan"
              aria-label="Visualisasi Rekapan">
              <i class="fe fe-monitor fe-16"></i>
              <span class="ml-3 item-text">Visualisasi Rekapan</span>
            </a>
          </li>
          @if(auth()->check() && auth()->user()->canAccessH1Report())
            <li class="nav-item w-100 {{ request()->routeIs('report.h1') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('report.h1') }}" data-sidebar-label="Daftar Rekapan"
                aria-label="Daftar Rekapan">
                <i class="fe fe-file-text fe-16"></i>
                <span class="ml-3 item-text">Daftar Rekapan</span>
              </a>
            </li>
          @endif
        </ul>
      </section>

      <section class="app-sidebar-group">
        <p class="text-muted nav-heading mt-2 mb-1 px-3">
          <span>Kegiatan</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
          @if(auth()->check() && auth()->user()->canManageActivities())
            <li class="nav-item w-100 {{ request()->routeIs('activities.create') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('activities.create') }}" data-sidebar-label="Kegiatan Baru"
                aria-label="Kegiatan Baru">
                <i class="fe fe-plus-circle fe-16"></i>
                <span class="ml-3 item-text">Kegiatan Baru</span>
              </a>
            </li>
          @endif
          <li class="nav-item w-100 {{ request()->routeIs('activities.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('activities.index') }}" data-sidebar-label="Daftar Kegiatan"
              aria-label="Daftar Kegiatan">
              <i class="fe fe-list fe-16"></i>
              <span class="ml-3 item-text">Daftar Kegiatan</span>
            </a>
          </li>
          <li class="nav-item w-100 {{ request()->routeIs('activities.past') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('activities.past') }}" data-sidebar-label="Kegiatan Selesai"
              aria-label="Kegiatan Selesai">
              <i class="fe fe-clock fe-15"></i>
              <span class="ml-3 item-text">Kegiatan Selesai</span>
            </a>
          </li>
          <li class="nav-item w-100 {{ request()->routeIs('followup.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('followup.dashboard') }}" data-sidebar-label="Tindak Lanjut"
              aria-label="Tindak Lanjut">
              <i class="fe fe-check-square fe-16"></i>
              <span class="ml-3 item-text">Tindak Lanjut</span>
            </a>
          </li>
        </ul>
      </section>

      @if(auth()->check() && (auth()->user()->canAccessAdminArea() || auth()->user()->canManageTopics()))
        <section class="app-sidebar-group">
          <p class="text-muted nav-heading mt-2 mb-1 px-3">
            <span>Data Master</span>
          </p>
          <ul class="navbar-nav flex-fill w-100 mb-2">
            @if(auth()->user()->canAccessAdminArea())
              <li class="nav-item w-100 {{ request()->routeIs('master-data.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('master-data.index') }}" data-sidebar-label="Master User"
                  aria-label="Master User">
                  <i class="fe fe-users fe-16"></i>
                  <span class="ml-3 item-text">Master User</span>
                </a>
              </li>
            @endif
            @if(auth()->user()->canManageTopics())
              <li class="nav-item w-100 {{ request()->routeIs('master-data.topics') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('master-data.topics') }}" data-sidebar-label="Master Topik"
                  aria-label="Master Topik">
                  <i class="fe fe-tag fe-16"></i>
                  <span class="ml-3 item-text">Master Topik</span>
                </a>
              </li>
            @endif
          </ul>
        </section>
      @endif

      <section class="app-sidebar-group">
        <p class="text-muted nav-heading mt-2 mb-1 px-3">
          <span>Akun</span>
        </p>
        <ul class="navbar-nav flex-fill w-100 mb-2">
          <li class="nav-item w-100 {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('profile.edit') }}" data-sidebar-label="Profil Saya"
              aria-label="Profil Saya">
              <i class="fe fe-user fe-16"></i>
              <span class="ml-3 item-text">Profil Saya</span>
            </a>
          </li>
          <li class="nav-item w-100">
            <a class="nav-link" href="{{ route('logout') }}" data-sidebar-label="Keluar" aria-label="Keluar"
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="fe fe-log-out fe-16"></i>
              <span class="ml-3 item-text">Keluar</span>
            </a>
          </li>
        </ul>
      </section>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
      </form>

      <section class="app-sidebar-group app-sidebar-group-meta">
        <p class="app-sidebar-meta-line mb-0">
          <span class="app-sidebar-meta-copy">SISKALA &copy; 2026</span>
          <a class="app-sidebar-meta-link {{ request()->routeIs('developer') ? 'is-active' : '' }}"
            href="{{ route('developer') }}" aria-label="Lihat info pengembang SISKALA">
            | &lt;&gt; Pengembang
          </a>
        </p>
      </section>
    </div>

  </nav>
</aside>

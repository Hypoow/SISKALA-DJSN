@php
    $active = $active ?? 'users';
    $tabs = [
        ['key' => 'users', 'route' => route('master-data.index'), 'icon' => 'fe-users', 'label' => 'Akun & Akses'],
        ['key' => 'staff', 'route' => route('master-data.staff.index'), 'icon' => 'fe-briefcase', 'label' => 'Staf Pendamping'],
        ['key' => 'divisions', 'route' => route('master-data.divisions'), 'icon' => 'fe-layers', 'label' => 'Builder Struktur'],
    ];
@endphp

<div class="dashboard-surface-card master-data-tabs-card">
    <div class="master-data-tabs" role="tablist" aria-label="Navigasi master data">
        @foreach($tabs as $tab)
            <a
                href="{{ $tab['route'] }}"
                class="master-data-tab-link {{ $active === $tab['key'] ? 'active' : '' }}"
                @if($active === $tab['key']) aria-current="page" @endif
            >
                <span class="master-data-tab-icon">
                    <i class="fe {{ $tab['icon'] }}"></i>
                </span>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

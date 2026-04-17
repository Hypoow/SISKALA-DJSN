@php
    $active = $active ?? 'users';
    $tabs = [
        [
            'key' => 'users',
            'route' => route('master-data.index'),
            'icon' => 'fe-shield',
            'label' => 'Akun & Akses',
            'desc' => 'Kelola Pengguna',
        ],
        [
            'key' => 'staff',
            'route' => route('master-data.staff.index'),
            'icon' => 'fe-users',
            'label' => 'Staf Pendamping',
            'desc' => 'Tim Support',
        ],
        [
            'key' => 'divisions',
            'route' => route('master-data.divisions'),
            'icon' => 'fe-layers',
            'label' => 'Builder Struktur',
            'desc' => 'Unit & Jabatan',
        ],
    ];
@endphp

<div class="master-nav-wrapper mb-4">
    <div class="master-nav-container" role="tablist" aria-label="Navigasi Master Data">
        @foreach($tabs as $tab)
            <a
                href="{{ $tab['route'] }}"
                class="master-nav-item {{ $active === $tab['key'] ? 'active' : '' }}"
                @if($active === $tab['key']) aria-current="page" @endif
            >
                <div class="master-nav-icon">
                    <i class="fe {{ $tab['icon'] }}"></i>
                </div>
                <div class="master-nav-text">
                    <span class="master-nav-title">{{ $tab['label'] }}</span>
                    <span class="master-nav-desc">{{ $tab['desc'] }}</span>
                </div>
            </a>
        @endforeach
    </div>
</div>

<style>
    .master-nav-wrapper {
        position: relative;
        width: 100%;
        overflow: hidden;
    }

    /* Container dengan scroll horizontal yang mulus */
    .master-nav-container {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.85rem;
        overflow-x: auto;
        padding-bottom: 0.5rem; /* Space for scrollbar if visible */
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
    }

    .master-nav-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Edge */
    }

    /* Item Tab (Card/Pill) */
    .master-nav-item {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.75rem 1rem;
        min-width: 220px; /* Lebar minimum agar muat di scroll */
        flex: 1; /* Stretch on desktop */
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 6px -4px rgba(15, 23, 42, 0.05);
    }

    .master-nav-item:hover {
        text-decoration: none;
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.08);
    }

    /* Active State */
    .master-nav-item.active {
        background: linear-gradient(135deg, #0F2C59 0%, #1a4382 100%);
        border-color: #0F2C59;
        box-shadow: 0 12px 20px -8px rgba(15, 44, 89, 0.4);
    }

    /* Icon Box */
    .master-nav-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #f8fafc;
        color: #64748b;
        font-size: 1.15rem;
        transition: all 0.2s ease;
    }

    .master-nav-item:hover .master-nav-icon {
        color: #0F2C59;
        background: #eef2f6;
    }

    .master-nav-item.active .master-nav-icon {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
    }

    /* Texts */
    .master-nav-text {
        display: flex;
        flex-direction: column;
    }

    .master-nav-title {
        color: #0f172a;
        font-weight: 700;
        font-size: 0.95rem;
        line-height: 1.2;
        margin-bottom: 0.15rem;
        transition: color 0.2s ease;
    }

    .master-nav-desc {
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1;
        transition: color 0.2s ease;
    }

    .master-nav-item.active .master-nav-title {
        color: #ffffff;
    }

    .master-nav-item.active .master-nav-desc {
        color: rgba(255, 255, 255, 0.7);
    }

    /* Mobile Adjustments */
    @media (max-width: 768px) {
        .master-nav-item {
            min-width: 200px;
            flex: 0 0 auto;
        }
        .master-nav-container {
            padding: 0 0.5rem 0.5rem 0.5rem;
            margin: 0 -0.5rem;
        }
    }
</style>

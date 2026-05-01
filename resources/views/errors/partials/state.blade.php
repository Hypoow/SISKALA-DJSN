@php
    $isAuthenticated = auth()->check();
    $primaryUrl = $primaryUrl ?? ($isAuthenticated ? route('dashboard') : route('login'));
    $primaryLabel = $primaryLabel ?? ($isAuthenticated ? 'Ke Dashboard' : 'Masuk ke SISKALA');
    $primaryIcon = $primaryIcon ?? ($isAuthenticated ? 'fe-home' : 'fe-log-in');
    $variant = $variant ?? 'primary';
@endphp

<div class="siskala-error-page">
    <section class="siskala-error-state siskala-error-state--{{ $variant }}" aria-labelledby="error-title">
        <div class="siskala-error-state__bar"></div>

        <div class="siskala-error-state__brand">
            <span class="siskala-error-state__logo">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo SISKALA">
            </span>
            <span>SISKALA</span>
        </div>

        <div class="siskala-error-state__status">
            <span class="siskala-error-state__icon">
                <i class="fe {{ $icon ?? 'fe-alert-triangle' }}" aria-hidden="true"></i>
            </span>
            <span class="siskala-error-state__code">{{ $code }}</span>
        </div>

        <h1 id="error-title">{{ $title }}</h1>
        <p>{{ $message }}</p>

        <div class="siskala-error-state__actions">
            <a href="{{ $primaryUrl }}" class="btn btn-primary rounded-pill px-4 shadow-sm font-weight-bold">
                <i class="fe {{ $primaryIcon }} mr-2" aria-hidden="true"></i>{{ $primaryLabel }}
            </a>

            @if($isAuthenticated)
                <button type="button" class="btn btn-light border rounded-pill px-4" onclick="window.history.back()">
                    <i class="fe fe-arrow-left mr-2" aria-hidden="true"></i>Kembali
                </button>
            @endif
        </div>

        <div class="siskala-error-state__meta">
            <i class="fe fe-shield mr-2" aria-hidden="true"></i>
            Sistem tetap melindungi data Anda saat permintaan tidak dapat dilanjutkan.
        </div>
    </section>
</div>

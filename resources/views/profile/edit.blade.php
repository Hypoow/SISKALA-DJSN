@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
@php
    $currentUser = auth()->user();

    $roleStyles = [
        'admin' => ['badgeClass' => 'bg-primary-soft', 'textClass' => 'text-primary', 'icon' => 'fe-shield', 'label' => 'Admin'],
        'DJSN' => ['badgeClass' => 'bg-success-soft', 'textClass' => 'text-success', 'icon' => 'fe-check-square', 'label' => 'Sekretariat DJSN'],
        'Tata Usaha' => ['badgeClass' => 'bg-info-soft', 'textClass' => 'text-info', 'icon' => 'fe-file-text', 'label' => 'Tata Usaha'],
        'Persidangan' => ['badgeClass' => 'bg-warning-soft', 'textClass' => 'text-warning', 'icon' => 'fe-users', 'label' => 'Persidangan'],
        'Bagian Umum' => ['badgeClass' => 'bg-danger-soft', 'textClass' => 'text-danger', 'icon' => 'fe-folder', 'label' => 'Bagian Umum'],
        'User' => ['badgeClass' => 'bg-secondary', 'textClass' => 'text-dark', 'icon' => 'fe-user', 'label' => 'User'],
        'Dewan' => ['badgeClass' => 'bg-dark text-white', 'textClass' => 'text-white', 'icon' => 'fe-star', 'label' => 'Dewan'],
    ];

    $roleMeta = $roleStyles[$currentUser->role] ?? ['badgeClass' => 'bg-secondary', 'textClass' => 'text-dark', 'icon' => 'fe-user', 'label' => 'User'];
@endphp

<div class="profile-page">
    <div class="profile-shell">
        <section class="card border-0 shadow-sm overflow-hidden profile-hero">
            <div class="profile-hero__ornament"></div>
            <div class="card-body profile-hero__body">
                <div class="profile-hero__intro">
                    <h3 class="mb-2 font-weight-bold text-white">Pengaturan Profil</h3>
                    <p class="mb-0 text-white-50">Kelola informasi akun dan keamanan Anda</p>
                </div>

                <div class="profile-hero__identity">
                    <div class="profile-avatar" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="58" height="58" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>

                    <div class="profile-hero__identity-copy">
                        <h4 class="mb-2 font-weight-bold text-white">{{ $currentUser->name }}</h4>
                        <div class="mb-2">
                            <span class="badge badge-pill {{ $roleMeta['badgeClass'] }} {{ $roleMeta['textClass'] }} profile-role-badge">
                                <i class="fe {{ $roleMeta['icon'] }} mr-2"></i>{{ $roleMeta['label'] }}
                            </span>
                        </div>
                        <small class="d-block text-white-50">{{ $currentUser->email }}</small>
                    </div>
                </div>
            </div>
        </section>

        <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')

            <div class="profile-grid">
                <section class="card border-0 shadow-sm profile-card">
                    <div class="card-body profile-card__body">
                        <div class="profile-card__heading">
                            <h5 class="text-primary font-weight-bold mb-0">Informasi Pribadi</h5>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-4">
                                    <label for="name" class="profile-label">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" class="form-control form-control-lg profile-input" value="{{ old('name', $user->name) }}" autocomplete="name" required>
                                    @error('name')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="email" class="profile-label">Alamat Email</label>
                                    <input type="email" id="email" name="email" class="form-control form-control-lg profile-input" value="{{ old('email', $user->email) }}" autocomplete="email" required>
                                    @error('email')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card border-0 shadow-sm profile-card">
                    <div class="card-body profile-card__body">
                        <div class="profile-card__heading profile-card__heading--stacked">
                            <h5 class="text-primary font-weight-bold mb-2">Keamanan (Ganti Password)</h5>
                            <p class="text-muted small mb-0">Biarkan kosong jika tidak ingin mengubah password Anda.</p>
                        </div>

                        <div class="form-group mb-4">
                            <label for="current_password" class="profile-label">Password Saat Ini</label>
                            <div class="input-group profile-input-group">
                                <input type="password" id="current_password" name="current_password" class="form-control form-control-lg profile-input" autocomplete="current-password">
                                <div class="input-group-append">
                                    <button class="btn profile-password-toggle toggle-password" type="button" data-target="#current_password" data-label-show="Tampilkan password saat ini" data-label-hide="Sembunyikan password saat ini" aria-label="Tampilkan password saat ini" aria-pressed="false">
                                        <i class="fe fe-eye"></i>
                                    </button>
                                </div>
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-4 mb-md-0">
                                    <label for="new_password" class="profile-label">Password Baru</label>
                                    <div class="input-group profile-input-group">
                                        <input type="password" id="new_password" name="new_password" class="form-control form-control-lg profile-input" autocomplete="new-password">
                                        <div class="input-group-append">
                                            <button class="btn profile-password-toggle toggle-password" type="button" data-target="#new_password" data-label-show="Tampilkan password baru" data-label-hide="Sembunyikan password baru" aria-label="Tampilkan password baru" aria-pressed="false">
                                                <i class="fe fe-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('new_password')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group mb-0">
                                    <label for="new_password_confirmation" class="profile-label">Konfirmasi Password</label>
                                    <div class="input-group profile-input-group">
                                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control form-control-lg profile-input" autocomplete="new-password">
                                        <div class="input-group-append">
                                            <button class="btn profile-password-toggle toggle-password" type="button" data-target="#new_password_confirmation" data-label-show="Tampilkan konfirmasi password" data-label-hide="Sembunyikan konfirmasi password" aria-label="Tampilkan konfirmasi password" aria-pressed="false">
                                                <i class="fe fe-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm profile-success-alert">
                    <i class="fe fe-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="profile-actions">
                <button type="submit" class="btn btn-primary shadow-sm font-weight-bold profile-submit">
                    <i class="fe fe-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    :root {
        --profile-primary: #245c8b;
        --profile-primary-dark: #16395f;
        --profile-accent: #2e7c82;
        --profile-surface: #ffffff;
        --profile-surface-soft: #f6f9fc;
        --profile-border: #dce5ef;
        --profile-text: #15324a;
        --profile-muted: #607287;
        --profile-shadow: 0 28px 55px -40px rgba(15, 44, 89, 0.48);
    }

    .profile-page { padding: 1.5rem 0 2.5rem; }
    .profile-shell { max-width: 1320px; margin: 0 auto; }
    .profile-form { margin-top: 1.5rem; }
    .profile-hero { position: relative; border-radius: 28px; background: radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 36%), linear-gradient(135deg, var(--profile-primary-dark) 0%, var(--profile-primary) 54%, var(--profile-accent) 100%); box-shadow: var(--profile-shadow); }
    .profile-hero__ornament { position: absolute; inset: 0; pointer-events: none; background: radial-gradient(circle at 85% 25%, rgba(255,255,255,0.18) 0, rgba(255,255,255,0) 24%), radial-gradient(circle at 72% 80%, rgba(255,255,255,0.12) 0, rgba(255,255,255,0) 18%); }
    .profile-hero__body { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(310px, auto); gap: 1.5rem; align-items: center; padding: 2rem; }
    .profile-hero__intro { max-width: 560px; }
    .profile-hero__identity { display: flex; align-items: center; justify-content: flex-end; gap: 1rem; min-width: 0; }
    .profile-hero__identity-copy { min-width: 0; text-align: right; }
    .profile-hero__identity-copy h4, .profile-hero__identity-copy small { overflow-wrap: anywhere; }
    .profile-avatar { display: inline-flex; align-items: center; justify-content: center; width: 96px; height: 96px; flex-shrink: 0; border-radius: 999px; color: rgba(255,255,255,0.92); background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); box-shadow: 0 20px 35px -24px rgba(4,18,34,0.55); backdrop-filter: blur(10px); }
    .profile-role-badge { display: inline-flex; align-items: center; padding: 0.65rem 0.95rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; box-shadow: 0 14px 22px -18px rgba(11,37,64,0.45); }
    .profile-grid { display: grid; gap: 1.5rem; }
    .profile-card { border-radius: 24px; background: var(--profile-surface); box-shadow: var(--profile-shadow); }
    .profile-card__body { padding: 1.75rem; }
    .profile-card__heading { display: flex; align-items: center; min-height: 3.25rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e8eef5; }
    .profile-card__heading--stacked { display: block; min-height: auto; }
    .profile-label { display: inline-block; margin-bottom: 0.65rem; color: var(--profile-text); font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
    .profile-input { min-height: 56px; padding: 0.85rem 1rem; border: 1px solid var(--profile-border); border-radius: 16px; background: var(--profile-surface-soft); color: var(--profile-text); font-size: 0.96rem; transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease; }
    .profile-input:hover { border-color: #c9d6e4; }
    .profile-input:focus { background-color: #ffffff !important; border-color: var(--profile-primary) !important; box-shadow: 0 0 0 0.2rem rgba(36,92,139,0.14) !important; color: var(--profile-text); }
    .profile-input-group .profile-input { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0; }
    .profile-password-toggle { min-width: 58px; border: 1px solid var(--profile-border); border-left: 0; border-radius: 0 16px 16px 0; background: var(--profile-surface-soft); color: var(--profile-muted); transition: border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease; }
    .profile-password-toggle:hover { color: var(--profile-primary); }
    .profile-input-group:focus-within .profile-input, .profile-input-group:focus-within .profile-password-toggle { background: #ffffff; border-color: var(--profile-primary); }
    .profile-input-group:focus-within { border-radius: 16px; box-shadow: 0 0 0 0.2rem rgba(36,92,139,0.14); }
    .profile-input-group:focus-within .profile-input { box-shadow: none !important; }
    .profile-success-alert { margin-top: 1.5rem; border-radius: 18px; padding: 1rem 1.25rem; }
    .profile-actions { display: flex; justify-content: flex-end; margin-top: 1.5rem; }
    .profile-submit { min-height: 56px; padding: 0.85rem 1.75rem; border-radius: 18px; font-size: 0.95rem; letter-spacing: 0.01em; }
    .bg-primary-soft { background-color: rgba(94, 114, 228, 0.15) !important; }
    .bg-success-soft { background-color: rgba(45, 206, 137, 0.15) !important; }
    .bg-info-soft { background-color: rgba(17, 205, 239, 0.15) !important; }
    .bg-warning-soft { background-color: rgba(251, 99, 64, 0.15) !important; }
    .bg-danger-soft { background-color: rgba(245, 54, 92, 0.15) !important; }

    @media (min-width: 1280px) {
        .profile-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 1199.98px) {
        .profile-hero__body { grid-template-columns: 1fr; }
        .profile-hero__identity { justify-content: flex-start; }
        .profile-hero__identity-copy { text-align: left; }
    }

    @media (max-width: 767.98px) {
        .profile-page { padding: 0.5rem 0 2rem; }
        .profile-form { margin-top: 1.25rem; }
        .profile-hero { border-radius: 22px; }
        .profile-hero__body { gap: 1.25rem; padding: 1.25rem; }
        .profile-hero__identity { align-items: flex-start; }
        .profile-avatar { width: 76px; height: 76px; }
        .profile-avatar svg { width: 42px; height: 42px; }
        .profile-card { border-radius: 20px; }
        .profile-card__body { padding: 1.25rem; }
        .profile-card__heading { margin-bottom: 1.25rem; padding-bottom: 0.85rem; }
        .profile-actions { justify-content: stretch; }
        .profile-submit { width: 100%; }
    }

    @media (max-width: 575.98px) {
        .profile-hero__identity { flex-direction: column; align-items: flex-start; }
        .profile-role-badge { font-size: 0.68rem; letter-spacing: 0.06em; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.querySelector(targetId);
                const icon = this.querySelector('i');

                if (!input || !icon) {
                    return;
                }

                const nextType = input.type === 'password' ? 'text' : 'password';
                const isVisible = nextType === 'text';

                input.type = nextType;
                icon.classList.toggle('fe-eye', !isVisible);
                icon.classList.toggle('fe-eye-off', isVisible);
                this.setAttribute('aria-pressed', String(isVisible));
                this.setAttribute('aria-label', isVisible ? this.dataset.labelHide : this.dataset.labelShow);
            });
        });
    });
</script>
@endpush

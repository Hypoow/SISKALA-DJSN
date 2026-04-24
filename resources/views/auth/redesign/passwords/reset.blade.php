@extends('layouts.auth')

@section('title', 'Reset Password - SISKALA')
@section('meta_description', 'Buat password baru untuk akun SISKALA Anda.')
@section('eyebrow', 'Password Baru')
@section('heading', 'Buat Password Baru')
@section('subheading', 'Masukkan password baru.')

@section('brand_panel')
    @include('auth.redesign.partials.brand-slider')
@endsection

@section('alerts')
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fe fe-alert-circle" aria-hidden="true"></i>
            <div>
                <strong>Masih ada isian yang perlu diperbaiki</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection

@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-stack">
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-shell">
                    <i class="fe fe-mail input-icon" aria-hidden="true"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ $email ?? old('email') }}"
                        autocomplete="email"
                        inputmode="email"
                        spellcheck="false"
                        required
                        autofocus
                    >
                </div>

                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password Baru</label>
                <div class="input-shell has-toggle">
                    <i class="fe fe-lock input-icon" aria-hidden="true"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password baru"
                        autocomplete="new-password"
                        required
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        data-password-toggle="password"
                        aria-controls="password"
                        aria-pressed="false"
                    >
                        <i class="fe fe-eye" aria-hidden="true"></i>
                        <span class="sr-only">Tampilkan password</span>
                    </button>
                </div>

                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <div class="input-shell has-toggle">
                    <i class="fe fe-shield input-icon" aria-hidden="true"></i>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Ulangi password baru"
                        autocomplete="new-password"
                        required
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        data-password-toggle="password_confirmation"
                        aria-controls="password_confirmation"
                        aria-pressed="false"
                    >
                        <i class="fe fe-eye" aria-hidden="true"></i>
                        <span class="sr-only">Tampilkan password</span>
                    </button>
                </div>
            </div>
        </div>

        <button type="submit" class="primary-button">Simpan Password</button>

        <div class="form-meta">
            <a href="{{ route('login') }}" class="back-link">
                <i class="fe fe-arrow-left" aria-hidden="true"></i>
                <span>Kembali ke Login</span>
            </a>
        </div>
    </form>
@endsection

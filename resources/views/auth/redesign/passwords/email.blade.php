@extends('layouts.auth')

@section('title', 'Lupa Password - SISKALA')
@section('meta_description', 'Minta tautan reset password untuk akun SISKALA.')
@section('eyebrow', 'Pemulihan Akses')
@section('heading', 'Lupa Password?')
@section('subheading', 'Masukkan email untuk menerima link reset.')

@section('brand_panel')
    @include('auth.redesign.partials.brand-slider')
@endsection

@section('alerts')
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fe fe-check-circle" aria-hidden="true"></i>
            <div>
                <strong>Link reset berhasil dikirim</strong>
                <p>{{ session('status') }}</p>
            </div>
        </div>
    @endif
@endsection

@section('content')
    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <div class="form-stack">
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <div class="input-shell">
                    <i class="fe fe-mail input-icon" aria-hidden="true"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        value="{{ old('email') }}"
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
        </div>

        <button type="submit" class="primary-button">Kirim Link</button>

        <div class="form-meta">
            <a href="{{ route('login') }}" class="back-link">
                <i class="fe fe-arrow-left" aria-hidden="true"></i>
                <span>Kembali ke Login</span>
            </a>
        </div>
    </form>
@endsection

@extends('layouts.auth')

@section('title', 'Masuk - SISKALA')
@section('meta_description', 'Masuk ke layanan SISKALA untuk mengakses agenda, tindak lanjut, dan administrasi.')
@section('eyebrow', 'Portal Masuk')
@section('heading', 'Selamat Datang')
@section('subheading', 'Masuk ke SISKALA.')

@section('brand_panel')
    @include('auth.redesign.partials.brand-slider')
@endsection

@section('overlay')
    <div id="loader-wrapper" class="loader-wrapper is-hidden" aria-hidden="true">
        <div class="loader-core">
            <div class="loader-ring"></div>
            <img src="{{ asset('images/logo.svg') }}" alt="Loading SISKALA">
        </div>
    </div>
@endsection

@section('alerts')
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fe fe-alert-circle" aria-hidden="true"></i>
            <div>
                <strong>Login belum berhasil</strong>
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
    <form method="POST" action="{{ route('login') }}" class="auth-form" data-auth-form>
        @csrf

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
                        placeholder="Masukkan email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        inputmode="email"
                        spellcheck="false"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-shell has-toggle">
                    <i class="fe fe-lock input-icon" aria-hidden="true"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
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
            </div>
        </div>

        <div class="form-meta">
            <label class="inline-check" for="remember">
                <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>
                <span>Tetap masuk</span>
            </label>

            <a href="{{ route('password.request') }}" class="inline-link">
                <span>Lupa Password?</span>
                <i class="fe fe-arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <button type="submit" class="primary-button">Masuk</button>

    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('[data-auth-form]');
            const loader = document.getElementById('loader-wrapper');

            if (!form || !loader) {
                return;
            }

            form.addEventListener('submit', function () {
                loader.classList.remove('is-hidden');
                document.body.classList.add('loading-active');
            });
        });
    </script>
@endpush

@extends('layouts.plain')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="text-center mt-5 pt-5">
    <h1 class="display-1 font-weight-bold text-primary">404</h1>
    <h2 class="h4 mb-4">Halaman Tidak Ditemukan</h2>
    <p class="text-muted mb-5">Kami tidak dapat menemukan halaman yang Anda cari.</p>
    <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4">Kembali ke Dashboard</a>
</div>
@endsection

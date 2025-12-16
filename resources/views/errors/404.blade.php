@extends('layouts.plain')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="row align-items-center justify-content-center h-100">
    <div class="col-md-6 text-center">
        <h1 class="display-1 fw-bold text-primary">404</h1>
        <h3 class="mb-4">Oops! Halaman Tidak Ditemukan</h3>
        <p class="text-muted mb-4">Maaf, halaman yang Anda cari mungkin telah dihapus, dipindahkan, atau tidak tersedia.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4">Kembali ke Dashboard</a>
    </div>
</div>
@endsection

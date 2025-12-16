@extends('layouts.plain')

@section('title', 'Akses Ditolak')

@section('content')
<div class="row align-items-center justify-content-center h-100">
    <div class="col-md-6 text-center">
        <h1 class="display-1 fw-bold text-danger">403</h1>
        <h3 class="mb-4">Akses Ditolak</h3>
        <p class="text-muted mb-4">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary rounded-pill px-4">Kembali ke Dashboard</a>
    </div>
</div>
@endsection

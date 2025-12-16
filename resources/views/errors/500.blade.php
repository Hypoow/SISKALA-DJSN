@extends('layouts.plain')

@section('title', 'Terjadi kesalahan')

@section('content')
<div class="row align-items-center justify-content-center h-100">
    <div class="col-md-6 text-center">
        <h1 class="display-1 fw-bold text-primary">500</h1>
        <h3 class="mb-4">Terjadi kesalahan</h3>
        <p class="text-muted mb-4">Maaf, terjadi kesalahan pada server.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4">Kembali ke Dashboard</a>
    </div>
</div>
@endsection
@extends('layouts.plain')

@section('title', 'Terjadi Kesalahan Server')

@section('content')
<div class="text-center mt-5 pt-5">
    <h1 class="display-1 font-weight-bold text-danger">500</h1>
    <h2 class="h4 mb-4">Terjadi Kesalahan Server</h2>
    <p class="text-muted mb-5">Maaf, terjadi kesalahan internal pada server kami. <br> Silakan coba beberapa saat lagi atau hubungi administrator.</p>
    <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4">Kembali ke Dashboard</a>
</div>
@endsection

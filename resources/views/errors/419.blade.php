@extends('layouts.plain')

@section('title', 'Sesi Halaman Kedaluwarsa')

@section('content')
@include('errors.partials.state', [
    'code' => '419',
    'title' => 'Sesi halaman kedaluwarsa',
    'message' => 'Token keamanan halaman sudah tidak cocok dengan server. Ini biasa terjadi setelah halaman dibiarkan lama.',
    'icon' => 'fe-clock',
    'variant' => 'warning',
    'primaryUrl' => url()->current(),
    'primaryLabel' => 'Muat ulang halaman',
    'primaryIcon' => 'fe-refresh-cw',
])
@endsection

@extends('layouts.plain')

@section('title', 'Terjadi kesalahan')

@section('content')
@include('errors.partials.state', [
    'code' => '500',
    'title' => 'Server belum dapat memproses',
    'message' => 'Terjadi kendala di server saat memuat permintaan. Silakan muat ulang atau kembali ke dashboard.',
    'icon' => 'fe-alert-triangle',
    'variant' => 'warning',
])
@endsection

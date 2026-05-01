@extends('layouts.plain')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
@include('errors.partials.state', [
    'code' => '404',
    'title' => 'Halaman tidak ditemukan',
    'message' => 'Alamat yang dibuka tidak tersedia, sudah berubah, atau belum terdaftar di sistem.',
    'icon' => 'fe-compass',
    'variant' => 'primary',
])
@endsection

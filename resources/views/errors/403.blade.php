@extends('layouts.plain')

@section('title', 'Akses Ditolak')

@section('content')
@include('errors.partials.state', [
    'code' => '403',
    'title' => 'Akses tidak tersedia',
    'message' => 'Akun Anda belum memiliki izin untuk membuka halaman atau menjalankan aksi ini.',
    'icon' => 'fe-lock',
    'variant' => 'danger',
])
@endsection

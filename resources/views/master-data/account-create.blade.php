@extends('layouts.app')

@section('title', 'Tambah Akun Baru')

@section('content')
    <div class="container-fluid py-4">
        @include('master-data.partials.user-form', [
            'mode' => 'create',
            'formAction' => route('master-data.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Simpan Akun',
            'pageTitle' => 'Tambah Akun Baru',
            'pageDescription' => 'Struktur akun kini dipilih dari unit kerja dan jabatan agar role, akses, dan komisi lebih mudah dikelola.',
        ])
    </div>
@endsection

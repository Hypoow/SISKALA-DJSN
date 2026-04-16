@extends('layouts.app')

@section('title', 'Edit Akun Pengguna')

@section('content')
    <div class="container-fluid py-4">
        @include('master-data.partials.user-form', [
            'user' => $user,
            'mode' => 'edit',
            'formAction' => route('master-data.update', $user->id),
            'formMethod' => 'PUT',
            'submitLabel' => 'Simpan Perubahan',
            'pageTitle' => 'Edit Akun Pengguna',
            'pageDescription' => 'Perbarui struktur, akses, dan override seperlunya tanpa perlu mengatur role manual satu per satu.',
        ])
    </div>
@endsection

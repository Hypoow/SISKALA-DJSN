@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">
        <h2 class="h3 mb-4 page-title">Edit Profil</h2>
        
        <div class="my-4">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mt-5 align-items-center">
                    <div class="col-md-3 text-center mb-5">
                        <div class="avatar avatar-xl">
                            <span class="avatar-title rounded-circle border border-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h4 class="mb-1">{{ auth()->user()->name }}</h4>
                                <p class="small mb-3"><span class="badge badge-dark">{{ auth()->user()->role }}</span></p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-7">
                                <p class="text-muted">
                                    Silakan perbarui informasi profil dan kata sandi Anda di sini.
                                </p>
                            </div>
                            <div class="col">
                                <p class="small mb-0 text-muted">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="form-group mb-3">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <hr class="my-4">
                <h5 class="mb-2 mt-4">Ganti Password</h5>
                <p class="text-muted">Biarkan kosong jika tidak ingin mengganti password.</p>

                <div class="form-group mb-3">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                    @error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="form-control">
                    @error('new_password')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control">
                </div>

                <div class="row align-items-center">
                    <div class="col-md-7"></div>
                    <div class="col-md-5 text-right">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

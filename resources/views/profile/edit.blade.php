@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow border-0 rounded-lg overflow-hidden my-5">
                <!-- Card Header with Background -->
                <div class="card-header bg-primary text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <h3 class="font-weight-bold mb-0 text-white">Pengaturan Profil</h3>
                    <p class="mb-0 text-white-50">Kelola informasi akun dan keamanan Anda</p>
                </div>

                <div class="card-body p-5">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Profile Header Section -->
                        <div class="d-flex align-items-center mb-5 profile-header-section">
                            <div class="mr-4">
                                <div class="avatar-wrapper rounded-circle bg-light d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px; border: 3px solid #fff;">
                                    <!-- Default SVG Avatar -->
                                    <svg viewBox="0 0 24 24" width="60" height="60" stroke="#adb5bd" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-weight-bold mb-1">{{ auth()->user()->name }}</h4>
                                <div class="mb-1">
                                    @if(auth()->user()->role === 'admin')
                                        <span class="badge badge-role-admin px-3 py-2 rounded-pill">Admin</span>
                                    @elseif(auth()->user()->role === 'Dewan')
                                        <span class="badge badge-role-dewan px-3 py-2 rounded-pill">Dewan</span>
                                    @elseif(auth()->user()->role === 'DJSN')
                                        <span class="badge badge-role-djsn px-3 py-2 rounded-pill">DJSN</span>
                                    @else
                                        <span class="badge badge-role-user px-3 py-2 rounded-pill">User</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </div>
                        </div>

                        <!-- Personal Info Section -->
                        <div class="mb-5">
                            <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2">Informasi Pribadi</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="font-weight-600 text-dark small text-uppercase">Nama Lengkap</label>
                                        <input type="text" id="name" name="name" class="form-control form-control-lg bg-light border-light" value="{{ old('name', $user->name) }}" required>
                                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-600 text-dark small text-uppercase">Alamat Email</label>
                                        <input type="email" id="email" name="email" class="form-control form-control-lg bg-light border-light" value="{{ old('email', $user->email) }}" required>
                                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div class="mb-4">
                            <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2">Keamanan (Ganti Password)</h5>
                            <p class="text-muted small mb-3">Biarkan kosong jika tidak ingin mengubah password Anda.</p>
                            
                            <div class="form-group">
                                <label for="current_password" class="font-weight-600 text-dark small text-uppercase">Password Saat Ini</label>
                                <div class="input-group">
                                    <input type="password" id="current_password" name="current_password" class="form-control form-control-lg bg-light border-light border-right-0">
                                    <div class="input-group-append">
                                        <button class="btn btn-light border-light border-left-0 toggle-password" type="button" data-target="#current_password">
                                            <i class="fe fe-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('current_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new_password" class="font-weight-600 text-dark small text-uppercase">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" id="new_password" name="new_password" class="form-control form-control-lg bg-light border-light border-right-0">
                                            <div class="input-group-append">
                                                <button class="btn btn-light border-light border-left-0 toggle-password" type="button" data-target="#new_password">
                                                    <i class="fe fe-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('new_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new_password_confirmation" class="font-weight-600 text-dark small text-uppercase">Konfirmasi Password</label>
                                        <div class="input-group">
                                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control form-control-lg bg-light border-light border-right-0">
                                            <div class="input-group-append">
                                                <button class="btn btn-light border-light border-left-0 toggle-password" type="button" data-target="#new_password_confirmation">
                                                    <i class="fe fe-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success border-0 shadow-sm mb-4">
                                <i class="fe fe-check-circle mr-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm font-weight-bold">
                                <i class="fe fe-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control-lg {
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
    }
    .form-control:focus {
        background-color: #fff !important;
        border-color: #2a5298 !important;
        box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15) !important;
    }
    .profile-header-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }
    /* Fix input group border when focused */
    .input-group:focus-within .form-control,
    .input-group:focus-within .btn {
         background-color: #fff !important;
         border-color: #2a5298 !important;
    }
    .input-group:focus-within {
         box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.15) !important;
         border-radius: 0.25rem;
    }
    .input-group:focus-within .form-control {
        box-shadow: none !important;
    }
    .toggle-password {
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Password Visibility
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.querySelector(targetId);
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fe-eye');
                    icon.classList.add('fe-eye-off');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fe-eye-off');
                    icon.classList.add('fe-eye');
                }
            });
        });
    });
</script>
@endsection

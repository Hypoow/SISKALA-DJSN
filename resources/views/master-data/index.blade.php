@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header -->
            <div class="row align-items-center mb-4 mt-2">
                <div class="col">
                    <h2 class="h3 font-weight-bold mb-0 text-dark">Data Pengguna</h2>
                    <p class="text-muted mb-0">Kelola akun pengguna, peran, dan hak akses sistem</p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4" data-toggle="modal" data-target="#userModal">
                        <i class="fe fe-plus-circle mr-2"></i> Tambah Akun
                    </button>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-pills nav-fill flex-column flex-sm-row mb-4 bg-white rounded shadow-sm p-2">
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active font-weight-bold shadow-sm" href="{{ route('master-data.index') }}">
                        <i class="fe fe-users mr-2"></i>Data Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 font-weight-bold text-muted" href="{{ route('master-data.staff.index') }}">
                        <i class="fe fe-briefcase mr-2"></i>Data Staf Pendamping
                    </a>
                </li>
            </ul>

            <!-- Card Table -->
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-5">
                <!-- Card Header -->
                <div class="card-header border-0 py-4 px-4" style="background: linear-gradient(87deg, #1a3681 100%) !important;">
                    <h5 class="card-title mb-0 text-white font-weight-bold">
                        <i class="fe fe-users mr-2"></i>Daftar Pengguna Sistem
                    </h5>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4" style="width: 5%;">No</th>
                                    <th style="width: 5%;"></th> <!-- Drag Handle -->
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Identitas Pengguna</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email & Kontak</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Peran & Akses</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Bergabung</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <!-- Loop through Groups -->
                            @foreach($groupedUsers as $groupName => $usersInGroup)
                            <tbody class="sortable-group" data-group-name="{{ $groupName }}">
                                <!-- Group Header -->
                                <tr class="bg-light">
                                    <td colspan="7" class="py-2 pl-4 font-weight-bold text-dark text-uppercase text-xs letter-spacing-1 border-top border-bottom">
                                        {{ $groupName }}
                                    </td>
                                </tr>
                                <!-- Users in Group -->
                                @foreach($usersInGroup as $user)
                                <tr class="element-row" data-id="{{ $user->id }}">
                                    <td class="pl-4 align-middle font-weight-bold text-secondary">{{ $loop->iteration }}</td>
                                    <td class="align-middle text-center cursor-move handle">
                                        <i class="fe fe-menu text-muted"></i>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm mr-3 bg-light rounded-circle text-muted border shadow-sm d-flex align-items-center justify-content-center">
                                                <i class="fe fe-user fe-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $user->name }}</h6>
                                                <small class="text-muted d-block">{{ $user->divisi ?? 'Tanpa Divisi' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="fe fe-mail text-muted mr-2"></i>
                                            <span class="text-sm text-secondary font-weight-medium">{{ $user->email }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            $textClass = 'text-muted';
                                            $icon = 'fe-user';
                                            $roleLabel = 'User';
                                            
                                            switch($user->role) {
                                                case 'admin':
                                                    $badgeClass = 'bg-primary-soft'; 
                                                    $textClass = 'text-primary';
                                                    $icon = 'fe-shield';
                                                    $roleLabel = 'Admin';
                                                    break;
                                                case 'DJSN':
                                                    $badgeClass = 'bg-success-soft';
                                                    $textClass = 'text-success';
                                                    $icon = 'fe-check-square';
                                                    $roleLabel = 'Sekretariat DJSN';
                                                    break;
                                                case 'Tata Usaha':
                                                    $badgeClass = 'bg-info-soft'; 
                                                    $textClass = 'text-info';
                                                    $icon = 'fe-file-text';
                                                    $roleLabel = 'Tata Usaha';
                                                    break;
                                                case 'Persidangan':
                                                    $badgeClass = 'bg-warning-soft';
                                                    $textClass = 'text-warning';
                                                    $icon = 'fe-users';
                                                    $roleLabel = 'Persidangan';
                                                    break;
                                                case 'Bagian Umum':
                                                    $badgeClass = 'bg-danger-soft';
                                                    $textClass = 'text-danger';
                                                    $icon = 'fe-folder';
                                                    $roleLabel = 'Bagian Umum';
                                                    break;
                                                case 'User':
                                                    $badgeClass = 'bg-gray-400 text-white';
                                                    $textClass = 'text-white';
                                                    $icon = 'fe-user';
                                                    $roleLabel = 'User';
                                                    break;
                                                case 'Dewan':
                                                    $badgeClass = 'bg-dark text-white';
                                                    $textClass = 'text-white';
                                                    $icon = 'fe-star';
                                                    $roleLabel = 'Dewan';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge badge-pill {{ $badgeClass }} {{ $textClass }} px-3 py-2 text-xs font-weight-bold shadow-sm d-inline-flex align-items-center">
                                            <i class="fe {{ $icon }} mr-2"></i> {{ $roleLabel }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                       <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-muted btn-white shadow-sm action-btn" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <i class="fe fe-more-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->divisi }}')">
                                                    <i class="fe fe-edit-2 mr-2 text-warning"></i> Edit
                                                </a>
                                                <form action="{{ route('master-data.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="dropdown-item delete-btn">
                                                        <i class="fe fe-trash-2 mr-2 text-danger"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @endforeach
                            
                            @if($groupedUsers->isEmpty())
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-6">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="icon icon-shape bg-light text-muted rounded-circle mb-3 shadow-inner" style="width: 64px; height: 64px;">
                                                <i class="fe fe-users" style="font-size: 24px;"></i>
                                            </div>
                                            <h5 class="text-muted font-weight-bold">Tidak ada data pengguna</h5>
                                            <p class="text-muted text-sm mb-0">Belum ada akun yang terdaftar dalam sistem.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0 rounded-xl">
      <div class="modal-header bg-gradient-primary text-white py-3">
        <h5 class="modal-title font-weight-bold pl-2" id="userModalLabel">
            <i class="fe fe-user-plus mr-2"></i>Tambah Akun Baru
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="userForm" method="POST" action="{{ route('master-data.store') }}">
        @csrf
        <input type="hidden" id="userId" name="user_id">
        <input type="hidden" id="formMethod" name="_method" value="POST">
        <div class="modal-body p-4 bg-secondary-soft">
          
          <!-- Warning Alert for Roles -->
          <div class="alert alert-soft-info border-0 shadow-sm mb-4 fade show" role="alert">
            <span class="alert-icon"><i class="fe fe-info"></i></span>
            <span class="alert-text small"><strong>Pilih Peran dengan Bijak.</strong> Peran menentukan hak akses pengguna terhadap fitur sistem.</span>
          </div>

          <div class="row">
            <!-- Left Column: Primary Data -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <h6 class="heading-small text-muted mb-3 text-uppercase">Informasi Akun</h6>
                
                <div class="form-group mb-3">
                    <label for="userName" class="form-control-label small font-weight-bold">Nama Lengkap</label>
                    <div class="input-group input-group-merge shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fe fe-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="userName" name="name" required placeholder="Masukkan Nama Lengkap">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="userEmail" class="form-control-label small font-weight-bold">Alamat Email</label>
                    <div class="input-group input-group-merge shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fe fe-mail"></i></span>
                        </div>
                        <input type="email" class="form-control" id="userEmail" name="email" required placeholder="Masukkan Alamat Email">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="userDivisi" class="form-control-label small font-weight-bold">Divisi / Jabatan</label>
                    <div class="input-group input-group-merge shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fe fe-briefcase"></i></span>
                        </div>
                        <input type="text" class="form-control" id="userDivisi" name="divisi" placeholder="Masukkan Divisi / Jabatan">
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label for="userPassword" class="form-control-label small font-weight-bold">Kata Sandi</label>
                    <div class="input-group input-group-merge shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fe fe-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="userPassword" name="password" placeholder="Minimal 8 karakter">
                    </div>
                    <small class="form-text text-muted mt-2" id="passwordHelp" style="display:none;">
                        <i class="fe fe-alert-circle mr-1"></i>Kosongkan jika tidak ingin mengubah sandi.
                    </small>
                </div>
            </div>

            <!-- Right Column: Role Selection -->
            <div class="col-lg-7 pl-lg-4 border-left-lg">
                <h6 class="heading-small text-muted mb-3 text-uppercase">Pilih Peran & Hak Akses</h6>
                
                <div class="role-selector-container" style="max-height: 450px; overflow-y: auto; overflow-x: hidden;">
                    
                    <!-- Admin Utama -->
                    <label class="role-item d-flex align-items-center p-3 mb-2 rounded shadow-sm border bg-white cursor-pointer position-relative transition-all">
                        <input type="radio" name="role" value="admin" class="d-none role-input" required>
                        <div class="icon-box bg-primary-soft text-primary rounded-circle p-2 mr-3">
                            <i class="fe fe-shield fe-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold text-dark">Admin Utama</h6>
                            <small class="text-muted line-height-sm d-block">Akses penuh ke seluruh sistem (Level 0).</small>
                        </div>
                        <div class="check-mark ml-3 opacity-0 transition-all">
                            <i class="fe fe-check-circle text-primary font-weight-bold" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="active-border position-absolute w-100 h-100 rounded" style="top:0; left:0; border: 2px solid transparent; pointer-events: none;"></div>
                    </label>

                    <!-- Sekretariat DJSN -->
                    <label class="role-item d-flex align-items-center p-3 mb-2 rounded shadow-sm border bg-white cursor-pointer position-relative transition-all">
                        <input type="radio" name="role" value="DJSN" class="d-none role-input">
                        <div class="icon-box bg-success-soft text-success rounded-circle p-2 mr-3">
                            <i class="fe fe-check-square fe-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold text-dark">Sekretariat DJSN</h6>
                            <small class="text-muted line-height-sm d-block">Akses penuh level sekretariat (Level 1).</small>
                        </div>
                        <div class="check-mark ml-3 opacity-0 transition-all">
                            <i class="fe fe-check-circle text-success font-weight-bold" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="active-border position-absolute w-100 h-100 rounded" style="top:0; left:0; border: 2px solid transparent; pointer-events: none;"></div>
                    </label>

                    <!-- Tata Usaha -->
                    <label class="role-item d-flex align-items-center p-3 mb-2 rounded shadow-sm border bg-white cursor-pointer position-relative transition-all">
                        <input type="radio" name="role" value="Tata Usaha" class="d-none role-input">
                        <div class="icon-box bg-info-soft text-info rounded-circle p-2 mr-3">
                            <i class="fe fe-file-text fe-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold text-dark">Tata Usaha</h6>
                            <small class="text-muted line-height-sm d-block">Kelola Kegiatan & Surat Tugas (Level 2).</small>
                        </div>
                        <div class="check-mark ml-3 opacity-0 transition-all">
                            <i class="fe fe-check-circle text-info font-weight-bold" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="active-border position-absolute w-100 h-100 rounded" style="top:0; left:0; border: 2px solid transparent; pointer-events: none;"></div>
                    </label>

                    <!-- Persidangan -->
                    <label class="role-item d-flex align-items-center p-3 mb-2 rounded shadow-sm border bg-white cursor-pointer position-relative transition-all">
                        <input type="radio" name="role" value="Persidangan" class="d-none role-input">
                        <div class="icon-box bg-warning-soft text-warning rounded-circle p-2 mr-3">
                            <i class="fe fe-users fe-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold text-dark">Persidangan</h6>
                            <small class="text-muted line-height-sm d-block">Notulensi, Absensi & Tindak Lanjut (Level 3).</small>
                        </div>
                        <div class="check-mark ml-3 opacity-0 transition-all">
                            <i class="fe fe-check-circle text-warning font-weight-bold" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="active-border position-absolute w-100 h-100 rounded" style="top:0; left:0; border: 2px solid transparent; pointer-events: none;"></div>
                    </label>

                    <!-- Bagian Umum -->
                    <label class="role-item d-flex align-items-center p-3 mb-2 rounded shadow-sm border bg-white cursor-pointer position-relative transition-all">
                        <input type="radio" name="role" value="Bagian Umum" class="d-none role-input">
                        <div class="icon-box bg-danger-soft text-danger rounded-circle p-2 mr-3">
                            <i class="fe fe-folder fe-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold text-dark">Bagian Umum</h6>
                            <small class="text-muted line-height-sm d-block">Upload & Kelola Dokumentasi (Level 4).</small>
                        </div>
                        <div class="check-mark ml-3 opacity-0 transition-all">
                            <i class="fe fe-check-circle text-danger font-weight-bold" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="active-border position-absolute w-100 h-100 rounded" style="top:0; left:0; border: 2px solid transparent; pointer-events: none;"></div>
                    </label>

                    <!-- User Biasa -->
                    <label class="role-item d-flex align-items-center p-3 mb-2 rounded shadow-sm border bg-white cursor-pointer position-relative transition-all">
                        <input type="radio" name="role" value="User" class="d-none role-input">
                        <div class="icon-box bg-secondary text-dark rounded-circle p-2 mr-3">
                            <i class="fe fe-user fe-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold text-dark">User</h6>
                            <small class="text-muted line-height-sm d-block">Akses Terbatas (Hanya melihat).</small>
                        </div>
                        <div class="check-mark ml-3 opacity-0 transition-all">
                            <i class="fe fe-check-circle text-dark font-weight-bold" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="active-border position-absolute w-100 h-100 rounded" style="top:0; left:0; border: 2px solid transparent; pointer-events: none;"></div>
                    </label>

                     <!-- Anggota Dewan -->
                     <label class="role-item d-flex align-items-center p-3 mb-2 rounded shadow-sm border bg-white cursor-pointer position-relative transition-all">
                        <input type="radio" name="role" value="Dewan" class="d-none role-input">
                        <div class="icon-box bg-dark text-white rounded-circle p-2 mr-3">
                            <i class="fe fe-star fe-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold text-dark">Anggota Dewan</h6>
                            <small class="text-muted line-height-sm d-block">Khusus Anggota Dewan (Disposisi Only).</small>
                        </div>
                        <div class="check-mark ml-3 opacity-0 transition-all">
                            <i class="fe fe-check-circle text-dark font-weight-bold" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="active-border position-absolute w-100 h-100 rounded" style="top:0; left:0; border: 2px solid transparent; pointer-events: none;"></div>
                    </label>

                </div>
            </div>
          </div>

        </div>
        <div class="modal-footer bg-white border-top py-3 rounded-bottom-xl d-flex justify-content-between">
          <button type="button" class="btn btn-light text-muted rounded-pill px-4 font-weight-bold" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg border-0 font-weight-bold"><i class="fe fe-save mr-2"></i>Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});

// Update role selection styling when radio changes
$(document).on('change', 'input[name="role"]', function() {
    updateRoleStyles();
});

function updateRoleStyles() {
    $('.role-item').each(function() {
        const checkbox = $(this).find('input[name="role"]');
        const activeBorder = $(this).find('.active-border');
        const checkMark = $(this).find('.check-mark');
        const iconBox = $(this).find('.icon-box'); // Optional: Enhance icon background

        if (checkbox.is(':checked')) {
            $(this).addClass('bg-secondary active-role-item');
            checkMark.removeClass('opacity-0');
            
            // Set Color based on value
            let color = '#5e72e4'; // Default Admin
            if(checkbox.val() == 'DJSN') color = '#2dce89';
            if(checkbox.val() == 'Tata Usaha') color = '#11cdef';
            if(checkbox.val() == 'Persidangan') color = '#fb6340';
            if(checkbox.val() == 'Bagian Umum') color = '#f5365c';
            if(checkbox.val() == 'User') color = '#adb5bd';
            if(checkbox.val() == 'Dewan') color = '#212529';

            activeBorder.css('border-color', color);
            $(this).css('background-color', color + '10'); // 10% opacity hex
        } else {
            $(this).removeClass('bg-secondary active-role-item');
            checkMark.addClass('opacity-0');
            activeBorder.css('border-color', 'transparent');
            $(this).css('background-color', '#fff'); // Reset background
        }
    });
}

function editUser(id, name, email, role, divisi) {
  $('#userModalLabel').html('<i class="fe fe-edit mr-2"></i>Edit Akun Pengguna');
  $('#userForm').attr('action', '{{ url("master-data") }}/' + id);
  $('#formMethod').val('PUT');
  $('#userId').val(id);
  $('#userName').val(name);
  $('#userEmail').val(email);
  $('#userDivisi').val(divisi || '');
  
  // Set Role
  // Set Role
  $('input[name="role"][value="' + role + '"]').prop('checked', true);
  updateRoleStyles();

  $('#userPassword').attr('required', false);
  $('#passwordHelp').show();
  $('#userModal').modal('show');
}

$('#userModal').on('hidden.bs.modal', function () {
  $('#userModalLabel').html('<i class="fe fe-user-plus mr-2"></i>Tambah Akun Baru');
  $('#userForm').attr('action', '{{ route("master-data.store") }}');
  $('#formMethod').val('POST');
  $('#userForm')[0].reset();
  
  // Reset Roles
  // Reset Roles
  $('input[name="role"]').prop('checked', false);
  $('.role-item').removeClass('active-role-item');
  $('.active-border').css('border-color', 'transparent');
  $('.check-mark').addClass('opacity-0');
  updateRoleStyles();
  
  $('#userPassword').attr('required', true);
  $('#passwordHelp').hide();
});

// SweetAlert2 for Delete Confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.confirm-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Hapus Akun?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f5365c',
                cancelButtonColor: '#2dce89',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-premium-popup',
                    title: 'swal2-premium-title',
                    content: 'swal2-premium-content'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<style>
    /* Premium Table Styling */
    .table thead th {
        border-bottom: 2px solid #e9ecef;
        letter-spacing: 0.5px;
    }
    .text-xxs { font-size: 0.7rem; }
    .opacity-7 { opacity: 0.7; }
    
    .element-row:hover {
        background-color: #f8f9fe;
        transition: all 0.2s ease;
    }
    
    /* Role Badges */
    .badge-pill { padding: 0.5em 0.8em; letter-spacing: 0.3px; }
    
    /* Modal Styling */
    .bg-secondary-soft { background-color: #f6f9fc; }
    .border-left-lg { border-left: 1px solid #e9ecef; }
    @media (max-width: 991px) {
        .border-left-lg { border-left: none; margin-top: 1.5rem; }
    }
    
    /* Interactive Role Cards */
    /* Cleanup old grid styles if any */
    /* Custom Scrollbar for Role list */
    .role-selector-container::-webkit-scrollbar { width: 4px; }
    .role-selector-container::-webkit-scrollbar-track { background: transparent; }
    .role-selector-container::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 4px; }
    .role-selector-container::-webkit-scrollbar-thumb:hover { background: #ced4da; }
    
    .bg-primary-soft { background-color: rgba(94, 114, 228, 0.15); }
    .bg-success-soft { background-color: rgba(45, 206, 137, 0.15); }
    .bg-info-soft { background-color: rgba(17, 205, 239, 0.15); }
    .bg-warning-soft { background-color: rgba(251, 99, 64, 0.15); }
    .bg-danger-soft { background-color: rgba(245, 54, 92, 0.15); }
    
    .role-item:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(50,50,93,0.11),0 1px 3px rgba(0,0,0,0.08); }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Sortable for EACH Group
        var tables = document.querySelectorAll('.sortable-group');
        
        tables.forEach(function(el) {
            Sortable.create(el, {
                handle: '.handle', 
                animation: 150,
                // Prevent dragging between groups (optional, usually default behavior for multiple lists without 'group' option)
                onEnd: function (evt) {
                    var order = [];
                    // Only get rows within THIS specific tbody group
                    el.querySelectorAll('.element-row').forEach(function(row) {
                        order.push(row.getAttribute('data-id'));
                    });

                    // Send new order to server
                    // The server is now smart enough to only re-shuffle the order values of THESE IDs
                    fetch('{{ route("master-data.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            console.log('Group Order saved successfully');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    });

    // Delete Confirmation
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn'))) {
            e.preventDefault();
            var btn = e.target.classList.contains('delete-btn') ? e.target : e.target.closest('.delete-btn');
            var form = btn.closest('form');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pengguna akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f5365c',
                cancelButtonColor: '#8898aa',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
</script>
@endpush

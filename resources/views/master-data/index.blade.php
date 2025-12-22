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
                    <p class="text-muted mb-0">Kelola akun pengguna, peran, dan divisi</p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#userModal">
                        <i class="fe fe-plus-circle mr-2"></i> Tambah Akun
                    </button>
                </div>
            </div>

            <!-- Card Table -->
            <div class="card shadow border-0 rounded-lg overflow-hidden mb-5">
                <!-- Card Header -->
                <div class="card-header bg-primary text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <h5 class="card-title mb-0 text-white font-weight-bold">Daftar Pengguna Sistem</h5>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4" style="width: 5%;">No</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pengguna</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Akses</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tanggal Dibuat</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td class="pl-4 align-middle font-weight-bold text-secondary">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm mr-2 bg-light rounded-circle d-flex justify-content-center align-items-center border">
                                                <i class="fe fe-user text-secondary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $user->divisi ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <p class="text-sm font-weight-medium mb-0 text-secondary">{{ $user->email }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($user->role === 'admin')
                                            <span class="badge badge-pill badge-primary px-3 py-2">Admin</span>
                                        @elseif($user->role === 'Dewan')
                                            <span class="badge badge-pill badge-success px-3 py-2">Dewan</span>
                                        @elseif($user->role === 'DJSN')
                                            <span class="badge badge-pill badge-info text-white px-3 py-2">DJSN</span>
                                        @else
                                            <span class="badge badge-pill badge-secondary px-3 py-2">User</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-sm font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-sm btn-icon btn-outline-info mr-1" type="button" 
                                            onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->divisi }}')"
                                            data-toggle="tooltip" title="Edit">
                                            <span class="fe fe-edit"></span>
                                        </button>
                                        <form method="POST" action="{{ route('master-data.destroy', $user) }}" class="d-inline delete-form">
                                          @csrf
                                          @method('DELETE')
                                          <button type="button" class="btn btn-sm btn-icon btn-outline-danger confirm-delete"
                                            data-toggle="tooltip" title="Hapus">
                                            <span class="fe fe-trash-2"></span>
                                          </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="mb-3 text-secondary" style="font-size: 3rem;">
                                                <i class="fe fe-inbox"></i>
                                            </div>
                                            <h5 class="text-muted">Tidak ada data pengguna ditemukan</h5>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title font-weight-bold" id="userModalLabel">Tambah Akun Baru</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="userForm" method="POST" action="{{ route('master-data.store') }}">
        @csrf
        <input type="hidden" id="userId" name="user_id">
        <input type="hidden" id="formMethod" name="_method" value="POST">
        <div class="modal-body p-4">
          <div class="form-group">
            <label for="userName" class="small text-muted text-uppercase font-weight-bold">Nama Lengkap</label>
            <div class="input-group input-group-merge">
                <input type="text" class="form-control" id="userName" name="name" required placeholder="Contoh: Budi Santoso">
                <div class="input-group-append">
                    <div class="input-group-text"><i class="fe fe-user"></i></div>
                </div>
            </div>
          </div>
          <div class="form-group">
            <label for="userEmail" class="small text-muted text-uppercase font-weight-bold">Alamat Email</label>
             <div class="input-group input-group-merge">
                <input type="email" class="form-control" id="userEmail" name="email" required placeholder="budi@example.com">
                 <div class="input-group-append">
                    <div class="input-group-text"><i class="fe fe-mail"></i></div>
                </div>
            </div>
          </div>
          <div class="form-group">
            <label for="userPassword" class="small text-muted text-uppercase font-weight-bold">Kata Sandi</label>
            <div class="input-group input-group-merge">
                <input type="password" class="form-control" id="userPassword" name="password" placeholder="Minimal 8 karakter">
                 <div class="input-group-append">
                    <div class="input-group-text"><i class="fe fe-lock"></i></div>
                </div>
            </div>
            <small class="form-text text-muted mt-2" id="passwordHelp">Kosongkan jika tidak ingin mengubah kata sandi (saat edit).</small>
          </div>
          
          <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                    <label for="userRole" class="small text-muted text-uppercase font-weight-bold">Peran (Role)</label>
                    <select class="form-control custom-select" id="userRole" name="role" required>
                      <option value="user">User</option>
                      <option value="admin">Admin</option>
                      <option value="Dewan">Dewan</option>
                      <option value="DJSN">DJSN</option>
                    </select>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="form-group">
                    <label for="userDivisi" class="small text-muted text-uppercase font-weight-bold">Divisi / Jabatan</label>
                    <input type="text" class="form-control" id="userDivisi" name="divisi" placeholder="Contoh: Kepala Bagian">
                  </div>
              </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4"><i class="fe fe-save mr-2"></i>Simpan</button>
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

function editUser(id, name, email, role, divisi) {
  $('#userModalLabel').text('Edit Akun Pengguna');
  $('#userForm').attr('action', '{{ url("master-data") }}/' + id);
  $('#formMethod').val('PUT');
  $('#userId').val(id);
  $('#userName').val(name);
  $('#userEmail').val(email);
  $('#userRole').val(role);
  $('#userDivisi').val(divisi || '');
  $('#userPassword').attr('required', false);
  $('#passwordHelp').show();
  $('#userModal').modal('show');
}

$('#userModal').on('hidden.bs.modal', function () {
  $('#userModalLabel').text('Tambah Akun Baru');
  $('#userForm').attr('action', '{{ route("master-data.store") }}');
  $('#formMethod').val('POST');
  $('#userForm')[0].reset();
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
                text: "Data akun ini akan dihapus permanen dan tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
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
    .text-xxs { font-size: 0.75rem; }
    .opacity-7 { opacity: 0.7; }
    .table thead th { border-bottom-width: 1px; }
    .avatar-sm { width: 36px; height: 36px; font-size: 0.875rem; }
    .input-group-merge .form-control:focus + .input-group-append .input-group-text,
    .input-group-merge .form-control:focus {
        border-color: #2a5298;
    }
    .badge-pill { padding-right: 0.8em; padding-left: 0.8em; }
</style>
@endpush

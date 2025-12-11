@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
<div class="row justify-content-center">
  <div class="col-12">
    <div class="row align-items-center my-4">
      <div class="col">
        <h2 class="h3 mb-0 page-title">Master Data</h2>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#userModal">
          <span class="fe fe-plus fe-12 mr-2"></span>Tambah Akun
        </button>
      </div>
    </div>
    <!-- table -->
    <div class="card shadow">
      <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Divisi</th>
                  <th>Email</th>
                  <th>Akses</th>
                  <th>Tanggal Dibuat</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($users as $user)
                <tr>
                  <td>{{ $user->id }}</td>
                  <td>
                    <p class="mb-0 text-muted"><strong>{{ $user->name }}</strong></p>
                    <small class="mb-0 text-muted">{{ $user->divisi ?? 'Tidak ada divisi' }}</small>
                  </td>
                  <td>
                    <p class="mb-0 text-muted">{{ $user->email }}</p>
                  </td>
                  <td>
                      @if($user->role === 'admin')
                        <span class="badge badge-primary">Admin</span>
                      @elseif($user->role === 'Dewan')
                        <span class="badge badge-success">Dewan</span>
                      @elseif($user->role === 'DJSN')
                        <span class="badge badge-info text-white">DJSN</span>
                      @else
                        <span class="badge badge-secondary">User</span>
                      @endif
                  </td>
                  <td class="text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                  <td>
                    <button class="btn btn-sm btn-outline-success" type="button" onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->divisi }}')">
                      <span class="fe fe-edit fe-12 mr-1"></span> Edit
                    </button>
                    <br></br>
                    <form method="POST" action="{{ route('master-data.destroy', $user) }}" class="d-inline delete-form">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn btn-sm btn-outline-danger confirm-delete">
                        <span class="fe fe-trash-2 fe-12 mr-1"></span> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
              </tbody>
            </table>
        </div>
      </div>
    </div>
  </div> <!-- .col-12 -->
</div> <!-- .row -->

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Tambah Akun</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="userForm" method="POST" action="{{ route('master-data.store') }}">
        @csrf
        <input type="hidden" id="userId" name="user_id">
        <input type="hidden" id="formMethod" name="_method" value="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="userName">Nama</label>
            <input type="text" class="form-control" id="userName" name="name" required>
          </div>
          <div class="form-group">
            <label for="userEmail">Email</label>
            <input type="email" class="form-control" id="userEmail" name="email" required>
          </div>
          <div class="form-group">
            <label for="userPassword">Kata Sandi</label>
            <input type="password" class="form-control" id="userPassword" name="password">
            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah kata sandi (untuk edit)</small>
          </div>
          <div class="form-group">
            <label for="userRole">Peran</label>
            <select class="form-control" id="userRole" name="role" required>
              <option value="user">User</option>
              <option value="admin">Admin</option>
              <option value="Dewan">Dewan</option>
              <option value="DJSN">DJSN</option>
            </select>
          </div>
          <div class="form-group">
            <label for="userDivisi">Divisi</label>
            <input type="text" class="form-control" id="userDivisi" name="divisi" placeholder="Masukkan nama divisi">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function editUser(id, name, email, role, divisi) {
  $('#userModalLabel').text('Edit Akun');
  $('#userForm').attr('action', '{{ url("master-data") }}/' + id);
  $('#formMethod').val('PUT');
  $('#userId').val(id);
  $('#userName').val(name);
  $('#userEmail').val(email);
  $('#userRole').val(role);
  $('#userDivisi').val(divisi || '');
  $('#userPassword').attr('required', false);
  $('#userModal').modal('show');
}

$('#userModal').on('hidden.bs.modal', function () {
  $('#userModalLabel').text('Tambah Akun');
  $('#userForm').attr('action', '{{ route("master-data.store") }}');
  $('#formMethod').val('POST');
  $('#userForm')[0].reset();
  $('#userPassword').attr('required', true);
});

// SweetAlert2 for Delete Confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.confirm-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data akun ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush


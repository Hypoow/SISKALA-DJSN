@extends('layouts.app')

@section('title', 'Master Data Staf')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header -->
            <div class="row align-items-center mb-4 mt-2">
                <div class="col">
                    <h2 class="h3 font-weight-bold mb-0 text-dark">Data Staf Pendamping</h2>
                    <p class="text-muted mb-0">Kelola daftar Staf Sekretariat DJSN dan Tenaga Ahli (TA)</p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4" data-toggle="modal" data-target="#staffModal">
                        <i class="fe fe-plus-circle mr-2"></i> Tambah Staf
                    </button>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-pills nav-fill flex-column flex-sm-row mb-4 bg-white rounded shadow-sm p-2">
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 font-weight-bold text-muted" href="{{ route('master-data.index') }}">
                        <i class="fe fe-users mr-2"></i>Data Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active font-weight-bold shadow-sm" href="{{ route('master-data.staff.index') }}">
                        <i class="fe fe-briefcase mr-2"></i>Data Staf Pendamping
                    </a>
                </li>
            </ul>

            <div class="row">
                <!-- Sekretariat DJSN -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-5">
                        <div class="card-header border-0 py-3 px-4" style="background: linear-gradient(87deg, #2dce89 100%) !important;">
                            <h5 class="card-title mb-0 text-white font-weight-bold">
                                <i class="fe fe-check-square mr-2"></i>Sekretariat DJSN
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-items-center mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4" style="width: 10%;">No</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Lengkap</th>
                                            <th class="text-center" style="width: 15%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($staffSekretariat as $staff)
                                        <tr class="element-row">
                                            <td class="pl-4 align-middle font-weight-bold text-secondary">{{ $loop->iteration }}</td>
                                            <td class="align-middle">
                                                <span class="text-sm font-weight-bold text-dark">{{ $staff->name }}</span>
                                            </td>
                                            <td class="align-middle text-right pr-4">
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-icon-only text-muted btn-white shadow-sm action-btn" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                      <i class="fe fe-more-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="editStaff({{ $staff->id }}, '{{ $staff->name }}', 'sekretariat')">
                                                            <i class="fe fe-edit-2 mr-2 text-warning"></i> Edit
                                                        </a>
                                                        <form action="{{ route('master-data.staff.destroy', $staff->id) }}" method="POST" class="d-inline delete-form">
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
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted small">Belum ada data staf Sekretariat.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tenaga Ahli -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-5">
                         <div class="card-header border-0 py-3 px-4" style="background: linear-gradient(87deg, #11cdef 100%) !important;">
                            <h5 class="card-title mb-0 text-white font-weight-bold">
                                <i class="fe fe-briefcase mr-2"></i>Tenaga Ahli (TA)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-items-center mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4" style="width: 10%;">No</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Lengkap</th>
                                            <th class="text-center" style="width: 15%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($staffTA as $staff)
                                        <tr class="element-row">
                                            <td class="pl-4 align-middle font-weight-bold text-secondary">{{ $loop->iteration }}</td>
                                            <td class="align-middle">
                                                <span class="text-sm font-weight-bold text-dark">{{ $staff->name }}</span>
                                            </td>
                                            <td class="align-middle text-right pr-4">
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-icon-only text-muted btn-white shadow-sm action-btn" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                      <i class="fe fe-more-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="editStaff({{ $staff->id }}, '{{ $staff->name }}', 'ta')">
                                                            <i class="fe fe-edit-2 mr-2 text-warning"></i> Edit
                                                        </a>
                                                        <form action="{{ route('master-data.staff.destroy', $staff->id) }}" method="POST" class="d-inline delete-form">
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
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted small">Belum ada data Tenaga Ahli.</td>
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
    </div>
</div>

<!-- Modal Tambah/Edit Staff -->
<div class="modal fade" id="staffModal" tabindex="-1" role="dialog" aria-labelledby="staffModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0 rounded-xl">
      <div class="modal-header bg-gradient-primary text-white py-3">
        <h5 class="modal-title font-weight-bold pl-2" id="staffModalLabel">
            <i class="fe fe-user-plus mr-2"></i>Tambah Staf Baru
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="staffForm" method="POST" action="{{ route('master-data.staff.store') }}">
        @csrf
        <input type="hidden" id="staffId" name="staff_id">
        <input type="hidden" id="formMethod" name="_method" value="POST">
        <div class="modal-body p-4 bg-secondary-soft">
            <div class="form-group mb-3">
                <label for="staffName" class="form-control-label small font-weight-bold">Nama Lengkap</label>
                <div class="input-group input-group-merge shadow-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fe fe-user"></i></span>
                    </div>
                    <input type="text" class="form-control" id="staffName" name="name" required placeholder="Masukkan Nama Staf">
                </div>
            </div>

            <div class="form-group mb-0">
                <label class="form-control-label small font-weight-bold mb-2">Jenis Staf</label>
                <div class="d-flex">
                    <div class="custom-control custom-radio mb-3 mr-4">
                        <input name="type" class="custom-control-input" id="typeSekretariat" type="radio" value="sekretariat" checked>
                        <label class="custom-control-label font-weight-bold text-dark" for="typeSekretariat">
                            Sekretariat DJSN
                        </label>
                    </div>
                    <div class="custom-control custom-radio mb-3">
                        <input name="type" class="custom-control-input" id="typeTA" type="radio" value="ta">
                        <label class="custom-control-label font-weight-bold text-dark" for="typeTA">
                            Tenaga Ahli (TA)
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-white border-top py-3 rounded-bottom-xl d-flex justify-content-between">
            <button type="button" class="btn btn-light text-muted rounded-pill px-4 font-weight-bold" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg border-0 font-weight-bold"><i class="fe fe-save mr-2"></i>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    function editStaff(id, name, type) {
        $('#staffModalLabel').html('<i class="fe fe-edit mr-2"></i>Edit Data Staf');
        $('#staffForm').attr('action', '{{ url("master-data/staff") }}/' + id);
        $('#formMethod').val('PUT');
        $('#staffId').val(id);
        $('#staffName').val(name);
        
        if (type === 'sekretariat') {
            $('#typeSekretariat').prop('checked', true);
        } else {
            $('#typeTA').prop('checked', true);
        }
        
        $('#staffModal').modal('show');
    }

    $('#staffModal').on('hidden.bs.modal', function () {
        $('#staffModalLabel').html('<i class="fe fe-user-plus mr-2"></i>Tambah Staf Baru');
        $('#staffForm').attr('action', '{{ route("master-data.staff.store") }}');
        $('#formMethod').val('POST');
        $('#staffForm')[0].reset();
        $('#typeSekretariat').prop('checked', true);
    });

    // Reuse existing Delete SweetAlert logic if available, or re-attach
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn'))) {
            e.preventDefault();
            var btn = e.target.classList.contains('delete-btn') ? e.target : e.target.closest('.delete-btn');
            var form = btn.closest('form');
            
            Swal.fire({
                title: 'Hapus Staf?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
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

    // Check for success messages from session
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>

<style>
    .bg-secondary-soft { background-color: #f6f9fc; }
    .text-xxs { font-size: 0.7rem; }
    .opacity-7 { opacity: 0.7; }
    .element-row:hover { background-color: #f8f9fe; transition: all 0.2s ease; }
</style>
@endpush

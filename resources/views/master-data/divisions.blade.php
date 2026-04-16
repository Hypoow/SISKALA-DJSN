@extends('layouts.app')

@section('title', 'Builder Struktur Role')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
            <div>
                <span class="text-uppercase text-muted small font-weight-bold d-block mb-1">Master Data</span>
                <h2 class="h3 font-weight-bold text-dark mb-2">Builder Struktur Role</h2>
                <p class="text-muted mb-0">Kelola kelompok akun, komisi Dewan, unit pendamping, dan master jabatan dari satu layar yang lebih mudah di-drag dan diurutkan.</p>
            </div>
            <div class="mt-3 mt-lg-0">
                <button type="button" class="btn btn-outline-primary rounded-pill px-4 mr-2" data-toggle="modal" data-target="#positionModal">
                    <i class="fe fe-award mr-2"></i>Tambah Jabatan
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-toggle="modal" data-target="#divisionModal">
                    <i class="fe fe-plus-circle mr-2"></i>Tambah Unit
                </button>
            </div>
        </div>

        @include('master-data.partials.tabs', ['active' => 'divisions'])

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 rounded-xl h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted font-weight-bold d-block mb-2">Unit Kerja</small>
                        <h3 class="font-weight-bold text-dark mb-1">{{ $divisions->count() }}</h3>
                        <p class="text-muted mb-0">Dipakai untuk penempatan akun dan pengelompokan struktur.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 rounded-xl h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted font-weight-bold d-block mb-2">Komisi Dewan</small>
                        <h3 class="font-weight-bold text-dark mb-1">{{ $divisions->where('is_commission', true)->count() }}</h3>
                        <p class="text-muted mb-0">Komisi aktif bisa langsung dipakai sebagai scope untuk Dewan, Persidangan, dan TA.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 rounded-xl h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted font-weight-bold d-block mb-2">Master Jabatan</small>
                        <h3 class="font-weight-bold text-dark mb-1">{{ $positions->count() }}</h3>
                        <p class="text-muted mb-0">Hak akses default, disposisi, dan label laporan diturunkan dari sini.</p>
                    </div>
                </div>
            </div>
        </div>

        @include('master-data.partials.division-builder-units')
        @include('master-data.partials.division-builder-positions')
        @include('master-data.partials.division-builder-modals')
    </div>
@endsection

@push('styles')
    <style>
        .rounded-xl { border-radius: 1.3rem; }
        .builder-column { background: #f9fbff; border: 1px solid #e4edf8; border-radius: 1.2rem; padding: 1rem; height: 100%; }
        .builder-column-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        .builder-column-header h5 { font-weight: 700; color: #0f172a; margin-bottom: .15rem; }
        .builder-column-header small { color: #7b8797; }
        .builder-dropzone { min-height: 130px; display: flex; flex-direction: column; gap: .9rem; }
        .builder-item { background: #fff; border: 1px solid #e5edf7; border-radius: 1rem; padding: 1rem; box-shadow: 0 12px 24px -26px rgba(15, 23, 42, .55); }
        .builder-empty { border: 1px dashed #cfd9e6; border-radius: 1rem; padding: 1rem; text-align: center; color: #7b8797; font-size: .9rem; }
        .drag-handle { color: #9aa6b2; cursor: move; }
        .badge-primary-soft { background: rgba(22, 47, 114, .08); color: #162f72; }
        .badge-info-soft { background: rgba(0, 123, 255, .08); color: #0b61c2; }
        .badge-warning-soft { background: rgba(255, 193, 7, .15); color: #9a6b00; }
        .badge-success-soft { background: rgba(40, 167, 69, .12); color: #1f7a38; }
        .sortable-ghost { opacity: .45; }
    </style>
@endpush
@endif

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        function resetDivisionModal() {
            $('#divisionModalLabel').text('Tambah Unit');
            $('#divisionForm').attr('action', '{{ route("master-data.divisions.store") }}');
            $('#divisionMethod').val('POST');
            $('#divisionForm')[0].reset();
            $('#divisionOrder').val(0);
            $('#divisionIsCommission').prop('checked', false);
            syncDivisionCommission();
        }

        function openDivisionModalForGroup(groupKey) {
            resetDivisionModal();
            $('#divisionStructureGroup').val(groupKey);
            $('#divisionModal').modal('show');
        }

        function editDivision(id, name, shortLabel, structureGroup, accessProfile, commissionCode, isCommission, description, order) {
            resetDivisionModal();
            $('#divisionModalLabel').text('Edit Unit');
            $('#divisionForm').attr('action', '{{ url("master-data/divisions") }}/' + id);
            $('#divisionMethod').val('PUT');
            $('#divisionName').val(name);
            $('#divisionShortLabel').val(shortLabel ?? '');
            $('#divisionStructureGroup').val(structureGroup ?? '');
            $('#divisionAccessProfile').val(accessProfile ?? '');
            $('#divisionCommissionCode').val(commissionCode ?? '');
            $('#divisionIsCommission').prop('checked', Boolean(isCommission));
            $('#divisionDescription').val(description ?? '');
            $('#divisionOrder').val(order ?? 0);
            syncDivisionCommission();
            $('#divisionModal').modal('show');
        }

        function syncDivisionCommission() {
            const isCommission = $('#divisionIsCommission').is(':checked');
            $('#divisionCommissionGroup').toggle(!isCommission);
            $('#divisionCommissionAuto').toggle(isCommission);
        }

        function resetPositionModal() {
            $('#positionModalLabel').text('Tambah Jabatan');
            $('#positionForm').attr('action', '{{ route("master-data.positions.store") }}');
            $('#positionMethod').val('POST');
            $('#positionForm')[0].reset();
            $('#positionOrder').val(0);
            $('#positionReceivesDisposition').val('1');
        }

        function openPositionModalForGroup(groupKey) {
            resetPositionModal();
            $('#positionStructureGroup').val(groupKey);
            $('#positionModal').modal('show');
        }

        function editPosition(id, name, structureGroup, accessProfile, order, receivesDisposition, dispositionGroupLabel, reportTargetLabel) {
            resetPositionModal();
            $('#positionModalLabel').text('Edit Jabatan');
            $('#positionForm').attr('action', '{{ url("master-data/positions") }}/' + id);
            $('#positionMethod').val('PUT');
            $('#positionName').val(name);
            $('#positionStructureGroup').val(structureGroup ?? '');
            $('#positionAccessProfile').val(accessProfile ?? '');
            $('#positionOrder').val(order ?? 0);
            $('#positionReceivesDisposition').val(receivesDisposition === true || receivesDisposition === 1 || receivesDisposition === '1' ? '1' : '0');
            $('#positionDispositionGroupLabel').val(dispositionGroupLabel ?? '');
            $('#positionReportTargetLabel').val(reportTargetLabel ?? '');
            $('#positionModal').modal('show');
        }

        function serializeBoard(selector) {
            const payload = [];
            document.querySelectorAll(selector).forEach(function (dropzone) {
                const group = dropzone.getAttribute('data-structure-group');
                dropzone.querySelectorAll('.builder-item').forEach(function (item) {
                    payload.push({ id: item.getAttribute('data-id'), structure_group: group });
                });
            });
            return payload;
        }

        document.addEventListener('DOMContentLoaded', function () {
            $('#divisionIsCommission').on('change', syncDivisionCommission);
            $('#divisionModal').on('hidden.bs.modal', resetDivisionModal);
            $('#positionModal').on('hidden.bs.modal', resetPositionModal);
            syncDivisionCommission();

            document.querySelectorAll('.division-dropzone').forEach(function (element) {
                Sortable.create(element, {
                    group: 'division-builder',
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function () {
                        fetch('{{ route("master-data.divisions.reorder") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ order: serializeBoard('.division-dropzone') })
                        });
                    }
                });
            });

            document.querySelectorAll('.position-dropzone').forEach(function (element) {
                Sortable.create(element, {
                    group: 'position-builder',
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function () {
                        fetch('{{ route("master-data.positions.reorder") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ order: serializeBoard('.position-dropzone') })
                        });
                    }
                });
            });

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.delete-btn');
                if (!button) return;
                event.preventDefault();
                const form = button.closest('form');
                Swal.fire({
                    title: 'Hapus data ini?',
                    text: 'Pastikan item ini tidak sedang dipakai oleh akun lain.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f5365c',
                    cancelButtonColor: '#8898aa',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush

@if(false)

@extends('layouts.app')

@section('title', 'Daftar Unit Kerja & Jabatan')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">

                <!-- Page Header -->
                <div class="row align-items-center mb-4 mt-2">
                    <div class="col">
                        <h2 class="h3 font-weight-bold mb-0 text-dark">Daftar Unit Kerja & Jabatan</h2>
                        <p class="text-muted mb-0">Kelola struktur per divisi dan perorangan, default akses, komisi, dan
                            disposisi dalam satu tempat yang tetap bisa diurutkan dengan drag & drop.</p>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-info shadow-sm rounded-pill px-4 mr-2"
                            data-toggle="modal" data-target="#positionModal">
                            <i class="fe fe-award mr-2"></i> Tambah Jabatan
                        </button>
                        <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4" data-toggle="modal"
                            data-target="#divisionModal">
                            <i class="fe fe-plus-circle mr-2"></i> Tambah Unit Kerja
                        </button>
                    </div>
                </div>

                @include('master-data.partials.tabs', ['active' => 'divisions'])

                <!-- Card Table -->
                <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-5">
                    <div class="card-header border-0 py-4 px-4"
                        style="background: linear-gradient(87deg, #11cdef 100%) !important;">
                        <h5 class="card-title mb-0 text-white font-weight-bold">
                            <i class="fe fe-layers mr-2"></i>Struktur Organisasi
                        </h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-items-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%;"></th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4">
                                            Urutan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                            Unit Kerja</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Kategori Struktural</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Profil Akses</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Scope Komisi</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Anggota</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                                            style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                                @foreach($divisions->groupBy('category') as $category => $items)
                                    <tbody class="sortable-division-group" data-category="{{ $category }}">
                                        <tr class="bg-light">
                                            <td colspan="8"
                                                class="py-2 pl-4 font-weight-bold text-info text-uppercase text-xs letter-spacing-1">
                                                <i class="fe fe-folder mr-2"></i>{{ $category }}
                                            </td>
                                        </tr>
                                        @foreach($items as $div)
                                            <tr class="division-row" data-id="{{ $div->id }}">
                                                <td class="align-middle text-center cursor-move handle">
                                                    <i class="fe fe-menu text-muted"></i>
                                                </td>
                                                <td class="pl-4 align-middle font-weight-bold text-secondary">{{ $loop->iteration }}
                                                </td>
                                                <td class="align-middle">
                                                    <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $div->name }}</h6>
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $badgeClass = 'badge-info';
                                                        if ($div->category == 'Ketua DJSN')
                                                            $badgeClass = 'badge-primary';
                                                        if ($div->category == 'Sekretariat DJSN')
                                                            $badgeClass = 'badge-success';
                                                    @endphp
                                                    <span class="badge badge-pill {{ $badgeClass }}-soft text-xs px-3">
                                                        {{ $div->category }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    @if($div->access_profile)
                                                        <span
                                                            class="badge badge-pill badge-primary-soft">{{ \App\Models\User::accessProfileLabel($div->access_profile) }}</span>
                                                    @else
                                                        <span class="badge badge-pill badge-secondary">Perlu dipilih</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <span
                                                        class="text-sm text-dark">{{ \App\Models\User::commissionLabel($div->commission_code) }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="badge badge-secondary rounded-circle shadow-sm"
                                                        style="width: 24px; height: 24px; line-height: 18px;">
                                                        {{ $div->users_count }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="dropdown">
                                                        <a class="btn btn-sm btn-icon-only text-muted btn-white shadow-sm action-btn"
                                                            href="#" role="button" data-toggle="dropdown">
                                                            <i class="fe fe-more-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                onclick="editDivision({{ $div->id }}, @js($div->name), @js($div->category), @js($div->access_profile), @js($div->commission_code), {{ $div->order }})">
                                                                <i class="fe fe-edit-2 mr-2 text-warning"></i> Edit
                                                            </a>
                                                            <form action="{{ route('master-data.divisions.destroy', $div->id) }}"
                                                                method="POST" class="d-inline delete-form">
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
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-5">
                    <div class="card-header border-0 py-4 px-4"
                        style="background: linear-gradient(87deg, #5e72e4 100%) !important;">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
                            <div>
                                <h5 class="card-title mb-0 text-white font-weight-bold">
                                    <i class="fe fe-award mr-2"></i>Master Jabatan
                                </h5>
                                <small class="text-white-50">Atur default akun yang tampil di checklist disposisi dan label
                                    target laporannya.</small>
                            </div>
                            <span class="badge badge-light text-primary mt-3 mt-md-0">{{ $positions->count() }}
                                jabatan</span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-items-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%;"></th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4">
                                            Urutan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                            Jabatan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Struktur</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Default Akses</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Default Disposisi</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Label Target Laporan</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            Dipakai</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                                            style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="sortable-position-group">
                                    @forelse($positions as $position)
                                        <tr class="position-row" data-id="{{ $position->id }}">
                                            <td class="align-middle text-center cursor-move handle-position">
                                                <i class="fe fe-menu text-muted"></i>
                                            </td>
                                            <td class="pl-4 align-middle font-weight-bold text-secondary">{{ $loop->iteration }}
                                            </td>
                                            <td class="align-middle">
                                                <h6 class="mb-1 text-sm font-weight-bold text-dark">{{ $position->name }}</h6>
                                                <small class="text-muted d-block">Kode internal: {{ $position->code }}</small>
                                                @if($position->disposition_group_label)
                                                    <span
                                                        class="badge badge-pill badge-primary-soft mt-2">{{ $position->disposition_group_label }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <span
                                                    class="badge badge-pill badge-info-soft">{{ \App\Models\User::structureGroupLabel($position->structure_group) }}</span>
                                            </td>
                                            <td class="align-middle">
                                                @if($position->access_profile)
                                                    <span
                                                        class="badge badge-pill badge-primary-soft">{{ \App\Models\User::accessProfileLabel($position->access_profile) }}</span>
                                                @else
                                                    <span class="badge badge-pill badge-secondary">Mengikuti unit kerja</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($position->receives_disposition === true)
                                                    <span class="badge badge-pill badge-success-soft">Ya, tampil di checklist</span>
                                                @elseif($position->receives_disposition === false)
                                                    <span class="badge badge-pill badge-danger-soft">Tidak ditampilkan</span>
                                                @else
                                                    <span class="badge badge-pill badge-secondary">Perlu dipilih</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <span
                                                    class="text-sm text-dark">{{ $position->report_target_label ?: '-' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge badge-secondary rounded-circle shadow-sm"
                                                    style="width: 24px; height: 24px; line-height: 18px;">
                                                    {{ $position->users_count }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-icon-only text-muted btn-white shadow-sm action-btn"
                                                        href="#" role="button" data-toggle="dropdown">
                                                        <i class="fe fe-more-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="editPosition(
                                                                    {{ $position->id }},
                                                                    @js($position->name),
                                                                    @js($position->structure_group),
                                                                    @js($position->access_profile),
                                                                    {{ $position->order }},
                                                                    @js($position->getRawOriginal('receives_disposition')),
                                                                    @js($position->disposition_group_label),
                                                                    @js($position->report_target_label)
                                                                )">
                                                            <i class="fe fe-edit-2 mr-2 text-warning"></i> Edit
                                                        </a>
                                                        <form
                                                            action="{{ route('master-data.positions.destroy', $position->id) }}"
                                                            method="POST" class="d-inline delete-form">
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
                                            <td colspan="9" class="text-center py-5 text-muted">Belum ada data jabatan.</td>
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

    <!-- Modal Tambah/Edit Division -->
    <div class="modal fade" id="divisionModal" tabindex="-1" role="dialog" aria-labelledby="divisionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0 rounded-xl">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title font-weight-bold" id="divisionModalLabel">
                        <i class="fe fe-plus mr-2"></i>Tambah Unit Kerja Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="divisionForm" method="POST" action="{{ route('master-data.divisions.store') }}">
                    @csrf
                    <input type="hidden" id="divisionId" name="division_id">
                    <input type="hidden" id="divisionMethod" name="_method" value="POST">
                    <div class="modal-body p-4">
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Nama Unit Kerja</label>
                            <input type="text" class="form-control shadow-sm" id="divisionName" name="name" required
                                placeholder="Contoh: Sekretariat DJSN / Komisi PME">
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Kategori Struktural</label>
                            <select class="form-control shadow-sm" id="divisionCategory" name="category" required>
                                <option value="Ketua DJSN">Ketua DJSN</option>
                                <option value="Komisi">Komisi (Anggota Dewan)</option>
                                <option value="Sekretariat DJSN">Sekretariat DJSN (Staf/Pimpinan)</option>
                            </select>
                            <small class="text-muted mt-2 d-block">Kategori menentukan pengelompokan di laporan visual dan
                                kalender.</small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Default Profil Akses</label>
                            <select class="form-control shadow-sm" id="divisionAccessProfile" name="access_profile"
                                required>
                                <option value="" disabled selected>Pilih profil akses unit kerja</option>
                                @foreach($accessProfiles as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block">Tentukan akses utama unit ini, misalnya Dewan, Set.DJSN,
                                Tata Usaha, Persidangan, ProtHum, Keuangan, atau Tenaga Ahli.</small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Scope Komisi</label>
                            <select class="form-control shadow-sm" id="divisionCommissionCode" name="commission_code">
                                <option value="">Semua komisi / tidak spesifik komisi</option>
                                @foreach($commissionOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block">Isi jika divisi hanya mendampingi komisi tertentu,
                                misalnya Persidangan PME atau Tenaga Ahli Komjakum.</small>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-control-label small font-weight-bold">Urutan</label>
                            <input type="number" class="form-control shadow-sm" id="divisionOrder" name="order" value="0">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-info rounded-pill px-5 shadow-sm font-weight-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="positionModal" tabindex="-1" role="dialog" aria-labelledby="positionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0 rounded-xl">
                <div class="modal-header text-white" style="background: linear-gradient(87deg, #5e72e4 100%);">
                    <h5 class="modal-title font-weight-bold" id="positionModalLabel">
                        <i class="fe fe-award mr-2"></i>Tambah Jabatan Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="positionForm" method="POST" action="{{ route('master-data.positions.store') }}">
                    @csrf
                    <input type="hidden" id="positionMethod" name="_method" value="POST">
                    <div class="modal-body p-4">
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Nama Jabatan</label>
                            <input type="text" class="form-control shadow-sm" id="positionName" name="name" required
                                placeholder="Contoh: Kepala Sub Bagian Umum">
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Kelompok Struktur</label>
                            <select class="form-control shadow-sm" id="positionStructureGroup" name="structure_group"
                                required>
                                <option value="" disabled selected>Pilih kelompok struktur</option>
                                @foreach($structureGroups as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block">Contoh: jabatan Dewan masuk grup Dewan, sedangkan
                                Sekretaris/Kabag/Kasubag bisa masuk grup Set.DJSN.</small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Default Profil Akses</label>
                            <select class="form-control shadow-sm" id="positionAccessProfile" name="access_profile">
                                <option value="">Mengikuti profil unit kerja</option>
                                @foreach($accessProfiles as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block">Pilih profil khusus jika jabatan ini memang perlu akses
                                berbeda. Jika tidak, jabatan akan memakai profil dari unit kerjanya.</small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Default Menerima Disposisi</label>
                            <select class="form-control shadow-sm" id="positionReceivesDisposition"
                                name="receives_disposition" required>
                                <option value="" disabled selected>Pilih status disposisi</option>
                                <option value="1">Ya, tampil di checklist disposisi</option>
                                <option value="0">Tidak ditampilkan di checklist disposisi</option>
                            </select>
                            <small class="text-muted mt-2 d-block">Setiap jabatan sekarang ditentukan secara eksplisit agar
                                checklist disposisi selalu konsisten.</small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Label Grup Disposisi (Opsional)</label>
                            <input type="text" class="form-control shadow-sm" id="positionDispositionGroupLabel"
                                name="disposition_group_label" placeholder="Contoh: Sekretariat DJSN">
                            <small class="text-muted mt-2 d-block">Dipakai untuk mengelompokkan checklist disposisi dan
                                tampilan detail kegiatan.</small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-control-label small font-weight-bold">Label Target Laporan (Opsional)</label>
                            <input type="text" class="form-control shadow-sm" id="positionReportTargetLabel"
                                name="report_target_label" placeholder="Contoh: Sekretaris DJSN">
                            <small class="text-muted mt-2 d-block">Dipakai untuk format “kegiatan ditujukan untuk” pada
                                laporan H-1.</small>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-control-label small font-weight-bold">Urutan</label>
                            <input type="number" class="form-control shadow-sm" id="positionOrder" name="order" value="0">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-primary rounded-pill px-5 shadow-sm font-weight-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        function editDivision(id, name, category, accessProfile, commissionCode, order) {
            $('#divisionModalLabel').html('<i class="fe fe-edit mr-2"></i>Edit Unit Kerja');
            $('#divisionForm').attr('action', '{{ url("master-data/divisions") }}/' + id);
            $('#divisionMethod').val('PUT');
            $('#divisionId').val(id);
            $('#divisionName').val(name);
            $('#divisionCategory').val(category);
            $('#divisionAccessProfile').val(accessProfile ?? '');
            $('#divisionCommissionCode').val(commissionCode ?? '');
            $('#divisionOrder').val(order);
            $('#divisionModal').modal('show');
        }

        function editPosition(id, name, structureGroup, accessProfile, order, receivesDisposition, dispositionGroupLabel, reportTargetLabel) {
            $('#positionModalLabel').html('<i class="fe fe-edit mr-2"></i>Edit Jabatan');
            $('#positionForm').attr('action', '{{ url("master-data/positions") }}/' + id);
            $('#positionMethod').val('PUT');
            $('#positionName').val(name);
            $('#positionStructureGroup').val(structureGroup ?? '');
            $('#positionAccessProfile').val(accessProfile ?? '');
            $('#positionOrder').val(order);
            $('#positionReceivesDisposition').val(
                receivesDisposition === true || receivesDisposition === '1' || receivesDisposition === 1 ? '1' :
                    receivesDisposition === false || receivesDisposition === '0' || receivesDisposition === 0 ? '0' :
                        ''
            );
            $('#positionDispositionGroupLabel').val(dispositionGroupLabel ?? '');
            $('#positionReportTargetLabel').val(reportTargetLabel ?? '');
            $('#positionModal').modal('show');
        }

        $('#divisionModal').on('hidden.bs.modal', function () {
            $('#divisionModalLabel').html('<i class="fe fe-plus mr-2"></i>Tambah Unit Kerja Baru');
            $('#divisionForm').attr('action', '{{ route("master-data.divisions.store") }}');
            $('#divisionMethod').val('POST');
            $('#divisionForm')[0].reset();
        });

        $('#positionModal').on('hidden.bs.modal', function () {
            $('#positionModalLabel').html('<i class="fe fe-award mr-2"></i>Tambah Jabatan Baru');
            $('#positionForm').attr('action', '{{ route("master-data.positions.store") }}');
            $('#positionMethod').val('POST');
            $('#positionForm')[0].reset();
        });

        document.addEventListener('DOMContentLoaded', function () {
            var tables = document.querySelectorAll('.sortable-division-group');
            tables.forEach(function (el) {
                Sortable.create(el, {
                    handle: '.handle',
                    animation: 150,
                    onEnd: function (evt) {
                        var order = [];
                        el.querySelectorAll('.division-row').forEach(function (row) {
                            order.push(row.getAttribute('data-id'));
                        });

                        fetch('{{ route("master-data.divisions.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ order: order })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    console.log('Order saved');
                                }
                            });
                    }
                });
            });

            const positionTable = document.querySelector('.sortable-position-group');
            if (positionTable) {
                Sortable.create(positionTable, {
                    handle: '.handle-position',
                    animation: 150,
                    onEnd: function () {
                        var order = [];
                        positionTable.querySelectorAll('.position-row').forEach(function (row) {
                            order.push(row.getAttribute('data-id'));
                        });

                        fetch('{{ route("master-data.positions.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ order: order })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    console.log('Position order saved');
                                }
                            });
                    }
                });
            }

            // Delete Confirmation
            document.addEventListener('click', function (e) {
                if (e.target && (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn'))) {
                    e.preventDefault();
                    var btn = e.target.classList.contains('delete-btn') ? e.target : e.target.closest('.delete-btn');
                    var form = btn.closest('form');

                    Swal.fire({
                        title: 'Hapus Jabatan?',
                        text: "Pastikan tidak ada pengguna yang menggunakan jabatan ini!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f5365c',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>

    <style>
        .badge-primary-soft {
            background-color: rgba(94, 114, 228, 0.1);
            color: #5e72e4;
        }

        .badge-info-soft {
            background-color: rgba(17, 205, 239, 0.1);
            color: #11cdef;
        }

        .badge-success-soft {
            background-color: rgba(45, 206, 137, 0.1);
            color: #2dce89;
        }

        .badge-danger-soft {
            background-color: rgba(245, 54, 92, 0.1);
            color: #f5365c;
        }

        .cursor-move {
            cursor: move;
        }

        .letter-spacing-1 {
            letter-spacing: 1px;
        }
    </style>
@endpush

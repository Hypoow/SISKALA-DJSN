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

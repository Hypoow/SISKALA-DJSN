@extends('layouts.app')

@section('title', 'Builder Struktur Role')

@section('content')
    <div class="container-fluid py-4">
        <div class="builder-header mb-4 mt-2">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h2 class="h3 font-weight-bold text-dark mb-2">Builder Struktur Role</h2>
                    <p class="text-muted mb-0">Kelola kelompok akun, komisi Dewan, unit pendamping, dan master jabatan.</p>
                </div>
                <div class="col-lg-5 mt-3 mt-lg-0 text-lg-right">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 mr-2 builder-btn" data-toggle="modal" data-target="#positionModal">
                        <i class="fe fe-award mr-2"></i>Tambah Jabatan
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm builder-btn" data-toggle="modal" data-target="#divisionModal">
                        <i class="fe fe-plus-circle mr-2"></i>Tambah Unit
                    </button>
                </div>
            </div>
        </div>

        @include('master-data.partials.tabs', ['active' => 'divisions'])

        <div class="row mb-5">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card h-100">
                    <div class="metric-card-inner">
                        <div class="metric-icon-box bg-primary-soft text-primary">
                            <i class="fe fe-grid"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-label">Unit Kerja Active</span>
                            <h3 class="metric-value">{{ $divisions->count() }}</h3>
                            <p class="metric-desc">Dipakai untuk penempatan akun dan struktur.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card h-100">
                    <div class="metric-card-inner">
                        <div class="metric-icon-box bg-success-soft text-success">
                            <i class="fe fe-star"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-label">Komisi Dewan</span>
                            <h3 class="metric-value">{{ $divisions->where('is_commission', true)->count() }}</h3>
                            <p class="metric-desc">Scope langsung untuk Dewan, Sidang, & TA.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card h-100">
                    <div class="metric-card-inner">
                        <div class="metric-icon-box bg-warning-soft text-warning">
                            <i class="fe fe-award"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-label">Master Jabatan</span>
                            <h3 class="metric-value">{{ $positions->count() }}</h3>
                            <p class="metric-desc">Hak akses, disposisi, & label diturunkan dari sini.</p>
                        </div>
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
        /* Core utilities */
        .rounded-xl { border-radius: 1.5rem; }
        
        /* Typography & Layout */
        .builder-header .btn {
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: all 0.2s ease;
        }
        .builder-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -6px rgba(15, 44, 89, 0.2);
        }

        /* Metric Cards Modern */
        .metric-card {
            background: linear-gradient(180deg, #ffffff 0%, #fcfdfd 100%);
            border: 1px solid #eef2f6;
            border-radius: 20px;
            padding: 1.25rem;
            box-shadow: 0 10px 20px -10px rgba(15, 23, 42, 0.05);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -12px rgba(15, 23, 42, 0.1);
            border-color: #e2e8f0;
        }
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(15, 44, 89, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .metric-card:hover::before {
            opacity: 1;
        }
        .metric-card-inner {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .metric-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .metric-content {
            flex: 1;
        }
        .metric-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.35rem;
        }
        .metric-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.25rem;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }
        .metric-desc {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 0;
            line-height: 1.4;
        }

        /* Builder Columns */
        .builder-column { 
            background: #f8fafc; 
            border: 1px dashed #cbd5e1; 
            border-radius: 1.2rem; 
            padding: 1.25rem; 
            height: 100%; 
            position: relative;
        }
        .builder-column-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            margin-bottom: 1.2rem; 
            padding-bottom: 0.8rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .builder-column-header h5 { 
            font-weight: 700; 
            color: #1e293b; 
            margin-bottom: .2rem; 
            font-size: 1.05rem;
        }
        .builder-column-header small { 
            color: #64748b; 
            font-size: 0.8rem;
        }
        .builder-dropzone { 
            min-height: 130px; 
            display: flex; 
            flex-direction: column; 
            gap: 1rem; 
        }

        /* Draggable Items */
        .builder-item { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 14px; 
            padding: 1.1rem; 
            box-shadow: 0 4px 6px -4px rgba(15, 23, 42, 0.04);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            cursor: grab;
        }
        .builder-item:hover {
            box-shadow: 0 12px 24px -8px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }
        .builder-item:active {
            cursor: grabbing;
        }
        
        .drag-handle { 
            color: #94a3b8; 
            cursor: grab; 
            padding: 0.25rem;
            margin-left: -0.25rem;
            border-radius: 4px;
            transition: all 0.15s ease;
        }
        .drag-handle:hover {
            background: #f1f5f9;
            color: #475569;
        }
        .drag-handle:active {
            cursor: grabbing;
        }

        .builder-empty { 
            border: 1px dashed #cbd5e1; 
            border-radius: 14px; 
            padding: 1.5rem 1rem; 
            text-align: center; 
            color: #94a3b8; 
            font-size: 0.85rem; 
            background: rgba(255, 255, 255, 0.5);
        }

        .builder-main-card {
            border-radius: 1.5rem;
            background: linear-gradient(180deg, #ffffff 0%, #fefeff 100%);
            box-shadow: 0 20px 40px -20px rgba(15, 23, 42, 0.08);
            border: 1px solid #eef2f6;
        }

        /* Soft Badges */
        .badge-primary-soft { background: #eff6ff; color: #1d4ed8; }
        .badge-info-soft { background: #f0f9ff; color: #0369a1; }
        .badge-warning-soft { background: #fefce8; color: #a16207; }
        .badge-success-soft { background: #f0fdf4; color: #15803d; }
        .badge-secondary-soft { background: #f8fafc; color: #475569; }

        .sortable-ghost { 
            opacity: .4; 
            background: #f1f5f9 !important;
            border: 1px dashed #cbd5e1 !important;
            box-shadow: none !important;
        }
        .sortable-drag {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            opacity: 0.9 !important;
        }
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

        function editDivision(id, name, structureGroup, accessProfile, commissionCode, isCommission, description, order) {
            resetDivisionModal();
            $('#divisionModalLabel').text('Edit Unit');
            $('#divisionForm').attr('action', '{{ url("master-data/divisions") }}/' + id);
            $('#divisionMethod').val('PUT');
            $('#divisionName').val(name);
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

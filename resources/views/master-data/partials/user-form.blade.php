@php
    $user = $user ?? null;
    $mode = $mode ?? 'create';
    $formAction = $formAction ?? route('master-data.store');
    $formMethod = $formMethod ?? 'POST';
    $submitLabel = $submitLabel ?? 'Simpan';
    $pageTitle = $pageTitle ?? 'Form Akun';
    $pageDescription = $pageDescription ?? 'Kelola akun mengikuti struktur organisasi.';
    $selectedDivisionId = old('division_id', $user?->division_id);
    $selectedPositionId = old('position_id', $user?->position_id);
    $isSuperAdmin = old('is_super_admin', $user?->isSuperAdmin() ? '1' : '0');
@endphp

<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
    <div>
        <span class="text-uppercase text-muted small font-weight-bold d-block mb-1">Master Data</span>
        <h2 class="h3 font-weight-bold text-dark mb-2">{{ $pageTitle }}</h2>
        <div class="markdown-content master-markdown-sm master-markdown-muted master-markdown-tight mb-0">
            {!! \Illuminate\Support\Str::markdown($pageDescription) !!}
        </div>
    </div>
    <div class="mt-3 mt-lg-0">
        <a href="{{ route('master-data.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fe fe-arrow-left mr-2"></i>Kembali
        </a>
    </div>
</div>

@include('master-data.partials.tabs', ['active' => 'users'])

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-lg">
        <strong class="d-block mb-2">Ada data yang perlu diperiksa.</strong>
        <ul class="mb-0 pl-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $formAction }}">
    @csrf
    @if($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="row">
        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm border-0 rounded-xl mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mb-4">
                        <div>
                            <h5 class="font-weight-bold text-dark mb-1">Informasi Akun</h5>
                            <div class="markdown-content master-markdown-xs master-markdown-muted master-markdown-tight mb-0">
                                {!! \Illuminate\Support\Str::markdown('Akun biasa cukup mengikuti unit kerja dan jabatan. Centang **Super Admin** hanya untuk akun pengelola penuh.') !!}
                            </div>
                        </div>
                        <label class="custom-control custom-switch mt-3 mt-md-0">
                            <input type="checkbox" class="custom-control-input" id="is_super_admin" name="is_super_admin" value="1" {{ $isSuperAdmin === '1' ? 'checked' : '' }}>
                            <span class="custom-control-label font-weight-bold text-dark">Super Admin</span>
                        </label>
                    </div>

                    <div class="form-row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Sapaan</label>
                                <select class="form-control shadow-sm" name="prefix">
                                    <option value="Bapak" {{ old('prefix', $user->prefix ?? 'Bapak') === 'Bapak' ? 'selected' : '' }}>Bapak</option>
                                    <option value="Ibu" {{ old('prefix', $user->prefix ?? 'Bapak') === 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Nama Lengkap</label>
                                <input type="text" class="form-control shadow-sm" name="name" value="{{ old('name', $user?->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Email</label>
                                <input type="email" class="form-control shadow-sm" name="email" value="{{ old('email', $user?->email) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted">Kata Sandi {{ $mode === 'edit' ? '(opsional)' : '' }}</label>
                        <input type="password" class="form-control shadow-sm" name="password" {{ $mode === 'create' ? 'required' : '' }} placeholder="{{ $mode === 'create' ? 'Minimal 8 karakter' : 'Kosongkan bila tidak diubah' }}">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-xl mb-4" id="structureCard">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold text-dark mb-1">Penempatan Struktur</h5>
                    <div class="markdown-content master-markdown-xs master-markdown-muted master-markdown-tight mb-4">
                        {!! \Illuminate\Support\Str::markdown('Struktur inilah yang dipakai untuk menurunkan **role**, akses halaman, perilaku dashboard, dan keterkaitan komisi.') !!}
                    </div>

                    <div class="form-row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Unit Kerja / Kelompok Akun</label>
                                <select class="form-control shadow-sm" name="division_id" id="division_id">
                                    <option value="">Pilih unit kerja</option>
                                    @foreach($divisions->groupBy('structure_group_label') as $groupLabel => $items)
                                        <optgroup label="{{ $groupLabel }}">
                                            @foreach($items as $division)
                                                <option
                                                    value="{{ $division->id }}"
                                                    data-access-profile="{{ $division->access_profile }}"
                                                    data-structure-group="{{ $division->structure_group_label }}"
                                                    data-unit-label="{{ $division->display_name }}"
                                                    data-commission-label="{{ $division->is_commission ? $division->display_name : \App\Models\Division::commissionLabel($division->commission_code) }}"
                                                    {{ (string) $selectedDivisionId === (string) $division->id ? 'selected' : '' }}
                                                >
                                                    {{ $division->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Jabatan / Posisi</label>
                                <select class="form-control shadow-sm" name="position_id" id="position_id">
                                    <option value="">Pilih jabatan</option>
                                    @foreach($positions as $position)
                                        <option
                                            value="{{ $position->id }}"
                                            data-access-profile="{{ $position->access_profile }}"
                                            data-receives-disposition="{{ $position->getRawOriginal('receives_disposition') }}"
                                            {{ (string) $selectedPositionId === (string) $position->id ? 'selected' : '' }}
                                        >
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border mb-0">
                        <div class="markdown-content master-markdown-xs master-markdown-muted master-markdown-tight mb-0">
                            {!! \Illuminate\Support\Str::markdown('Dewan dan **Sekretaris DJSN** bersifat disposisi-based di dashboard kalender. Persidangan dan Tenaga Ahli akan mengikuti komisi Dewan yang didampingi.') !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-xl">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold text-dark mb-1">Override Per Akun</h5>
                    <div class="markdown-content master-markdown-xs master-markdown-muted master-markdown-tight mb-4">
                        {!! \Illuminate\Support\Str::markdown('Biarkan kosong jika Anda ingin akun sepenuhnya mengikuti default unit dan jabatan.') !!}
                    </div>

                    <div class="form-row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Label Tujuan Laporan</label>
                                <input type="text" class="form-control shadow-sm" name="report_target_label" value="{{ old('report_target_label', $user?->report_target_label) }}">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Menerima Disposisi</label>
                                <select class="form-control shadow-sm" name="receives_disposition" id="receives_disposition">
                                    <option value="" {{ old('receives_disposition', $user?->getRawOriginal('receives_disposition')) === null ? 'selected' : '' }}>Ikuti jabatan</option>
                                    <option value="1" {{ (string) old('receives_disposition', $user?->getRawOriginal('receives_disposition')) === '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ (string) old('receives_disposition', $user?->getRawOriginal('receives_disposition')) === '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-muted">Label Grup Disposisi</label>
                                <input type="text" class="form-control shadow-sm" name="disposition_group_label" value="{{ old('disposition_group_label', $user?->getRawOriginal('disposition_group_label')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0 rounded-xl sticky-top" style="top: 90px;">
                <div class="card-body p-4">
                    <span class="text-uppercase text-muted small font-weight-bold d-block mb-2">Preview</span>
                    <h5 class="font-weight-bold text-dark mb-4">Ringkasan Hak Akses</h5>

                    <div class="preview-box mb-3">
                        <small>Profil Akses</small>
                        <strong id="previewAccess">Viewer</strong>
                    </div>
                    <div class="preview-box mb-3">
                        <small>Kelompok Struktur</small>
                        <strong id="previewGroup">Belum dipilih</strong>
                    </div>
                    <div class="preview-box mb-3">
                        <small>Unit Aktif</small>
                        <strong id="previewUnit">Belum dipilih</strong>
                    </div>
                    <div class="preview-box mb-4">
                        <small>Scope Komisi</small>
                        <strong id="previewCommission">Umum</strong>
                    </div>

                    <div class="alert alert-light border mb-4">
                        <div class="small text-muted markdown-content master-markdown-xs master-markdown-muted master-markdown-tight mb-0" id="previewCalendar">Pilih unit dan jabatan untuk melihat ringkasan perilaku dashboard kalender.</div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fe fe-save mr-2"></i>{{ $submitLabel }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('styles')
    <style>
        .rounded-xl { border-radius: 1.3rem; }
        .preview-box {
            padding: 1rem;
            border: 1px solid #e5edf7;
            border-radius: 1rem;
            background: #fff;
        }
        .preview-box small {
            display: block;
            color: #7b8797;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 700;
            margin-bottom: .25rem;
        }
        .preview-box strong { color: #0f172a; }
        #structureCard.is-disabled { opacity: .55; }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const labels = @json($accessProfiles);
            const superAdmin = document.getElementById('is_super_admin');
            const division = document.getElementById('division_id');
            const position = document.getElementById('position_id');
            const structureCard = document.getElementById('structureCard');
            const disposition = document.getElementById('receives_disposition');

            const previewAccess = document.getElementById('previewAccess');
            const previewGroup = document.getElementById('previewGroup');
            const previewUnit = document.getElementById('previewUnit');
            const previewCommission = document.getElementById('previewCommission');
            const previewCalendar = document.getElementById('previewCalendar');

            function selectedData(select) {
                return select && select.selectedIndex >= 0 ? select.options[select.selectedIndex].dataset : {};
            }

            function updatePreview() {
                if (superAdmin.checked) {
                    previewAccess.textContent = 'Super Admin';
                    previewGroup.textContent = 'Lintas Struktur';
                    previewUnit.textContent = 'Semua unit';
                    previewCommission.textContent = 'Semua komisi';
                    previewCalendar.textContent = 'Akun ini bisa melihat dan mengelola seluruh sistem tanpa batasan struktur.';
                    structureCard.classList.add('is-disabled');
                    division.disabled = true;
                    position.disabled = true;
                    return;
                }

                const divisionData = selectedData(division);
                const positionData = selectedData(position);
                const accessProfile = positionData.accessProfile || divisionData.accessProfile || 'viewer';
                const group = divisionData.structureGroup || 'Belum dipilih';
                const commission = divisionData.commissionLabel && divisionData.commissionLabel !== '-' ? divisionData.commissionLabel : 'Umum';
                const receivesDisposition = disposition.value === '1' || (disposition.value === '' && (positionData.receivesDisposition === '1' || accessProfile === 'dewan' || group === 'Sekretaris DJSN'));

                previewAccess.textContent = labels[accessProfile] || 'Viewer';
                previewGroup.textContent = group;
                previewUnit.textContent = divisionData.unitLabel || 'Belum dipilih';
                previewCommission.textContent = commission;

                if (accessProfile === 'dewan') {
                    previewCalendar.textContent = 'Dashboard kalender hanya menampilkan kegiatan yang terdisposisi ke akun Dewan ini.';
                } else if (accessProfile === 'set_djsn') {
                    previewCalendar.textContent = 'Dashboard kalender mengikuti kegiatan yang terdisposisi ke Sekretaris DJSN.';
                } else if (accessProfile === 'persidangan' || accessProfile === 'tenaga_ahli') {
                    previewCalendar.textContent = 'Dashboard kalender mengikuti kegiatan Dewan pada komisi ' + commission + '.';
                } else if (accessProfile === 'tata_usaha') {
                    previewCalendar.textContent = 'Akun ini dapat CRUD kegiatan dan surat tugas.';
                } else if (accessProfile === 'prothum') {
                    previewCalendar.textContent = 'Akun ini fokus pada dokumentasi kegiatan.';
                } else if (accessProfile === 'keuangan') {
                    previewCalendar.textContent = 'Akun ini view-only untuk kebutuhan pemantauan dan keuangan.';
                } else {
                    previewCalendar.textContent = receivesDisposition
                        ? 'Akun ini akan ikut checklist disposisi sesuai default jabatan.'
                        : 'Akun ini mengikuti akses dasar dari unit dan jabatan yang dipilih.';
                }

                structureCard.classList.remove('is-disabled');
                division.disabled = false;
                position.disabled = false;
            }

            [superAdmin, division, position, disposition].forEach(function (element) {
                if (element) {
                    element.addEventListener('change', updatePreview);
                }
            });

            updatePreview();
        });
    </script>
@endpush

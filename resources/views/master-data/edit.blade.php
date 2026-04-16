@extends('layouts.app')

@section('title', 'Edit Akun Pengguna')

@section('content')
    <div class="container-fluid py-4">
        @include('master-data.partials.user-form', [
            'user' => $user,
            'mode' => 'edit',
            'formAction' => route('master-data.update', $user->id),
            'formMethod' => 'PUT',
            'submitLabel' => 'Simpan Perubahan',
            'pageTitle' => 'Edit Akun Pengguna',
            'pageDescription' => 'Perbarui struktur, akses, dan override seperlunya tanpa perlu mengatur role manual satu per satu.',
        ])
    </div>
@endsection

@if(false)

@extends('layouts.app')

@section('title', 'Edit Akun Pengguna | Schedulo')

@section('content')
<div class="header bg-primary pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Edit Akun Pengguna</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('master-data.index') }}">Master Data</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Akun</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-6 col-5 text-right">
                    <a href="{{ route('master-data.index') }}" class="btn btn-sm btn-neutral"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card shadow-sm border-0 premium-card">
                <div class="card-header border-0 bg-white pt-4 pb-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0 text-dark"><span class="user-modal-title-icon mr-2 bg-primary-soft text-primary p-2 rounded"><i class="fe fe-edit"></i></span> Perbarui Data Akun</h3>
                            <p class="text-muted text-sm mt-2 mb-0">Sesuaikan informasi pengguna dan hak aksesnya pada form di bawah ini.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body bg-secondary-soft">
                    <form id="userForm" action="{{ route('master-data.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row justify-content-center">
                            <div class="col-lg-10">
                                <!-- Informasi Dasar & Kontak -->
                                <div class="card shadow-sm border-0 mb-4">
                                    <div class="card-body p-4">
                                        <h6 class="heading-small text-muted mb-4 text-uppercase border-bottom pb-3"><i class="fe fe-info mr-2"></i>Informasi Dasar & Kontak</h6>
                                        <div class="row">
                                            <div class="col-md-2 pr-md-1">
                                                <div class="form-group mb-3">
                                                    <label for="userPrefix" class="form-control-label small font-weight-bold">Sapaan</label>
                                                    <select class="form-control shadow-sm" id="userPrefix" name="prefix">
                                                        <option value="Bapak" {{ ($user->prefix ?? 'Bapak') == 'Bapak' ? 'selected' : '' }}>Bapak</option>
                                                        <option value="Ibu" {{ ($user->prefix ?? 'Bapak') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 pl-md-1 pr-md-2">
                                                <div class="form-group mb-3">
                                                    <label for="userName" class="form-control-label small font-weight-bold">Nama Lengkap</label>
                                                    <div class="input-group input-group-merge shadow-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fe fe-user"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" id="userName" name="name" required value="{{ $user->name }}" placeholder="Masukkan Nama Lengkap">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 pl-md-2 pr-md-1">
                                                <div class="form-group mb-3">
                                                    <label for="userEmail" class="form-control-label small font-weight-bold">Alamat Email</label>
                                                    <div class="input-group input-group-merge shadow-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fe fe-mail"></i></span>
                                                        </div>
                                                        <input type="email" class="form-control" id="userEmail" name="email" required value="{{ $user->email }}" placeholder="Alamat Email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 pl-md-1">
                                                <div class="form-group mb-3">
                                                    <label for="userPassword" class="form-control-label small font-weight-bold">Kata Sandi</label>
                                                    <div class="input-group input-group-merge shadow-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fe fe-lock"></i></span>
                                                        </div>
                                                        <input type="password" class="form-control" id="userPassword" name="password" placeholder="Kosongkan jika tdk diubah">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Penugasan Pribadi -->
                                <div class="card shadow-sm border-0 mb-4">
                                    <div class="card-body p-4">
                                        <h6 class="heading-small text-muted mb-4 text-uppercase border-bottom pb-3"><i class="fe fe-briefcase mr-2"></i>Penugasan Pribadi</h6>
                                        <div class="row">
                                            <div class="col-md-4 pr-md-2">
                                                <div class="form-group mb-3">
                                                    <label for="userDivisionId" class="form-control-label small font-weight-bold">Unit Kerja / Komisi</label>
                                                    <div class="input-group input-group-merge shadow-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fe fe-briefcase"></i></span>
                                                        </div>
                                                        <select class="form-control" id="userDivisionId" name="division_id">
                                                            <option value="">Pilih Unit Kerja</option>
                                                            @foreach($divisions->groupBy('category') as $category => $items)
                                                                <optgroup label="{{ $category }}">
                                                                    @foreach($items as $div)
                                                                        <option value="{{ $div->id }}" {{ $user->division_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 px-md-1">
                                                <div class="form-group mb-3">
                                                    <label for="userPositionId" class="form-control-label small font-weight-bold">Jabatan / Posisi</label>
                                                    <div class="input-group input-group-merge shadow-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fe fe-award"></i></span>
                                                        </div>
                                                        <select class="form-control" id="userPositionId" name="position_id">
                                                            <option value="">Pilih Jabatan</option>
                                                            @foreach($positions as $pos)
                                                                <option value="{{ $pos->id }}" {{ $user->position_id == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 pl-md-2">
                                                <div class="form-group mb-3">
                                                    <label for="userReportTargetLabel" class="form-control-label small font-weight-bold">Label Tujuan Laporan (Opsional)</label>
                                                    <div class="input-group input-group-merge shadow-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fe fe-edit-3"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" id="userReportTargetLabel" name="report_target_label" value="{{ $user->report_target_label }}" placeholder="Cth: Wakil Ketua PME">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 pr-md-2">
                                                <div class="form-group mb-3">
                                                    <label for="userReceivesDisposition" class="form-control-label small font-weight-bold">Menerima Disposisi</label>
                                                    <select class="form-control shadow-sm" id="userReceivesDisposition" name="receives_disposition">
                                                        <option value="" {{ $user->getRawOriginal('receives_disposition') === null ? 'selected' : '' }}>Gunakan default jabatan</option>
                                                        <option value="1" {{ $user->getRawOriginal('receives_disposition') === 1 || $user->getRawOriginal('receives_disposition') === '1' ? 'selected' : '' }}>Ya, tampil di checklist disposisi</option>
                                                        <option value="0" {{ $user->getRawOriginal('receives_disposition') === 0 || $user->getRawOriginal('receives_disposition') === '0' ? 'selected' : '' }}>Tidak, sembunyikan dari checklist disposisi</option>
                                                    </select>
                                                    <small class="text-muted d-block mt-2">Biarkan kosong jika akun ini cukup memakai aturan disposisi dari jabatannya.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4 px-md-1">
                                                <div class="form-group mb-3">
                                                    <label for="userDispositionGroupLabel" class="form-control-label small font-weight-bold">Label Grup Disposisi (Opsional)</label>
                                                    <div class="input-group input-group-merge shadow-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fe fe-layers"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control" id="userDispositionGroupLabel" name="disposition_group_label" value="{{ $user->getRawOriginal('disposition_group_label') }}" placeholder="Cth: Sekretariat DJSN">
                                                    </div>
                                                    <small class="text-muted d-block mt-2">Dipakai untuk pengelompokan checklist dan tampilan tujuan disposisi.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4 pl-md-2">
                                                <div class="form-group mb-0">
                                                    <div class="alert alert-light border mb-0">
                                                        <div class="small text-muted">
                                                            <strong class="text-dark d-block mb-2">Status saat ini</strong>
                                                            @if($user->canReceiveDisposition())
                                                                Akun ini saat ini masuk target disposisi dengan label grup <strong>{{ $user->disposition_group_label }}</strong>.
                                                            @else
                                                                Akun ini saat ini tidak tampil di checklist disposisi.
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hak Akses & Peran -->
                                <div class="card shadow-sm border-0 mb-4">
                                    <div class="card-body p-4">
                                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mb-4 border-bottom pb-3">
                                            <h6 class="heading-small text-muted mb-0 text-uppercase"><i class="fe fe-shield mr-2"></i>Pilih Peran & Hak Akses Primer</h6>
                                            <span class="selection-tip text-xs mt-2 mt-md-0 d-inline-flex align-items-center px-3 py-1 bg-primary-soft text-primary rounded-pill"><i class="fe fe-info mr-1"></i>Pilih peran utama akun, lalu unit kerja dan jabatan akan membantu menyesuaikan akses detailnya.</span>
                                        </div>
                                        
                                        <div class="row">
                                            @php
                                            $roles = [
                                                ['value' => 'super_admin', 'icon' => 'fe-shield', 'title' => 'Super Admin', 'desc' => 'Akses penuh ke seluruh sistem, master data, dan jobdesk.', 'color_class' => 'primary'],
                                                ['value' => 'DJSN', 'icon' => 'fe-check-square', 'title' => 'Set.DJSN', 'desc' => 'Khusus Sekretaris DJSN atau pimpinan Set.DJSN yang memang perlu akses disposisi individual.', 'color_class' => 'success'],
                                                ['value' => 'Tata Usaha', 'icon' => 'fe-file-text', 'title' => 'Tata Usaha', 'desc' => 'Kelola Kegiatan & Surat Tugas (Level 2).', 'color_class' => 'info'],
                                                ['value' => 'Persidangan', 'icon' => 'fe-users', 'title' => 'Persidangan', 'desc' => 'Notulensi, Absensi & Tindak Lanjut (Level 3).', 'color_class' => 'warning'],
                                                ['value' => 'Bagian Umum', 'icon' => 'fe-image', 'title' => 'ProtHum', 'desc' => 'Kelola dokumentasi kegiatan dan pengunggahan foto.', 'color_class' => 'danger'],
                                                ['value' => 'Keuangan', 'icon' => 'fe-credit-card', 'title' => 'Keuangan', 'desc' => 'Akses pencarian dan unduh dokumen laporan.', 'color_class' => 'secondary'],
                                                ['value' => 'TA', 'icon' => 'fe-briefcase', 'title' => 'Tenaga Ahli', 'desc' => 'View-only dan mengikuti komisi Dewan yang didampingi.', 'color_class' => 'info'],
                                                ['value' => 'Dewan', 'icon' => 'fe-star', 'title' => 'Dewan', 'desc' => 'Mendapatkan kegiatan hanya saat ter-dispo dan aksesnya view-only.', 'color_class' => 'dark'],
                                                ['value' => 'User', 'icon' => 'fe-eye', 'title' => 'Viewer', 'desc' => 'Akses lihat dasar tanpa kewenangan kelola.', 'color_class' => 'dark']
                                            ];
                                            $userRole = $user->role === 'admin' ? 'super_admin' : $user->role;
                                            @endphp

                                            @foreach($roles as $role)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <label class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                                    <input type="radio" name="role" value="{{ $role['value'] }}" class="d-none role-input" required {{ $userRole === $role['value'] ? 'checked' : '' }}>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="icon-box bg-{{ $role['color_class'] === 'dark' ? 'dark text-white' : $role['color_class'].'-soft text-'.$role['color_class'] }} rounded p-2 mr-2">
                                                            <i class="fe {{ $role['icon'] }}"></i>
                                                        </div>
                                                        <h6 class="mb-0 font-weight-bold text-dark text-sm">{{ $role['title'] }}</h6>
                                                    </div>
                                                    <small class="text-muted line-height-sm flex-grow-1">{{ $role['desc'] }}</small>
                                                    <div class="check-mark position-absolute opacity-0 transition-all" style="top: 15px; right: 15px;">
                                                        <i class="fe fe-check-circle text-{{ $role['color_class'] }}" style="font-size: 1.2rem;"></i>
                                                    </div>
                                                    <div class="active-border position-absolute w-100 h-100 rounded-lg" style="top:0; left:0; border: 2px solid transparent; pointer-events: none;"></div>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mb-5">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow px-5">
                                        <i class="fe fe-save mr-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .bg-secondary-soft { background-color: #f8f9fe; }
    .input-group.input-group-merge {
        border-radius: .95rem;
        overflow: hidden;
    }
    .input-group.input-group-merge .input-group-text,
    .input-group.input-group-merge .form-control,
    #userPrefix,
    #userDivisionId,
    #userPositionId {
        min-height: 48px;
    }
    .input-group.input-group-merge .input-group-text {
        background: #f7faff;
        color: #5c6b82;
        border-color: #dbe4f0;
    }
    #userPrefix,
    #userDivisionId,
    #userPositionId,
    .input-group.input-group-merge .form-control {
        border-color: #dbe4f0;
    }
    .role-item {
        border-radius: 1.15rem;
        border: 1px solid rgba(17, 24, 39, 0.08);
        background: #fff;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    }
    .role-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 38px -30px rgba(15, 23, 42, 0.5);
    }
    .role-item .active-border {
        border-radius: 1.15rem !important;
    }
    .check-mark {
        transition: opacity .18s ease, transform .18s ease;
        transform: scale(.92);
    }
    .role-item.active-role-item .check-mark {
        transform: scale(1);
    }
    .cursor-pointer { cursor: pointer; }
    .transition-all { transition: all 0.3s ease; }
</style>
@endpush
@endif

@push('scripts')
<script>
    function updateRoleStyles() {
        $('.role-item').each(function() {
            const checkbox = $(this).find('input[name="role"]');
            const activeBorder = $(this).find('.active-border');
            const checkMark = $(this).find('.check-mark');

            if (checkbox.is(':checked')) {
                $(this).addClass('active-role-item');
                checkMark.removeClass('opacity-0');
                
                let color = '#5e72e4'; // primary
                const val = checkbox.val();
                if(val == 'super_admin' || val == 'admin') color = '#5e72e4';
                if(val == 'DJSN') color = '#2dce89';
                if(val == 'Tata Usaha') color = '#11cdef';
                if(val == 'Persidangan') color = '#fb6340';
                if(val == 'Bagian Umum') color = '#f5365c';
                if(val == 'Keuangan') color = '#8898aa';
                if(val == 'TA') color = '#11cdef';
                if(val == 'Dewan' || val == 'User') color = '#343a40';

                activeBorder.css('border-color', color);
                $(this).css('background-color', color + '10');
                $(this).css('box-shadow', '0 22px 36px -28px ' + color + 'dd');
            } else {
                $(this).removeClass('active-role-item');
                checkMark.addClass('opacity-0');
                activeBorder.css('border-color', 'transparent');
                $(this).css('background-color', '#fff');
                $(this).css('box-shadow', '');
            }
        });
    }

    $(document).ready(function() {
        $('input[name="role"]').on('change', function() {
            updateRoleStyles();
        });
        updateRoleStyles();
    });
</script>
@endpush

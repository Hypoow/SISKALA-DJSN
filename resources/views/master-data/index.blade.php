@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
    @php
        $totalUsers = $groupedUsers->sum(function ($usersInGroup) {
            return $usersInGroup->count();
        });
        $totalGroups = $groupedUsers->count();
    @endphp

    <div class="master-users-page">
        <section class="master-users-hero">
            <div class="master-users-hero-copy">
                <span class="master-users-eyebrow">Dashboard Master Data</span>
                <h1 class="master-users-title mb-2">Data Pengguna</h1>
                <p class="master-users-subtitle mb-0">Kelola akun, peran, dan hak akses sistem.</p>
                <div class="master-users-metrics">
                    <span class="master-users-metric">
                        <strong>{{ $totalUsers }}</strong>
                        <span>Akun terdaftar</span>
                    </span>
                    <span class="master-users-metric">
                        <strong>{{ $totalGroups }}</strong>
                        <span>Kelompok akses</span>
                    </span>
                </div>
            </div>
            <div class="master-users-hero-action">
                <a href="{{ route('master-data.create') }}" class="btn btn-primary master-users-cta">
                    <span class="master-users-cta-icon">
                        <i class="fe fe-plus"></i>
                    </span>
                    <span>Tambah Akun</span>
                </a>
            </div>
        </section>

        @include('master-data.partials.tabs', ['active' => 'users'])

        <div class="dashboard-surface-card master-users-card mb-5">
            <div class="master-users-card-header">
                <div>
                    <span class="master-users-card-caption">Direktori Akun Sistem</span>
                    <h5 class="master-users-card-title mb-1">Daftar Pengguna Sistem</h5>
                    <p class="master-users-card-subtitle mb-0">Setiap akun dikelompokkan berdasarkan akses agar lebih mudah
                        dipindai dan diatur ulang.</p>
                </div>
                <div class="master-users-card-hint">
                    <i class="fe fe-move mr-2"></i>Drag & drop urutan aktif
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive master-users-table-wrap">
                    <table class="table table-hover align-items-center mb-0 master-users-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4"
                                    style="width: 5%;">No</th>
                                <th style="width: 5%;"></th> <!-- Drag Handle -->
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Identitas
                                    Pengguna</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email &
                                    Kontak</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Peran & Akses</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                    Bergabung</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                                    style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <!-- Loop through Groups -->
                        @foreach($groupedUsers as $groupName => $usersInGroup)
                            <tbody class="sortable-group" data-group-name="{{ $groupName }}">
                                <!-- Group Header -->
                                <tr class="master-users-group-row">
                                    <td colspan="7"
                                        class="py-2 pl-4 font-weight-bold text-dark text-uppercase text-xs letter-spacing-1 border-top border-bottom">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <span>{{ $groupName }}</span>
                                            <span class="master-users-group-count">{{ $usersInGroup->count() }} akun</span>
                                        </div>
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
                                                                    <div
                                                                        class="avatar avatar-sm mr-3 master-users-avatar text-muted d-flex align-items-center justify-content-center">
                                                                        <i class="fe fe-user fe-lg"></i>
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $user->name }}</h6>
                                                                        <small class="text-muted d-block">{{ $user->position?->name ?? 'Tanpa Jabatan' }}</small>
                                                                        @if($user->canReceiveDisposition())
                                                                            <span class="badge badge-pill bg-info-soft text-info mt-2">Disposisi:
                                                                                {{ $user->disposition_group_label }}</span>
                                                                        @endif
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
                                                                    $roleMeta = [
                                                                        'super_admin' => ['badge' => 'bg-primary-soft', 'text' => 'text-primary', 'icon' => 'fe-shield', 'label' => 'Super Admin'],
                                                                        'dewan' => ['badge' => 'bg-dark text-white', 'text' => 'text-white', 'icon' => 'fe-star', 'label' => 'Dewan'],
                                                                        'set_djsn' => ['badge' => 'bg-success-soft', 'text' => 'text-success', 'icon' => 'fe-check-square', 'label' => 'Set.DJSN'],
                                                                        'tata_usaha' => ['badge' => 'bg-info-soft', 'text' => 'text-info', 'icon' => 'fe-file-text', 'label' => 'Tata Usaha'],
                                                                        'persidangan' => ['badge' => 'bg-warning-soft', 'text' => 'text-warning', 'icon' => 'fe-users', 'label' => 'Persidangan'],
                                                                        'prothum' => ['badge' => 'bg-danger-soft', 'text' => 'text-danger', 'icon' => 'fe-image', 'label' => 'ProtHum'],
                                                                        'keuangan' => ['badge' => 'bg-secondary-soft', 'text' => 'text-secondary', 'icon' => 'fe-credit-card', 'label' => 'Keuangan'],
                                                                        'tenaga_ahli' => ['badge' => 'bg-info-soft', 'text' => 'text-info', 'icon' => 'fe-briefcase', 'label' => 'Tenaga Ahli'],
                                                                        'viewer' => ['badge' => 'bg-gray-400 text-white', 'text' => 'text-white', 'icon' => 'fe-eye', 'label' => 'Viewer'],
                                                                    ];

                                                                    $meta = $roleMeta[$user->resolved_access_profile] ?? ['badge' => 'bg-secondary', 'text' => 'text-muted', 'icon' => 'fe-user', 'label' => $user->display_role_label];
                                                                @endphp
                                     <span
                                                                    class="badge badge-pill master-users-role-badge {{ $meta['badge'] }} {{ $meta['text'] }} px-3 py-2 text-xs font-weight-bold shadow-sm d-inline-flex align-items-center">
                                                                    <i class="fe {{ $meta['icon'] }} mr-2"></i> {{ $meta['label'] }}
                                                                </span>
                                                                @if($user->resolved_report_target_label)
                                                                    <div class="small text-muted mt-2">{{ $user->resolved_report_target_label }}</div>
                                                                @endif
                                                                @if($user->resolved_commission_code)
                                                                    <div class="small text-muted mt-1">
                                                                        {{ \App\Models\User::commissionLabel($user->resolved_commission_code) }}</div>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <span
                                                                    class="text-secondary text-xs font-weight-bold">{{ $user->created_at->format('d M Y') }}</span>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <div class="dropdown">
                                                                    <a class="btn btn-sm btn-icon-only text-muted btn-white shadow-sm action-btn master-users-action-btn"
                                                                        href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        <i class="fe fe-more-vertical"></i>
                                                                    </a>
                                                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                                        <a class="dropdown-item" href="{{ route('master-data.edit', $user->id) }}">
                                                                            <i class="fe fe-edit-2 mr-2 text-warning"></i> Edit
                                                                        </a>
                                                                        <form action="{{ route('master-data.destroy', $user->id) }}" method="POST"
                                                                            class="d-inline delete-form">
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
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-center master-users-empty">
                                            <div class="icon icon-shape bg-light text-muted rounded-circle mb-3 shadow-inner"
                                                style="width: 64px; height: 64px;">
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

    <!-- Modal Tambah/Edit User -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content shadow-lg border-0 rounded-xl user-modal-shell">
                <div class="modal-header user-modal-header py-3">
                    <div>
                        <h5 class="modal-title font-weight-bold pl-2 mb-1 d-flex align-items-center" id="userModalLabel">
                            <span class="user-modal-title-icon mr-2"><i class="fe fe-user-plus"></i></span>Tambah Akun Baru
                        </h5>
                        <p class="mb-0 text-muted small pl-2" id="userModalSubtitle">Lengkapi data akun dan pilih peran yang
                            paling sesuai dengan kebutuhan akses pengguna.</p>
                    </div>
                    <button type="button" class="close text-muted" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="userForm" method="POST" action="{{ route('master-data.store') }}">
                    @csrf
                    <input type="hidden" id="userId" name="user_id">
                    <input type="hidden" id="formMethod" name="_method" value="POST">
                    <div class="modal-body p-4 user-modal-body">

                        <!-- Warning Alert for Roles -->
                        <div class="user-modal-banner mb-4" role="alert">
                            <span class="user-modal-banner-badge"><i class="fe fe-shield mr-2"></i>Konfigurasi Hak
                                Akses</span>
                            <div class="small text-muted">
                                <strong class="text-dark d-block mb-1">Atur akun dengan lebih cepat dan minim
                                    kesalahan.</strong>
                                Pisahkan identitas pengguna dan pilihan peran agar proses input terasa lebih rapi dan mudah
                                dicek sebelum disimpan.
                            </div>
                        </div>

                        <!-- Informasi Akun Grid -->
                        <div class="mb-4">
                            <h6 class="heading-small text-muted mb-3 text-uppercase border-bottom pb-2">Informasi Dasar &
                                Kontak</h6>

                            <div class="row">
                                <div class="col-md-2 pr-md-1">
                                    <div class="form-group mb-3">
                                        <label for="userPrefix"
                                            class="form-control-label small font-weight-bold">Sapaan</label>
                                        <select class="form-control shadow-sm" id="userPrefix" name="prefix">
                                            <option value="Bapak">Bapak</option>
                                            <option value="Ibu">Ibu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-md-1 pr-md-2">
                                    <div class="form-group mb-3">
                                        <label for="userName" class="form-control-label small font-weight-bold">Nama
                                            Lengkap</label>
                                        <div class="input-group input-group-merge shadow-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fe fe-user"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="userName" name="name" required
                                                placeholder="Masukkan Nama Lengkap">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 pl-md-2 pr-md-1">
                                    <div class="form-group mb-3">
                                        <label for="userEmail" class="form-control-label small font-weight-bold">Alamat
                                            Email</label>
                                        <div class="input-group input-group-merge shadow-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fe fe-mail"></i></span>
                                            </div>
                                            <input type="email" class="form-control" id="userEmail" name="email" required
                                                placeholder="Alamat Email">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 pl-md-1">
                                    <div class="form-group mb-3">
                                        <label for="userPassword" class="form-control-label small font-weight-bold">Kata
                                            Sandi</label>
                                        <div class="input-group input-group-merge shadow-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fe fe-lock"></i></span>
                                            </div>
                                            <input type="password" class="form-control" id="userPassword" name="password"
                                                placeholder="Minimal 8 karakter">
                                        </div>
                                        <small class="form-text text-muted mt-1" id="passwordHelp"
                                            style="display:none; position:absolute;">Kosongkan jika tidak diubah.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Kerja & Jabatan -->
                        <div class="mb-4">
                            <h6 class="heading-small text-muted mb-3 text-uppercase border-bottom pb-2">Penugasan Pribadi
                            </h6>

                            <div class="row">
                                <div class="col-md-4 pr-md-2">
                                    <div class="form-group mb-3">
                                        <label for="userDivisionId" class="form-control-label small font-weight-bold">Unit
                                            Kerja / Komisi</label>
                                        <div class="input-group input-group-merge shadow-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fe fe-briefcase"></i></span>
                                            </div>
                                            <select class="form-control" id="userDivisionId" name="division_id">
                                                <option value="">Pilih Unit Kerja</option>
                                                @foreach($divisions->groupBy('category') as $category => $items)
                                                    <optgroup label="{{ $category }}">
                                                        @foreach($items as $div)
                                                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 px-md-1">
                                    <div class="form-group mb-3">
                                        <label for="userPositionId"
                                            class="form-control-label small font-weight-bold">Jabatan / Posisi</label>
                                        <div class="input-group input-group-merge shadow-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fe fe-award"></i></span>
                                            </div>
                                            <select class="form-control" id="userPositionId" name="position_id">
                                                <option value="">Pilih Jabatan</option>
                                                @foreach($positions as $pos)
                                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-md-2">
                                    <div class="form-group mb-3">
                                        <label for="userReportTargetLabel"
                                            class="form-control-label small font-weight-bold">Label Tujuan Laporan
                                            (Opsional)</label>
                                        <div class="input-group input-group-merge shadow-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fe fe-edit-3"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="userReportTargetLabel"
                                                name="report_target_label" placeholder="Cth: Wakil Ketua PME">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Selection Grid -->
                        <div>
                            <div
                                class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mb-3 border-bottom pb-2">
                                <h6 class="heading-small text-muted mb-0 text-uppercase">Pilih Peran & Hak Akses Primer</h6>
                                <span class="selection-tip text-xs mt-2 mt-md-0"><i class="fe fe-info mr-1"></i>Peran
                                    dipadukan dengan Unit Kerja untuk menentukan modul.</span>
                            </div>

                            <div class="row">
                                <!-- Super Admin -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="super_admin" class="d-none role-input"
                                            required>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-primary-soft text-primary rounded p-2 mr-2">
                                                <i class="fe fe-shield"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">Super Admin</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Akses penuh ke seluruh sistem,
                                            master data, dan jobdesk.</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                                <!-- Sekretariat DJSN -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="DJSN" class="d-none role-input">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-success-soft text-success rounded p-2 mr-2">
                                                <i class="fe fe-check-square"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">Sekretariat DJSN</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Akses penuh level pimpinan
                                            sekretariat (Level 1).</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-success" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                                <!-- Tata Usaha -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="Tata Usaha" class="d-none role-input">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-info-soft text-info rounded p-2 mr-2">
                                                <i class="fe fe-file-text"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">Tata Usaha</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Kelola Kegiatan & Surat Tugas
                                            (Level 2).</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-info" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                                <!-- Persidangan -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="Persidangan" class="d-none role-input">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-warning-soft text-warning rounded p-2 mr-2">
                                                <i class="fe fe-users"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">Persidangan</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Notulensi, Absensi & Tindak
                                            Lanjut (Level 3).</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-warning" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                                <!-- Bagian Umum -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="Bagian Umum" class="d-none role-input">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-danger-soft text-danger rounded p-2 mr-2">
                                                <i class="fe fe-folder"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">Bagian Umum</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Upload & Kelola Dokumentasi
                                            (Level 4).</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-danger" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                                <!-- Keuangan -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="Keuangan" class="d-none role-input">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-secondary-soft text-secondary rounded p-2 mr-2">
                                                <i class="fe fe-credit-card"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">Keuangan</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Akses pencarian dan unduh
                                            dokumen laporan.</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-secondary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                                <!-- Tenaga Ahli -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="TA" class="d-none role-input">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-info-soft text-info rounded p-2 mr-2">
                                                <i class="fe fe-briefcase"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">Tenaga Ahli</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Informasi kegiatan berdasarkan
                                            komisi terkait.</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-info" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                                <!-- Anggota Dewan -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="Dewan" class="d-none role-input">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-dark text-white rounded p-2 mr-2">
                                                <i class="fe fe-star"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">Anggota Dewan</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Khusus Anggota Dewan (Disposisi
                                            otomatis).</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-dark" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                                <!-- User Biasa -->
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <label
                                        class="role-item h-100 d-flex flex-column p-3 mb-0 rounded-lg shadow-sm border bg-white cursor-pointer position-relative transition-all">
                                        <input type="radio" name="role" value="User" class="d-none role-input">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="icon-box bg-secondary text-dark rounded p-2 mr-2">
                                                <i class="fe fe-user"></i>
                                            </div>
                                            <h6 class="mb-0 font-weight-bold text-dark text-sm">User Terbatas</h6>
                                        </div>
                                        <small class="text-muted line-height-sm flex-grow-1">Akses level terbawah (Hanya
                                            melihat tabel dasar).</small>
                                        <div class="check-mark position-absolute opacity-0 transition-all"
                                            style="top: 15px; right: 15px;">
                                            <i class="fe fe-check-circle text-dark" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="active-border position-absolute w-100 h-100 rounded-lg"
                                            style="top:0; left:0; border: 2px solid transparent; pointer-events: none;">
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div
                        class="modal-footer user-modal-footer bg-white border-top py-3 rounded-bottom-xl d-flex justify-content-between">
                        <button type="button" class="btn btn-light text-muted rounded-pill px-4 font-weight-bold"
                            data-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-primary rounded-pill px-5 shadow-lg border-0 font-weight-bold user-modal-submit"
                            id="userModalSubmitBtn"><i class="fe fe-save mr-2"></i>Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });



        // SweetAlert2 for Delete Confirmation
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.confirm-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
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
        .master-users-table thead th {
            border-bottom: 2px solid #e9ecef;
            letter-spacing: 0.5px;
        }

        .text-xxs {
            font-size: 0.7rem;
        }

        .opacity-7 {
            opacity: 0.7;
        }

        .master-users-table .element-row:hover {
            background-color: #f8f9fe;
            transition: all 0.2s ease;
        }

        /* Role Badges */
        .master-users-role-badge {
            padding: 0.5em 0.8em;
            letter-spacing: 0.3px;
        }

        .bg-primary-soft {
            background-color: rgba(94, 114, 228, 0.15);
        }

        .bg-success-soft {
            background-color: rgba(45, 206, 137, 0.15);
        }

        .bg-info-soft {
            background-color: rgba(17, 205, 239, 0.15);
        }

        .bg-warning-soft {
            background-color: rgba(251, 99, 64, 0.15);
        }

        .bg-danger-soft {
            background-color: rgba(245, 54, 92, 0.15);
        }

        /* Modal Styling */
        .bg-secondary-soft {
            background-color: #f6f9fc;
        }

        .border-left-lg {
            border-left: 1px solid #e9ecef;
        }

        .user-modal-shell {
            border-radius: 1.5rem;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 26%);
        }

        .user-modal-header {
            background: linear-gradient(135deg, rgba(26, 54, 129, 0.08), rgba(17, 205, 239, 0.04));
            border-bottom: 1px solid rgba(17, 24, 39, 0.06);
        }

        .user-modal-title-icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, #1a3681, #2d6cdf);
            box-shadow: 0 12px 28px -18px rgba(26, 54, 129, 0.75);
        }

        .user-modal-body {
            background:
                radial-gradient(circle at top left, rgba(45, 206, 137, 0.07), transparent 32%),
                radial-gradient(circle at top right, rgba(94, 114, 228, 0.06), transparent 32%),
                #f8fbff;
        }

        .user-modal-banner {
            padding: 1rem 1.1rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(240, 247, 255, 0.96));
            border: 1px solid rgba(17, 24, 39, 0.06);
            box-shadow: 0 18px 40px -32px rgba(26, 54, 129, 0.45);
        }

        .user-modal-banner-badge {
            display: inline-flex;
            align-items: center;
            padding: .45rem .85rem;
            margin-bottom: .65rem;
            border-radius: 999px;
            background: rgba(26, 54, 129, 0.08);
            color: #1a3681;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .selection-tip {
            display: inline-flex;
            align-items: center;
            padding: .5rem .85rem;
            border-radius: 999px;
            background: #edf4ff;
            color: #31589c;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }

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

        .user-modal-footer {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0), #ffffff 35%);
            padding-top: 1rem;
            padding-bottom: 1.35rem;
        }

        .user-modal-submit {
            min-width: 220px;
            background: linear-gradient(135deg, #1a3681, #0f1f53);
        }

        @media (max-width: 991px) {
            .border-left-lg {
                border-left: none;
                margin-top: 1.5rem;
            }

            .selection-tip {
                white-space: normal;
            }

            .user-modal-footer {
                flex-direction: column-reverse;
                gap: .75rem;
            }

            .user-modal-submit,
            .user-modal-footer .btn {
                width: 100%;
            }

            .role-selector-container {
                max-height: none !important;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Init Sortable for EACH Group
            var tables = document.querySelectorAll('.sortable-group');

            tables.forEach(function (el) {
                Sortable.create(el, {
                    handle: '.handle',
                    animation: 150,
                    // Prevent dragging between groups (optional, usually default behavior for multiple lists without 'group' option)
                    onEnd: function (evt) {
                        var order = [];
                        // Only get rows within THIS specific tbody group
                        el.querySelectorAll('.element-row').forEach(function (row) {
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
                                if (data.success) {
                                    console.log('Group Order saved successfully');
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            });
        });

        // Delete Confirmation
        document.addEventListener('click', function (e) {
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

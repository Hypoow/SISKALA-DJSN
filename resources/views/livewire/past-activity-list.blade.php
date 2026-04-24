<div {{ $isModalOpen ? '' : '' }} x-data="{
    showUndo: false,
    startDelete(id, isBulk = false) {
        this.showUndo = true;
        if (isBulk) {
             @this.call('deleteSelected');
        } else {
             @this.call('delete', id);
        }
    },
    undoDelete() {
        @this.call('restoreDeleted');
        this.showUndo = false;
    },
    closeUndo() {
        @this.call('forceDeleteDeleted');
        this.showUndo = false;
    }
}" style="overflow: visible;">
    <!-- Undo Toast Notification -->
    <div x-show="showUndo" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-full"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-full"
         style="position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 1050; min-width: 320px; max-width: 90%;"
         class="shadow-lg rounded-lg overflow-hidden">
        <div class="bg-dark text-white p-3 d-flex align-items-center justify-content-between shadow-lg" style="background: #32325d !important;">
            <div class="d-flex align-items-center">
                <i class="fe fe-trash-2 text-danger mr-3" style="font-size: 1.5rem;"></i>
                <div>
                     <span class="font-weight-bold d-block" style="font-size: 0.95rem;">Kegiatan dihapus.</span>
                    <small class="text-white-50">Item telah dipindahkan ke sampah.</small>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <button @click="undoDelete()" class="btn btn-warning btn-sm font-weight-bold rounded-pill px-3 shadow-sm mr-3">
                    <i class="fe fe-rotate-ccw mr-1"></i> UNDO
                </button>
                <button @click="closeUndo()" class="btn btn-link text-white-50 p-0" title="Tutup & Hapus Permanen">
                    <i class="fe fe-x" style="font-size: 1.2rem;"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card shadow border-0 rounded-lg my-4" style="overflow: visible;">
        <!-- Header -->
        <div class="card-header bg-primary text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
             <div class="d-flex justify-content-between align-items-center">
                <div>
                     <h5 class="card-title mb-1 text-white font-weight-bold text-uppercase" style="letter-spacing: 1px;">Kegiatan Selesai</h5>
                     <p class="mb-0 text-white-50 small">Arsip kegiatan dan dokumen (Notulensi/Surat Tugas)</p>
                </div>
             </div>
        </div>

        <style>
            .custom-dropdown-menu {
                padding: 0.5rem !important;
                border: 1px solid #edf2f7 !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                border-radius: 0.5rem !important;
            }
            .custom-dropdown-item {
                border-radius: 0.375rem !important;
                padding: 0.5rem 1rem !important;
                color: #4a5568 !important;
                font-size: 0.95rem;
                transition: all 0.2s ease-in-out;
                margin-bottom: 2px;
                background-color: transparent;
            }
            .custom-dropdown-item:last-child {
                margin-bottom: 0;
            }
            .custom-dropdown-item:hover, .custom-dropdown-item:focus {
                background-color: #f7fafc !important;
                color: #2b6cb0 !important;
                transform: translateX(4px);
            }
            .custom-dropdown-item.bg-primary {
                background-color: #0052cc !important;
                color: white !important;
                transform: none;
            }

            /* Smooth pagination transition */
            .table-loading-wrapper {
                position: relative;
                transition: opacity 0.25s ease;
            }
            .table-loading-wrapper.is-loading {
                opacity: 0.45;
                pointer-events: none;
            }
            .table-loading-overlay {
                display: none;
                position: absolute;
                inset: 0;
                z-index: 10;
                background: linear-gradient(90deg,
                    rgba(255,255,255,0) 0%,
                    rgba(255,255,255,0.55) 50%,
                    rgba(255,255,255,0) 100%);
                background-size: 200% 100%;
                animation: shimmer-slide 1.2s infinite;
                border-radius: 0;
                pointer-events: none;
            }
            .table-loading-overlay.active {
                display: block;
            }
            @keyframes shimmer-slide {
                0%   { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }

            /* Spinner badge positioned at top-right of table area */
            .pagination-loading-badge {
                display: none;
                position: absolute;
                top: 12px;
                right: 16px;
                z-index: 20;
                background: rgba(255,255,255,0.92);
                border: 1px solid #e2e8f0;
                border-radius: 999px;
                padding: 5px 14px;
                font-size: 0.78rem;
                font-weight: 600;
                color: #2a5298;
                box-shadow: 0 2px 8px rgba(30,60,114,0.10);
                align-items: center;
                gap: 7px;
                backdrop-filter: blur(4px);
            }
            .pagination-loading-badge.active {
                display: flex;
            }
        </style>
        <!-- Toolbar -->
        <div class="bg-light border-bottom p-3" style="position: relative; z-index: 100; overflow: visible;">
             <div class="row align-items-center mx-n1" style="overflow: visible;">
                 
                 <!-- Search -->
                 <div class="col-12 col-md-3 px-1 mb-2 mb-md-0">
                     <div class="input-group input-group-merge bg-white shadow-sm rounded-pill overflow-hidden" style="border: 1px solid #e2e8f0;">
                         <input type="text" class="form-control border-0 pl-4 bg-transparent py-2" wire:model.live.debounce.300ms="search" placeholder="Cari kegiatan..." style="box-shadow: none;">
                         <div class="input-group-append">
                             <div class="input-group-text border-0 bg-transparent pr-4"><i class="fe fe-search text-muted"></i></div>
                         </div>
                     </div>
                 </div>

                 <!-- Year -->
                 <div class="col-6 col-md-1 px-1 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                     <div class="position-relative">
                         <button type="button" @click="open = !open" class="btn bg-white w-100 shadow-sm text-center d-flex align-items-center justify-content-center px-1 px-md-2 rounded-pill" style="border: 1px solid #e2e8f0;">
                             <span class="text-truncate font-weight-bold text-dark" x-text="$wire.year"></span>
                         </button>
                         <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1" :class="{ 'd-block': open }" x-show="open" x-transition style="display: none; max-height: 250px; overflow-y: auto; position: absolute; z-index: 1050;">
                             @foreach(range(date('Y'), 2023) as $y)
                                 <button type="button" class="dropdown-item custom-dropdown-item text-center w-100 border-0" :class="{ 'bg-primary text-white': $wire.year == {{ $y }} }" @click="$wire.set('year', '{{ $y }}'); open = false">{{ $y }}</button>
                             @endforeach
                         </div>
                     </div>
                 </div>

                 <!-- Month -->
                 <div class="col-6 col-md-2 px-1 mb-2 mb-md-0" x-data="{ open: false, getMonthName(m) { return m ? ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][m] : 'Bulan'; } }" @click.away="open = false">
                     <div class="position-relative">
                         <button type="button" @click="open = !open" class="btn bg-white w-100 shadow-sm text-left d-flex align-items-center justify-content-between px-3 rounded-pill" style="border: 1px solid #e2e8f0;">
                             <span class="text-truncate font-weight-bold text-dark" x-text="getMonthName($wire.month)">Bulan</span>
                             <i class="fe fe-chevron-down ml-1 text-muted" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                         </button>
                         <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1" :class="{ 'd-block': open }" x-show="open" x-transition style="display: none; max-height: 250px; overflow-y: auto; position: absolute; z-index: 1050;">
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '' }" @click="$wire.set('month', ''); open = false">Semua</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '1' }" @click="$wire.set('month', '1'); open = false">Januari</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '2' }" @click="$wire.set('month', '2'); open = false">Februari</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '3' }" @click="$wire.set('month', '3'); open = false">Maret</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '4' }" @click="$wire.set('month', '4'); open = false">April</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '5' }" @click="$wire.set('month', '5'); open = false">Mei</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '6' }" @click="$wire.set('month', '6'); open = false">Juni</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '7' }" @click="$wire.set('month', '7'); open = false">Juli</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '8' }" @click="$wire.set('month', '8'); open = false">Agustus</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '9' }" @click="$wire.set('month', '9'); open = false">September</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '10' }" @click="$wire.set('month', '10'); open = false">Oktober</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '11' }" @click="$wire.set('month', '11'); open = false">November</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.month == '12' }" @click="$wire.set('month', '12'); open = false">Desember</button>
                         </div>
                     </div>
                 </div>

                 <!-- Type -->
                 <div class="col-12 col-md-2 px-1 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                     <div class="position-relative">
                         <button type="button" @click="open = !open" class="btn bg-white w-100 shadow-sm text-left d-flex align-items-center justify-content-between px-3 rounded-pill" style="border: 1px solid #e2e8f0;">
                             <span class="text-truncate font-weight-bold text-dark" x-text="$wire.type === 'external' ? 'Eksternal' : ($wire.type === 'internal' ? 'Internal' : 'Tipe Kegiatan')">Tipe Kegiatan</span>
                             <i class="fe fe-chevron-down ml-1 text-muted" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                         </button>
                         <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1" :class="{ 'd-block': open }" x-show="open" x-transition style="display: none; position: absolute; z-index: 1050;">
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.type === '' }" @click="$wire.set('type', ''); open = false">Semua</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.type === 'internal' }" @click="$wire.set('type', 'internal'); open = false">Internal</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.type === 'external' }" @click="$wire.set('type', 'external'); open = false">Eksternal</button>
                         </div>
                     </div>
                 </div>

                 <!-- PIC -->
                 <div class="col-12 col-md-2 px-1 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                     <div class="position-relative">
                         <button type="button" @click="open = !open" class="btn bg-white w-100 shadow-sm text-left d-flex align-items-center justify-content-between px-3 rounded-pill" style="border: 1px solid #e2e8f0;">
                             <span class="text-truncate font-weight-bold text-dark" x-text="$wire.pic ? $wire.pic : 'PIC Kegiatan'">PIC Kegiatan</span>
                             <i class="fe fe-chevron-down ml-1 text-muted" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                         </button>
                         <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1" :class="{ 'd-block': open }" x-show="open" x-transition style="display: none; max-height: 250px; overflow-y: auto; position: absolute; z-index: 1050;">
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.pic === '' }" @click="$wire.set('pic', ''); open = false">Semua PIC</button>
                             @foreach(\App\Models\Activity::INTERNAL_PICS as $opt)
                                <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.pic === '{{ $opt }}' }" @click="$wire.set('pic', '{{ $opt }}'); open = false">{{ $opt }}</button>
                             @endforeach
                         </div>
                     </div>
                 </div>

                 <!-- Sort -->
                 <div class="col-12 col-md-2 px-1 mb-0" x-data="{ open: false }" @click.away="open = false">
                     <div class="position-relative">
                         <button type="button" @click="open = !open" class="btn bg-white w-100 shadow-sm text-left d-flex align-items-center justify-content-between px-3 rounded-pill" style="border: 1px solid #e2e8f0;">
                             <div class="d-flex align-items-center" style="overflow: hidden;">
                                 <i class="fe fe-filter mr-1 text-muted flex-shrink-0"></i>
                                 <span class="text-truncate font-weight-bold text-dark" x-text="$wire.sortDirection === 'desc' ? 'Terbaru' : 'Terlama'">Terbaru</span>
                             </div>
                             <i class="fe fe-chevron-down ml-1 text-muted flex-shrink-0" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                         </button>
                         <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1 dropdown-menu-right" :class="{ 'd-block': open }" x-show="open" x-transition style="display: none; position: absolute; z-index: 1050;">
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.sortDirection === 'desc' }" @click="$wire.set('sortDirection', 'desc'); open = false"><i class="fe fe-arrow-down mr-2"></i>Terbaru</button>
                             <button type="button" class="dropdown-item custom-dropdown-item text-left w-100 border-0" :class="{ 'bg-primary text-white': $wire.sortDirection === 'asc' }" @click="$wire.set('sortDirection', 'asc'); open = false"><i class="fe fe-arrow-up mr-2"></i>Terlama</button>
                         </div>
                     </div>
                 </div>

             </div>
        </div>

        @if (session()->has('success_upload'))
            <div class="alert alert-success alert-dismissible fade show m-3 border-0 shadow-sm" role="alert">
                <i class="fe fe-check-circle mr-2"></i> {{ session('success_upload') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card-body p-0">
             <!-- Bulk Actions -->
            @if(auth()->check() && auth()->user()->canManageActivities())
                <div class="col-12 p-0" 
                     x-data="{ count: @entangle('selected').live }" 
                     x-show="count && count.length > 0" 
                     x-cloak 
                     style="display: none;"
                     x-transition>
                    <div class="alert alert-primary border-0 rounded-0 mb-0 d-flex align-items-center justify-content-between py-3 px-4">
                        <span class="text-red"><i class="fe fe-check-circle mr-2"></i> <strong x-text="count ? count.length : 0"></strong> kegiatan terpilih.</span>
                        <button type="button" 
                                @click="startDelete(null, true)"
                                class="btn btn-sm btn-light text-danger font-weight-bold shadow-sm">
                            <i class="fe fe-trash-2 mr-1"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>
            @endif

             <!-- Table -->
             <div class="table-loading-wrapper" id="pastActivityTableWrapper">
                 <!-- Shimmer overlay shown during loading -->
                 <div class="table-loading-overlay" id="pastTableShimmer"></div>
                 <!-- "Memuat..." badge shown in top-right while loading -->
                 <div class="pagination-loading-badge" id="pastTableLoadingBadge">
                     <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width:12px;height:12px;border-width:2px;"></span>
                     Memuat data...
                 </div>
             <div class="table-responsive">
                 <table class="table mb-0">
                     <thead class="bg-light">
                         <tr>
                             @if(auth()->check() && auth()->user()->canManageActivities())
                                <th class="pl-4 align-middle" style="width: 5%;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAllPast" wire:model.live="selectAll">
                                        <label class="custom-control-label" for="selectAllPast"></label>
                                    </div>
                                </th>
                             @endif
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4">Tanggal & Waktu</th>
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kegiatan</th>
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status</th>
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Dokumen</th>
                              @if(!auth()->check() || !auth()->user()->hasRole('Dewan'))
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Surat Tugas</th>
                              @else
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Kehadiran</th>
                              @endif
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Dokumentasi</th>
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                         </tr>
                     </thead>
                     <tbody>
                        @forelse($groupedActivities as $month => $monthActivities)
                            <tr class="bg-light">
                                <td colspan="{{ auth()->check() && auth()->user()->canManageActivities() ? 7 : 6 }}" class="py-2 pl-4">
                                    <h6 class="mb-0 text-primary font-weight-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">
                                        <i class="fe fe-calendar mr-2"></i>{{ $month }}
                                    </h6>
                                </td>
                            </tr>
                            @foreach($monthActivities as $activity)
                                <tr style="border-left: 4px solid {{ $activity->type == 'internal' ? '#004085' : '#17a2b8' }};" wire:key="row-{{ $activity->id }}">
                                    @if(auth()->check() && auth()->user()->canManageActivities())
                                    <td class="pl-4 align-middle">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check_{{ $activity->id }}" value="{{ $activity->id }}" wire:model.live="selected">
                                            <label class="custom-control-label" for="check_{{ $activity->id }}"></label>
                                        </div>
                                    </td>
                                    @endif
                                    <td class="align-middle pl-4" style="min-width: 150px;">
                                        <div class="d-flex align-items-center">
                                            <div class="text-center rounded p-2 shadow-sm text-white" style="min-width: 50px; background-color: {{ $activity->type == 'internal' ? '#004085' : '#17a2b8' }};">
                                                <span class="d-block font-weight-bold small text-uppercase" style="line-height:1;">{{ $activity->date_time->format('M') }}</span>
                                                <span class="d-block font-weight-bold h4 mb-0" style="line-height:1; color: #d3d3d3;">{{ $activity->date_time->format('d') }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <h6 class="mb-0 font-weight-bold text-dark">{{ $activity->date_time->format('H:i') }} WIB</h6>
                                                <small class="text-muted text-uppercase font-weight-bold">{{ $activity->date_time->isoFormat('dddd') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('activities.show', $activity->id) }}" class="text-dark font-weight-bold mb-1 d-block text-decoration-none h6">
                                            {{ $activity->name }}
                                        </a>
                                        <small class="text-muted d-block mb-1">
                                            <i class="fe fe-map-pin mr-1"></i>
                                            @if($activity->location_type == 'online' && !$activity->location)
                                                Pelaksanaan secara daring (tautan tersedia di detail).
                                            @else
                                                {{ $activity->location ?? 'Tidak ada detail lokasi' }}
                                            @endif
                                        </small>

                                        {{-- Location Type Badge --}}
                                        <div class="mt-2 mb-2">
                                            @php
                                                $locType = $activity->location_type;
                                                $locClass = $locType == 'online' ? 'bg-primary' : ($locType == 'hybrid' ? 'bg-success' : 'bg-secondary');
                                                $locIcon = $locType == 'online' ? 'fe-video' : ($locType == 'hybrid' ? 'fe-monitor' : 'fe-map-pin');
                                            @endphp
                                            <span class="badge {{ $locClass }} text-white px-3 py-1 shadow-sm" style="font-size: 0.65rem; letter-spacing: 0.5px; border-radius: 50px;">
                                                <i class="fe {{ $locIcon }} mr-1"></i> {{ strtoupper($locType) }}
                                            </span>
                                        </div>
                                        
                                        {{-- PIC Badges --}}
                                        @if(!empty($activity->display_pic_groups))
                                            <div class="d-flex flex-wrap mt-1">
                                                @php
                                                    $pics = $activity->display_pic_groups;
                                                    
                                                    // Mapping matching App\Livewire\ActivityList::getPicColor
                                                    $classMap = [
                                                        'Ketua DJSN' => 'badge-ketua',
                                                        'Komisi PME' => 'badge-pme',
                                                        'Komjakum' => 'badge-komjakum',
                                                        'Sekretariat DJSN' => 'badge-sekretariat',
                                                        'Sekretaris DJSN' => 'badge-sekretariat',
                                                        'Anggota DJSN' => 'badge-djsn'
                                                    ];

                                                    // Priority Sort
                                                    $priority = [
                                                        'Ketua DJSN' => 1,
                                                        'Komisi PME' => 2,
                                                        'Komjakum' => 3,
                                                        'Sekretariat DJSN' => 4,
                                                        'Sekretaris DJSN' => 4
                                                    ];

                                                    usort($pics, function($a, $b) use ($priority) {
                                                        $pa = $priority[$a] ?? 99;
                                                        $pb = $priority[$b] ?? 99;
                                                        return $pa <=> $pb;
                                                    });
                                                @endphp
                                                @foreach($pics as $picName)
                                                    @php
                                                        $badgeClass = 'badge-primary'; // Fallback
                                                        foreach ($classMap as $key => $cls) {
                                                            if (str_contains(strtoupper($picName), strtoupper($key)) || 
                                                                (str_contains($picName, 'PME') && $key == 'Komisi PME') ||
                                                                (str_contains($picName, 'Komjakum') && $key == 'Komjakum')
                                                            ) {
                                                                $badgeClass = $cls;
                                                                break;
                                                            }
                                                        }
                                                        // Fallback specific checks if needed (copied from ActivityList logic)
                                                        if (str_contains(strtoupper($picName), 'PME')) $badgeClass = 'badge-pme';
                                                        if (str_contains(strtoupper($picName), 'KOMJAKUM')) $badgeClass = 'badge-komjakum';
                                                        if (str_contains(strtoupper($picName), 'SEKRETARIAT') || str_contains(strtoupper($picName), 'SEKRETARIS')) $badgeClass = 'badge-sekretariat';

                                                    @endphp
                                                    <span class="badge badge-pill {{ $badgeClass }} mr-1 mb-1 px-2" style="font-size: 0.65rem;">
                                                        {{ $picName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                         @switch($activity->status)
                                            @case(0) <span class="badge badge-pill badge-light text-success border border-success px-3">Terlaksana</span> @break
                                            @case(1) <span class="badge badge-pill badge-light text-warning border border-warning px-3">Reschedule</span> @break
                                            @case(2) <span class="badge badge-pill badge-light text-secondary border border-secondary px-3">Menunggu Disposisi</span> @break
                                            @case(3) <span class="badge badge-pill badge-light text-danger border border-danger px-3">Batal</span> @break
                                        @endswitch

                                        <div class="mt-2">
                                            @if(!empty($activity->summary_content) && trim(strip_tags($activity->summary_content)) != '')
                                                <small class="text-success font-weight-bold" 
                                                    style="cursor: pointer;" wire:click="openSummaryModal({{ $activity->id }})" title="Klik untuk lihat ringkasan">
                                                    <span wire:loading.remove wire:target="openSummaryModal({{ $activity->id }})"><i class="fe fe-check-circle mr-1"></i></span>
                                                    <span wire:loading wire:target="openSummaryModal({{ $activity->id }})" class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                                    Ringkasan Rapat Terisi
                                                </small>
                                            @else
                                                @if(auth()->user()->canManagePostActivity())
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm px-3"
                                                        wire:click="openSummaryModal({{ $activity->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="openSummaryModal({{ $activity->id }})"
                                                        title="Isi ringkasan rapat"
                                                    >
                                                        <span wire:loading.remove wire:target="openSummaryModal({{ $activity->id }})">
                                                            <i class="fe fe-edit-2 mr-1"></i> Isi Ringkasan
                                                        </span>
                                                        <span wire:loading wire:target="openSummaryModal({{ $activity->id }})">
                                                            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Membuka...
                                                        </span>
                                                    </button>
                                                @else
                                                    <span class="badge badge-light border text-muted px-3 py-2">
                                                        <i class="fe fe-file-text mr-1"></i> Ringkasan Rapat Belum Diisi
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-middle text-center" style="min-width: 170px;">
                                        <!-- MoM (Minutes of Meeting) Button -->
                                        <div class="mb-2">
                                            <button type="button" onclick="$('#momModal').modal('show')" wire:click="openMomModal({{ $activity->id }})" wire:loading.attr="disabled" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm px-3 w-100 d-flex align-items-center justify-content-center text-nowrap">
                                                <span wire:loading.remove wire:target="openMomModal({{ $activity->id }})"><i class="fe fe-file-text mr-2"></i></span>
                                                <span wire:loading wire:target="openMomModal({{ $activity->id }})" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                                MoM
                                                @if($activity->moms->count() > 0)
                                                    <span class="badge badge-primary ml-2">{{ $activity->moms->count() }}</span>
                                                @endif
                                            </button>
                                        </div>
                                        
                                        <!-- Materials Button -->
                                        <div>
                                            <button type="button" onclick="$('#materialModal').modal('show')" wire:click="openMaterialModal({{ $activity->id }})" wire:loading.attr="disabled" class="btn btn-sm btn-outline-info rounded-pill shadow-sm px-3 w-100 d-flex align-items-center justify-content-center text-nowrap">
                                                <span wire:loading.remove wire:target="openMaterialModal({{ $activity->id }})"><i class="fe fe-folder mr-2"></i></span>
                                                <span wire:loading wire:target="openMaterialModal({{ $activity->id }})" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                                Bahan Materi 
                                                @if($activity->materials->count() > 0)
                                                    <span class="badge badge-info ml-2">{{ $activity->materials->count() }}</span>
                                                @elseif($activity->shows_no_materials_notice)
                                                    <span class="badge badge-secondary ml-2">Tidak Ada</span>
                                                @endif
                                            </button>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center" style="min-width: 140px;">
                                        <button class="btn btn-sm btn-outline-light border text-dark shadow-sm px-3 py-2 text-left" wire:click="openAssignmentModal({{ $activity->id }})" wire:loading.attr="disabled" style="min-width: 140px; position: relative;">
                                            <div wire:loading.remove wire:target="openAssignmentModal({{ $activity->id }})">
                                                <div class="d-flex align-items-center mb-1">
                                                     @if(!empty($activity->attendance_list))
                                                        <i class="fe fe-check-circle text-success mr-2"></i>
                                                    @else
                                                        <i class="fe fe-minus text-muted mr-2"></i>
                                                    @endif
                                                     <span class="small font-weight-bold">Kehadiran</span>
                                                </div>
                                                
                                                @if(!auth()->check() || !auth()->user()->hasRole('Dewan'))
                                                <div class="d-flex align-items-center">
                                                    @if($activity->assignment_letter_path)
                                                        <i class="fe fe-check-circle text-success mr-2"></i>
                                                    @else
                                                        <i class="fe fe-minus text-muted mr-2"></i>
                                                    @endif
                                                     <span class="small font-weight-bold">Surat Tugas</span>
                                                </div>
                                                @endif
                                            </div>
                                            <div wire:loading wire:target="openAssignmentModal({{ $activity->id }})" class="w-100 text-center py-2">
                                                 <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                                                 <span class="d-block small text-muted mt-1">Loading...</span>
                                            </div>
                                        </button>
                                    </td>
                                    <td class="align-middle text-center" style="min-width: 140px;">
                                        @php $docCount = $activity->documentations->count(); @endphp
                                        <button class="btn btn-sm {{ $docCount > 0 ? 'btn-outline-primary' : 'btn-outline-secondary' }} rounded-pill shadow-sm px-3" wire:click="openDocumentationModal({{ $activity->id }})" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="openDocumentationModal({{ $activity->id }})"><i class="fe fe-image mr-1"></i></span>
                                            <span wire:loading wire:target="openDocumentationModal({{ $activity->id }})" class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                            @if(auth()->check() && auth()->user()->canManageDocumentation())
                                                {{ $docCount > 0 ? $docCount.' Foto' : 'Upload' }}
                                            @else
                                                {{ $docCount }} Foto
                                            @endif
                                        </button>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon btn-light shadow-sm" type="button" data-toggle="dropdown">
                                                <i class="fe fe-more-vertical text-muted"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right shadow-lg border-0">
                                                <a class="dropdown-item" href="{{ route('activities.show', $activity->id) }}">
                                                    <i class="fe fe-eye mr-2 text-primary"></i> Detail
                                                </a>
                                                @if(auth()->user()->canManageActivities())
                                                <a class="dropdown-item" href="{{ route('activities.edit', $activity->id) }}">
                                                    <i class="fe fe-edit mr-2 text-warning"></i> Edit
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <button type="button" class="dropdown-item text-danger" @click="startDelete({{ $activity->id }})">
                                                    <i class="fe fe-trash-2 mr-2"></i> Hapus
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && auth()->user()->canManageActivities() ? 7 : 6 }}" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-light rounded-circle p-4 mb-3">
                                            <i class="fe fe-archive text-muted display-4"></i>
                                        </div>
                                        <h5 class="text-muted font-weight-bold">Tidak ada kegiatan selesai</h5>
                                        <p class="text-muted small">Kegiatan yang telah selesai akan muncul di sini.</p>
                                    </div>
                                </td>
                         </tr>
                        @endforelse
                     </tbody>
                 </table>
             </div>{{-- /table-responsive --}}
             </div>{{-- /table-loading-wrapper --}}
             <div class="p-3 border-top bg-light">
                 {{ $activities->links('vendor.livewire.custom-pagination') }}
             </div>
        </div>
    </div>
    
    <script>
        /* Smooth in-place pagination - no scroll jump */
        (function () {
            var wrapper = document.getElementById('pastActivityTableWrapper');
            var shimmer = document.getElementById('pastTableShimmer');
            var badge   = document.getElementById('pastTableLoadingBadge');

            function showLoading() {
                if (wrapper) wrapper.classList.add('is-loading');
                if (shimmer) shimmer.classList.add('active');
                if (badge)   badge.classList.add('active');
            }
            function hideLoading() {
                if (wrapper) wrapper.classList.remove('is-loading');
                if (shimmer) shimmer.classList.remove('active');
                if (badge)   badge.classList.remove('active');
            }

            // Livewire 3 global request hooks
            document.addEventListener('livewire:request', showLoading);
            document.addEventListener('livewire:response', hideLoading);

            // Livewire 2 fallback
            document.addEventListener('livewire:load', function () {
                if (window.Livewire && Livewire.hook) {
                    Livewire.hook('message.sent',      showLoading);
                    Livewire.hook('message.processed', hideLoading);
                    Livewire.hook('message.failed',    hideLoading);
                }
            });
        })();
    </script>

    <script>
        function confirmModalDelete(type) {
            let title = '';
            let text = '';
            let method = '';

            if (type === 'assignment') {
                title = 'Hapus Surat Tugas?';
                text = 'File surat tugas akan dihapus permanen.';
                method = 'deleteAssignmentInModal';
            } else if (type === 'attachment') {
                title = 'Hapus Surat Undangan?';
                text = 'File surat undangan akan dihapus permanen.';
                method = 'deleteAttachmentInModal';
            } else if (type === 'minutes') {
                title = 'Hapus Notulensi?';
                text = 'File notulensi akan dihapus permanen.';
                method = 'deleteMinutesInModal';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call(method);
                }
            })
        }

        function confirmDeleteFile(type, id) {
            let title = '';
            let text = '';
            let method = '';

            if (type === 'minutes') {
                title = 'Hapus Notulensi?';
                text = 'File notulensi akan dihapus permanen.';
                method = 'deleteMinutes';
            } else if (type === 'material') {
                title = 'Hapus Materi?';
                text = 'File materi akan dihapus permanen.';
                method = 'deleteMaterial';
            } else if (type === 'documentation') {
                title = 'Hapus Foto?';
                text = 'Foto dokumentasi akan dihapus permanen.';
                method = 'deleteDocumentationFile';
            } else if (type === 'mom') {
                title = 'Hapus MoM?';
                text = 'File MoM akan dihapus permanen.';
                method = 'deleteMom';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call(method, id);
                }
            })
        }

        window.addEventListener('close-assignment-modal', event => {
            $('#assignmentModal').modal('hide');
        });
        
        window.addEventListener('open-assignment-modal', event => {
            $('#assignmentModal').modal('show');
        });

        window.addEventListener('open-documentation-modal', event => {
            $('#documentationModal').modal('show');
        });
        
        window.addEventListener('open-material-modal', event => {
            $('#materialModal').modal('show');
            window.setPastMaterialUploadNoticeName('');
        });

        window.addEventListener('close-material-modal', event => {
            $('#materialModal').modal('hide');
            window.setPastMaterialUploadNoticeName('');
        });

        window.addEventListener('open-mom-modal', event => {
            $('#momModal').modal('show');
            window.setPastMomUploadNoticeName('');
        });
        
        window.addEventListener('close-mom-modal', event => {
            $('#momModal').modal('hide');
            window.setPastMomUploadNoticeName('');
        });

        window.setPastMomUploadNoticeName = function (fileName) {
            const nameEl = document.getElementById('past_mom_uploading_name');

            if (!nameEl) {
                return;
            }

            nameEl.textContent = fileName || 'File MoM sedang diproses';
        };

        window.handlePastMomFileSelection = function (input) {
            var fileName = '';

            if (input && input.files && input.files.length > 0) {
                fileName = input.files[0].name;
            }

            window.setPastMomUploadNoticeName(fileName);
        };

        window.setPastMaterialUploadNoticeName = function (fileName) {
            const nameEl = document.getElementById('past_material_uploading_name');

            if (!nameEl) {
                return;
            }

            nameEl.textContent = fileName || 'File materi sedang diproses';
        };

        window.handlePastMaterialFileSelection = function (input) {
            var fileName = '';

            if (input && input.files && input.files.length > 0) {
                fileName = input.files[0].name;
            }

            window.setPastMaterialUploadNoticeName(fileName);
        };

        document.addEventListener('livewire:initialized', () => {
             $('#assignmentModal').on('hidden.bs.modal', function () {
                @this.call('closeModalState');
            });
            $('#materialModal').on('hidden.bs.modal', function () {
                @this.call('closeModalState');
            });
            $('#momModal').on('hidden.bs.modal', function () {
                @this.call('closeModalState');
            });
            $('#documentationModal').on('hidden.bs.modal', function () {
                @this.call('closeModalState');
            });

            window.addEventListener('show-alert', event => {
                Swal.fire({
                    icon: event.detail.type || 'success',
                    title: 'Berhasil!',
                    text: event.detail.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            });

            function initStaffSelect2() {
                if ($('.select2-staff-sekretariat').length > 0) {
                     $('.select2-staff-sekretariat').select2({
                        theme: 'bootstrap4',
                        placeholder: 'Cari atau Ketik Nama Sekretariat...',
                        allowClear: true,
                        tags: true,
                        dropdownParent: $('#wrapper-sekretariat')
                    }).on('change', function (e) {
                        var data = $(this).val();
                        @this.set('selectedSekretariat', data);
                    });
                }

                if ($('.select2-staff-ta').length > 0) {
                    $('.select2-staff-ta').select2({
                        theme: 'bootstrap4',
                        placeholder: 'Cari atau Ketik Nama Tenaga Ahli...',
                        allowClear: true,
                        tags: true,
                        dropdownParent: $('#wrapper-ta')
                    }).on('change', function (e) {
                        var data = $(this).val();
                        @this.set('selectedTA', data);
                    });
                }
            }

            // Init on load (in case logic changes and it's visible)
            initStaffSelect2();

            window.addEventListener('open-assignment-modal', event => {
                $('#assignmentModal').modal('show');
                // Re-init select2 after modal shows/DOM updates
                setTimeout(() => {
                    initStaffSelect2();
                    
                    // Also trigger update if data was sent (optional, usually handled by update-staff-select but that might run before init)
                    // If we have data in the component, we might want to sync. 
                    // But typically update-staff-select is dispatched AFTER open-assignment-modal.
                }, 100); 
            });

            window.addEventListener('update-staff-select', event => {
                 // Wait for init
                 setTimeout(() => {
                    $('.select2-staff-sekretariat').val(event.detail.sekretariat).trigger('change');
                    $('.select2-staff-ta').val(event.detail.ta).trigger('change');
                 }, 150);
            });
        });
    </script>

    <!-- Material Management Modal -->
    <div wire:ignore.self class="modal fade" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="materialModalLabel" aria-hidden="true" style="z-index: 10000 !important;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="materialModalLabel">Kelola Bahan Materi Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php $hasMaterialFiles = collect($materialList)->isNotEmpty(); @endphp

                    @if(auth()->user()->canManagePostActivity())
                    <div class="alert {{ $hasNoMaterials ? 'alert-warning' : 'alert-light' }} border shadow-sm d-flex justify-content-between align-items-start flex-column flex-md-row">
                        <div class="pr-md-3">
                            <div class="custom-control custom-checkbox">
                                <input
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="pastHasNoMaterials"
                                    wire:change="toggleNoMaterialStatus"
                                    {{ $hasNoMaterials ? 'checked' : '' }}
                                    {{ $hasMaterialFiles ? 'disabled' : '' }}
                                >
                                <label class="custom-control-label font-weight-bold" for="pastHasNoMaterials">
                                    Kegiatan ini tidak memiliki bahan materi
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">
                                @if($hasMaterialFiles)
                                    Hapus semua file bahan materi terlebih dahulu jika ingin menandai kegiatan tanpa bahan materi.
                                @else
                                    Centang jika kegiatan memang tidak memiliki file bahan materi.
                                @endif
                            </small>
                        </div>
                        @if($hasNoMaterials)
                            <span class="badge badge-warning mt-3 mt-md-0">Tidak ada bahan materi</span>
                        @endif
                    </div>
                    @endif

                    @if (session()->has('success_material'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success_material') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Add New Material Form -->
                    @if(auth()->user()->canManagePostActivity())
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3">Tambah Materi Baru</h6>
                            <form wire:submit.prevent="saveMaterial">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Judul Materi <span class="text-danger">*</span></label>
                                            <input type="text" wire:model="newMaterialTitle" class="form-control" placeholder="Contoh: Slide Paparan Narasumber A" required>
                                            @error('newMaterialTitle') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>File Materi (Max 20MB)</label>
                                            <div class="custom-file">
                                                <input type="file" wire:model="newMaterialFile" class="custom-file-input" id="materialFile" onchange="window.handlePastMaterialFileSelection(this)">
                                                <label class="custom-file-label" for="materialFile">
                                                    {{ $newMaterialFile ? $newMaterialFile->getClientOriginalName() : 'Pilih file...' }}
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mb-2">
                                                Format file: PDF, DOC/DOCX, PPT/PPTX, XLS/XLSX, dan CSV.
                                            </small>
                                            @error('newMaterialFile') <span class="text-danger small">{{ $message }}</span> @enderror
                                            <div wire:loading.flex wire:target="newMaterialFile" class="mt-2 align-items-start border rounded bg-white shadow-sm px-3 py-2">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded mr-3 flex-shrink-0" style="width: 34px; height: 34px;">
                                                    <i class="fe fe-folder text-info"></i>
                                                </div>
                                                <div class="flex-fill overflow-hidden">
                                                    <div class="font-weight-bold text-dark small">Mengupload 1 item</div>
                                                    <div class="text-muted small mb-2">Menyelesaikan upload...</div>
                                                    <div class="d-flex align-items-center">
                                                        <span id="past_material_uploading_name" class="small font-weight-bold text-dark text-truncate" style="max-width: 220px;">
                                                            {{ $newMaterialFile ? $newMaterialFile->getClientOriginalName() : 'File materi sedang diproses' }}
                                                        </span>
                                                        <span class="spinner-border spinner-border-sm text-info ml-2" role="status" aria-hidden="true"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled" wire:target="saveMaterial,newMaterialFile">
                                        <i class="fe fe-plus"></i> Tambahkan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Material List -->
                    <h6 class="font-weight-bold mb-3">Daftar Materi Tersimpan</h6>
                    @if(collect($materialList)->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table align-items-center w-100">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Judul Materi</th>
                                        <th>File</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($materialList as $material)
                                        <tr>
                                            <td class="font-weight-bold text-dark">{{ $material->title }}</td>
                                            <td>
                                                @php
                                                    $ext = strtolower(pathinfo($material->file_path, PATHINFO_EXTENSION));
                                                    $icon = 'fe-file';
                                                    $color = 'text-secondary';
                                                    
                                                    if(in_array($ext, ['ppt', 'pptx'])) {
                                                        $icon = 'fe-monitor';
                                                        $color = 'text-warning';
                                                    } elseif(in_array($ext, ['doc', 'docx'])) {
                                                        $icon = 'fe-file-text';
                                                        $color = 'text-primary';
                                                    } elseif($ext == 'pdf') {
                                                        $icon = 'fe-file';
                                                        $color = 'text-danger';
                                                    } elseif(in_array($ext, ['xls', 'xlsx', 'csv'])) {
                                                        $icon = 'fe-bar-chart-2';
                                                        $color = 'text-success';
                                                    }
                                                @endphp
                                                <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="d-flex align-items-center text-dark text-decoration-none">
                                                    <div class="avatar avatar-sm mr-2 {{ $color }} bg-light rounded">
                                                        <i class="fe {{ $icon }} font-weight-bold" style="font-size: 1.2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <span class="d-block font-weight-bold small text-truncate" style="max-width: 200px;">{{ basename($material->file_path) }}</span>
                                                        <span class="badge badge-light border text-uppercase" style="font-size: 10px;">{{ $ext }}</span>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteFile('material', {{ $material->id }})" {{ !auth()->user()->canManagePostActivity() ? 'disabled' : '' }}>
                                                    <i class="fe fe-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="border rounded-lg bg-light text-center py-5 px-3 w-100">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm mb-3" style="width: 72px; height: 72px;">
                                <i class="fe fe-folder text-secondary" style="font-size: 2rem;"></i>
                            </div>
                            @if($hasNoMaterials)
                                <p class="font-weight-bold text-muted mb-1">Kegiatan ini ditandai tidak memiliki bahan materi.</p>
                                <p class="text-muted mb-0 small">Unggah file kapan saja jika status kegiatan berubah.</p>
                            @else
                                <p class="text-muted mb-0">Belum ada bahan materi yang diupload.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- MoM Management Modal -->
    <div wire:ignore.self class="modal fade" id="momModal" tabindex="-1" role="dialog" aria-labelledby="momModalLabel" aria-hidden="true" style="z-index: 10000 !important;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="momModalLabel">Kelola MoM (Notulensi) Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (session()->has('success_mom'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success_mom') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Add New MoM Form -->
                    @if(auth()->user()->canManagePostActivity())
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3">Tambah MoM Baru</h6>
                            <form wire:submit.prevent="saveMom">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Judul MoM <span class="text-danger">*</span></label>
                                            <input type="text" wire:model="newMomTitle" class="form-control" placeholder="Contoh: Notulensi Rapat Internal" required>
                                            @error('newMomTitle') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>File MoM (Max 20MB)</label>
                                            
                                            <div class="custom-file">
                                                <input
                                                    type="file"
                                                    wire:model="newMomFile"
                                                    class="custom-file-input"
                                                    id="momFile"
                                                    accept=".pdf,application/pdf"
                                                    onchange="window.handlePastMomFileSelection(this)">
                                                <label class="custom-file-label" for="momFile">
                                                    {{ $newMomFile ? $newMomFile->getClientOriginalName() : 'Pilih file...' }}
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mb-2">
                                                Format file yang didukung: PDF. Ukuran maksimal 20 MB per file.
                                            </small>
                                            @error('newMomFile') <span class="text-danger small">{{ $message }}</span> @enderror
                                            <div wire:loading.flex wire:target="newMomFile" class="mt-2 align-items-start border rounded bg-white shadow-sm px-3 py-2">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded mr-3 flex-shrink-0" style="width: 34px; height: 34px;">
                                                    <i class="fe fe-file-text text-primary"></i>
                                                </div>
                                                <div class="flex-fill overflow-hidden">
                                                    <div class="font-weight-bold text-dark small">Mengupload 1 item</div>
                                                    <div class="text-muted small mb-2">Menyelesaikan upload...</div>
                                                    <div class="d-flex align-items-center">
                                                        <span id="past_mom_uploading_name" class="small font-weight-bold text-dark text-truncate" style="max-width: 220px;">
                                                            {{ $newMomFile ? $newMomFile->getClientOriginalName() : 'File MoM sedang diproses' }}
                                                        </span>
                                                        <span class="spinner-border spinner-border-sm text-primary ml-2" role="status" aria-hidden="true"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled" wire:target="saveMom,newMomFile">
                                        <i class="fe fe-plus"></i> Tambahkan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- MoM List -->
                    <h6 class="font-weight-bold mb-3">Daftar MoM Tersimpan</h6>
                    @if(collect($momList)->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table align-items-center w-100">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Judul MoM</th>
                                        <th>File</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($momList as $mom)
                                        <tr wire:key="mom-row-{{ $mom->id }}">
                                            <td class="align-middle">
                                                @if($editingMomId === $mom->id)
                                                    <div>
                                                        <input
                                                            type="text"
                                                            wire:model.defer="editingMomTitle"
                                                            wire:keydown.enter.prevent="updateMom"
                                                            class="form-control form-control-sm"
                                                            placeholder="Masukkan judul MoM">
                                                        @error('editingMomTitle')
                                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                @else
                                                    <span class="font-weight-bold text-dark">{{ $mom->title }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $ext = strtolower(pathinfo($mom->file_path, PATHINFO_EXTENSION));
                                                    $icon = 'fe-file';
                                                    $color = 'text-secondary';
                                                    
                                                    if(in_array($ext, ['ppt', 'pptx'])) {
                                                        $icon = 'fe-monitor';
                                                        $color = 'text-warning';
                                                    } elseif(in_array($ext, ['doc', 'docx'])) {
                                                        $icon = 'fe-file-text';
                                                        $color = 'text-primary';
                                                    } elseif($ext == 'pdf') {
                                                        $icon = 'fe-file';
                                                        $color = 'text-danger';
                                                    } elseif(in_array($ext, ['xls', 'xlsx', 'csv'])) {
                                                        $icon = 'fe-bar-chart-2';
                                                        $color = 'text-success';
                                                    }
                                                @endphp
                                                <a href="{{ Storage::url($mom->file_path) }}" target="_blank" class="d-flex align-items-center text-dark text-decoration-none">
                                                    <div class="avatar avatar-sm mr-2 {{ $color }} bg-light rounded">
                                                        <i class="fe {{ $icon }} font-weight-bold" style="font-size: 1.2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <span class="d-block font-weight-bold small text-truncate" style="max-width: 200px;">{{ basename($mom->file_path) }}</span>
                                                        <span class="badge badge-light border text-uppercase" style="font-size: 10px;">{{ $ext }}</span>
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="align-middle">
                                                @if(auth()->user()->canManagePostActivity())
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Aksi MoM">
                                                        @if($editingMomId === $mom->id)
                                                            <button type="button" class="btn btn-primary" wire:click="updateMom" wire:loading.attr="disabled" wire:target="updateMom">
                                                                <i class="fe fe-save"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-light border" wire:click="cancelEditingMom" wire:loading.attr="disabled" wire:target="updateMom">
                                                                <i class="fe fe-x"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-outline-primary" wire:click="startEditingMom({{ $mom->id }})">
                                                                <i class="fe fe-edit-2"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteFile('mom', {{ $mom->id }})">
                                                                <i class="fe fe-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="border rounded-lg bg-light text-center py-5 px-3 w-100">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm mb-3" style="width: 72px; height: 72px;">
                                <i class="fe fe-file-text text-secondary" style="font-size: 2rem;"></i>
                            </div>
                            <p class="text-muted mb-0">Belum ada MoM yang diupload.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Assignment & Validation Modal -->
    <div wire:ignore.self class="modal fade" id="assignmentModal" tabindex="-1" role="dialog" aria-labelledby="assignmentModalLabel" aria-hidden="true" style="z-index: 10000 !important;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                @if($activeActivityId && $activeActivity = \App\Models\Activity::find($activeActivityId))
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold" id="assignmentModalLabel">
                        <i class="fe fe-check-square mr-2 text-primary"></i>Kehadiran & Surat Tugas
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <h6 class="font-weight-bold text-uppercase text-muted small mb-2">Kegiatan</h6>
                            <p class="font-weight-bold ml-1 mb-1">{{ $activeActivity->name }}</p>
                            @if($activeActivity->date_time)
                                <span class="badge badge-light border ml-1">{{ $activeActivity->date_time->format('d M Y') }}</span>
                            @else
                                <span class="badge badge-light border ml-1 text-muted">Tanggal belum diset</span>
                            @endif
                        </div>
                    </div>

                    <ul class="nav nav-tabs nav-fill mb-4" id="assignmentTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab == 'attendance' ? 'active' : '' }}" 
                               wire:click="$set('activeTab', 'attendance')"
                               id="attendance-tab" data-toggle="tab" href="#attendance" role="tab">
                                <i class="fe fe-users mr-2"></i> Kehadiran
                            </a>
                        </li>
                        @if(!auth()->check() || !auth()->user()->hasRole('Dewan'))
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold {{ $activeTab == 'letter' ? 'active' : '' }}" 
                               wire:click="$set('activeTab', 'letter')"
                               id="letter-tab" data-toggle="tab" href="#letter" role="tab">
                                <i class="fe fe-file-text mr-2"></i>Surat Tugas
                            </a>
                        </li>
                        @endif
                    </ul>
                    
                    <div class="tab-content" id="assignmentTabContent">
                        <!-- Tab 1: Attendance -->
                        <div class="tab-pane fade {{ $activeTab == 'attendance' ? 'show active' : '' }}" id="attendance" role="tabpanel">
                            <div class="alert alert-light border-left-primary border-0 shadow-sm">
                                <small class="text-muted"><i class="fe fe-info mr-2"></i>Centang nama Dewan yang <strong>hadir</strong> pada kegiatan ini.</small>
                            </div>
                            
                            @if(!empty($activeActivity->disposition_to))
                                <div class="row">
                                    @php $hasDewan = false; @endphp
                                    @foreach($dewanUsers as $division => $users)
                                         @php
                                             $filteredUsers = $users->filter(function($u) use ($activeActivity) {
                                                 return is_array($activeActivity->disposition_to) && in_array($u->name, $activeActivity->disposition_to);
                                             });
                                         @endphp

                                         @if($filteredUsers->count() > 0)
                                            @php $hasDewan = true; @endphp
                                            <div class="col-12 mt-3">
                                                <h6 class="font-weight-bold text-primary border-bottom pb-2">{{ $division }}</h6>
                                            </div>
                                            @foreach($filteredUsers as $user)
                                                <div class="col-md-6 mb-2" wire:key="attendance-user-{{ $user->id }}">
                                                    <div class="custom-control custom-checkbox image-checkbox h-100">

                                                    <input type="checkbox" class="custom-control-input" id="attendee_{{ $user->id }}" value="{{ $user->name }}" wire:model.live="attendanceData" {{ !auth()->user()->canManagePostActivity() ? 'disabled' : '' }}>
                                                        <label class="custom-control-label p-2 border rounded w-100 bg-white shadow-sm h-100 d-flex flex-column justify-content-center" for="attendee_{{ $user->id }}">
                                                            <span class="d-flex align-items-center">
                                                                <span class="font-weight-bold text-dark">{{ $user->name }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    
                                                    {{-- Handling for Sekretariat DJSN Representative (Only for Sekretaris DJSN / Imron Rosadi) --}}
                                                    @if($user->name === 'Imron Rosadi')
                                                        @php
                                                            $isInputPresent = in_array($user->name, $attendanceData);
                                                            $repValue = $attendanceDetails[$user->id]['representative'] ?? '';
                                                            $isAdmin = auth()->check() && auth()->user()->canManagePostActivity();
                                                            // Show if: (Admin) OR (Has Value)
                                                            // Independent of 'Hadir' checkbox
                                                            $showRepInput = ($isAdmin) || (!empty($repValue));
                                                        @endphp
                                                        @if($showRepInput)
                                                        <div class="mt-2 fade-in">
                                                            <div class="input-group input-group-sm shadow-sm rounded">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-light border-0"><small class="font-weight-bold">Diwakili:</small></span>
                                                                </div>
                                                                <input type="text" 
                                                                    class="form-control border-0 bg-light" 
                                                                    placeholder="{{ $isAdmin ? 'Isi Nama Perwakilan...' : '' }}" 
                                                                    wire:model="attendanceDetails.{{ $user->id }}.representative"
                                                                    {{ !$isAdmin ? 'readonly' : '' }}>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endforeach
                                         @endif
                                    @endforeach
                                    
                                    @if(!$hasDewan)
                                        <div class="col-12">
                                            <p class="text-center text-muted font-italic my-4">Tidak ada Anggota Dewan dalam daftar disposisi.</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {{-- Manual Staff Input Section --}}
                            @if(auth()->user()->canManagePostActivity())
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="bg-light p-3 rounded border border-light">
                                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                            <i class="fe fe-user-plus mr-2 text-primary"></i>Staf Pendamping
                                        </h6>

                                        <div class="form-group mb-3" wire:ignore id="wrapper-sekretariat" style="position: relative;">
                                            <label class="small font-weight-bold text-primary mb-2">
                                                <i class="fe fe-users mr-1"></i> Sekretariat DJSN
                                            </label>
                                            <select class="form-control select2-staff-sekretariat shadow-sm" multiple="multiple" style="width: 100%;">
                                                @foreach($staffSekretariat as $staff)
                                                    <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mb-0" wire:ignore id="wrapper-ta" style="position: relative;">
                                            <label class="small font-weight-bold text-info mb-2">
                                                <i class="fe fe-briefcase mr-1"></i> Tenaga Ahli (TA)
                                            </label>
                                            <select class="form-control select2-staff-ta shadow-sm" multiple="multiple" style="width: 100%;">
                                                @foreach($staffTA as $staff)
                                                    <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Read-Only Manual Staff List for Non-Managers --}}
                            @if(!auth()->user()->canManagePostActivity() && (!empty($selectedSekretariat) || !empty($selectedTA)))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="bg-white p-3 rounded border shadow-sm">
                                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                            <i class="fe fe-users mr-2 text-muted"></i>Staf Pendamping
                                        </h6>
                                        
                                        @if(!empty($selectedSekretariat))
                                            <div class="mb-3">
                                                <small class="font-weight-bold text-primary text-uppercase" style="letter-spacing: 0.5px;">Sekretariat DJSN</small>
                                                <ul class="list-unstyled mt-2 mb-0">
                                                    @foreach($selectedSekretariat as $name)
                                                        <li class="d-flex align-items-center mb-1">
                                                            <i class="fe fe-check-circle text-success mr-2"></i> {{ $name }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        @if(!empty($selectedTA))
                                            <div class="mb-0">
                                                <small class="font-weight-bold text-info text-uppercase" style="letter-spacing: 0.5px;">Tenaga Ahli</small>
                                                <ul class="list-unstyled mt-2 mb-0">
                                                    @foreach($selectedTA as $name)
                                                        <li class="d-flex align-items-center mb-1">
                                                            <i class="fe fe-check-circle text-success mr-2"></i> {{ $name }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if(session()->has('success_attendance'))
                                <div class="alert alert-success mt-3 small">{{ session('success_attendance') }}</div>
                            @endif

                            @if(auth()->user()->canManagePostActivity())
                            <div class="mt-4 text-right">
                                <button type="button" class="btn btn-primary rounded-pill px-4" wire:click="saveAttendance" wire:loading.attr="disabled">
                                    <i class="fe fe-save mr-2"></i>Simpan Kehadiran
                                </button>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Tab 2: Surat Tugas -->
                        <div class="tab-pane fade {{ $activeTab == 'letter' ? 'show active' : '' }}" id="letter" role="tabpanel">
                            <div class="text-center bg-light rounded p-4 border border-dashed mb-4">
                                @if($activeActivity->assignment_letter_path)
                                    <div class="mb-3">
                                        <i class="fe fe-file-text text-success display-4"></i>
                                        <h6 class="mt-2 font-weight-bold text-success">Surat Tugas Tersedia</h6>
                                    </div>
                                    <div class="btn-group shadow-sm">
                                        <a href="{{ Storage::url($activeActivity->assignment_letter_path) }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="fe fe-download mr-2"></i>Download / Lihat
                                        </a>
                                        @if(auth()->user()->canUploadAssignment())
                                        <button class="btn btn-outline-danger" onclick="confirmModalDelete('assignment')">
                                            <i class="fe fe-trash-2 mr-2"></i>Hapus
                                        </button>
                                        @endif
                                    </div>
                                    <hr class="my-4">
                                    <p class="small text-muted mb-2">Ganti File:</p>
                                @else
                                    <div class="mb-3">
                                        <i class="fe fe-upload-cloud text-muted display-4"></i>
                                        <h6 class="mt-2 text-muted">Belum ada Surat Tugas</h6>
                                    </div>
                                @endif
                                
                                @if(auth()->user()->canUploadAssignment())
                                <div class="w-100 d-flex justify-content-center mb-3">
                                    <div class="custom-file w-100">
                                        <input type="file" wire:model="newAssignmentFile" class="custom-file-input" id="newAssignmentFile" accept="application/pdf">
                                        <label class="custom-file-label text-left" for="newAssignmentFile">
                                            {{ $newAssignmentFile ? $newAssignmentFile->getClientOriginalName() : 'Pilih File PDF...' }}
                                        </label>
                                    </div>
                                </div>
                                <div wire:loading wire:target="newAssignmentFile" class="text-center mt-2 mb-3">
                                     <small class="text-muted"><span class="spinner-border spinner-border-sm mr-1"></span>Uploading...</small>
                                </div>
                                @endif
                                @if(session()->has('success_upload_assignment'))
                                    <div class="alert alert-success mt-3 small">{{ session('success_upload_assignment') }}</div>
                                @endif

                                <!-- Minutes Upload Logic -->
                                <div class="mb-2">
                                    @php
                                        $minutesDocuments = $activeActivity->minutes_documents;
                                        $primaryMinutesDocument = $activeActivity->primary_minutes_document;
                                    @endphp
                                     <p class="small text-muted font-weight-bold mb-2">Dokumen Pendukung:</p>
                                    
                                    <!-- Surat Undangan (Attachment) -->
                                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-white border rounded">
                                        <div class="d-flex align-items-center overflow-hidden">
                                            @if($activeActivity->attachment_path)
                                                <a href="{{ Storage::url($activeActivity->attachment_path) }}" target="_blank" class="badge badge-primary mr-2 text-truncate" style="max-width: 200px;" title="Lihat Surat Undangan">
                                                    <i class="fe fe-paperclip mr-1"></i> Surat Undangan
                                                </a>
                                            @else
                                                <span class="badge badge-light border mr-2 text-muted"><i class="fe fe-paperclip mr-1"></i> Surat Undangan Kosong</span>
                                            @endif
                                        </div>
                                        
                                        @if(auth()->user()->canManageActivities())
                                            <div class="d-flex align-items-center">
                                                @if($activeActivity->attachment_path)
                                                    <button class="btn btn-xs btn-link text-danger p-0 ml-2" title="Hapus Surat Undangan" onclick="confirmModalDelete('attachment')">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                @endif
                                                
                                                <label class="btn btn-xs btn-outline-secondary mb-0 ml-2" title="Ganti/Upload Surat Undangan" style="cursor: pointer;">
                                                    <i class="fe fe-upload"></i>
                                                    <input type="file" wire:model="newAttachmentFile" class="d-none" accept=".pdf">
                                                </label>
                                                <div wire:loading wire:target="newAttachmentFile" class="spinner-border spinner-border-sm text-secondary ml-2"></div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Notulensi (Minutes) -->
                                    <div class="d-flex align-items-center justify-content-between mb-1 p-2 bg-white border rounded">
                                        <div class="d-flex flex-column align-items-start overflow-hidden">
                                            @if($primaryMinutesDocument)
                                                <a href="{{ Storage::url($primaryMinutesDocument['file_path']) }}" target="_blank" class="badge badge-success mr-2 text-truncate" title="Lihat Notulensi">
                                                    <i class="fe fe-file-text mr-1"></i> {{ $primaryMinutesDocument['label'] }}
                                                </a>
                                                @if($primaryMinutesDocument['source'] === 'mom')
                                                    <small class="text-muted mt-1">
                                                        Diambil dari MoM
                                                        @if(!empty($primaryMinutesDocument['title']) && $primaryMinutesDocument['title'] !== $primaryMinutesDocument['label'])
                                                            : {{ \Illuminate\Support\Str::limit($primaryMinutesDocument['title'], 40) }}
                                                        @endif
                                                        @if($minutesDocuments->count() > 1)
                                                            • {{ $minutesDocuments->count() }} file
                                                        @endif
                                                    </small>
                                                @elseif($minutesDocuments->count() > 1)
                                                    <small class="text-muted mt-1">{{ $minutesDocuments->count() }} dokumen notulensi tersedia</small>
                                                @endif
                                            @else
                                                <span class="badge badge-light border mr-2 text-muted"><i class="fe fe-file-text mr-1"></i> Notulensi Kosong</span>
                                            @endif
                                        </div>

                                        @if(auth()->user()->canManagePostActivity())
                                            <div class="d-flex align-items-center">
                                                @if($activeActivity->minutes_path)
                                                    <button class="btn btn-xs btn-link text-danger p-0 ml-2" title="Hapus Notulensi" onclick="confirmModalDelete('minutes')">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                @elseif($primaryMinutesDocument && $primaryMinutesDocument['source'] === 'mom')
                                                    <small class="text-muted mr-2">Kelola dari MoM</small>
                                                @endif
                                                
                                                <label class="btn btn-xs btn-outline-secondary mb-0 ml-2" title="Ganti/Upload Notulensi" style="cursor: pointer;">
                                                    <i class="fe fe-upload"></i>
                                                    <input type="file" wire:model="newMinutesFile" class="d-none" accept=".pdf">
                                                </label>
                                                 <div wire:loading wire:target="newMinutesFile" class="spinner-border spinner-border-sm text-secondary ml-2"></div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                     @if(session()->has('success_upload_attachment'))
                                        <div class="alert alert-success mt-2 small p-2">{{ session('success_upload_attachment') }}</div>
                                    @endif
                                </div>
                                



                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Documentation Modal -->
    <div wire:ignore.self class="modal fade" id="documentationModal" tabindex="-1" role="dialog" aria-labelledby="docModalLabel" aria-hidden="true" style="z-index: 10000 !important;">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                @if($activeActivityId && $activeActivity = \App\Models\Activity::find($activeActivityId))
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="docModalLabel">
                        <i class="fe fe-image mr-2 text-primary"></i>Dokumentasi Kegiatan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                     <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                             <h6 class="font-weight-bold mb-1">{{ $activeActivity->name }}</h6>
                             <small class="text-muted">Total: {{ $activeActivity->documentations->count() }} Foto (Minimal {{ \App\Models\Activity::DOCUMENTATION_MIN_COUNT }}, Maks {{ \App\Models\Activity::DOCUMENTATION_MAX_COUNT }} Foto)</small>
                        </div>
                        @if(auth()->check() && auth()->user()->canManageDocumentation())
                        <div>
                            <input type="file" wire:model="documentationPhotos" multiple id="docUpload" class="d-none" accept="image/*">
                            <button class="btn btn-primary rounded-pill shadow-sm" onclick="document.getElementById('docUpload').click()">
                                <i class="fe fe-plus mr-2"></i>Tambah Foto
                            </button>
                        </div>
                        @endif
                     </div>

                     @error('documentationPhotos') <div class="alert alert-danger">{{ $message }}</div> @enderror
                     @if(session()->has('success_documentation'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success_documentation') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                     @endif
                     
                     <div wire:loading wire:target="documentationPhotos" class="alert alert-info border-0 shadow-sm w-100">
                         <span class="spinner-border spinner-border-sm mr-2"></span> Mengupload foto...
                     </div>

                     <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 text-center">
                        @forelse($activeActivity->documentations as $doc)
                            <div class="col mb-4">
                                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                                    <div class="position-relative" style="height: 200px; background-color: #f8f9fa;">
                                        <img src="{{ Storage::url($doc->file_path) }}" class="w-100 h-100" style="object-fit: cover; cursor: pointer;" onclick="window.open('{{ Storage::url($doc->file_path) }}', '_blank')">
                                        <div class="position-absolute p-2 w-100 d-flex justify-content-between align-items-start fixed-top bg-gradient-top">
                                             <a href="{{ Storage::url($doc->file_path) }}" download class="btn btn-sm btn-light btn-icon shadow-sm" title="Download">
                                                <i class="fe fe-download"></i>
                                             </a>
                                             @if(auth()->check() && auth()->user()->canManageDocumentation())
                                             <button type="button" class="btn btn-sm btn-light btn-icon shadow-sm text-danger" title="Hapus" onclick="confirmDeleteFile('documentation', {{ $doc->id }})">
                                                <i class="fe fe-trash-2"></i>
                                             </button>
                                             @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                    <i class="fe fe-image display-4 mb-3" style="opacity: 0.3"></i>
                                    <p>Belum ada dokumentasi.</p>
                                </div>
                            </div>
                        @endforelse
                     </div>
                </div>
                @else
                <div class="modal-body text-center py-5">
                    <span class="spinner-border spinner-border text-primary"></span>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Summary Editor Modal -->
    <div wire:ignore.self class="modal fade" id="summaryEditorModal" tabindex="-1" role="dialog" aria-labelledby="summaryEditorModalLabel" aria-hidden="true" style="z-index: 10000 !important;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold" id="summaryEditorModalLabel">
                        <i class="fe fe-file-text mr-2 text-primary"></i>Ringkasan Rapat
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    @if(auth()->user()->canManagePostActivity())
                        <form wire:submit.prevent="saveSummary">
                            <div class="form-group mb-4" wire:ignore>
                                <label class="font-weight-bold text-dark mb-2">Isi Ringkasan Rapat</label>
                                
                                <!-- Quill Editor Container -->
                                <div id="summary-editor" style="height: 300px; background: white;"></div>
                                
                                <!-- Hidden Input for Data Binding -->
                                <input type="hidden" id="summary_content_input" wire:model="summaryContent">
                                
                                <small class="text-muted mt-2 d-block">
                                    <i class="fe fe-info mr-1"></i> Tuliskan poin-poin penting hasil pertemuan.
                                </small>
                            </div>
                            @error('summaryContent') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                            
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary rounded-pill px-4 mr-2" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" wire:loading.attr="disabled" onclick="syncQuillContent(); @this.call('saveSummary');">
                                    <span wire:loading.remove wire:target="saveSummary"><i class="fe fe-save mr-2"></i>Simpan</span>
                                    <span wire:loading wire:target="saveSummary"><span class="spinner-border spinner-border-sm mr-2"></span>Menyimpan...</span>
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- Read Only View -->
                        <div class="markdown-content border p-3 rounded bg-white" style="min-height: 200px; max-height: 60vh; overflow-y: auto;">
                            {!! $summaryContent !!}
                        </div>
                        <div class="mt-3 text-right">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Tutup</button>
                        </div>
                        
                        {{-- Dummy editor container to prevent JS error if script tries to init it --}}
                        <div id="summary-editor" style="display: none;"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <style>
        /* Fix for tooltip flickering: prevent tooltip from capturing mouse events */
        .tooltip {
            pointer-events: none !important;
        }
    </style>
</div>


<script>
    window.addEventListener('show-alert', event => {
        Swal.fire({
            icon: event.detail.type,
            title: 'Berhasil!',
            text: event.detail.message,
            timer: 2000,
            showConfirmButton: false
        });
    });

    document.addEventListener('livewire:initialized', () => {
        // Function to re-init tooltips safely
        const initTooltips = () => {
             // Dispose existing to prevent duplicates/memory leaks
             $('[data-toggle="tooltip"]').tooltip('dispose'); 
             // Re-initialize with HTML enabled
             $('[data-toggle="tooltip"]').tooltip({
                 html: true,
                 container: 'body'
             });
        };

        // Init on load
        initTooltips();

        // Init on ANY Livewire message processed (covers refreshes, updates, pagination)
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                // Wait for DOM to update
                setTimeout(() => {
                    initTooltips();
                }, 100);
            });
        });

        let lastActive = Date.now();
        const updateLastActive = () => { lastActive = Date.now(); };
        ['mousemove', 'click', 'scroll', 'keydown', 'touchstart'].forEach(evt => 
            document.addEventListener(evt, updateLastActive)
        );

        setInterval(() => {
            if (Date.now() - lastActive > 10000) {
                @this.$refresh();
            }
        }, 5000);
    });
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('tinydash/css/quill.snow.css') }}">
<style>
    .ql-container { font-family: inherit; font-size: 0.9rem; }
    .ql-editor { min-height: 200px; }
    
    /* Fix Z-Index for SweetAlert2 over Modals */
    .swal2-container {
        z-index: 20000 !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('tinydash/js/quill.min.js') }}"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        var quill = new Quill('#summary-editor', {
            theme: 'snow',
            placeholder: 'Ketik ringkasan hasil rapat di sini...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'header': [1, 2, 3, false] }],
                    ['clean']
                ]
            }
        });

        window.addEventListener('open-summary-modal', event => {
            let content = event.detail.content !== undefined ? event.detail.content : (event.detail[0] ? event.detail[0].content : ''); 
            quill.root.innerHTML = content || '';
            $('#summaryEditorModal').modal('show');
        });

        window.addEventListener('close-summary-modal', event => {
            $('#summaryEditorModal').modal('hide');
        });
        
        window.syncQuillContent = function() {
            @this.set('summaryContent', quill.root.innerHTML);
        }
    });
</script>
@endpush

 

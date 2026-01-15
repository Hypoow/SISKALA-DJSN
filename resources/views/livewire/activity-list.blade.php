<div x-data="{
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

    <div>
        <!-- Urgent Activities Alert -->
        @if($this->urgentActivities->isNotEmpty() && auth()->check() && auth()->user()->isAdmin())
            <div class="alert alert-warning shadow-sm border-0 rounded-lg mb-4 mx-1" role="alert" style="background: linear-gradient(to right, #fff3cd, #fff8e1); border-left: 5px solid #ffc107 !important;">
                <div class="d-flex align-items-start">
                    <div class="mr-3 mt-1">
                        <span class="avatar avatar-sm bg-warning text-white rounded-circle shadow-sm">
                            <i class="fe fe-bell"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="alert-heading text-dark font-weight-bold mb-1" style="font-size: 1rem;">Perhatian: Kegiatan H-3 Belum Didisposisi</h4>
                        <p class="mb-2 text-muted small">Terdapat <strong class="text-warning">{{ $this->urgentActivities->count() }}</strong> kegiatan yang akan dilaksanakan dalam 3 hari ke depan namun belum memiliki disposisi.</p>
                        
                        <div class="list-group list-group-flush border-top border-warning-light mt-2 pt-2">
                            @foreach($this->urgentActivities as $urgent)
                                <div class="list-group-item bg-transparent px-0 py-1 d-flex justify-content-between align-items-center">
                                    <span class="text-dark small">
                                        <i class="fe fe-calendar mr-2 text-muted"></i>
                                        <strong>{{ $urgent->start_date->isoFormat('D MMM Y') }}</strong> - {{ $urgent->name }}
                                    </span>
                                    <a href="{{ route('activities.edit', $urgent->id) }}" class="btn btn-xs btn-warning text-dark font-weight-bold shadow-sm">
                                        <i class="fe fe-edit-2 mr-1"></i> Disposisi
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card shadow border-0 rounded-lg my-4 fade-in" style="overflow: visible;">
            <!-- Card Header -->
            <div class="card-header bg-primary text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1 text-white font-weight-bold text-uppercase" style="letter-spacing: 1px;">Daftar Kegiatan</h5>
                        <p class="mb-0 text-white-50 small">Kelola jadwal kegiatan (Internal & Eksternal)</p>
                    </div>
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('activities.create') }}" class="btn btn-light shadow-sm text-primary font-weight-bold rounded-pill px-4">
                            <i class="fe fe-plus-circle mr-2"></i>Tambah Kegiatan
                        </a>
                    @endif
                </div>
            </div>

            <!-- Toolbar & Filters -->
            <div class="bg-light border-bottom p-3" style="position: relative; z-index: 100; overflow: visible;">
                <div class="row align-items-center" style="overflow: visible;">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group input-group-merge input-group-premium bg-white">
                            <input type="text" class="form-control border-0 pl-4 bg-transparent" wire:model.live.debounce.300ms="search" placeholder="Cari kegiatan...">
                            <div class="input-group-append">
                                <div class="input-group-text border-0 bg-transparent pr-4"><i class="fe fe-search text-muted"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false" style="position: relative; z-index: 1050; overflow: visible;">
                        <div class="position-relative">
                            <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                                <span class="text-truncate" x-text="$wire.type === 'external' ? 'Eksternal' : ($wire.type === 'internal' ? 'Internal' : 'Semua Tipe Kegiatan')">Semua Tipe Kegiatan</span>
                                <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                            </button>
                            <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 250px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1060;">
                                <div class="dropdown-item-premium" :class="{ 'active': $wire.type === '' }" @click="$wire.set('type', ''); open = false">Semua Tipe Kegiatan</div>
                                <div class="dropdown-item-premium" :class="{ 'active': $wire.type === 'internal' }" @click="$wire.set('type', 'internal'); open = false">Internal</div>
                                <div class="dropdown-item-premium" :class="{ 'active': $wire.type === 'external' }" @click="$wire.set('type', 'external'); open = false">Eksternal</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false" style="position: relative; z-index: 1049; overflow: visible;">
                        <div class="position-relative">
                            <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                                <span class="text-truncate" x-text="$wire.sortDirection === 'asc' ? 'Waktu: Terdekat (Awal - Akhir)' : 'Waktu: Terjauh (Akhir - Awal)'">Waktu: Terdekat (Awal - Akhir)</span>
                                <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                            </button>
                            <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 250px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1060;">
                                <div class="dropdown-item-premium" :class="{ 'active': $wire.sortDirection === 'asc' }" @click="$wire.set('sortDirection', 'asc'); open = false">Waktu: Terdekat (Awal - Akhir)</div>
                                <div class="dropdown-item-premium" :class="{ 'active': $wire.sortDirection === 'desc' }" @click="$wire.set('sortDirection', 'desc'); open = false">Waktu: Terjauh (Akhir - Awal)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="d-inline-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm border">
                            <span class="mr-2 small text-muted font-weight-bold">Ket:</span>
                            <div class="d-flex align-items-center mr-3">
                                <span class="rounded-circle mr-1" style="width: 10px; height: 10px; background-color: #004085;"></span>
                                <span class="small font-weight-bold text-dark">Internal</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="rounded-circle mr-1" style="width: 10px; height: 10px; background-color: #17a2b8;"></span>
                                <span class="small font-weight-bold text-dark">Eksternal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- Bulk Actions -->
                @if(auth()->check() && auth()->user()->isAdmin())
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
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <th class="pl-4 align-middle" style="width: 5%;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAllActivity" wire:model.live="selectAll">
                                        <label class="custom-control-label" for="selectAllActivity"></label>
                                    </div>
                                </th>
                                @endif
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4">Tanggal & Waktu</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Detail Kegiatan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status Pelaksanaan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status Undangan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Surat Undangan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Tipe & Lokasi</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groupedActivities as $month => $activities)
                                <tr class="bg-light">
                                    <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="py-2 pl-4">
                                        <h6 class="mb-0 text-primary font-weight-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">
                                            <i class="fe fe-calendar mr-2"></i>{{ $month }}
                                        </h6>
                                    </td>
                                </tr>
                                @foreach($activities as $activity)
                                <tr style="border-left: 4px solid {{ $activity->type == 'internal' ? '#004085' : '#17a2b8' }};">
                                    @if(auth()->check() && auth()->user()->isAdmin())
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
                                                <span class="d-block font-weight-bold h4 mb-0" style="line-height:1; color: #ffffffff;">{{ $activity->date_time->format('d') }}</span>
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
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="fe fe-map-pin mr-1" style="color: #EF4444;"></i>
                                            @if($activity->location_type == 'online')
                                                <span>Online</span>
                                            @else
                                                <span class="text-truncate" style="max-width: 250px;">{{ $activity->location ?? '-' }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                         @switch($activity->status)
                                            @case(0) <span class="badge badge-pill badge-light text-success border border-success px-3">On Schedule</span> @break
                                            @case(1) <span class="badge badge-pill badge-light text-secondary border border-secondary px-3">Reschedule</span> @break
                                            @case(2) <span class="badge badge-pill badge-light text-warning border border-warning px-3">Menunggu Disposisi</span> @break
                                            @case(3) <span class="badge badge-pill badge-light text-danger border border-danger px-3">Dibatalkan</span> @break
                                        @endswitch
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($activity->type == 'external')
                                            @switch($activity->invitation_status)
                                                @case(0) <small class="text-warning font-weight-bold"><i class="fe fe-clock mr-1"></i>Proses Disposisi</small> @break
                                                @case(1) <small class="text-primary font-weight-bold"><i class="fe fe-check mr-1"></i>Sudah Disposisi</small> @break
                                                @case(2) <small class="text-danger font-weight-bold"><i class="fe fe-info mr-1"></i>Diketahui Ketua</small> @break
                                                @case(3) <small class="text-success font-weight-bold"><i class="fe fe-user-check mr-1"></i>Terjadwal Hadir</small> @break
                                            @endswitch
                                        @elseif($activity->type == 'internal')
                                            @switch($activity->invitation_status)
                                                @case(0) <small class="text-success font-weight-bold"><i class="fe fe-send mr-1"></i>Proses Terkirim</small> @break
                                                @case(1) <small class="text-primary font-weight-bold"><i class="fe fe-pen-tool mr-1"></i>Proses TTD</small> @break
                                                @case(2) <small class="text-danger font-weight-bold"><i class="fe fe-file-text mr-1"></i>Proses Drafting & Acc</small> @break
                                            @endswitch
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($activity->attachment_path)
                                            <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-info shadow-sm rounded-pill px-3" data-toggle="tooltip" title="Lihat Surat Undangan">
                                                <i class="fe fe-file-text mr-1"></i> Lihat
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="mb-1">
                                            @if($activity->type == 'internal')
                                                <span class="badge badge-primary px-3" style="background-color: #004085;">Internal</span>
                                            @else
                                                <span class="badge badge-info text-white px-3">Eksternal</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="badge text-white px-3" style="background-color: #6c757d;">{{ ucfirst($activity->location_type) }}</span>
                                        </div>
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
                                                @if(auth()->check() && auth()->user()->isAdmin())
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
                                    <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fe fe-calendar text-muted display-4"></i>
                                            </div>
                                            <h5 class="text-muted font-weight-bold">Tidak ada kegiatan ditemukan</h5>
                                            <p class="text-muted small">Coba ubah filter atau kata kunci pencarian Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 border-top bg-light">
                     <!-- Pagination or Footer info if needed -->
                     <small class="text-muted text-center d-block">Menampilkan daftar kegiatan berdasarkan jadwal terbaru.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
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

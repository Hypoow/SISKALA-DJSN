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

        <!-- Toolbar -->
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
                 <div class="col-md-2 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                    <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                            <span class="text-truncate" x-text="$wire.year"></span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                         <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 250px; overflow-y: auto;">
                             @foreach(range(date('Y'), 2023) as $y)
                                 <div class="dropdown-item-premium" :class="{ 'active': $wire.year == {{ $y }} }" @click="$wire.set('year', '{{ $y }}'); open = false">{{ $y }}</div>
                             @endforeach
                         </div>
                    </div>
                 </div>
                <div class="col-md-2 mb-2 mb-md-0" x-data="{ open: false, getMonthName(m) { return m ? ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][m] : 'Semua Bulan'; } }" @click.away="open = false" style="position: relative; z-index: 1050; overflow: visible;">
                     <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                            <span class="text-truncate" x-text="getMonthName($wire.month)">Semua Bulan</span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                        <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 400px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1060;">
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '' }" @click="$wire.set('month', ''); open = false">Semua Bulan</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '1' }" @click="$wire.set('month', '1'); open = false">Januari</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '2' }" @click="$wire.set('month', '2'); open = false">Februari</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '3' }" @click="$wire.set('month', '3'); open = false">Maret</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '4' }" @click="$wire.set('month', '4'); open = false">April</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '5' }" @click="$wire.set('month', '5'); open = false">Mei</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '6' }" @click="$wire.set('month', '6'); open = false">Juni</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '7' }" @click="$wire.set('month', '7'); open = false">Juli</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '8' }" @click="$wire.set('month', '8'); open = false">Agustus</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '9' }" @click="$wire.set('month', '9'); open = false">September</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '10' }" @click="$wire.set('month', '10'); open = false">Oktober</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '11' }" @click="$wire.set('month', '11'); open = false">November</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '12' }" @click="$wire.set('month', '12'); open = false">Desember</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2.5 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                    <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                             <span class="text-truncate" x-text="$wire.type === 'external' ? 'Eksternal' : ($wire.type === 'internal' ? 'Internal' : 'Semua Tipe Kegiatan')">Semua Tipe Kegiatan</span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                        <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none;">
                             <div class="dropdown-item-premium" :class="{ 'active': $wire.type === '' }" @click="$wire.set('type', ''); open = false">Semua Tipe Kegiatan</div>
                             <div class="dropdown-item-premium" :class="{ 'active': $wire.type === 'internal' }" @click="$wire.set('type', 'internal'); open = false">Internal</div>
                             <div class="dropdown-item-premium" :class="{ 'active': $wire.type === 'external' }" @click="$wire.set('type', 'external'); open = false">Eksternal</div>
                        </div>
                    </div>
                </div>
              <div class="col-md-3 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                     <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                            <span class="text-truncate" x-text="$wire.sortDirection === 'desc' ? 'Terbaru' : 'Terlama'">Terbaru</span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                        <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none;">
                             <div class="dropdown-item-premium" :class="{ 'active': $wire.sortDirection === 'desc' }" @click="$wire.set('sortDirection', 'desc'); open = false">Terbaru</div>
                             <div class="dropdown-item-premium" :class="{ 'active': $wire.sortDirection === 'asc' }" @click="$wire.set('sortDirection', 'asc'); open = false">Terlama</div>
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
                        @forelse($groupedActivities as $month => $activities)
                            <tr class="bg-light">
                                <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="py-2 pl-4">
                                    <h6 class="mb-0 text-primary font-weight-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">
                                        <i class="fe fe-calendar mr-2"></i>{{ $month }}
                                    </h6>
                                </td>
                            </tr>
                            @foreach($activities as $activity)
                                <tr style="border-left: 4px solid {{ $activity->type == 'internal' ? '#004085' : '#17a2b8' }};" wire:key="row-{{ $activity->id }}">
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
                                        <small class="text-muted"><i class="fe fe-map-pin mr-1"></i>{{ $activity->location_type == 'online' ? 'Online' : $activity->location }}</small>
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
                                                <small class="text-success font-weight-bold" title="Hasil Rapat Tersedia">
                                                    <i class="fe fe-check-circle mr-1"></i>Ringkasan Rapat Terisi
                                                </small>
                                            @else
                                                <small class="text-muted" title="Belum ada ringkasan hasil rapat">
                                                    <i class="fe fe-minus-circle mr-1"></i>Ringkasan Rapat Belum Diisi
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-middle text-center" style="min-width: 170px;">
                                        @if($activity->minutes_path)
                                            <div class="btn-group shadow-sm rounded-pill">
                                                <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary border-0 bg-white d-flex align-items-center justify-content-center" title="Lihat Notulensi">
                                                    <i class="fe fe-eye mr-2"></i> Notulensi
                                                </a>
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split border-0 bg-white" data-toggle="dropdown">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('minutes_{{ $activity->id }}').click()">
                                                        <i class="fe fe-upload mr-2"></i> Ganti File
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDeleteFile('minutes', {{ $activity->id }})">
                                                        <i class="fe fe-trash-2 mr-2"></i> Hapus File
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                            @if(auth()->check() && auth()->user()->isAdmin())
                                                <input type="file" id="minutes_{{ $activity->id }}" wire:model.live="minutesFiles.{{ $activity->id }}" class="d-none" accept="application/pdf">
                                                <div wire:loading wire:target="minutesFiles.{{ $activity->id }}" class="text-center mt-1">
                                                     <small class="text-muted"><span class="spinner-border spinner-border-sm"></span></small>
                                                </div>
                                            @endif
                                        @else
                                            @if(auth()->check() && auth()->user()->isAdmin())
                                            <input type="file" id="minutes_{{ $activity->id }}" wire:model.live="minutesFiles.{{ $activity->id }}" class="d-none" accept="application/pdf">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill w-100 border-dashed d-flex align-items-center justify-content-center" onclick="document.getElementById('minutes_{{ $activity->id }}').click()">
                                                <span wire:loading.remove wire:target="minutesFiles.{{ $activity->id }}" class="d-flex align-items-center"><i class="fe fe-upload-cloud mr-2"></i> Notulensi</span>
                                                <span wire:loading wire:target="minutesFiles.{{ $activity->id }}"><span class="spinner-border spinner-border-sm text-secondary"></span></span>
                                            </button>
                                            @else
                                            <span class="text-muted small font-italic">-</span>
                                            @endif
                                        @endif
                                        @error("minutesFiles.{$activity->id}") <span class="d-block text-danger small mt-1">{{ $message }}</span> @enderror
                                        
                                        <!-- Materials Button -->
                                        <div class="mt-2">
                                            <button wire:click="openMaterialModal({{ $activity->id }})" class="btn btn-sm btn-outline-info rounded-pill shadow-sm px-3 w-100 d-flex align-items-center justify-content-center text-nowrap">
                                                <i class="fe fe-folder mr-2"></i> Bahan Materi 
                                                @if($activity->materials->count() > 0)
                                                    <span class="badge badge-info ml-2">{{ $activity->materials->count() }}</span>
                                                @endif
                                            </button>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center" style="min-width: 140px;">
                                        <button class="btn btn-sm btn-outline-light border text-dark shadow-sm px-3 py-2 text-left" wire:click="openAssignmentModal({{ $activity->id }})" wire:loading.attr="disabled" style="min-width: 140px;">
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
                                        </button>
                                    </td>
                                    <td class="align-middle text-center" style="min-width: 140px;">
                                        @php $docCount = $activity->documentations->count(); @endphp
                                        <button class="btn btn-sm {{ $docCount > 0 ? 'btn-outline-primary' : 'btn-outline-secondary' }} rounded-pill shadow-sm px-3" wire:click="openDocumentationModal({{ $activity->id }})" wire:loading.attr="disabled">
                                            <i class="fe fe-image mr-1"></i>
                                            @if(auth()->check() && auth()->user()->isAdmin())
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
             </div>
        </div>
    </div>
    
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
        });

        window.addEventListener('close-material-modal', event => {
            $('#materialModal').modal('hide');
        });

        document.addEventListener('livewire:initialized', () => {
             $('#assignmentModal').on('hidden.bs.modal', function () {
                @this.call('closeModalState');
            });
            $('#materialModal').on('hidden.bs.modal', function () {
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
        });
    </script>

    <!-- Material Management Modal -->
    <div wire:ignore.self class="modal fade" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="materialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="materialModalLabel">Kelola Bahan Materi Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (session()->has('success_material'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success_material') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Add New Material Form -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3">Tambah Materi Baru</h6>
                            <form wire:submit.prevent="saveMaterial">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Judul Materi</label>
                                            <input type="text" wire:model="newMaterialTitle" class="form-control" placeholder="Contoh: Slide Paparan Narasumber A">
                                            @error('newMaterialTitle') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>File Materi (Max 20MB)</label>
                                            <div class="custom-file">
                                                <input type="file" wire:model="newMaterialFile" class="custom-file-input" id="materialFile">
                                                <label class="custom-file-label" for="materialFile">
                                                    {{ $newMaterialFile ? $newMaterialFile->getClientOriginalName() : 'Pilih file...' }}
                                                </label>
                                            </div>
                                            @error('newMaterialFile') <span class="text-danger small">{{ $message }}</span> @enderror
                                            <div wire:loading wire:target="newMaterialFile" class="text-xs text-muted mt-1">
                                                Uploading...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                                        <i class="fe fe-plus"></i> Tambahkan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Material List -->
                    <h6 class="font-weight-bold mb-3">Daftar Materi Tersimpan</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>Judul Materi</th>
                                    <th>File</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materialList as $material)
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
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteFile('material', {{ $material->id }})">
                                                <i class="fe fe-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fe fe-folder display-4 mb-3 d-block"></i>
                                            Belum ada bahan materi yang diupload.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Assignment & Validation Modal -->
    <div wire:ignore.self class="modal fade" id="assignmentModal" tabindex="-1" role="dialog" aria-labelledby="assignmentModalLabel" aria-hidden="true">
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
                                                <div class="col-md-6 mb-2">
                                                    <div class="custom-control custom-checkbox image-checkbox h-100">
                                                        <input type="checkbox" class="custom-control-input" id="attendee_{{ $user->id }}" value="{{ $user->name }}" wire:model="attendanceData" {{ auth()->check() && !auth()->user()->isAdmin() ? 'disabled' : '' }}>
                                                        <label class="custom-control-label p-2 border rounded w-100 bg-white shadow-sm h-100 d-flex flex-column justify-content-center" for="attendee_{{ $user->id }}">
                                                            <span class="d-flex align-items-center">
                                                                <span class="font-weight-bold text-dark">{{ $user->name }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    
                                                    {{-- Special Handling for Imron Rosadi Representative --}}
                                                    @if($user->name === 'Imron Rosadi' || str_contains($user->name, 'Sekretaris DJSN'))
                                                        @php
                                                            $isInputPresent = in_array($user->name, $attendanceData);
                                                            $repValue = $attendanceDetails[$user->id]['representative'] ?? '';
                                                            $isAdmin = auth()->check() && auth()->user()->isAdmin();
                                                            // Show if: (Admin AND Present) OR (Non-Admin AND Present AND Has Value)
                                                            $showRepInput = ($isAdmin && $isInputPresent) || (!$isAdmin && $isInputPresent && !empty($repValue));
                                                        @endphp
                                                        @if($showRepInput)
                                                        <div class="mt-1 ml-4 fade-in">
                                                            <div class="input-group input-group-sm shadow-sm rounded">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-light border-0"><small class="font-weight-bold">Diwakili:</small></span>
                                                                </div>
                                                                <input type="text" 
                                                                    class="form-control border-0 bg-light" 
                                                                    placeholder="{{ $isAdmin ? 'Nama Perwakilan (jika tidak hadir)...' : '' }}" 
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
                            @else
                                <p class="text-center text-muted font-italic my-4">Tidak ada data disposisi untuk kegiatan ini.</p>
                            @endif
                            
                            @if(session()->has('success_attendance'))
                                <div class="alert alert-success mt-3 small">{{ session('success_attendance') }}</div>
                            @endif

                            @if(auth()->check() && auth()->user()->isAdmin())
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
                                        @if(auth()->check() && auth()->user()->isAdmin())
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
                                
                                @if(auth()->check() && auth()->user()->isAdmin())
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
                                        
                                        @if(auth()->check() && auth()->user()->isAdmin())
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
                                        <div class="d-flex align-items-center overflow-hidden">
                                            @if($activeActivity->minutes_path)
                                                <a href="{{ Storage::url($activeActivity->minutes_path) }}" target="_blank" class="badge badge-success mr-2 text-truncate" title="Lihat Notulensi">
                                                    <i class="fe fe-file-text mr-1"></i> Notulensi
                                                </a>
                                            @else
                                                <span class="badge badge-light border mr-2 text-muted"><i class="fe fe-file-text mr-1"></i> Notulensi Kosong</span>
                                            @endif
                                        </div>

                                        @if(auth()->check() && auth()->user()->isAdmin())
                                            <div class="d-flex align-items-center">
                                                @if($activeActivity->minutes_path)
                                                    <button class="btn btn-xs btn-link text-danger p-0 ml-2" title="Hapus Notulensi" onclick="confirmModalDelete('minutes')">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
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
    <div wire:ignore.self class="modal fade" id="documentationModal" tabindex="-1" role="dialog" aria-labelledby="docModalLabel" aria-hidden="true">
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
                             <small class="text-muted">Total: {{ $activeActivity->documentations->count() }} Foto</small>
                        </div>
                        @if(auth()->check() && auth()->user()->isAdmin())
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
                                <div class="card h-100 shadow-sm border-0 overflow-hidden group-hover-zoom">
                                    <div class="position-relative" style="height: 200px; background-color: #f8f9fa;">
                                        <img src="{{ Storage::url($doc->file_path) }}" class="w-100 h-100" style="object-fit: cover; cursor: pointer;" onclick="window.open('{{ Storage::url($doc->file_path) }}', '_blank')">
                                        <div class="position-absolute p-2 w-100 d-flex justify-content-between align-items-start fixed-top bg-gradient-top">
                                             <a href="{{ Storage::url($doc->file_path) }}" download class="btn btn-sm btn-light btn-icon shadow-sm" title="Download">
                                                <i class="fe fe-download"></i>
                                             </a>
                                             @if(auth()->check() && auth()->user()->isAdmin())
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

<div wire:poll.8s>
    <div class="card shadow border-0 rounded-lg overflow-hidden my-4">
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
        <div class="bg-light border-bottom p-3">
             <div class="row align-items-center">
                  <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group input-group-merge shadow-sm">
                            <input type="text" class="form-control border-0 pl-4" wire:model.live="search" placeholder="Cari kegiatan...">
                            <div class="input-group-append">
                                <div class="input-group-text border-0 bg-white pr-4"><i class="fe fe-search text-muted"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <select class="form-control border-0 shadow-sm" wire:model.live="type" style="background-image: none;">
                            <option value="">Semua Tipe</option>
                            <option value="external">Eksternal</option>
                            <option value="internal">Internal</option>
                        </select>
                    </div>
                  <div class="col-md-3 mb-2 mb-md-0">
                         <select class="form-control border-0 shadow-sm" wire:model.live="sortDirection" style="background-image: none;">
                            <option value="desc">Waktu: Terbaru (Akhir - Awal)</option>
                            <option value="asc">Waktu: Terlama (Awal - Akhir)</option>
                        </select>
                  </div>
                    <div class="col-md-3 text-right">
                        <div class="d-inline-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm border">
                            <span class="mr-2 small text-muted font-weight-bold">Ket:</span>
                            <div class="d-flex align-items-center mr-3">
                                <span class="rounded-circle mr-1" style="width: 10px; height: 10px; background-color: #007bff;"></span>
                                <span class="small font-weight-bold text-dark">Internal</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="rounded-circle mr-1" style="width: 10px; height: 10px; background-color: #fd7e14;"></span>
                                <span class="small font-weight-bold text-dark">Eksternal</span>
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
                                onclick="confirmBulkDeletePast()"
                                class="btn btn-sm btn-light text-danger font-weight-bold shadow-sm">
                            <i class="fe fe-trash-2 mr-1"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>
                <script>
                    function confirmBulkDeletePast() {
                        Swal.fire({
                            title: 'Hapus Kegiatan Terpilih?',
                            text: "Data yang dihapus tidak dapat dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                @this.call('deleteSelected');
                            }
                        })
                    }
                </script>
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
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Notulensi</th>
                             <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Surat Tugas</th>
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
                                <tr style="border-left: 4px solid {{ $activity->type == 'internal' ? '#007bff' : '#fd7e14' }};" wire:key="row-{{ $activity->id }}">
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
                                            <div class="text-center rounded p-2 shadow-sm text-white" style="min-width: 50px; background-color: {{ $activity->type == 'internal' ? '#007bff' : '#fd7e14' }};">
                                                <span class="d-block font-weight-bold small text-uppercase" style="line-height:1;">{{ $activity->date_time->format('M') }}</span>
                                                <span class="d-block font-weight-bold h4 mb-0" style="line-height:1;">{{ $activity->date_time->format('d') }}</span>
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
                                            @if(!empty($activity->summary_content))
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
                                    <td class="align-middle text-center" style="min-width: 140px;">
                                        @if($activity->minutes_path)
                                            <div class="btn-group shadow-sm rounded-pill">
                                                <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary border-0 bg-white d-flex align-items-center justify-content-center" title="Lihat Notulensi">
                                                    <i class="fe fe-eye mr-2"></i> Lihat
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
                                                <span wire:loading.remove wire:target="minutesFiles.{{ $activity->id }}" class="d-flex align-items-center"><i class="fe fe-upload-cloud mr-2"></i> Upload</span>
                                                <span wire:loading wire:target="minutesFiles.{{ $activity->id }}"><span class="spinner-border spinner-border-sm text-secondary"></span></span>
                                            </button>
                                            @else
                                            <span class="text-muted small font-italic">-</span>
                                            @endif
                                        @endif
                                        @error("minutesFiles.{$activity->id}") <span class="d-block text-danger small mt-1">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="align-middle text-center" style="min-width: 140px;">
                                        @if($activity->assignment_letter_path)
                                            <div class="btn-group shadow-sm rounded-pill">
                                                <a href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank" class="btn btn-sm btn-outline-primary border-0 bg-white d-flex align-items-center justify-content-center" title="Lihat Surat Tugas">
                                                    <i class="fe fe-eye mr-2"></i> Lihat
                                                </a>
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split border-0 bg-white" data-toggle="dropdown">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('assignment_{{ $activity->id }}').click()">
                                                        <i class="fe fe-upload mr-2"></i> Ganti File
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDeleteFile('assignment', {{ $activity->id }})">
                                                        <i class="fe fe-trash-2 mr-2"></i> Hapus File
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                            @if(auth()->check() && auth()->user()->isAdmin())
                                                <input type="file" id="assignment_{{ $activity->id }}" wire:model.live="assignmentFiles.{{ $activity->id }}" class="d-none" accept="application/pdf">
                                                <div wire:loading wire:target="assignmentFiles.{{ $activity->id }}" class="text-center mt-1">
                                                     <small class="text-muted"><span class="spinner-border spinner-border-sm"></span></small>
                                                </div>
                                            @endif
                                        @else
                                            @if(auth()->check() && auth()->user()->isAdmin())
                                            <input type="file" id="assignment_{{ $activity->id }}" wire:model.live="assignmentFiles.{{ $activity->id }}" class="d-none" accept="application/pdf">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill w-100 border-dashed d-flex align-items-center justify-content-center" onclick="document.getElementById('assignment_{{ $activity->id }}').click()">
                                                <span wire:loading.remove wire:target="assignmentFiles.{{ $activity->id }}" class="d-flex align-items-center"><i class="fe fe-upload-cloud mr-2"></i> Upload</span>
                                                <span wire:loading wire:target="assignmentFiles.{{ $activity->id }}"><span class="spinner-border spinner-border-sm text-secondary"></span></span>
                                            </button>
                                            @else
                                            <span class="text-muted small font-italic">-</span>
                                            @endif
                                        @endif
                                        @error("assignmentFiles.{$activity->id}") <span class="d-block text-danger small mt-1">{{ $message }}</span> @enderror
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
        function confirmDeleteFile(type, id) {
            let title = type === 'minutes' ? 'Hapus Notulensi?' : 'Hapus Surat Tugas?';
            let text = type === 'minutes' ? 'File notulensi akan dihapus.' : 'File surat tugas akan dihapus.';

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
                    @this.call(type === 'minutes' ? 'deleteMinutes' : 'deleteAssignment', id);
                }
            })
        }
    </script>
</div>

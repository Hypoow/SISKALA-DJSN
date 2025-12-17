<div wire:poll.5s>
    <div class="card shadow">
        <div class="card-body">
            <div class="toolbar row mb-3">
                <div class="col-md-6">
                    <form class="form-inline" onsubmit="event.preventDefault();">
                        <div class="form-row align-items-center">
                            <div class="col-auto my-1">
                                <label class="mr-sm-2 sr-only" for="typeFilter">Tipe</label>
                                <select class="custom-select mr-sm-2" id="typeFilter" wire:model.live="type">
                                    <option value="">Semua Tipe</option>
                                    <option value="external">Eksternal</option>
                                    <option value="internal">Internal</option>
                                </select>
                            </div>
                            <div class="col-auto my-1">
                                <label class="mr-sm-2 sr-only" for="sortDirectionPast">Urutan</label>
                                <select class="custom-select mr-sm-2" id="sortDirectionPast" wire:model.live="sortDirection">
                                    <option value="desc">Waktu: Terbaru (Akhir - Awal)</option>
                                    <option value="asc">Waktu: Terlama (Awal - Akhir)</option>
                                </select>
                            </div>
                            <div class="col-auto my-1">
                                <label class="sr-only" for="search">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" wire:model.live="search" placeholder="Cari kegiatan...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button"><i class="fe fe-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right d-flex align-items-center justify-content-end">
                    {{-- Legend --}}
                    <div class="d-flex align-items-center">
                        <span class="mr-2 font-weight-bold">
                            <span class="d-none d-md-inline">Keterangan:</span>
                            <span class="d-inline d-md-none">Ket:</span>
                        </span>
                        <span class="mr-2"><span class="status-dot" style="background-color: var(--primary-color);"></span> Internal</span>
                        <span><span class="status-dot" style="background-color: var(--accent-color);"></span> Eksternal</span>
                    </div>
                </div>
            </div>

            @if (session()->has('success_upload'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success_upload') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Bulk Actions -->
            @if(auth()->check() && auth()->user()->isAdmin())
                <div class="col-md-12 mb-2" 
                     x-data="{ count: @entangle('selected').live }" 
                     x-show="count && count.length > 0" 
                     x-cloak 
                     style="display: none;"
                     x-transition>
                    <div class="alert alert-info d-flex align-items-center justify-content-between py-2">
                        <span><strong x-text="count ? count.length : 0"></strong> kegiatan terpilih.</span>
                        <button type="button" 
                                onclick="confirmBulkDeletePast()"
                                class="btn btn-sm btn-danger">
                            Hapus Terpilih
                        </button>
                    </div>
                </div>
                <script>
                    function confirmBulkDeletePast() {
                        Swal.fire({
                            title: 'Yakin hapus kegiatan terpilih?',
                            text: "Data yang dihapus tidak dapat dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                @this.call('deleteSelected');
                            }
                        })
                    }
                </script>
            @endif

            <!-- table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <th style="width: 40px;">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAllPast" wire:model.live="selectAll">
                                    <label class="custom-control-label" for="selectAllPast"></label>
                                </div>
                            </th>
                            @endif
                            <th>Waktu</th>
                            <th>Nama Kegiatan</th>
                            <th>Status Pelaksanaan</th>
                            <th>Notulensi</th>
                            <th>Surat Tugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedActivities as $month => $activities)
                            <tr role="group" class="bg-light">
                                <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="font-weight-bold text-uppercase text-primary pl-4" style="letter-spacing: 1px; border-bottom: 2px solid #007bff;">{{ $month }}</td>
                            </tr>
                            @foreach($activities as $activity)
                                <tr class="{{ $activity->type == 'internal' ? 'row-internal' : 'row-external' }}" wire:key="row-{{ $activity->id }}">
                                    @if(auth()->check() && auth()->user()->isAdmin())
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check_{{ $activity->id }}" value="{{ $activity->id }}" wire:model.live="selected">
                                            <label class="custom-control-label" for="check_{{ $activity->id }}"></label>
                                        </div>
                                    </td>
                                    @endif
                                    <td style="min-width: 140px;">
                                        <div class="d-flex align-items-center">
                                            <div class="text-center bg-white border border-secondary shadow-sm rounded" style="width: 50px; overflow: hidden;">
                                                <div class="bg-secondary text-white small font-weight-bold py-0" style="font-size: 10px; line-height: 1.2;">
                                                    {{ $activity->date_time->format('M') }}
                                                </div>
                                                <div class="h4 mb-0 font-weight-bold text-dark py-2">
                                                    {{ $activity->date_time->format('d') }}
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <span class="h5 mb-0 font-weight-bold text-secondary">{{ $activity->date_time->format('H:i') }}</span>
                                                <div class="small text-muted font-weight-bold text-uppercase">{{ $activity->date_time->isoFormat('dddd') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('activities.show', $activity->id) }}" class="text-dark font-weight-bold">{{ $activity->name }}</a>
                                        <br>
                                        <small class="text-muted">{{ $activity->location_type == 'online' ? 'Online' : $activity->location }}</small>
                                    </td>
                                    <td>
                                        @switch($activity->status)
                                            @case(0) <span class="badge badge-success">Terlaksana</span> @break
                                            @case(1) <span class="badge badge-warning">Reschedule</span> @break
                                            @case(2) <span class="badge badge-secondary">Belom ada Dispo</span> @break
                                            @case(3) <span class="badge badge-danger">Batal</span> @break
                                        @endswitch
                                    </td>
                                    <td>
                                        {{-- Minutes Upload --}}
                                        @if($activity->minutes_path)
                                            <div class="btn-group btn-block">
                                                <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary text-truncate" title="Lihat Notulensi">
                                                    <i class="fe fe-eye"></i> Lihat
                                                </a>
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                                     <small class="text-muted"><span class="spinner-border spinner-border-sm"></span> Uploading...</small>
                                                </div>
                                            @endif
                                        @else
                                            @if(auth()->check() && auth()->user()->isAdmin())
                                            <input type="file" id="minutes_{{ $activity->id }}" wire:model.live="minutesFiles.{{ $activity->id }}" class="d-none" accept="application/pdf">
                                            <button type="button" class="btn btn-sm btn-primary w-100" style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: 100% !important; border-radius: 50px !important;" onclick="document.getElementById('minutes_{{ $activity->id }}').click()">
                                                <span wire:loading.remove wire:target="minutesFiles.{{ $activity->id }}" style="display: flex; align-items: center;"><i class="fe fe-upload mr-2"></i> Upload</span>
                                                <span wire:loading wire:target="minutesFiles.{{ $activity->id }}" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </button>
                                            @else
                                            <span class="text-muted small font-italic">Belum ada</span>
                                            @endif
                                        @endif
                                        @error("minutesFiles.{$activity->id}") <span class="text-danger small">{{ $message }}</span> @enderror
                                    </td>
                                    <td>
                                        {{-- Assignment Upload --}}
                                        @if($activity->assignment_letter_path)
                                            <div class="btn-group btn-block">
                                                <a href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank" class="btn btn-sm btn-outline-primary text-truncate" title="Lihat Surat Tugas">
                                                    <i class="fe fe-eye"></i> Lihat
                                                </a>
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                                     <small class="text-muted"><span class="spinner-border spinner-border-sm"></span> Uploading...</small>
                                                </div>
                                            @endif
                                        @else
                                            @if(auth()->check() && auth()->user()->isAdmin())
                                            <input type="file" id="assignment_{{ $activity->id }}" wire:model.live="assignmentFiles.{{ $activity->id }}" class="d-none" accept="application/pdf">
                                            <button type="button" class="btn btn-sm btn-primary w-100" style="display: flex; align-items: center; justify-content: center; white-space: nowrap; width: 100% !important; border-radius: 50px !important;" onclick="document.getElementById('assignment_{{ $activity->id }}').click()">
                                                <span wire:loading.remove wire:target="assignmentFiles.{{ $activity->id }}" style="display: flex; align-items: center;"><i class="fe fe-upload mr-2"></i> Upload</span>
                                                <span wire:loading wire:target="assignmentFiles.{{ $activity->id }}" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </button>
                                            @else
                                            <span class="text-muted small font-italic">Belum ada</span>
                                            @endif
                                        @endif
                                        @error("assignmentFiles.{$activity->id}") <span class="text-danger small">{{ $message }}</span> @enderror
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton-{{ $activity->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fe fe-more-horizontal"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-{{ $activity->id }}">
                                                <a class="dropdown-item" href="{{ route('activities.show', $activity->id) }}">
                                                    <i class="fe fe-eye mr-2"></i> Detail
                                                </a>
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                <a class="dropdown-item" href="{{ route('activities.edit', $activity->id) }}">
                                                    <i class="fe fe-edit mr-2"></i> Edit
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="text-center">Belum ada kegiatan selesai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Generic Delete Confirmation Script --}}
    <script>
        function confirmDelete(type, id) {
            let title = type === 'minutes' ? 'Hapus Notulensi?' : 'Hapus Surat Tugas?';
            let text = type === 'minutes' ? 'File notulensi akan dihapus.' : 'File surat tugas akan dihapus.';
            
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Find the component instance for the row
                    // Using @this in loop is tricky, so we rely on Livewire.find or dispatch event to row
                    // But simpler: each row component can listen for an event, OR we can emit to specific component
                    // Actually, easiest way in loop is to call method on the row component instance.
                    // But `confirmDelete` is global script.
                    
                    // Solution: We'll modify the loop to embed the script inside the row or use wire:click to a helper that emits event?
                    // Better: The row component has the method. We should trigger it from `wire:click`.
                    // But we want SweetAlert.
                    
                    // Refined Approach: Use Livewire.dispatchTo specific component.
                    // Or easier: 
                    // Pass the component ID to the JS function?
                    // Let's rely on `wire:click` passing the confirmation via a custom event listener in JS?
                    // No, existing pattern: `onclick="confirmDelete..."`.
                    
                    // We need to call the method on the Correct Livewire Component.
                    // We can use `Livewire.dispatch('delete-file', { type: type, id: id })` and have rows listen?
                    // Or simpler: put the script INSIDE the row component? No, duplication.
                    
                    // Let's use `Livewire.find` if we have component ID.
                    // But we don't have it easily here.
                    
                    // ALTERNATIVE:
                    // Use `wire:click` on the button to trigger a `confirmingDelete` method in PHP, which dispatches a browser event to show Swal?
                    // 1. Button wire:click="confirmDelete('minutes')"
                    // 2. Component: public function confirmDelete($type) { $this->dispatch('show-delete-confirmation', type: $type, id: $this->id); }
                    // 3. Global JS listens to `show-delete-confirmation`, shows Swal, then calls back.
                    // This is robust.
                    
                    // FOR NOW: To keep it verifying quickly, I'll move the SWAL script logic INTO the row component view or assume the `onclick` can target the row.
                    
                    // ACTUALLY: The best way for Row components is to have the `x-data` Alpine component wrapper handle it, or just:
                    // `wire:click` triggers a sweet alert confirmation via `Livewire.on`.
                }
            })
        }
    </script>
    
    {{-- 
       Correction: The previous plan had the script in the list, but now methods are on the ROW.
       The row component needs to receive the call.
       
       Let's use the AlpineJS approach or inline script in the row is easiest for 100% correctness without complex ID passing.
       I'll place a small script script tag in the row view? No, bad performance.
       
       Better: 
       Button: `wire:click="$dispatch('trigger-delete', { type: 'minutes', id: {{ $this->getId() }} })"`
       But `$this->getId()` is generic.
       
       Let's go with:
       Button: `onclick="confirmDeleteRow('{{ $this->getId() }}', 'minutes')"`
       
       Script (Global):
       function confirmDeleteRow(componentId, type) {
           Swal.fire({...}).then(r => {
               if(r.isConfirmed) {
                   Livewire.find(componentId).call(type === 'minutes' ? 'deleteMinutes' : 'deleteAssignment');
               }
           })
       }
    --}}
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
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call(type === 'minutes' ? 'deleteMinutes' : 'deleteAssignment', id);
                }
            })
        }
    </script>
</div>

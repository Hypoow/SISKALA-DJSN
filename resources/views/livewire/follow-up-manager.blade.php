<div class="card shadow-sm mb-4 followup-shell border-0 overflow-hidden">
    <div class="card-header followup-header border-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="mb-2 mb-md-0">
                <strong class="card-title mb-1 d-block text-white">
                    <span class="fe fe-check-square mr-2"></span>Arahan & Tindak Lanjut
                </strong>
                <small class="text-white-50">Kelola arahan, PIC, deadline, dan progres dalam satu tempat.</small>
            </div>
            @if(!$isEditing && auth()->user()->canManageFollowUp())
                <button type="button" wire:click="$toggle('showForm')" class="btn btn-sm btn-light text-primary font-weight-bold rounded-pill px-3" wire:loading.attr="disabled" wire:target="$toggle('showForm')">
                    <span wire:loading.remove wire:target="$toggle('showForm')"><i class="fe fe-plus mr-1"></i> Tambah Item</span>
                    <span wire:loading wire:target="$toggle('showForm')"><span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Memuat...</span>
                </button>
            @endif
        </div>
    </div>
    <div class="card-body bg-white">

    <div class="row mb-3">
        <div class="col-6 col-lg-3 mb-2">
            <button wire:click="setFilter('all')" class="btn btn-block text-left followup-filter {{ $filterStatus === 'all' ? 'active' : '' }}">
                <small>Total Item</small>
                <div class="h5 mb-0">{{ $followups->count() }}</div>
            </button>
        </div>
        <div class="col-6 col-lg-3 mb-2">
            <button wire:click="setFilter('pending')" class="btn btn-block text-left followup-filter is-pending {{ $filterStatus === 'pending' ? 'active' : '' }}">
                <small>Pending</small>
                <div class="h5 mb-0">{{ $stats['pending'] }}</div>
            </button>
        </div>
        <div class="col-6 col-lg-3 mb-2">
            <button wire:click="setFilter('progress')" class="btn btn-block text-left followup-filter is-progress {{ $filterStatus === 'progress' ? 'active' : '' }}">
                <small>Progress</small>
                <div class="h5 mb-0">{{ $stats['progress'] }}</div>
            </button>
        </div>
        <div class="col-6 col-lg-3 mb-2">
            <button wire:click="setFilter('completed')" class="btn btn-block text-left followup-filter is-completed {{ $filterStatus === 'completed' ? 'active' : '' }}">
                <small>Selesai</small>
                <div class="h5 mb-0">{{ $stats['completed'] }}</div>
            </button>
        </div>
    </div>

    <!-- Input Form (Visible if Adding or Editing) -->
    <!-- Loading State for Form Toggle -->
    <div wire:loading wire:target="showForm" class="w-100 mb-4">
        <div class="card shadow-sm border-0 rounded-lg p-4 text-center">
            <div class="spinner-border text-primary mb-2" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="text-muted small mb-0">Memuat formulir...</p>
        </div>
    </div>

    @if($showForm || $isEditing)
    <div class="card shadow-sm mb-4 border rounded-lg overflow-hidden fade-in">
        <div class="card-header bg-light border-bottom p-4 pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title text-primary font-weight-bold mb-1">
                        {{ $isEditing ? 'Edit Tindak Lanjut' : 'Tindak Lanjut Baru' }}
                    </h5>
                    <p class="text-muted small mb-0">
                        {{ $isEditing ? 'Perbarui detail arahan.' : 'Distribusikan arahan ke PIC terkait.' }}
                    </p>
                </div>
                <button wire:click="cancelEdit" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle">
                    <i class="fe fe-x"></i>
                </button>
            </div>
        </div>
        
        <div class="card-body p-4">
            @if($isEditing)
                <!-- Edit Mode -->
                <form wire:submit.prevent="update">
                    <div class="alert alert-info border-0 mb-3 d-flex align-items-center py-2">
                        <i class="fe fe-info mr-2"></i>
                        <span class="small">Mengedit item.</span>
                    </div>

                    <!-- Layout: PIC on Left, Instruction on Right -->
                    <div class="row">
                        <div class="col-md-4">
                            <!-- PIC -->
                             <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark small mb-1">PIC / Penanggung Jawab <span class="text-danger">*</span></label>
                                <div wire:ignore
                                     x-data
                                    x-init="
                                        $($refs.editPicSelect).select2({
                                            theme: 'bootstrap4',
                                            width: '100%',
                                            placeholder: 'Pilih PIC...',
                                            tags: true,
                                            templateResult: formatState,
                                            templateSelection: formatState
                                        }).on('change', function (e) {
                                            @this.set('editPic', $(this).val());
                                        });
                                        $($refs.editPicSelect).val(@json($editPic)).trigger('change');
                                     "
                                >
                                    <select x-ref="editPicSelect" class="form-control select2">
                                        <option value="{{ $editPic }}">{{ $editPic }}</option>
                                        <option value="Ketua DJSN" data-color="#8B5CF6">Ketua DJSN</option>
                                        <option value="Komisi PME" data-color="#28a745">Komisi PME</option>
                                        <option value="Komjakum" data-color="#007bff">Komjakum</option>
                                        <option value="Sekretaris DJSN" data-color="#F97316">Sekretaris DJSN</option>
                                    </select>
                                </div>
                                @error('editPic') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <!-- Deadline -->
                             <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark small mb-1">Deadline</label>
                                <input type="date" wire:model="editDeadline" class="form-control">
                            </div>

                            <!-- Topic (Only if NOT set, unlikely here since edit implies existing) -->
                            @if(!$existingTopic)
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark small mb-1">Topik</label>
                                    <input type="text" wire:model="editTopic" class="form-control" placeholder="Isi Topik...">
                                </div>
                            @endif
                        </div>

                        <div class="col-md-8">
                             <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark small mb-1">Arahan / Deskripsi <span class="text-danger">*</span></label>
                                <textarea wire:model="editInstruction" rows="8" class="form-control bg-white" style="resize: vertical;"></textarea>
                                @error('editInstruction') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                        <button type="button" wire:click="cancelEdit" class="btn btn-link text-muted mr-3">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update"><i class="fe fe-save mr-2"></i> Simpan</span>
                            <span wire:loading wire:target="update"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            @else
                <!-- Create Mode -->
                <form wire:submit.prevent="save">
                    <!-- Global Settings Row -->
                    <div class="row">
                        <!-- Topic (Only if not set) -->
                        @if(!$existingTopic)
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1">Tema / Topik</label>
                            <div wire:ignore
                                    x-data
                                    x-init="
                                    $($refs.select).select2({
                                        theme: 'bootstrap4',
                                        width: '100%',
                                        allowClear: true,
                                        placeholder: 'Pilih...',
                                        tags: true,
                                        templateResult: formatState,
                                        templateSelection: formatState
                                    }).on('change', function (e) {
                                        @this.set('topic', $(this).val());
                                    });
                                    "
                            >
                                <select x-ref="select" id="topicSelect" class="form-control select2">
                                    <option></option>
                                    @foreach(\App\Models\Topic::orderBy('name')->get() as $t)
                                        <option value="{{ $t->name }}" data-color="{{ $t->color ?? '#6c757d' }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @else
                            <input type="hidden" wire:model="topic">
                        @endif

                        <!-- PIC -->
                        <div class="col-md-{{ $existingTopic ? '12' : '6' }} mb-3">
                            <label class="font-weight-bold text-dark small mb-1">PIC / Penanggung Jawab <span class="text-danger">*</span></label>
                            <div wire:ignore
                                    x-data
                                    x-init="
                                    $($refs.picSelect).select2({
                                        theme: 'bootstrap4',
                                        width: '100%',
                                        placeholder: 'Pilih PIC...',
                                        allowClear: true,
                                        tags: true,
                                        templateResult: formatState,
                                        templateSelection: formatState
                                    }).on('change', function (e) {
                                        @this.set('selectedPic', $(this).val());
                                    });
                                    "
                            >
                                <select x-ref="picSelect" id="picSelect" class="form-control select2">
                                    <option></option>
                                    <option value="Ketua DJSN" data-color="#8B5CF6">Ketua DJSN</option>
                                    <option value="Komisi PME" data-color="#28a745">Komisi PME</option>
                                    <option value="Komjakum" data-color="#007bff">Komjakum</option>
                                    <option value="Sekretaris DJSN" data-color="#F97316">Sekretaris DJSN</option>
                                </select>
                            </div>
                                @error('selectedPic') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Instructions List -->
                    <div class="mt-2">
                        <label class="font-weight-bold text-dark small mb-2">Daftar Arahan</label>
                        @error('instructionRows') <div class="alert alert-danger py-2 px-3 mb-2 small">{{ $message }}</div> @enderror

                        <div class="bg-white">
                            @foreach($instructionRows as $index => $row)
                                <div class="card mb-3 border shadow-sm instruction-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <textarea wire:model="instructionRows.{{ $index }}.text" class="form-control border-0 p-0 shadow-none text-dark" rows="3" placeholder="Tulis arahan detail di sini..." style="resize: vertical; font-size: 1rem; background: transparent; min-height: 80px;"></textarea>
                                                @error("instructionRows.{$index}.text") <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            
                                            <div class="pl-3 d-flex flex-column align-items-end" style="min-width: 150px;">
                                                <!-- Date Picker -->
                                                <div class="input-group input-group-sm mb-2 rounded-pill bg-light border px-2 py-1" style="width: auto;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text border-0 bg-transparent p-0 pr-2 text-muted d-flex align-items-center">
                                                            <span class="font-weight-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Deadline</span>
                                                        </span>
                                                    </div>
                                                    <input type="date" wire:model="instructionRows.{{ $index }}.deadline" class="form-control border-0 bg-transparent p-0 text-muted small shadow-none" style="height: auto; width: 110px;">
                                                </div>

                                                <!-- Delete Button -->
                                                @if(count($instructionRows) > 1)
                                                    <button type="button" wire:click="removeInstructionRow({{ $index }})" class="btn btn-sm btn-outline-danger btn-pill px-3 shadow-none" title="Hapus Poin">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="button" wire:click="addInstructionRow" class="btn btn-outline-primary btn-block p-3 border-dashed font-weight-bold">
                                <i class="fe fe-plus-circle mr-2"></i> Tambah Poin Arahan
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                            <button type="button" wire:click="cancelEdit" class="btn btn-link text-muted mr-3">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save"><i class="fe fe-save mr-2"></i> Simpan</span>
                                <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Menyimpan...</span>
                            </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif

    <!-- Data View: Action Cards -->
    <div class="followup-board fade-in mt-4">
        @php
            $groupedFollowups = $followups->groupBy('pic');
        @endphp
        
        @forelse($groupedFollowups as $pic => $items)
            <div class="mb-4">
                <!-- PIC Header -->
                <div class="d-flex align-items-center mb-3">
                    @php
                        $badgeBg = 'bg-primary';
                        if (str_contains(strtoupper($pic), 'PME')) $badgeBg = 'bg-success';
                        elseif ($pic == 'Sekretaris DJSN' || $pic == 'Sekretariat DJSN') $badgeBg = 'bg-warning';
                        elseif ($pic == 'Ketua DJSN') $badgeBg = 'bg-purple';
                        elseif ($pic == 'Anggota DJSN') $badgeBg = 'bg-info';
                        elseif ($pic == 'Komjakum') $badgeBg = 'bg-blue';
                    @endphp
                    <div class="d-inline-flex align-items-center px-3 py-2 rounded-lg shadow-sm text-white" style="background-color: var(--{{ $badgeBg }}, #0d6efd);">
                        <i class="fe fe-users mr-2"></i>
                        <strong style="font-size: 0.95rem; letter-spacing: 0.5px;">{{ $pic ?: 'Tanpa PIC' }}</strong>
                    </div>
                    <div class="ml-3 flex-grow-1 border-top" style="border-top-color: #e2e8f0 !important; border-top-width: 2px !important;"></div>
                </div>

                <!-- Cards Grid -->
                <div class="row">
                    @foreach($items as $item)
                        @php
                            $statusClass = 'secondary';
                            $statusLabel = 'Pending';
                            if($item->status == 1) { $statusClass = 'warning'; $statusLabel = 'Progress'; }
                            if($item->status == 2) { $statusClass = 'success'; $statusLabel = 'Selesai'; }
                            if($item->status == 3) { $statusClass = 'danger'; $statusLabel = 'Batal'; }
                            
                            $isOverdue = $item->deadline && $item->deadline->isPast() && $item->status < 2;
                        @endphp
                        <div class="col-md-6 col-xl-4 mb-4">
                            <div class="card h-100 border-0 task-card {{ $statusClass === 'success' ? 'task-completed' : '' }}">
                                <!-- Card Header: Status & Actions -->
                                <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between align-items-start">
                                    @if(auth()->user()->canManageFollowUp())
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-{{ $statusClass }} rounded-pill dropdown-toggle px-3 py-1 font-weight-bold shadow-sm" type="button" data-toggle="dropdown" style="font-size: 0.75rem; border-width: 1.5px;">
                                                <span class="status-dot bg-{{ $statusClass }} mr-1"></span> {{ $statusLabel }}
                                            </button>
                                            <div class="dropdown-menu shadow-lg border-0 rounded-lg">
                                                <a class="dropdown-item small py-2 font-weight-bold text-secondary" href="#" wire:click.prevent="updateStatus({{ $item->id }}, 0)"><span class="status-dot bg-secondary mr-2"></span> Pending</a>
                                                <a class="dropdown-item small py-2 font-weight-bold text-warning" href="#" wire:click.prevent="updateStatus({{ $item->id }}, 1)"><span class="status-dot bg-warning mr-2"></span> Progress</a>
                                                <a class="dropdown-item small py-2 font-weight-bold text-success" href="#" wire:click.prevent="updateStatus({{ $item->id }}, 2)"><span class="status-dot bg-success mr-2"></span> Selesai</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item small py-2 font-weight-bold text-danger" href="#" wire:click.prevent="updateStatus({{ $item->id }}, 3)"><span class="status-dot bg-danger mr-2"></span> Batal</a>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge badge-{{ $statusClass }} px-3 py-2 rounded-pill shadow-sm">{{ $statusLabel }}</span>
                                    @endif

                                    @if(auth()->user()->canManageFollowUp())
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-light rounded-circle shadow-none text-muted" type="button" data-toggle="dropdown">
                                            <i class="fe fe-more-horizontal"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 rounded-lg">
                                            <a href="#" wire:click.prevent="edit({{ $item->id }})" class="dropdown-item small py-2"><i class="fe fe-edit-2 mr-2 text-primary"></i> Edit Arahan</a>
                                            <a href="#" onclick="confirmDeleteFollowUp({{ $item->id }}); return false;" class="dropdown-item small py-2 text-danger"><i class="fe fe-trash-2 mr-2"></i> Hapus Arahan</a>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Card Body: Instruction -->
                                <div class="card-body py-3">
                                    <div class="task-instruction markdown-content text-dark mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                                        {!! \Illuminate\Support\Str::markdown($item->instruction) !!}
                                    </div>
                                    @if($item->progress_notes)
                                        <div class="p-2 bg-light rounded-lg text-muted small border d-flex align-items-start">
                                            <i class="fe fe-corner-down-right mr-2 mt-1 text-primary"></i> 
                                            <div>
                                                <strong class="d-block text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Catatan Progress</strong>
                                                {{ $item->progress_notes }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Card Footer: Deadline -->
                                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3">
                                    <div class="d-flex align-items-center p-2 rounded {{ $isOverdue ? 'bg-danger text-white' : 'bg-light text-muted' }}" style="width: max-content;">
                                        <i class="fe fe-calendar mr-2"></i>
                                        <small class="font-weight-bold">
                                            @if($item->deadline)
                                                {{ $item->deadline->format('d M Y') }}
                                                @if($isOverdue) <span class="badge badge-light text-danger ml-2 px-2 py-1">Terlambat</span> @endif
                                            @else
                                                Tanpa Tenggat
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="text-center py-5 bg-white rounded-xl border-dashed shadow-sm">
                <div class="avatar avatar-xl bg-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                    <i class="fe fe-clipboard" style="font-size: 2rem;"></i>
                </div>
                <h5 class="text-dark font-weight-bold mb-2">Belum ada arahan tindak lanjut</h5>
                <p class="text-muted small mb-4 mx-auto" style="max-width: 400px;">Mulai dengan menambahkan arahan pertama untuk mendistribusikan tugas kepada PIC terkait kegiatan ini.</p>
                @if(auth()->user()->canManageFollowUp())
                <button type="button" wire:click="$toggle('showForm')" class="btn btn-primary rounded-pill px-4 shadow-sm font-weight-bold" wire:loading.attr="disabled" wire:target="$toggle('showForm')">
                    <span wire:loading.remove wire:target="$toggle('showForm')"><i class="fe fe-plus mr-2"></i> Tambah Arahan Baru</span>
                    <span wire:loading wire:target="$toggle('showForm')"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Memuat...</span>
                </button>
                @endif
            </div>
        @endforelse
    </div>
    </div>
</div>

@push('styles')
<style>
    .followup-shell {
        border-radius: 16px;
    }
    .followup-header {
        background: linear-gradient(135deg, #0d2f72 0%, #1b5bd6 100%);
        padding: 1rem 1.25rem;
    }
    .followup-filter {
        border: 1px solid #e7ecf6;
        border-radius: 12px;
        background: #fbfcff;
        color: #243a63;
        min-height: 76px;
    }
    .followup-filter small {
        color: #8a99b5;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .followup-filter.active {
        border-color: #1b5bd6;
        background: #eef4ff;
        box-shadow: 0 4px 14px rgba(27, 91, 214, 0.16);
    }
    .followup-filter.is-pending.active { border-color: #6c757d; background: #f1f3f5; }
    .followup-filter.is-progress.active { border-color: #f0ad4e; background: #fff8ea; }
    .followup-filter.is-completed.active { border-color: #28a745; background: #edfbf1; }
    .instruction-card {
        border-radius: 12px;
        transition: 0.2s ease;
    }
    .instruction-card:focus-within {
        border-color: #1b5bd6 !important;
        box-shadow: 0 0 0 0.2rem rgba(27, 91, 214, 0.12);
    }
    .border-dashed {
        border-radius: 12px;
        border-width: 2px;
        border-style: dashed !important;
    }
    .border-dashed {
        border-radius: 12px;
        border-width: 2px;
        border-style: dashed !important;
    }
    .task-card {
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #f3f4f6 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important;
    }
    .task-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06) !important;
    }
    .task-completed {
        opacity: 0.75;
        background-color: #fafbfc !important;
    }
    .task-instruction p:last-child {
        margin-bottom: 0;
    }
    .status-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .bg-purple {
        background-color: #8b5cf6 !important;
    }
    .bg-blue {
        background-color: #3b82f6 !important;
    }
</style>
@endpush

<script>
    function formatState(state) {
        if (!state.id) { return state.text; }
        var color = $(state.element).data('color');
        if(color) {
             var $state = $(
                '<span><span class="status-dot" style="display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:5px; background-color:' + color + '"></span> ' + state.text + '</span>'
            );
            return $state;
        }
        return state.text;
    };

    document.addEventListener('livewire:initialized', () => {
        // Alpine handles init now
    });

    Livewire.on('followup-saved', () => {
        $('#topicSelect').val(null).trigger('change');
    });

     Livewire.on('edit-mode-toggled', (topicName) => {
         // Wait for DOM
         setTimeout(() => {
            $('#editTopicSelect').val(topicName).trigger('change');
         }, 100);
    });

    function confirmDeleteFollowUp(id) {
        Swal.fire({
            title: 'Hapus Tindak Lanjut?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-danger shadow-sm mx-1',
                cancelButton: 'btn btn-light shadow-sm text-dark mx-1',
                popup: 'rounded-lg border-0 shadow-lg',
                title: 'text-dark font-weight-bold h5 pt-3',
                htmlContainer: 'text-muted small pb-2',
                icon: 'mx-auto mb-3'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('delete', id);
            }
        })
    }
</script>
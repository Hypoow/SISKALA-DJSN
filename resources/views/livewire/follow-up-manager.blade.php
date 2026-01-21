<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong class="card-title mb-0"><span class="fe fe-check-square mr-2"></span>Tindak Lanjut & Arahan</strong>
        @if(!$isEditing)
        <button type="button" wire:click="$toggle('showForm')" class="btn btn-sm btn-outline-primary" wire:loading.attr="disabled" wire:target="$toggle('showForm')">
            <span wire:loading.remove wire:target="$toggle('showForm')"><i class="fe fe-plus mr-1"></i> Tambah Item</span>
            <span wire:loading wire:target="$toggle('showForm')"><span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Memuat...</span>
        </button>
        @endif
    </div>
    <div class="card-body">

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
    <div class="card shadow-lg mb-4 border-0 rounded-lg overflow-hidden fade-in" style="background: #ffffff;">
        <div class="card-header bg-white border-bottom-0 p-4 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title text-primary font-weight-bold mb-1">
                        {{ $isEditing ? 'Edit Tindak Lanjut' : 'Tindak Lanjut Baru' }}
                    </h5>
                    <p class="text-muted small mb-0">
                        {{ $isEditing ? 'Perbarui detail arahan.' : 'Distribusikan arahan ke PIC terkait.' }}
                    </p>
                </div>
                <button wire:click="cancelEdit" class="btn btn-sm btn-icon btn-light text-muted rounded-circle">
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
                                <label class="font-weight-bold text-dark small mb-1">PIC / Penanggung Jawab</label>
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
                                        <option value="Komisi Komjakum" data-color="#007bff">Komisi Komjakum</option>
                                        <option value="Sekretariat DJSN" data-color="#F97316">Sekretariat DJSN</option>
                                    </select>
                                </div>
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
                                <textarea wire:model="editInstruction" rows="8" class="form-control bg-light" style="resize: vertical;"></textarea>
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
                                    <option value="Komisi Komjakum" data-color="#007bff">Komisi Komjakum</option>
                                    <option value="Sekretariat DJSN" data-color="#F97316">Sekretariat DJSN</option>
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
                                <div class="card mb-3 border shadow-sm instruction-card" style="border-radius: 12px; transition: all 0.2s;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <textarea wire:model="instructionRows.{{ $index }}.text" class="form-control border-0 p-0 shadow-none text-dark" rows="2" placeholder="Tulis arahan detail di sini..." style="resize: none; font-size: 1rem; background: transparent;"></textarea>
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
                                                    <button type="button" wire:click="removeInstructionRow({{ $index }})" class="btn btn-sm btn-outline-danger btn-pill px-3 shadow-none opacity-50 hover-opacity-100" title="Hapus Poin">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="button" wire:click="addInstructionRow" class="btn btn-outline-primary btn-block p-3 border-dashed font-weight-bold" style="border-radius: 12px; border-width: 2px; border-style: dashed !important;">
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

    <!-- Table View -->
    <div class="table-responsive fade-in">
        <table class="table table-hover table-striped mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 20%;">PIC / Komisi</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 50%;">Arahan / Tindak Lanjut</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 15%;">Deadline</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 10%;">Status</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 5%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedFollowups = $followups->groupBy('pic');
                @endphp
                
                @forelse($groupedFollowups as $pic => $items)
                    @foreach($items as $item)
                        <tr>
                            <!-- PIC Column with Rowspan -->
                            @if($loop->first)
                                <td rowspan="{{ $items->count() }}" class="align-top py-3 text-center border-right" style="background-color: #fff;">
                                    @if($pic)
                                        @php
                                            $badgeClass = 'badge-komjakum'; // Default Blue (Komjakum)
                                            if (str_contains(strtoupper($pic), 'PME')) {
                                                $badgeClass = 'badge-pme'; // Green
                                            } elseif ($pic == 'Sekretariat DJSN') {
                                                $badgeClass = 'badge-sekretariat';
                                            } elseif ($pic == 'Ketua DJSN') {
                                                $badgeClass = 'badge-ketua';
                                            } elseif ($pic == 'Anggota DJSN') {
                                                $badgeClass = 'badge-djsn';
                                            }
                                        @endphp
                                        <span class="badge badge-pill {{ $badgeClass }} px-2 mb-1" style="white-space: normal; line-height: 1.4; display: inline-block;">
                                            {{ $pic }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            @endif

                            <!-- Instruction -->
                            <td class="align-top py-3">
                                <div class="mb-0 text-dark small markdown-content">
                                    {!! \Illuminate\Support\Str::markdown($item->instruction) !!}
                                </div>
                                
                                @if($item->progress_notes)
                                    <div class="mt-2 p-2 bg-light rounded border border-light">
                                        <small class="d-block text-muted font-italic mb-1" style="font-size: 10px;">Progress:</small>
                                        <div class="text-dark small"><i class="fe fe-activity text-primary mr-1"></i> {{ $item->progress_notes }}</div>
                                    </div>
                                @endif
                            </td>

                            <!-- Deadline -->
                            <td class="align-top py-3 text-center">
                                @if($item->deadline)
                                    <span class="small {{ $item->deadline->isPast() && $item->status < 2 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                                        {{ $item->deadline->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="align-top py-3 text-center">
                                @php
                                    $statusClass = 'secondary';
                                    $statusLabel = 'Pending';
                                    if($item->status == 1) { $statusClass = 'warning'; $statusLabel = 'Progress'; }
                                    if($item->status == 2) { $statusClass = 'success'; $statusLabel = 'Selesai'; }
                                    if($item->status == 3) { $statusClass = 'danger'; $statusLabel = 'Batal'; }
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="align-top py-3 text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-light shadow-sm" type="button" data-toggle="dropdown">
                                        <i class="fe fe-more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" wire:click.prevent="edit({{ $item->id }})" class="dropdown-item small">
                                            <i class="fe fe-edit-2 mr-2 text-primary"></i> Edit
                                        </a>
                                        <a href="#" onclick="confirmDeleteFollowUp({{ $item->id }}); return false;" class="dropdown-item small text-danger">
                                            <i class="fe fe-trash-2 mr-2"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fe fe-clipboard text-muted display-4 mb-3 d-block" style="opacity: 0.5;"></i>
                            <h6 class="text-muted font-weight-bold">Belum ada tindak lanjut</h6>
                            <button type="button" wire:click="$toggle('showForm')" class="btn btn-primary btn-sm mt-2" wire:loading.attr="disabled" wire:target="$toggle('showForm')">
                                <span wire:loading.remove wire:target="$toggle('showForm')"><i class="fe fe-plus mr-1"></i> Tambah Baru</span>
                                <span wire:loading wire:target="$toggle('showForm')"><span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Memuat...</span>
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</div>

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
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('delete', id);
            }
        })
    }
</script>

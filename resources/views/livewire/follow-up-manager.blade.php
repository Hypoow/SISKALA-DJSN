<div>
    <div class="row align-items-center mb-3">
        <div class="col">
            <h4 class="mb-0 text-primary">
                <i class="fe fe-check-square mr-2"></i>Tindak Lanjut & Arahan
            </h4>
        </div>
        <div class="col-auto">
             @if(!$isEditing)
            <button type="button" wire:click="$toggle('showForm')" class="btn btn-primary btn-sm">
                <i class="fe fe-plus mr-1"></i> Tambah Item
            </button>
            @endif
        </div>
    </div>

    <!-- Input Form (Visible if Adding or Editing) -->
    @if($showForm || $isEditing)
    <div class="card shadow-sm mb-4 border-primary" style="border-left: 4px solid #007bff;">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $isEditing ? 'Edit Tindak Lanjut' : 'Tambah Tindak Lanjut Baru' }}</h5>
            
            @if($isEditing)
                <!-- Edit Mode (Single Item) -->
                <form wire:submit.prevent="update">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Tema / Topik <small class="text-muted">(Opsional)</small></label>
                            <input type="text" wire:model="editTopic" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>PIC / Yang Bertugas</label>
                            <input type="text" wire:model="editPic" class="form-control" placeholder="Nama PIC">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>Arahan / Deskripsi <span class="text-danger">*</span></label>
                            <textarea wire:model="editInstruction" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Deadline</label>
                            <input type="date" wire:model="editDeadline" class="form-control">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" wire:click="cancelEdit" class="btn btn-secondary mr-2">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fe fe-save mr-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            @else
                <!-- Create Mode (Batch Input based on Disposition) -->
                <form wire:submit.prevent="save">
                    <p class="text-muted small mb-3">
                        <i class="fe fe-info mr-1"></i> Masukkan arahan untuk masing-masing PIC yang didisposisikan. Kosongkan jika tidak ada arahan untuk PIC tersebut.
                    </p>
                    
                    @foreach($inputs as $comm => $val)
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body py-2 px-3">
                                <h6 class="font-weight-bold text-primary mb-2">{{ $comm }}</h6>
                                <div class="form-row">
                                    <div class="col-md-8 mb-2 mb-md-0">
                                        <input type="text" wire:model="inputs.{{ $comm }}" class="form-control" placeholder="Arahan / Tindak Lanjut untuk {{ $comm }}...">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" wire:model="deadlines.{{ $comm }}" class="form-control" title="Target Penyelesaian">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="text-right">
                         <button type="button" wire:click="resetInputs" class="btn btn-sm btn-link text-muted mr-2">Reset Form</button>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" wire:click="$toggle('showForm')" class="btn btn-secondary mr-2">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save mr-1"></i> Simpan Semua
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif

    <!-- List of Items -->
    <div class="list-group list-group-flush shadow-sm rounded">
        @forelse($followups as $item)
            <div class="list-group-item list-group-item-action flex-column align-items-start p-3 border-bottom {{ $item->status == 2 ? 'bg-light' : '' }}">
                <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                    <div class="descriptive-header d-flex align-items-center">
                        <!-- Status Badge -->
                        @php
                            $badgeClass = 'secondary';
                            $statusText = 'Pending';
                            if($item->status == 1) { $badgeClass = 'warning text-dark'; $statusText = 'On Progress'; }
                            if($item->status == 2) { $badgeClass = 'success'; $statusText = 'Selesai'; }
                            if($item->status == 3) { $badgeClass = 'danger'; $statusText = 'Batal'; }
                        @endphp
                        <span class="badge badge-{{ $badgeClass }} mr-2 px-2 py-1">{{ $statusText }}</span>
                        
                        <!-- Topic Badge -->
                        @if($item->topic)
                            <span class="badge badge-outline-dark mr-2">{{ $item->topic }}</span>
                        @endif

                        <!-- Deadline -->
                        @if($item->deadline)
                            <small class="text-muted {{ $item->deadline->isPast() && $item->status != 2 ? 'text-danger font-weight-bold' : '' }}">
                                <i class="fe fe-calendar mr-1"></i> Deadline: {{ $item->deadline->format('d M Y') }}
                            </small>
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="action-buttons">
                        <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-light text-primary" title="Edit">
                            <i class="fe fe-edit-2"></i>
                        </button>
                        <button wire:click="delete({{ $item->id }})" class="btn btn-sm btn-light text-danger ml-1" title="Hapus" onclick="return confirm('Yakin ingin menghapus item ini?') || event.stopImmediatePropagation()">
                            <i class="fe fe-trash-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <p class="mb-2 text-dark font-weight-medium" style="white-space: pre-wrap;">@if($item->pic)<strong class="text-primary">{{ $item->pic }}:</strong> @endif{{ $item->instruction }}</p>

                <!-- Footer Info -->
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                         @if($item->pic)
                            <div class="small text-muted">
                                <i class="fe fe-user mr-1"></i> <strong>PIC:</strong> {{ $item->pic }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Progress Section (Mini View) -->
                     @if($item->progress_notes)
                        <div class="text-right small text-muted" style="max-width: 50%;">
                            <i class="fe fe-activity mr-1"></i> <em>{{ \Illuminate\Support\Str::limit($item->progress_notes, 60) }}</em>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="list-group-item text-center py-5 text-muted bg-light">
                <i class="fe fe-clipboard fe-24 mb-2 d-block text-gray-300"></i>
                <p class="mb-0">Belum ada daftar tindak lanjut.</p>
                <button type="button" wire:click="$toggle('showForm')" class="btn btn-link btn-sm mt-1">
                    Tambah sekarang
                </button>
            </div>
        @endforelse
    </div>
</div>

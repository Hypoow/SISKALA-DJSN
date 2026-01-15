<div>
    <div class="row align-items-center mb-4 mt-2">
        <div class="col">
            <h2 class="h3 font-weight-bold mb-0 text-dark">Data Topik</h2>
            <p class="text-muted mb-0">Kelola daftar topik dan label warna untuk Tindak Lanjut</p>
        </div>
        <div class="col-auto">
             <button wire:click="create" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#topicModal">
                <i class="fe fe-plus-circle mr-2"></i> Tambah Topik
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="background-color: #d4edda; color: #155724;">
            <div class="d-flex align-items-center">
                <i class="fe fe-check-circle mr-2"></i> 
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow border-0 rounded-lg overflow-hidden mb-5">
        <div class="card-header bg-primary text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <h5 class="card-title mb-0 text-white font-weight-bold">Daftar Topik Kegiatan</h5>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4" style="width: 5%;">No</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Topik</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Label Warna</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topics as $index => $topic)
                            <tr>
                                <td class="pl-4 align-middle font-weight-bold text-secondary">{{ $topics->firstItem() + $index }}</td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm mr-2 bg-light rounded-circle d-flex justify-content-center align-items-center border">
                                            <i class="fe fe-tag text-secondary" style="color: {{ $topic->color }} !important;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-sm font-weight-bold text-dark">{{ $topic->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background-color: {{ $topic->color }}20; color: {{ $topic->color }}; border: 1px solid {{ $topic->color }};">
                                        {{ $topic->color }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <button wire:click="edit({{ $topic->id }})" class="btn btn-sm btn-icon btn-outline-info mr-1" data-toggle="tooltip" title="Edit">
                                        <i class="fe fe-edit"></i>
                                    </button>
                                    <button onclick="confirmDeleteTopic({{ $topic->id }})" class="btn btn-sm btn-icon btn-outline-danger" data-toggle="tooltip" title="Hapus">
                                        <i class="fe fe-trash-2"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-3 text-secondary" style="font-size: 3rem; opacity: 0.3;">
                                            <i class="fe fe-inbox"></i>
                                        </div>
                                        <h5 class="text-muted">Tidak ada data topik ditemukan</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($topics->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $topics->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="topicModal" tabindex="-1" role="dialog" aria-labelledby="topicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="topicModalLabel">{{ $isEditing ? 'Edit Topik' : 'Tambah Topik Baru' }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body p-4">
                        <div class="form-group">
                            <label for="name" class="small text-muted text-uppercase font-weight-bold">Nama Topik</label>
                            <div class="input-group input-group-merge">
                                <input type="text" class="form-control" id="name" wire:model.live="name" placeholder="Contoh: Rapat Pleno...">
                                <div class="input-group-append">
                                    <div class="input-group-text"><i class="fe fe-tag"></i></div>
                                </div>
                            </div>
                            @error('name') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="color" class="small text-muted text-uppercase font-weight-bold">Warna Label</label>
                            <div class="d-flex align-items-center">
                                <input type="color" class="form-control form-control-color border shadow-none mr-3" id="color" wire:model.live="color" title="Pilih warna" style="width: 50px; height: 38px; cursor: pointer; padding: 0.2rem;">
                                <div class="input-group input-group-merge">
                                    <input type="text" class="form-control" wire:model.live="color" placeholder="#000000">
                                    <div class="input-group-append">
                                        <div class="input-group-text"><i class="fe fe-droplet"></i></div>
                                    </div>
                                </div>
                            </div>
                            @error('color') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Preview -->
                        <div class="mt-4 p-3 rounded bg-light border text-center">
                            <small class="text-muted d-block mb-2 font-weight-bold text-uppercase" style="font-size: 0.7rem;">Preview Tampilan</small>
                            <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background-color: {{ $color }}20; color: {{ $color }}; border: 1px solid {{ $color }}; transition: all 0.3s;">
                                {{ $name ?: 'Nama Topik' }}
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fe fe-save mr-2"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteTopic(id) {
             Swal.fire({
                title: 'Hapus Topik?',
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

        window.addEventListener('close-modal', event => {
            $('#topicModal').modal('hide');
        });

        window.addEventListener('open-modal', event => {
            $('#topicModal').modal('show');
        });
        
        document.addEventListener('livewire:initialized', () => {
             $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    
    <style>
        .text-xxs { font-size: 0.75rem; }
        .opacity-7 { opacity: 0.7; }
        .avatar-sm { width: 36px; height: 36px; font-size: 0.875rem; }
        .input-group-merge .form-control:focus + .input-group-append .input-group-text,
        .input-group-merge .form-control:focus {
            border-color: #2a5298;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
    </style>
</div>

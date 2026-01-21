<div>
    <div class="row align-items-center mb-4 mt-2">
        <div class="col-md-6 mb-3 mb-md-0">
            <h2 class="h3 font-weight-bold mb-1 text-dark">Manajemen Topik</h2>
             <p class="text-muted mb-0 small">Atur kategori dan label warna untuk pengelompokan kegiatan.</p>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-md-end align-items-center">
                 <div class="input-group input-group-merge shadow-sm mr-3" style="max-width: 300px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 pl-3"><i class="fe fe-search text-muted"></i></span>
                    </div>
                    <input type="text" class="form-control border-left-0 pl-2" wire:model.live.debounce.300ms="search" placeholder="Cari topik...">
                </div>
                <button wire:click="create" class="btn btn-primary shadow-sm rounded-pill px-4 font-weight-bold" data-toggle="modal" data-target="#topicModal">
                    <i class="fe fe-plus mr-2"></i> Tambah
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-lg" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
            <div class="d-flex align-items-center">
                <i class="fe fe-check-circle mr-3" style="font-size: 1.2rem;"></i> 
                <strong>{{ session('message') }}</strong>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4 py-3" style="width: 5%;">No</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3">Nama Topik</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3 text-center">Warna Label</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 py-3 text-right pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topics as $index => $topic)
                            <tr style="transition: background-color 0.2s;">
                                <td class="pl-4">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $topics->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                         <div class="icon icon-shape icon-sm shadow-sm rounded-circle mr-3 text-white d-flex align-items-center justify-content-center" style="background-color: {{ $topic->color }}; width: 32px; height: 32px;">
                                            <i class="fe fe-tag text-xs"></i>
                                        </div>
                                        <span class="font-weight-bold text-dark text-sm">{{ $topic->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill px-3 py-2 text-xs" style="background-color: {{ $topic->color }}15; color: {{ $topic->color }}; border: 1px solid {{ $topic->color }}40;">
                                        {{ strtoupper($topic->color) }}
                                    </span>
                                </td>
                                <td class="text-right pr-4">
                                    <button wire:click="edit({{ $topic->id }})" class="btn btn-sm btn-outline-info rounded-circle btn-icon shadow-sm mr-2" data-toggle="tooltip" title="Edit">
                                        <i class="fe fe-edit-2"></i>
                                    </button>
                                    <button onclick="confirmDeleteTopic({{ $topic->id }})" class="btn btn-sm btn-outline-danger rounded-circle btn-icon shadow-sm" data-toggle="tooltip" title="Hapus">
                                        <i class="fe fe-trash-2"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center my-4">
                                        <div class="icon icon-shape bg-light text-muted rounded-circle mb-3 shadow-none" style="width: 64px; height: 64px;">
                                            <i class="fe fe-search" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <h5 class="text-muted mb-1">Tidak ada topik ditemukan</h5>
                                        <p class="text-small text-muted mb-0">Coba kata kunci lain atau tambahkan topik baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($topics->hasPages())
                <div class="p-4 border-top">
                    {{ $topics->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="topicModal" tabindex="-1" role="dialog" aria-labelledby="topicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0 rounded-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title font-weight-bold ml-2" id="topicModalLabel">{{ $isEditing ? 'Edit Topik' : 'Tambah Topik Baru' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body p-4 pt-3">
                         <p class="text-muted small mb-4 ml-2">Lengkapi form di bawah ini untuk {{ $isEditing ? 'memperbarui' : 'membuat' }} topik kegiatan.</p>

                        <div class="form-group mb-4">
                            <label for="name" class="form-control-label small text-muted font-weight-bold ml-1 mb-2">NAMA TOPIK</label>
                            <div class="input-group input-group-merge shadow-sm rounded-lg overflow-hidden">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0 pl-3"><i class="fe fe-type text-primary"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-lg border-left-0 pl-2" id="name" wire:model.live="name" placeholder="Contoh: Rapat Koordinasi">
                            </div>
                            @error('name') <span class="text-danger text-xs mt-2 ml-1 d-block"><i class="fe fe-alert-circle mr-1"></i> {{ $message }}</span> @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-control-label small text-muted font-weight-bold ml-1 mb-2">PILIH WARNA LABEL</label>
                            
                            <!-- Color Presets -->
                            <div class="d-flex flex-wrap mb-3 px-1">
                                @php
                                    $presets = [
                                        ['#007bff', 'Biru'], ['#6610f2', 'Ungu'], ['#6f42c1', 'Ungu Muda'], ['#e83e8c', 'Pink'],
                                        ['#dc3545', 'Merah'], ['#fd7e14', 'Oranye'], ['#ffc107', 'Kuning'], ['#28a745', 'Hijau'],
                                        ['#20c997', 'Teal'], ['#17a2b8', 'Cyan'], ['#343a40', 'Dark']
                                    ];
                                @endphp
                                @foreach($presets as $preset)
                                    <button type="button" class="btn btn-sm rounded-circle mr-2 mb-2 p-0 shadow-sm border-0 position-relative color-preset-btn" 
                                            wire:click="$set('color', '{{ $preset[0] }}')"
                                            style="width: 32px; height: 32px; background-color: {{ $preset[0] }}; transition: transform 0.2s;">
                                        @if($color == $preset[0])
                                            <i class="fe fe-check text-white position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 0.8rem;"></i>
                                        @endif
                                    </button>
                                @endforeach
                            </div>

                            <div class="input-group input-group-merge shadow-sm rounded-lg overflow-hidden" style="max-width: 200px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0 pl-2 pr-2">
                                        <input type="color" class="form-control-color border-0 p-0 shadow-none m-0" id="color" wire:model.live="color" title="Custom Color" style="width: 28px; height: 28px;">
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0 pl-2 text-uppercase font-weight-bold text-muted" wire:model.live="color" placeholder="#000000" maxlength="7">
                            </div>
                             @error('color') <span class="text-danger text-xs mt-2 ml-1 d-block"><i class="fe fe-alert-circle mr-1"></i> {{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Live Preview Card -->
                        <div class="card bg-secondary border-0 rounded-lg p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem;">Live Preview</small>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm border">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-pill px-3 py-2 mr-3" style="background-color: {{ $color ?? '#007bff' }}15; color: {{ $color ?? '#007bff' }}; border: 1px solid {{ $color ?? '#007bff' }}40;">
                                        {{ strtoupper($color ?? '#007bff') }}
                                    </span>
                                    <h5 class="mb-0 text-dark font-weight-bold">{{ $name ?: 'Nama Topik Kegiatan' }}</h5>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light text-muted font-weight-bold rounded-pill px-4" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm font-weight-bold">
                            <i class="fe fe-save mr-2"></i> {{ $isEditing ? 'Simpan Perubahan' : 'Buat Topik' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteTopic(id) {
             Swal.fire({
                title: 'Hapus Topik?',
                text: "Topik yang dihapus tidak dapat dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-rounded-lg shadow-lg',
                    confirmButton: 'btn btn-danger font-weight-bold rounded-pill px-4',
                    cancelButton: 'btn btn-secondary font-weight-bold rounded-pill px-4'
                }
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
        .color-preset-btn:hover {
            transform: scale(1.1);
            cursor: pointer;
        }
        .swal2-rounded-lg {
            border-radius: 1rem !important;
        }
    </style>
</div>

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

    <!-- Grid View for Topics -->
    <div class="row">
        @forelse($topics as $index => $topic)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card h-100 border-0 shadow-sm topic-card position-relative overflow-hidden" style="border-radius: 1.25rem;">
                    <!-- Decorative back blob -->
                    <div class="position-absolute" style="top: -30px; right: -30px; width: 120px; height: 120px; border-radius: 50%; opacity: 0.15; background-color: {{ $topic->color }}; z-index: 0; transition: all 0.3s ease;"></div>
                    
                    <div class="card-body p-4 d-flex flex-column position-relative" style="z-index: 1;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; border-radius: 1rem; background-color: {{ $topic->color }}15; color: {{ $topic->color }}; border: 1px solid {{ $topic->color }}30;">
                                <i class="fe fe-tag" style="font-size: 1.5rem;"></i>
                            </div>
                            
                            <!-- Actions Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="outline: none; box-shadow: none;">
                                    <i class="fe fe-more-vertical" style="font-size: 1.4rem;"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 0.8rem; padding: 0.5rem; min-width: 140px;">
                                    <button wire:click="edit({{ $topic->id }})" class="dropdown-item text-primary font-weight-bold d-flex align-items-center py-2" style="border-radius: 0.5rem; transition: background 0.15s;">
                                        <i class="fe fe-edit-2 mr-2"></i> Edit
                                    </button>
                                    <div class="dropdown-divider my-1"></div>
                                    <button onclick="confirmDeleteTopic({{ $topic->id }})" class="dropdown-item text-danger font-weight-bold d-flex align-items-center py-2" style="border-radius: 0.5rem; transition: background 0.15s;">
                                        <i class="fe fe-trash-2 mr-2"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-auto">
                            <h4 class="font-weight-bold text-dark mb-2 pr-1" style="font-size: 1.15rem; line-height: 1.3;">{{ $topic->name }}</h4>
                            <div class="d-flex align-items-center mt-3">
                                <span class="badge px-3 py-2 text-xs font-weight-bold" style="background-color: {{ $topic->color }}; color: #fff; border-radius: 0.5rem; box-shadow: 0 4px 10px {{ $topic->color }}40; letter-spacing: 0.5px;">
                                    <i class="fe fe-droplet mr-1"></i> {{ strtoupper($topic->color) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 1.25rem;">
                    <div class="card-body py-5 text-center">
                        <div class="icon-shape bg-light text-muted rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fe fe-search" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-dark font-weight-bold mb-2">Tidak ada topik kegiatan</h4>
                        <p class="text-muted mb-4">Mulai dengan menambahkan kategori atau topik baru untuk pelaporan.</p>
                        <button wire:click="create" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm" data-toggle="modal" data-target="#topicModal">
                            <i class="fe fe-plus mr-2"></i> Buat Topik Perdana
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    
    @if($topics->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <div class="bg-white px-4 py-2 shadow-sm" style="border-radius: 2rem;">
                {{ $topics->links() }}
            </div>
        </div>
    @endif

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
        .topic-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            top: 0;
            border: 1px solid rgba(0,0,0,0.04) !important;
        }
        .topic-card:hover {
            top: -5px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
        }
        .topic-card:hover .position-absolute {
            transform: scale(1.2);
            opacity: 0.25 !important;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</div>

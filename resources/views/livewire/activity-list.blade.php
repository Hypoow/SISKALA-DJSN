<div>
    <div wire:poll.5s>
        <div class="row my-4">
            <div class="col-md-12">
                <div class="card shadow">
                    @if($this->urgentActivities->isNotEmpty() && auth()->check() && auth()->user()->isAdmin())
                        <div class="alert alert-danger mx-4 mt-4 mb-0" role="alert">
                            <h4 class="alert-heading"><i class="fe fe-alert-triangle mr-2"></i>Perhatian: Kegiatan H-3 Belum Didisposisi</h4>
                            <p>Terdapat <strong>{{ $this->urgentActivities->count() }}</strong> kegiatan yang akan dilaksanakan dalam 3 hari ke depan namun belum memiliki disposisi.</p>
                            <hr>
                            <ul class="mb-0 pl-3">
                                @foreach($this->urgentActivities as $urgent)
                                    <li>
                                        <strong>{{ $urgent->start_date->isoFormat('D MMM Y') }}</strong> - {{ $urgent->name }}
                                        <a href="{{ route('activities.edit', $urgent->id) }}" class="alert-link ml-2"><i class="fe fe-edit"></i> Disposisi Sekarang</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="toolbar row mb-3">
                            <div class="col-md-6">
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
                                        <label class="sr-only" for="sortDirection">Urutan</label>
                                        <select class="custom-select mr-sm-2" id="sortDirection" wire:model.live="sortDirection">
                                            <option value="asc">Waktu: Terdekat (Awal - Akhir)</option>
                                            <option value="desc">Waktu: Terjauh (Akhir - Awal)</option>
                                        </select>
                                    </div>
                                    <div class="col-auto my-1">
                                        <label class="sr-only" for="search">Search</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="search" wire:model.live.debounce.300ms="search" placeholder="Cari kegiatan...">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button"><i class="fe fe-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right d-flex align-items-center justify-content-end">
                                {{-- Legend --}}
                                <div class="mr-3 d-flex align-items-center">
                                    <span class="mr-2 font-weight-bold">
                                        <span class="d-none d-md-inline">Keterangan:</span>
                                        <span class="d-inline d-md-none">Ket:</span>
                                    </span>
                                    <span class="mr-2"><span class="status-dot" style="background-color: var(--primary-color);"></span> Internal</span>
                                    <span><span class="status-dot" style="background-color: var(--accent-color);"></span> Eksternal</span>
                                </div>

                                {{-- Admin Only --}}
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <a href="{{ route('activities.create') }}" class="btn btn-primary">
                                    <span class="fe fe-plus fe-16 mr-0 mr-md-2"></span>
                                    <span class="d-none d-md-inline">Tambah Kegiatan</span>
                                </a>
                                @endif
                            </div>
                        </div>
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
                                                    onclick="confirmBulkDelete()"
                                                    class="btn btn-sm btn-danger">
                                                Hapus Terpilih
                                            </button>
                                        </div>
                                    </div>
                                    <script>
                                        function confirmBulkDelete() {
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
                                                        <input type="checkbox" class="custom-control-input" id="selectAllActivity" wire:model.live="selectAll">
                                                        <label class="custom-control-label" for="selectAllActivity"></label>
                                                    </div>
                                                </th>
                                                @endif
                                                <th>Waktu</th>
                                                <th>Nama Kegiatan</th>
                                                <th>Status Pelaksanaan</th>
                                                <th>Status Undangan</th>
                                                <th>Lokasi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($groupedActivities as $month => $activities)
                                                <tr role="group" class="bg-light">
                                                    <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 5 }}" class="font-weight-bold text-uppercase text-primary pl-4" style="letter-spacing: 1px; border-bottom: 2px solid #007bff;">{{ $month }}</td>
                                                </tr>
                                                @foreach($activities as $activity)
                                                <tr class="{{ $activity->type == 'internal' ? 'row-internal' : 'row-external' }}">
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
                                                    <!-- Calendar Icon Box -->
                                                    <div class="text-center bg-white border border-secondary shadow-sm rounded" style="width: 50px; overflow: hidden;">
                                                        <div class="bg-primary text-white small font-weight-bold py-0" style="font-size: 10px; line-height: 1.2;">
                                                            {{ $activity->date_time->format('M') }}
                                                        </div>
                                                        <div class="h4 mb-0 font-weight-bold text-dark py-2">
                                                            {{ $activity->date_time->format('d') }}
                                                        </div>
                                                    </div>
                                                    <!-- Time Info -->
                                                    <div class="ml-3">
                                                        <span class="h5 mb-0 font-weight-bold text-primary">{{ $activity->date_time->format('H:i') }}</span>
                                                        <div class="small text-muted font-weight-bold text-uppercase">{{ $activity->date_time->isoFormat('dddd') }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('activities.show', $activity->id) }}" class="text-dark font-weight-bold">{{ $activity->name }}</a>
                                            </td>
                                            <td>
                                                @switch($activity->status)
                                                    @case(0) <span class="badge badge-success">On Schedule</span> @break
                                                    @case(1) <span class="badge badge-warning">Reschedule</span> @break
                                                    @case(2) <span class="badge badge-secondary">Belom ada Dispo</span> @break
                                                    @case(3) <span class="badge badge-danger">Tidak Dilaksanakan</span> @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($activity->type == 'external')
                                                    @switch($activity->invitation_status)
                                                        @case(0) <span class="badge badge-success">Proses Disposisi</span> @break
                                                        @case(1) <span class="badge badge-secondary" style="background-color: brown;">Sudah ada Disposisi</span> @break
                                                        @case(2) <span class="badge badge-danger">Untuk Diketahui Ketua</span> @break
                                                        @case(3) <span class="badge badge-primary">Terjadwal Hadir</span> @break
                                                    @endswitch
                                                @else
                                                    @switch($activity->invitation_status)
                                                        @case(0) <span class="badge badge-success">Proses Terkirim</span> @break
                                                        @case(1) <span class="badge badge-primary">Proses TTD</span> @break
                                                        @case(2) <span class="badge badge-danger">Proses Drafting dan Acc</span> @break
                                                    @endswitch
                                                @endif
                                            </td>
                                            <td>
                                                @if($activity->location_type == 'offline')
                                                    <span class="badge badge-secondary">Offline</span>
                                                @else
                                                    <span class="badge badge-info">Online</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted sr-only">Action</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="{{ route('activities.show', $activity->id) }}">Detail</a>
                                                    @if(auth()->check() && auth()->user()->isAdmin())
                                                    <a class="dropdown-item" href="{{ route('activities.edit', $activity->id) }}">Edit</a>
                                                    <form id="delete-form-{{ $activity->id }}" action="{{ route('activities.destroy', $activity->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger" onclick="confirmDelete({{ $activity->id }})">Hapus</button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 6 : 5 }}" class="text-center">Belum ada kegiatan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

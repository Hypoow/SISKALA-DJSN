@extends('layouts.app')

@section('title', 'Daftar Kegiatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="mb-2 page-title">Daftar Kegiatan</h2>
        <p class="card-text">Daftar semua kegiatan eksternal dan internal.</p>
        
        <div class="row my-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="toolbar row mb-3">
                            <div class="col-md-6">
                                <form class="form-inline" action="{{ route('activities.index') }}" method="GET">
                                    <div class="form-row align-items-center">
                                        <div class="col-auto my-1">
                                            <label class="mr-sm-2 sr-only" for="typeFilter">Tipe</label>
                                            <select class="custom-select mr-sm-2" id="typeFilter" name="type" onchange="this.form.submit()">
                                                <option value="">Semua Tipe</option>
                                                <option value="external" {{ request('type') == 'external' ? 'selected' : '' }}>Eksternal</option>
                                                <option value="internal" {{ request('type') == 'internal' ? 'selected' : '' }}>Internal</option>
                                            </select>
                                        </div>
                                        <div class="col-auto my-1">
                                            <label class="sr-only" for="search">Search</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Cari kegiatan...">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit"><i class="fe fe-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 text-right d-flex align-items-center justify-content-end">
                                {{-- Legend --}}
                                <div class="mr-3 d-flex align-items-center">
                                    <span class="mr-2 font-weight-bold">
                                        <span class="d-none d-md-inline">Keterangan:</span>
                                        <span class="d-inline d-md-none">Ket:</span>
                                    </span>
                                    <span class="mr-2"><span class="status-dot" style="background-color: #e3f2fd; border: 1px solid #ccc;"></span> Internal</span>
                                    <span><span class="status-dot" style="background-color: #fff3cd; border: 1px solid #ccc;"></span> Eksternal</span>
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
                        <!-- table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Nama Kegiatan</th>
                                        <th>Status Pelaksanaan</th>
                                        <th>Status Undangan</th>
                                        <th>Lokasi</th>
                                        @if(auth()->check() && auth()->user()->isAdmin())
                                        <th>Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($groupedActivities as $month => $activities)
                                        <tr role="group" class="bg-light">
                                            <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 6 : 5 }}" class="font-weight-bold text-uppercase text-primary pl-4" style="letter-spacing: 1px; border-bottom: 2px solid #007bff;">{{ $month }}</td>
                                        </tr>
                                        @foreach($activities as $activity)
                                        <tr class="{{ $activity->type == 'internal' ? 'row-internal' : 'row-external' }}">
                                            <td style="min-width: 140px;"> <!-- Increased width -->
                                                <div class="d-flex align-items-center">
                                                    <!-- Calendar Icon Box -->
                                                    <div class="text-center bg-white border border-secondary shadow-sm rounded" style="width: 50px; overflow: hidden;">
                                                        <div class="bg-primary text-white small font-weight-bold py-0" style="font-size: 10px; line-height: 1.2;">
                                                            {{ $activity->date_time->format('M') }}
                                                        </div>
                                                        <div class="h4 mb-0 font-weight-bold text-dark py-2"> <!-- Increased padding -->
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
                                            @if(auth()->check() && auth()->user()->isAdmin())
                                            <td>
                                                <button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted sr-only">Action</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="{{ route('activities.show', $activity->id) }}">Detail</a>
                                                    <a class="dropdown-item" href="{{ route('activities.edit', $activity->id) }}">Edit</a>
                                                    <form id="delete-form-{{ $activity->id }}" action="{{ route('activities.destroy', $activity->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger" onclick="confirmDelete({{ $activity->id }})">Remove</button>
                                                    </form>
                                                </div>
                                            </td>
                                            @endif
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
            </div> <!-- simple table -->
        </div> <!-- end section -->
    </div> <!-- .col-12 -->
</div> <!-- .row -->
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data kegiatan yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush

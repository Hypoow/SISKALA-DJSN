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
                            <div class="col-md-6 text-right">
                                {{-- Admin Only --}}
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <a href="{{ route('activities.create') }}" class="btn btn-primary"><span class="fe fe-plus fe-16 mr-2"></span>Tambah Kegiatan</a>
                                @endif
                            </div>
                        </div>
                        <!-- table -->
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedActivities as $month => $activities)
                                    <tr role="group" class="bg-light">
                                        <td colspan="5"><strong>{{ $month }}</strong></td>
                                    </tr>
                                    @foreach($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->date_time->format('d M Y H:i') }}</td>
                                        <td>{{ $activity->name }}</td>
                                        <td>
                                            @if($activity->type == 'external')
                                                <span class="badge badge-info">Eksternal</span>
                                            @else
                                                <span class="badge badge-primary">Internal</span>
                                            @endif
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
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ route('activities.show', $activity->id) }}">Detail</a>
                                                {{-- Admin Only --}}
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                    <a class="dropdown-item" href="{{ route('activities.edit', $activity->id) }}">Edit</a>
                                                    <form id="delete-form-{{ $activity->id }}" action="{{ route('activities.destroy', $activity->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger" onclick="confirmDelete({{ $activity->id }})">Remove</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada kegiatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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

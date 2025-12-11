@extends('layouts.app')

@section('title', 'Kegiatan Eksternal')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="mb-2 page-title">Kegiatan Eksternal</h2>
        <p class="card-text">Daftar kegiatan eksternal yang akan datang dan telah terlaksana.</p>
        
        <div class="row my-4">
            <!-- Small table -->
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="toolbar row mb-3">
                            <div class="col">
                                <form class="form-inline">
                                    <div class="form-row">
                                        <div class="form-group col-auto">
                                            <label for="search" class="sr-only">Search</label>
                                            <input type="text" class="form-control" id="search" value="" placeholder="Cari kegiatan...">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col ml-auto">
                                <div class="dropdown float-right">
                                    {{-- Admin Only --}}
                                    @if(auth()->check() && auth()->user()->isAdmin())
                                    <a href="{{ route('activities.create', ['type' => 'external']) }}" class="btn btn-primary float-right ml-3">Tambah Kegiatan +</a>
                                    @endif
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="actionMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Action </button>
                                    <div class="dropdown-menu" aria-labelledby="actionMenuButton">
                                        <a class="dropdown-item" href="#">Export</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr role="row">
                                    <th colspan="4">Detail Kegiatan</th>
                                    <th colspan="2">Status</th>
                                    <th colspan="2">Lainnya</th>
                                </tr>
                                <tr role="row">
                                    <th>Nama Kegiatan</th>
                                    <th>Waktu</th>
                                    <th>Lokasi</th>
                                    <th>Keterangan</th>
                                    <th>Pelaksanaan</th>
                                    <th>Undangan</th>
                                    <th>Lampiran</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedActivities as $month => $activities)
                                    <tr role="group" class="bg-light">
                                        <td colspan="8"><strong>{{ $month }}</strong></td>
                                    </tr>
                                    @foreach($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->name }}</td>
                                        <td>{{ $activity->date_time->format('d M Y H:i') }}</td>
                                        <td>{{ $activity->location }}</td>
                                        <td>{{ $activity->dispo_note }}</td>
                                        <td>
                                            @switch($activity->status)
                                                @case(0) <span class="badge badge-primary">On Schedule</span> @break
                                                @case(1) <span class="badge badge-success">Reschedule</span> @break
                                                @case(2) <span class="badge badge-warning">Belom ada Dispo</span> @break
                                                @case(3) <span class="badge badge-danger">Tidak Dilaksanakan</span> @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($activity->invitation_status)
                                                @case(0) <span class="badge badge-success">Proses Disposisi</span> @break
                                                @case(1) <span class="badge badge-secondary" style="background-color: brown;">Sudah ada Disposisi</span> @break
                                                @case(2) <span class="badge badge-danger">Untuk Diketahui Ketua</span> @break
                                                @case(3) <span class="badge badge-primary">Terjadwal Hadir</span> @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($activity->attachment_path)
                                                <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank" class="btn btn-sm btn-info"><i class="fe fe-file"></i> PDF</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                {{-- Admin Only --}}
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                    <a class="dropdown-item" href="{{ route('external-activities.edit', $activity->id) }}">Edit</a>
                                                    <form action="{{ route('external-activities.destroy', $activity->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Remove</button>
                                                    </form>
                                                @else
                                                    <span class="dropdown-item text-muted">View Only</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada kegiatan.</td>
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

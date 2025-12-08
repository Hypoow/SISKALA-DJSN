@extends('layouts.app')

@section('title', 'Kegiatan Lampau')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="mb-2 page-title">Kegiatan Lampau</h2>
        <p class="card-text">Daftar kegiatan yang sudah lewat.</p>
        
        <div class="row my-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="toolbar row mb-3">
                            <div class="col-md-6">
                                <form class="form-inline" action="{{ route('activities.past') }}" method="GET">
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
                                <a href="{{ route('activities.index') }}" class="btn btn-secondary">
                                    <span class="fe fe-arrow-left fe-16 mr-0 mr-md-2"></span>
                                    <span class="d-none d-md-inline">Kembali ke Daftar Kegiatan</span>
                                </a>
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
                                        <th>Notulensi</th>
                                        <th>Surat Tugas</th>
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
                                            <td style="min-width: 140px;">
                                                <div class="d-flex align-items-center">
                                                    <div class="text-center bg-white border border-secondary shadow-sm rounded" style="width: 50px; overflow: hidden;">
                                                        <div class="bg-secondary text-white small font-weight-bold py-0" style="font-size: 10px; line-height: 1.2;">
                                                            {{ $activity->date_time->format('M') }}
                                                        </div>
                                                        <div class="h4 mb-0 font-weight-bold text-dark py-2">
                                                            {{ $activity->date_time->format('d') }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <span class="h5 mb-0 font-weight-bold text-secondary">{{ $activity->date_time->format('H:i') }}</span>
                                                        <div class="small text-muted font-weight-bold text-uppercase">{{ $activity->date_time->isoFormat('dddd') }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('activities.show', $activity->id) }}" class="text-dark font-weight-bold">{{ $activity->name }}</a>
                                                <br>
                                                <small class="text-muted">{{ $activity->location_type == 'online' ? 'Online' : $activity->location }}</small>
                                            </td>
                                            <td>
                                                @switch($activity->status)
                                                    @case(0) <span class="badge badge-success">Terlaksana</span> @break
                                                    @case(1) <span class="badge badge-warning">Reschedule</span> @break
                                                    @case(2) <span class="badge badge-secondary">Belom ada Dispo</span> @break
                                                    @case(3) <span class="badge badge-danger">Batal</span> @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($activity->minutes_path)
                                                    <div class="mb-2">
                                                        <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block">
                                                            <i class="fe fe-download"></i> Download
                                                        </a>
                                                    </div>
                                                    @if(auth()->check() && auth()->user()->isAdmin())
                                                    <button class="btn btn-sm btn-outline-secondary btn-block" type="button" data-toggle="collapse" data-target="#uploadCollapse{{ $activity->id }}" aria-expanded="false">
                                                        Ganti File
                                                    </button>
                                                    @endif
                                                @else
                                                    @if(auth()->check() && auth()->user()->isAdmin())
                                                    <span class="text-muted small font-italic d-block mb-2">Belum ada</span>
                                                    <button class="btn btn-sm btn-primary btn-block" type="button" data-toggle="collapse" data-target="#uploadCollapse{{ $activity->id }}" aria-expanded="false">
                                                        <i class="fe fe-upload"></i> Upload
                                                    </button>
                                                    @else
                                                    <span class="text-muted small font-italic">Belum ada</span>
                                                    @endif
                                                @endif

                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                <div class="collapse mt-2" id="uploadCollapse{{ $activity->id }}">
                                                    <form action="{{ route('activities.upload-minutes', $activity->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group mb-2">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="minutes_{{ $activity->id }}" name="minutes_path" accept="application/pdf" required onchange="this.nextElementSibling.innerText = this.files[0].name">
                                                                <label class="custom-file-label" for="minutes_{{ $activity->id }}" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">Pilih PDF...</label>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-success btn-block">Simpan</button>
                                                    </form>
                                                </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($activity->assignment_letter_path)
                                                    <div class="mb-2">
                                                        <a href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block">
                                                            <i class="fe fe-download"></i> Download
                                                        </a>
                                                    </div>
                                                    @if(auth()->check() && auth()->user()->isAdmin())
                                                    <button class="btn btn-sm btn-outline-secondary btn-block" type="button" data-toggle="collapse" data-target="#uploadAssignmentCollapse{{ $activity->id }}" aria-expanded="false">
                                                        Ganti File
                                                    </button>
                                                    @endif
                                                @else
                                                    @if(auth()->check() && auth()->user()->isAdmin())
                                                    <span class="text-muted small font-italic d-block mb-2">Belum ada</span>
                                                    <button class="btn btn-sm btn-primary btn-block" type="button" data-toggle="collapse" data-target="#uploadAssignmentCollapse{{ $activity->id }}" aria-expanded="false">
                                                        <i class="fe fe-upload"></i> Upload
                                                    </button>
                                                    @else
                                                    <span class="text-muted small font-italic">Belum ada</span>
                                                    @endif
                                                @endif

                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                <div class="collapse mt-2" id="uploadAssignmentCollapse{{ $activity->id }}">
                                                    <form action="{{ route('activities.upload-assignment', $activity->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group mb-2">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="assignment_{{ $activity->id }}" name="assignment_letter_path" accept="application/pdf" required onchange="this.nextElementSibling.innerText = this.files[0].name">
                                                                <label class="custom-file-label" for="assignment_{{ $activity->id }}" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">Pilih PDF...</label>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-success btn-block">Simpan</button>
                                                    </form>
                                                </div>
                                                @endif
                                            </td>
                                            @if(auth()->check() && auth()->user()->isAdmin())
                                            <td>
                                                <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 6 : 5 }}" class="text-center">Belum ada kegiatan lampau.</td>
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

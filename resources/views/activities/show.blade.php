@extends('layouts.app')

@section('title', 'Detail Kegiatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="h5 page-title"><small class="text-muted text-uppercase">Detail Kegiatan</small><br />{{ $activity->name }}</h2>
            </div>
            <div class="col-auto">
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-primary">Edit</a>
                @endif
                <a href="{{ route('activities.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title">Informasi Utama</strong>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Tipe Kegiatan</dt>
                            <dd class="col-sm-9">
                                @if($activity->type == 'external')
                                    <span class="badge badge-info">Eksternal</span>
                                @else
                                    <span class="badge badge-primary">Internal</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">Waktu</dt>
                            <dd class="col-sm-9">{{ $activity->date_time->format('d F Y, H:i') }} WIB</dd>

                            <dt class="col-sm-3">Lokasi</dt>
                            <dd class="col-sm-9">
                                @if($activity->location_type == 'offline')
                                    <span class="badge badge-secondary">Offline</span> {{ $activity->location }}
                                @else
                                    <span class="badge badge-info">Online</span> 
                                    @if($activity->meeting_link)
                                        <a href="{{ $activity->meeting_link }}" target="_blank">{{ $activity->meeting_link }}</a>
                                    @else
                                        -
                                    @endif
                                @endif
                            </dd>

                            <dt class="col-sm-3">Status Pelaksanaan</dt>
                            <dd class="col-sm-9">
                                @switch($activity->status)
                                    @case(0) <span class="badge badge-success">On Schedule</span> @break
                                    @case(1) <span class="badge badge-warning">Reschedule</span> @break
                                    @case(2) <span class="badge badge-secondary">Belom ada Dispo</span> @break
                                    @case(3) <span class="badge badge-danger">Tidak Dilaksanakan</span> @break
                                @endswitch
                            </dd>

                            <dt class="col-sm-3">Status Undangan</dt>
                            <dd class="col-sm-9">
                                <span class="badge badge-light border">{{ $activity->invitation_type == 'inbound' ? 'Surat Masuk' : 'Surat Keluar' }}</span>
                                <br>
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
                            </dd>

                            @if($activity->type == 'internal' && $activity->pic)
                            <dt class="col-sm-3">PIC</dt>
                            <dd class="col-sm-9">
                                @foreach($activity->pic as $pic)
                                    @if($pic == 'Komjakum') <span class="badge badge-danger">{{ $pic }}</span>
                                    @elseif($pic == 'PME') <span class="badge badge-success">{{ $pic }}</span>
                                    @else <span class="badge badge-secondary">{{ $pic }}</span>
                                    @endif
                                @endforeach
                            </dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div> <!-- /.col -->

            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title">Keterangan & Lampiran</strong>
                    </div>
                    <div class="card-body">
                        <p><strong>Dresscode:</strong></p>
                        <p>{{ $activity->dresscode ?? '-' }}</p>

                        <p><strong>Tujuan Disposisi:</strong></p>
                        @if($activity->disposition_to)
                            <ul class="pl-3">
                                @foreach($activity->disposition_to as $dewan)
                                    <li>{{ $dewan }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">-</p>
                        @endif

                        <p><strong>Keterangan Dispo:</strong></p>
                        <div class="border p-3 rounded bg-light mb-3">
                            {!! $activity->dispo_note ?? '-' !!}
                        </div>

                        <p><strong>Lampiran:</strong></p>
                        @if($activity->attachment_path)
                            <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank" class="btn btn-block btn-info"><i class="fe fe-file"></i> Lihat PDF</a>
                        @else
                            <p class="text-muted">Tidak ada lampiran.</p>
                        @endif
                    </div>
                </div>
            </div> <!-- /.col -->
        </div> <!-- .row -->
    </div> <!-- .col-12 -->
</div> <!-- .row -->
@endsection

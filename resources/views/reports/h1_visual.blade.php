@extends('layouts.app')

@section('title', 'Visualisasi H-1')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="page-title">Visualisasi Agenda Kegiatan</h2>
        <p class="text-muted">Tampilan visual agenda kegiatan untuk H-1.</p>
        
        {{-- Filter Date --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('report.h1-visual') }}" method="GET" class="form-inline">
                    <label class="my-1 mr-2" for="date">Tanggal Kegiatan</label>
                    <input type="date" class="form-control mr-sm-2" id="date" name="date" value="{{ $dateStr }}">
                    <button type="submit" class="btn btn-primary my-1">Tampilkan</button>
                    <a href="{{ route('report.h1') }}" class="btn btn-secondary ml-2 my-1">Kembali ke Generator Teks</a>
                </form>
            </div>
        </div>

        @if($activities->count() > 0)
            <div class="row">
                @foreach($activities as $activity)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header {{ $activity->type === 'external' ? 'bg-warning text-dark' : 'bg-primary text-white' }} d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 {{ $activity->type === 'external' ? 'text-dark' : 'text-white' }}" style="font-size: 1rem;">
                                    {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }}
                                </h5>
                                <span class="badge {{ $activity->type === 'external' ? 'badge-dark text-white' : 'badge-light text-primary' }}">
                                    {{ $activity->type === 'external' ? 'Eksternal' : 'Internal' }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h6 class="font-weight-bold mb-3">{{ $activity->name }}</h6>
                                
                                <p class="mb-1">
                                    <i class="fe fe-calendar fe-12 mr-1"></i>
                                    {{ \Carbon\Carbon::parse($activity->start_date)->isoFormat('dddd, D MMMM Y') }}
                                </p>
                                
                                {{-- Location Details Block --}}
                                <div class="bg-light p-3 rounded border mb-3" style="font-size: 0.9rem;">
                                    {{-- 1. Tipe Lokasi --}}
                                    <div class="mb-2">
                                        <span class="text-muted d-block" style="font-size: 0.8rem;">Tipe Lokasi:</span>
                                        @if($activity->location_type === 'online')
                                            <span class="badge badge-success">Online</span>
                                        @elseif($activity->location_type === 'hybrid')
                                            <span class="badge badge-info">Hybrid (Offline & Online)</span>
                                        @else
                                            <span class="badge badge-secondary">Offline</span>
                                        @endif
                                    </div>

                                    {{-- 2. Lokasi Kegiatan (Offline/Hybrid) --}}
                                    @if($activity->location_type !== 'online')
                                        <div class="mb-2">
                                            <span class="text-muted d-block" style="font-size: 0.8rem;">Lokasi Kegiatan:</span>
                                            <strong class="text-dark"><i class="fe fe-map-pin mr-1 text-danger"></i> {{ $activity->location ?? '-' }}</strong>
                                        </div>
                                    @endif

                                    {{-- 3. Media (Online/Hybrid) --}}
                                    @if($activity->location_type === 'online' || $activity->location_type === 'hybrid')
                                        <div class="mb-2">
                                            <span class="text-muted d-block" style="font-size: 0.8rem;">Media:</span>
                                            <strong class="text-primary"><i class="fe fe-video mr-1"></i> {{ $activity->media_online ?? 'Online' }}</strong>
                                        </div>

                                        {{-- 4. Link (Online/Hybrid) --}}
                                        @if($activity->meeting_link)
                                            <div class="mb-2">
                                                <span class="text-muted d-block" style="font-size: 0.8rem;">Link Meeting:</span>
                                                <a href="{{ $activity->meeting_link }}" target="_blank" class="text-break"><i class="fe fe-link mr-1"></i> Klik untuk Bergabung</a>
                                            </div>
                                        @endif

                                        {{-- 5. ID & Passcode (Online/Hybrid) --}}
                                        @if($activity->meeting_id || $activity->passcode)
                                            <div class="mb-2">
                                                <div style="font-family: monospace; font-size: 0.9rem;" class="bg-white p-2 border rounded text-dark">
                                                    @if($activity->meeting_id)
                                                        <div>Meeting ID: <strong>{{ $activity->meeting_id }}</strong></div>
                                                    @endif
                                                    @if($activity->passcode)
                                                        <div>Passcode: <strong>{{ $activity->passcode }}</strong></div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                @php
                                    $dispositions = $activity->disposition_to ?? [];
                                    $pics = $activity->pic ?? [];
                                    
                                    // Deduplicate
                                    $dispositions = array_unique($dispositions);
                                    $pics = array_unique($pics);
                                @endphp

                                <hr>
                                <small class="text-muted">PIC / Disposisi:</small>
                                <div class="d-flex flex-wrap mt-2">
                                    {{-- Display Dispositions --}}
                                    @foreach($dispositions as $dispo)
                                        <span class="badge badge-pill badge-light border mr-2 mb-2 p-2" style="font-size: 0.85rem;">
                                            <i class="fe fe-user mr-1"></i> {{ $dispo }}
                                        </span>
                                    @endforeach

                                    {{-- Display PICs --}}
                                    @foreach($pics as $pic)
                                        @if($activity->type === 'external')
                                            {{-- External PIC --}}
                                            <span class="badge badge-pill badge-warning text-dark border mr-2 mb-2 p-2" style="font-size: 0.85rem;">
                                                <i class="fe fe-globe mr-1"></i> {{ $pic }} (Eksternal)
                                            </span>
                                        @else
                                            {{-- Internal PIC --}}
                                            <span class="badge badge-pill badge-info text-white border mr-2 mb-2 p-2" style="font-size: 0.85rem;">
                                                <i class="fe fe-users mr-1"></i> {{ $pic }}
                                            </span>
                                        @endif
                                    @endforeach

                                    @if(empty($dispositions) && empty($pics))
                                        <span class="text-muted small font-italic">-</span>
                                    @endif
                                </div>

                                {{-- Action Buttons (Optional) --}}
                                <div class="mt-3">
                                    @if($activity->meeting_link)
                                        <a href="{{ $activity->meeting_link }}" target="_blank" class="btn btn-sm btn-outline-primary">Link Meeting</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info text-center" role="alert">
                <span class="fe fe-info fe-16 mr-2"></span> Tidak ada kegiatan terjadwal untuk tanggal ini.
            </div>
        @endif
    </div>
</div>
@endsection

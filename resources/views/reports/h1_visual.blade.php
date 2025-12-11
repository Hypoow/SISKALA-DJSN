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
                                
                                <p class="mb-1">
                                    @if($activity->location_type === 'online')
                                        <i class="fe fe-video fe-12 mr-1"></i> Online (Zoom/Meet)
                                    @elseif($activity->location_type === 'hybrid')
                                        <i class="fe fe-map-pin fe-12 mr-1"></i> {{ $activity->location }} <span class="badge badge-info ml-1">Hybrid</span>
                                    @else
                                        <i class="fe fe-map-pin fe-12 mr-1"></i> {{ $activity->location }}
                                    @endif
                                </p>

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

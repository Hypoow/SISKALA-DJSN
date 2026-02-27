@php
    // Pre-fetch Users for Role Checking to avoid N+1 in loop
    $allDispos = [];
    foreach($activities as $act) {
        if(!empty($act->disposition_to) && is_array($act->disposition_to)) {
             $allDispos = array_merge($allDispos, $act->disposition_to);
        }
    }
    $allDispos = array_unique($allDispos);
    $usersMap = \App\Models\User::whereIn('name', $allDispos)->get()->keyBy('name');
@endphp

@extends('layouts.app')

@section('title', 'Visualisasi H-1')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header Container -->
            <div class="card shadow-sm border-0 mb-4 rounded-lg overflow-hidden">
                <div class="card-body p-4 bg-white d-md-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="h3 font-weight-bold mb-1 text-dark">Visualisasi Agenda</h2>
                        <p class="text-muted mb-0">Jadwal kegiatan untuk H-1</p>
                    </div>
                    
                    <div class="mt-3 mt-md-0">
                        <form action="{{ route('report.h1-visual') }}" method="GET" class="form-inline bg-light p-2 rounded border">
                            <label class="my-1 mr-2 font-weight-bold text-secondary text-uppercase small" for="date">Tanggal</label>
                            <input type="date" class="form-control form-control-sm border-0 bg-white shadow-sm mr-2" id="date" name="date" value="{{ $dateStr }}">
                            <button type="submit" class="btn btn-sm btn-primary shadow-sm px-3">
                                <i class="fe fe-filter mr-1"></i> Tampilkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($activities->count() > 0)
                <div class="row">
                    @foreach($activities as $activity)
                        @php
                            $isGrayedOut = false;
                            if (auth()->check() && in_array(auth()->user()->role, ['Dewan', 'DJSN'])) {
                                $userDispos = $activity->disposition_to ?? [];
                                if (!in_array(auth()->user()->name, $userDispos)) {
                                    $isGrayedOut = true;
                                }
                            }
                            
                            // Logic setup
                            $dispositions = $activity->disposition_to ?? [];
                            $pics = $activity->pic ?? [];
                            $dispositions = array_unique($dispositions);
                            $pics = array_unique($pics);
                            
                            // Separate Dewan vs Sekretariat
                            $dewanMembers = [];
                            $hasSekretariat = false;
                            
                            foreach($dispositions as $name) {
                                $userObj = $usersMap->get($name);
                                if ($userObj && $userObj->role === 'Dewan') {
                                    $dewanMembers[] = $name;
                                } else {
                                    $hasSekretariat = true;
                                }
                            }
                            
                            // Check if "Sekretariat DJSN" is explicitly in PICs for deduplication
                            $sekretariatPicIndex = array_search('Sekretariat DJSN', $pics);
                            if ($sekretariatPicIndex !== false) {
                                $hasSekretariat = true; 
                                unset($pics[$sekretariatPicIndex]); 
                            }
                        @endphp
                        
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm rounded-lg activity-card {{ $isGrayedOut ? 'check-attendance' : '' }}" style="{{ $isGrayedOut ? 'opacity: 0.75;' : '' }}">
                                <!-- Status Stripe -->
                                <div class="card-status-left {{ $activity->type === 'external' ? 'bg-info' : '' }}" style="{{ $activity->type !== 'external' ? 'background-color: #004085;' : '' }}"></div>
                                
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box {{ $activity->type === 'external' ? 'bg-info-light text-info' : 'bg-primary-light text-primary' }} rounded-circle mr-3 d-flex align-items-center justify-content-center">
                                                <i class="fe {{ $activity->type === 'external' ? 'fe-globe' : 'fe-check-circle' }}" style="{{ $activity->type !== 'external' ? 'color: #004085 !important;' : '' }}"></i>
                                            </div>
                                            <div>
                                                <h2 class="mb-0 font-weight-bold {{ $activity->type === 'external' ? 'text-info' : '' }}" style="{{ $activity->type !== 'external' ? 'color: #004085;' : '' }}">{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }}</h2>
                                                <small class="text-uppercase font-weight-bold text-muted" style="font-size: 0.7rem;">
                                                    {{ $activity->type === 'external' ? 'Eksternal' : 'Internal' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h5 class="font-weight-bold mb-1" style="line-height: 1.4;">
                                        <a href="{{ route('activities.show', $activity->id) }}" class="text-dark text-decoration-none activity-title-link">
                                            {{ $activity->name }}
                                        </a>
                                    </h5>
                                    <div class="mb-3 text-secondary text-sm">
                                        <i class="fe fe-calendar mr-1"></i> {{ \Carbon\Carbon::parse($activity->start_date)->isoFormat('dddd, D MMMM Y') }}
                                    </div>
                                    
                                    <!-- Info Box -->
                                    <div class="bg-light rounded p-3 mb-3 border border-light info-box">
                                        <!-- Location Type -->
                                        <div class="mb-2">
                                            <span class="badge text-white px-2 py-1" style="background-color: #6c757d;">{{ ucfirst($activity->location_type) }}</span>
                                        </div>

                                        <!-- Offline Location -->
                                        @if($activity->location_type === 'offline')
                                            <div class="d-flex align-items-start mb-2">
                                                <i class="fe fe-map-pin mt-1 mr-2" style="color: #EF4444;"></i>
                                                <span class="text-dark small font-weight-medium">{{ $activity->location ?? '-' }}</span>
                                            </div>
                                        @endif

                                        <!-- Hybrid Display -->
                                        @if($activity->location_type === 'hybrid')
                                            <!-- Offline Part -->
                                            <div class="d-flex align-items-start mb-2">
                                                <i class="fe fe-map-pin mt-1 mr-2" style="color: #EF4444;"></i>
                                                <span class="text-dark small font-weight-medium">{{ $activity->location ?? '-' }}</span>
                                            </div>
                                            <!-- Online Part -->
                                            <div class="d-flex align-items-start mb-2 pt-2 border-top">
                                                <i class="fe fe-video mt-1 mr-2 text-primary"></i>
                                                <span class="text-dark small font-weight-medium">{{ $activity->media_online ?? 'Online' }}</span>
                                            </div>
                                            @if($activity->meeting_link)
                                                <div class="pl-4 mb-2">
                                                    <a href="{{ $activity->meeting_link }}" target="_blank" class="small font-weight-bold text-primary text-break text-decoration-none">
                                                        {{ $activity->meeting_link }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if($activity->meeting_id || $activity->passcode)
                                                <div class="pl-4">
                                                    @if($activity->meeting_id)
                                                        <div class="small text-muted mb-1">ID: <span class="text-dark font-family-monospace">{{ $activity->meeting_id }}</span></div>
                                                    @endif
                                                    @if($activity->passcode)
                                                        <div class="small text-muted">Pass: <span class="text-dark font-family-monospace">{{ $activity->passcode }}</span></div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif

                                        <!-- Online Details (Pure Online) -->
                                        @if($activity->location_type === 'online')
                                            <div class="d-flex align-items-start mb-2">
                                                <i class="fe fe-video mt-1 mr-2 text-primary"></i>
                                                <span class="text-dark small font-weight-medium">{{ $activity->media_online ?? 'Online' }}</span>
                                            </div>
                                            
                                            @if($activity->meeting_link)
                                                <div class="d-flex align-items-start mb-2">
                                                    <i class="fe fe-link mt-1 mr-2 text-muted"></i>
                                                    <a href="{{ $activity->meeting_link }}" target="_blank" class="small font-weight-bold text-primary text-break text-decoration-none">
                                                        Bergabung ke Meeting
                                                    </a>
                                                </div>
                                            @endif
                                            
                                            @if($activity->meeting_id || $activity->passcode)
                                                <div class="mt-2 pt-2 border-top">
                                                    @if($activity->meeting_id)
                                                        <div class="small text-muted mb-1">ID: <span class="text-dark font-family-monospace">{{ $activity->meeting_id }}</span></div>
                                                    @endif
                                                    @if($activity->passcode)
                                                        <div class="small text-muted">Pass: <span class="text-dark font-family-monospace">{{ $activity->passcode }}</span></div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Dispositions / PICs -->
                                    <div class="mt-3">
                                        {{-- 1. DEWAN GROUPING --}}
                                        @php
                                            $councilStructure = \App\Models\Activity::COUNCIL_STRUCTURE;
                                            
                                            // Helper to get color class
                                            $getGroupClass = function($group) {
                                                if($group == 'Ketua DJSN') return 'text-purple bg-purple-light';
                                                if($group == 'Komisi PME') return 'text-success bg-success-light';
                                                if($group == 'Komjakum') return 'text-primary bg-primary-light';
                                                return 'text-secondary bg-secondary-light';
                                            };

                                            $getBadgeClass = function($group) {
                                                if($group == 'Ketua DJSN') return 'badge-ketua';
                                                if($group == 'Komisi PME') return 'badge-pme';
                                                if($group == 'Komjakum') return 'badge-komjakum';
                                                return 'badge-secondary';
                                            };
                                        @endphp

                                        <div class="row">
                                            @foreach($councilStructure as $groupName => $members)
                                                @php
                                                    // Filter members present in this activity's disposition
                                                    $presentMembers = array_intersect($dewanMembers, $members);
                                                @endphp
                                                
                                                @if(!empty($presentMembers))
                                                <div class="col-12 mb-3">
                                                    <h6 class="font-weight-bold mb-2 d-flex align-items-center {{ $getGroupClass($groupName) }} p-2 rounded">
                                                        <span class="badge {{ $getBadgeClass($groupName) }} mr-2 p-1" style="height: 8px; width: 8px; border-radius: 50%;"> </span>
                                                        {{ $groupName }}
                                                    </h6>
                                                    <ol class="pl-4 mb-0 text-sm text-dark font-weight-medium">
                                                        @foreach($presentMembers as $pm)
                                                            <li class="mb-1">{{ $pm }}</li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                                @endif
                                            @endforeach

                                            {{-- 2. COMMISSIONS / PICS --}}
                                            @if(!empty($pics) || $hasSekretariat)
                                                <div class="col-12 mb-2">
                                                    @if(!empty($pics))
                                                    <h6 class="font-weight-bold text-muted small text-uppercase mb-2">PIC Lainnya</h6>
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($pics as $pic)
                                                            @if($activity->type === 'external')
                                                                <span class="badge badge-pill badge-outline-info text-dark border-info mb-2 mr-2 px-3 py-2">
                                                                    {{ $pic }}
                                                                </span>
                                                            @else
                                                                 @php
                                                                    $picClass = 'badge-secondary';
                                                                    $picUpper = strtoupper($pic);
                                                                    if (str_contains($picUpper, 'KOMJAKUM')) $picClass = 'badge-komjakum';
                                                                    elseif (str_contains($picUpper, 'PME')) $picClass = 'badge-pme';
                                                                    elseif ($pic == 'Sekretariat DJSN') $picClass = 'badge-sekretariat';
                                                                    elseif ($pic == 'Ketua DJSN') $picClass = 'badge-ketua';
                                                                 @endphp
                                                                 <span class="badge badge-pill {{ $picClass }} mb-2 mr-2 px-3 py-2">
                                                                    {{ $pic }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    @endif

                                                    {{-- 3. SEKRETARIAT GROUP --}}
                                                    @if($hasSekretariat)
                                                        <div class="mt-2">
                                                            <span class="badge badge-pill badge-sekretariat text-white px-3 py-2 shadow-sm d-inline-flex align-items-center">
                                                                <i class="fe fe-users mr-2"></i> Sekretariat DJSN
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        @if(empty($dewanMembers) && !$hasSekretariat && empty($pics))
                                            <div class="text-center py-3 bg-light rounded text-muted small font-italic">
                                                Tidak ada data PIC / Disposisi
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="fe fe-calendar text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                        <h4 class="text-muted font-weight-bold">Tidak Ada Jadwal</h4>
                        <p class="text-muted mb-0">Belum ada kegiatan yang terjadwal untuk tanggal ini.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card-status-left {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        border-top-left-radius: 0.5rem;
        border-bottom-left-radius: 0.5rem;
    }
    .icon-box {
        width: 45px;
        height: 45px;
        font-size: 1.25rem;
    }
    .bg-primary-light { background-color: rgba(44, 123, 229, 0.1); }
    .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
    .bg-purple-light { background-color: rgba(139, 92, 246, 0.1); }
    .bg-secondary-light { background-color: rgba(108, 117, 125, 0.1); }
    
    .text-primary { color: #2c7be5 !important; }
    .text-success { color: #28a745 !important; }
    .text-purple { color: #8B5CF6 !important; }
    
    .bg-info-light { background-color: rgba(23, 162, 184, 0.15); }

    .bg-warning-light { background-color: rgba(246, 195, 67, 0.15); }
    .text-warning-dark { color: #d39e00; }
    
    .activity-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease;
    }
    .activity-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
    .badge-outline-info {
        background: transparent;
        border: 1px solid #17a2b8;
        color: #17a2b8 !important;
    }
    .text-xxs {
        font-size: 0.65rem;
        letter-spacing: 0.5px;
    }
    .user-badge {
        font-size: 0.8rem;
    }
    .font-family-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    
    /* Gray out effect */
    .check-attendance {
        filter: grayscale(100%);
        transform: scale(0.98);
    }
    
    .activity-title-link:hover {
        color: #2c7be5 !important;
        text-decoration: underline !important;
        transition: color 0.2s ease;
    }
</style>
@endsection

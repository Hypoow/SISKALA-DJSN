@php
    // Pre-fetch Users for Role Checking to avoid N+1 in loop
    $allDispos = [];
    foreach($activities as $act) {
        if(!empty($act->disposition_to) && is_array($act->disposition_to)) {
             $allDispos = array_merge($allDispos, $act->disposition_to);
        }
    }
    $allDispos = array_unique($allDispos);
    $usersMap = \App\Models\User::with('division')->whereIn('name', $allDispos)->get()->keyBy('name');
@endphp

@extends('layouts.app')

@section('title', 'Visualisasi Mutasi Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header Container -->
            <div class="mb-4">
                <h2 class="h3 font-weight-bold mb-1 text-dark">Visualisasi Mutasi Kegiatan</h2>
                <p class="text-muted mb-3">Jadwal kegiatan berdasarkan rentang mutasi waktu</p>
                
                <div class="d-inline-block mt-2">
                    <div class="bg-white rounded-pill shadow-sm border border-light p-2 d-flex align-items-center">
                        <form action="{{ route('report.h1-visual') }}" method="GET" class="d-flex align-items-center m-0">
                            <!-- Start Date -->
                            <div class="d-flex align-items-center px-3 border-right">
                                <i class="fe fe-calendar text-primary mr-2" style="font-size: 1.1rem;"></i>
                                <div class="d-flex flex-column">
                                    <label class="text-uppercase text-muted mb-0" style="font-size:0.65rem; font-weight:700; letter-spacing:0.5px;" for="start_date">Mulai</label>
                                    <input type="date" class="form-control form-control-sm border-0 p-0 text-dark font-weight-bold" id="start_date" name="start_date" value="{{ $startDateStr }}" style="box-shadow:none; background:transparent;">
                                </div>
                            </div>
                            <!-- End Date -->
                            <div class="d-flex align-items-center px-3 border-right">
                                <div class="d-flex flex-column">
                                    <label class="text-uppercase text-muted mb-0" style="font-size:0.65rem; font-weight:700; letter-spacing:0.5px;" for="end_date">Selesai</label>
                                    <input type="date" class="form-control form-control-sm border-0 p-0 text-dark font-weight-bold" id="end_date" name="end_date" value="{{ $endDateStr }}" style="box-shadow:none; background:transparent;">
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="px-2 pl-3">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 py-2 font-weight-bold shadow-sm" style="transition: all 0.2s;">
                                    <i class="fe fe-search mr-1"></i> Tampilkan Visualisasi
                                </button>
                            </div>
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

                            $dispositions = array_values(array_unique($dispositions));
                            $pics = array_values(array_unique($pics));
                            $visualizationGroups = collect();

                            if ($activity->type !== 'external') {
                                $visualizationGroups = \App\Models\Activity::buildVisualizationGroupsFromDisposition($dispositions, $usersMap);
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
                                        @php
                                            $sekretariatGroup = $visualizationGroups->firstWhere('type', 'sekretariat');
                                            $dewanVisualizationGroups = $visualizationGroups->reject(function ($group) {
                                                return ($group['type'] ?? null) === 'sekretariat';
                                            })->values();

                                            $getGroupHeaderClass = function($group) {
                                                $type = $group['type'] ?? null;
                                                $label = strtoupper($group['label'] ?? '');

                                                if ($type === 'ketua') return 'visualization-group-header visualization-group-header--ketua';
                                                if ($type === 'sekretariat') return 'visualization-group-header visualization-group-header--sekretariat';
                                                if (str_contains($label, 'PME')) return 'visualization-group-header visualization-group-header--pme';
                                                if (str_contains($label, 'KOMJAKUM') || str_contains($label, 'KEBIJAKAN')) return 'visualization-group-header visualization-group-header--komjakum';

                                                return 'visualization-group-header visualization-group-header--komisi';
                                            };

                                            $getGroupDotClass = function($group) {
                                                $type = $group['type'] ?? null;
                                                $label = strtoupper($group['label'] ?? '');

                                                if ($type === 'ketua') return 'badge-ketua';
                                                if ($type === 'sekretariat') return 'badge-sekretariat';
                                                if (str_contains($label, 'PME')) return 'badge-pme';
                                                if (str_contains($label, 'KOMJAKUM') || str_contains($label, 'KEBIJAKAN')) return 'badge-komjakum';

                                                return 'badge-primary';
                                            };
                                        @endphp

                                        @if($activity->type === 'external')
                                            @if(!empty($pics))
                                                <div>
                                                    <h6 class="font-weight-bold text-muted small text-uppercase mb-2">PIC</h6>
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($pics as $pic)
                                                            <span class="badge badge-pill badge-outline-info text-dark border-info mb-2 mr-2 px-3 py-2">
                                                                {{ $pic }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @elseif($dewanVisualizationGroups->isNotEmpty() || $sekretariatGroup)
                                            @foreach($dewanVisualizationGroups as $group)
                                                <div class="visualization-group">
                                                    <div class="{{ $getGroupHeaderClass($group) }}">
                                                        <span class="badge {{ $getGroupDotClass($group) }} visualization-group-dot"></span>
                                                        <span>{{ $group['label'] }}</span>
                                                    </div>
                                                    <ol class="visualization-group-list">
                                                        @foreach($group['members'] as $member)
                                                            <li>{{ $member }}</li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                            @endforeach

                                            @if($sekretariatGroup)
                                                <div class="mt-2">
                                                    <span class="badge badge-pill badge-sekretariat text-white px-3 py-2 shadow-sm d-inline-flex align-items-center">
                                                        <i class="fe fe-users mr-2"></i> {{ $sekretariatGroup['label'] }}
                                                    </span>
                                                </div>
                                            @endif
                                        @elseif(!empty($pics))
                                            <div>
                                                <h6 class="font-weight-bold text-muted small text-uppercase mb-2">PIC Tercatat</h6>
                                                <div class="d-flex flex-wrap">
                                                    @foreach($pics as $pic)
                                                        @php
                                                            $picClass = 'badge-secondary';
                                                            $picUpper = strtoupper($pic);
                                                            if (str_contains($picUpper, 'KOMJAKUM') || str_contains($picUpper, 'KEBIJAKAN')) $picClass = 'badge-komjakum';
                                                            elseif (str_contains($picUpper, 'PME')) $picClass = 'badge-pme';
                                                            elseif ($pic == 'Sekretaris DJSN' || $pic == 'Sekretariat DJSN') $picClass = 'badge-sekretariat';
                                                            elseif ($pic == 'Ketua DJSN') $picClass = 'badge-ketua';
                                                        @endphp
                                                        <span class="badge badge-pill {{ $picClass }} mb-2 mr-2 px-3 py-2">
                                                            {{ $pic }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
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
    .visualization-group {
        margin-bottom: 1rem;
    }
    .visualization-group:last-child {
        margin-bottom: 0;
    }
    .visualization-group-header {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.75rem 0.9rem;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.4;
    }
    .visualization-group-header--ketua {
        background-color: rgba(139, 92, 246, 0.12);
        color: #7c3aed;
    }
    .visualization-group-header--komisi {
        background-color: rgba(44, 123, 229, 0.1);
        color: #2c7be5;
    }
    .visualization-group-header--komjakum {
        background-color: rgba(0, 123, 255, 0.1);
        color: #007bff;
    }
    .visualization-group-header--pme {
        background-color: rgba(40, 167, 69, 0.12);
        color: #28a745;
    }
    .visualization-group-header--sekretariat {
        background-color: rgba(249, 115, 22, 0.12);
        color: #ea580c;
    }
    .visualization-group-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        min-width: 10px;
        padding: 0;
        border-radius: 999px;
    }
    .visualization-group-list {
        margin: 0.75rem 0 0 1.5rem;
        padding-left: 1rem;
        color: #212529;
        font-size: 0.95rem;
        font-weight: 500;
    }
    .visualization-group-list li {
        margin-bottom: 0.35rem;
    }
    .visualization-group-list li:last-child {
        margin-bottom: 0;
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

@extends('layouts.app')

@section('title', 'Detail Kegiatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-11">
        
        <!-- Sticky Action Header (Minimalist) -->
        <div class="row align-items-center mb-4 p-2 border-bottom sticky-action-header bg-white shadow-sm" style="position: sticky; top: 70px; z-index: 99; transition: top 0.3s;">
            <div class="col-6 col-md">
                <h2 class="font-weight-bold mb-0 text-dark" style="font-size: clamp(1rem, 2vw, 1.25rem);">
                    Detail Kegiatan
                </h2>
            </div>
            <div class="col-6 col-md-auto text-right">
                <div class="d-flex justify-content-end">
                    @php
                        $backUrl = url()->previous();
                        if ($backUrl == url()->current() || empty($backUrl)) {
                            $backUrl = route('activities.index');
                        }
                    @endphp
                    <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 mr-2">
                        <i class="fe fe-arrow-left mr-1"></i> <span class="d-none d-sm-inline">Kembali</span>
                    </a>
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                            <i class="fe fe-edit mr-1"></i> <span class="d-none d-sm-inline">Edit</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Main Info -->
            <div class="col-md-7">
                
                <!-- Name Card -->
                 <div class="card mb-4" style="border-left: 5px solid {{ $activity->type == 'external' ? '#17a2b8' : '#004085' }};">
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label class="form-label-premium">Nama Kegiatan</label>
                            <p class="h5 font-weight-bold text-dark mb-0">{{ $activity->name }}</p>
                        </div>
                    </div>
                 </div>

                <!-- Informasi Utama Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <div class="icon-shape bg-light text-primary rounded-circle mr-3">
                            <i class="fe fe-info"></i>
                        </div>
                        <strong class="card-title">Informasi Utama</strong>
                    </div>
                    <div class="card-body">
                        <!-- Nomor Surat -->

                        <!-- Status & Update Info (Moved from Header) -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted font-weight-bold">Status</label>
                            <div class="col-sm-9 d-flex align-items-center flex-wrap">
                                @if($activity->type == 'external')
                                    <span class="badge badge-pill badge-info px-3 py-2 mr-2 mb-2 text-white">Kegiatan Eksternal</span>
                                @else
                                    <span class="badge badge-pill badge-primary px-3 py-2 mr-2 mb-2" style="background-color: #004085;">Kegiatan Internal</span>
                                @endif
                                
                                @switch($activity->status)
                                    @case(0) <span class="badge badge-pill badge-success mr-2 mb-2">On Schedule</span> @break
                                    @case(1) <span class="badge badge-pill badge-secondary mr-2 mb-2">Reschedule</span> @break
                                    @case(2) <span class="badge badge-pill badge-warning mr-2 mb-2">Belum ada Disposisi</span> @break
                                    @case(3) <span class="badge badge-pill badge-danger mr-2 mb-2">Tidak Dilaksanakan</span> @break
                                @endswitch

                                <span class="text-muted small mb-2 d-inline-block">
                                    <i class="fe fe-clock mr-1"></i>Updated {{ $activity->updated_at->diffForHumans() }}
                                    @if($activity->lastEditor)
                                         by <strong>{{ $activity->lastEditor->name }}</strong>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Nomor Surat -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted font-weight-bold">Nomor Surat</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext text-dark font-weight-bold">{{ $activity->letter_number ?? '-' }}</p>
                            </div>
                        </div>

                        @if($activity->type == 'external')
                        <!-- Organizer Name -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted font-weight-bold">Penyelenggara</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext text-dark">{{ $activity->organizer_name ?? '-' }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Waktu -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted font-weight-bold">Waktu</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext text-dark font-weight-bold mb-0">
                                    {{ $activity->start_date->isoFormat('D MMMM Y') }}
                                </p>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} WIB - 
                                    @if($activity->end_time)
                                        {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }} WIB
                                    @else
                                        Selesai
                                    @endif
                                    @if($activity->end_date && $activity->end_date != $activity->start_date)
                                        (s.d {{ $activity->end_date->isoFormat('D MMMM Y') }})
                                    @endif
                                </small>
                            </div>
                        </div>
                        
                        <!-- Lokasi -->
                         <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted font-weight-bold">Lokasi</label>
                            <div class="col-sm-9">
                                @if($activity->location_type == 'offline')
                                    <span class="badge mb-1 badge-secondary">Offline</span>
                                    <p class="form-control-plaintext text-dark">
                                        <i class="fe fe-map-pin mr-1" style="color: #6c757d;"></i>{{ $activity->location }}
                                    </p>
                                @elseif($activity->location_type == 'online')
                                    <span class="badge mb-1 badge-secondary">Online ({{ $activity->media_online }})</span>
                                    @if($activity->meeting_link)
                                        @if(filter_var($activity->meeting_link, FILTER_VALIDATE_URL))
                                            <p class="mb-0 mt-1"><a href="{{ $activity->meeting_link }}" target="_blank" class="text-truncate d-block">{{ $activity->meeting_link }}</a></p>
                                        @else
                                            <p class="mb-0 mt-1">{{ $activity->meeting_link }}</p>
                                        @endif
                                    @else
                                        <p class="mb-0 mt-1">-</p>
                                    @endif
                                    @if($activity->meeting_id)
                                        <div class="mt-1 small">
                                            <strong>ID:</strong> {{ $activity->meeting_id }} <br>
                                            <strong>Pass:</strong> {{ $activity->passcode ?? '-' }}
                                        </div>
                                    @endif
                                @else
                                    <span class="badge mb-1 badge-secondary">Hybrid</span>
                                    <div class="mb-2">
                                        <small class="text-muted font-weight-bold d-block">Offline:</small>
                                        <p class="form-control-plaintext text-dark mb-0 pl-2 border-left">
                                            <i class="fe fe-map-pin mr-1" style="color: #6c757d;"></i>{{ $activity->location }}
                                        </p>
                                    </div>
                                    <div class="mb-0">
                                        <small class="text-muted font-weight-bold d-block">Online ({{ $activity->media_online }}):</small>
                                        <div class="pl-2 border-left">
                                            @if($activity->meeting_link)
                                                @if(filter_var($activity->meeting_link, FILTER_VALIDATE_URL))
                                                    <p class="mb-1"><a href="{{ $activity->meeting_link }}" target="_blank" class="text-truncate d-block">{{ $activity->meeting_link }}</a></p>
                                                @else
                                                    <p class="mb-1">{{ $activity->meeting_link }}</p>
                                                @endif
                                            @endif
                                            
                                            @if($activity->meeting_id)
                                                <div class="small text-muted">
                                                    <strong>ID:</strong> {{ $activity->meeting_id }} <br>
                                                    <strong>Pass:</strong> {{ $activity->passcode ?? '-' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status Undangan -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted font-weight-bold">Status Undangan</label>
                            <div class="col-sm-9">
                                <span class="badge badge-pill badge-light border mb-1">{{ $activity->invitation_type == 'inbound' ? 'Surat Masuk' : 'Surat Keluar' }}</span>
                                <div class="mt-1">
                                     @if($activity->type == 'external')
                                        @switch($activity->invitation_status)
                                            @case(0) <span class="badge badge-success">Sudah ada Disposisi</span> @break
                                            @case(1) <span class="badge badge-warning">Proses Disposisi</span> @break
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
                                </div>
                            </div>
                        </div>

                        <!-- PIC -->
                        @if($activity->pic)
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted font-weight-bold">PIC</label>
                            <div class="col-sm-9">
                                 @if($activity->type == 'external')
                                    <p class="form-control-plaintext text-dark">{{ is_array($activity->pic) ? implode(', ', $activity->pic) : $activity->pic }}</p>
                                @else
                                    @foreach($activity->pic as $pic)
                                        @php
                                            $badgeClass = 'badge-info';
                                            $picUpper = strtoupper($pic);
                                            
                                            if (str_contains($picUpper, 'KOMJAKUM')) {
                                                $badgeClass = 'badge-komjakum';
                                            } elseif (str_contains($picUpper, 'PME')) {
                                                $badgeClass = 'badge-pme';
                                            } elseif ($pic == 'Sekretariat DJSN') {
                                                $badgeClass = 'badge-sekretariat';
                                            } elseif ($pic == 'Ketua DJSN') {
                                                $badgeClass = 'badge-ketua';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} mr-1 mb-1">{{ $pic }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                <!-- Hasil Rapat & Follow Up Wrapper to maintain single column flow if needed, or keep existing Follow Up Manager -->
                <div class="card mb-4">
                     <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-light text-primary rounded-circle mr-3">
                                <i class="fe fe-file-text"></i>
                            </div>
                            <strong class="card-title mb-0">Hasil Rapat Secara Singkat</strong>
                        </div>
                        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->role === 'Sekretariat DJSN'))
                            @if(!empty($activity->summary_content) && trim(strip_tags($activity->summary_content)) != '')
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-toggle="modal" data-target="#summaryModal">
                                    <span class="fe fe-edit"></span> Edit
                                </button>
                            @endif
                        @endif
                    </div>
                    <div class="card-body">
                        @if(!empty($activity->summary_content) && trim(strip_tags($activity->summary_content)) != '')
                            <div class="markdown-content text-justify">{!! $activity->summary_content !!}</div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-3">Belum ada hasil rapat singkat.</p>
                                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->role === 'Sekretariat DJSN'))
                                    <button type="button" class="btn btn-primary rounded-pill" data-toggle="modal" data-target="#summaryModal">
                                        <span class="fe fe-plus"></span> Tambahkan Hasil Rapat
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @livewire('follow-up-manager', ['activity' => $activity])

            </div>

             <!-- Right Column: Details & Disposition -->
            <div class="col-md-5">
                
                <!-- Dokumen Pendukung Card -->
                <div class="card mb-4">
                     <div class="card-header d-flex align-items-center">
                        <div class="icon-shape bg-light text-primary rounded-circle mr-3">
                            <i class="fe fe-folder"></i>
                        </div>
                        <strong class="card-title">Dokumen Pendukung</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <!-- 1. Surat Undangan -->
                            <div class="list-group-item p-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm bg-light text-primary rounded-circle">
                                            <i class="fe fe-mail"></i>
                                        </span>
                                    </div>
                                    <div class="col pl-0">
                                        <small class="text-muted d-block mb-0 font-weight-bold uppercase-label">Surat Undangan</small>
                                        @if($activity->attachment_path)
                                            <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank" class="font-weight-bold text-dark d-block text-truncate" style="max-width: 250px;">{{ basename($activity->attachment_path) }}</a>
                                        @else
                                            <span class="text-muted font-italic small">-</span>
                                        @endif
                                    </div>
                                    @if($activity->attachment_path)
                                        <div class="col-auto">
                                            <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle"><i class="fe fe-download"></i></a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- 2. Notulensi -->
                            <div class="list-group-item p-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm bg-light text-primary rounded-circle">
                                            <i class="fe fe-file-text"></i>
                                        </span>
                                    </div>
                                    <div class="col pl-0">
                                        <small class="text-muted d-block mb-0 font-weight-bold uppercase-label">Notulensi</small>
                                        @if($activity->minutes_path)
                                            <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="font-weight-bold text-dark d-block text-truncate" style="max-width: 250px;">{{ basename($activity->minutes_path) }}</a>
                                        @else
                                            <span class="text-muted font-italic small">-</span>
                                        @endif
                                    </div>
                                     @if($activity->minutes_path)
                                        <div class="col-auto">
                                            <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle"><i class="fe fe-download"></i></a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 3. Bahan Materi -->
                            <div class="list-group-item p-3">
                                <div class="row align-items-start">
                                    <div class="col-auto mt-1">
                                        <span class="avatar avatar-sm bg-light text-primary rounded-circle">
                                            <i class="fe fe-briefcase"></i>
                                        </span>
                                    </div>
                                    <div class="col pl-0">
                                        <small class="text-muted d-block mb-1 font-weight-bold uppercase-label">Bahan Materi</small>
                                        @if($activity->materials && $activity->materials->count() > 0)
                                            <ul class="list-unstyled mb-0">
                                                @foreach($activity->materials as $mat)
                                                    <li class="mb-2 d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                                        <a href="{{ Storage::url($mat->file_path) }}" target="_blank" class="text-dark font-weight-bold text-truncate" style="max-width: 200px;">
                                                            {{ $mat->title ?? basename($mat->file_path) }}
                                                        </a>
                                                        <a href="{{ Storage::url($mat->file_path) }}" target="_blank" class="text-primary"><i class="fe fe-download"></i></a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted font-italic small">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Surat Tugas -->
                            @if(!auth()->check() || !auth()->user()->hasRole('Dewan'))
                            <div class="list-group-item p-3">
                                 <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm bg-light text-primary rounded-circle">
                                            <i class="fe fe-check-circle"></i>
                                        </span>
                                    </div>
                                    <div class="col pl-0">
                                        <small class="text-muted d-block mb-0 font-weight-bold uppercase-label">Surat Tugas</small>
                                        @if($activity->assignment_letter_path)
                                            <a href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank" class="font-weight-bold text-dark d-block text-truncate" style="max-width: 250px;">{{ basename($activity->assignment_letter_path) }}</a>
                                        @else
                                            <span class="text-muted font-italic small">-</span>
                                        @endif
                                    </div>
                                     @if($activity->assignment_letter_path)
                                        <div class="col-auto">
                                            <a href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle"><i class="fe fe-download"></i></a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- 5. Dokumentasi Foto -->
                            <div class="list-group-item p-3">
                                 <div class="row align-items-start">
                                    <div class="col-auto mt-1">
                                        <span class="avatar avatar-sm bg-light text-primary rounded-circle">
                                            <i class="fe fe-image"></i>
                                        </span>
                                    </div>
                                    <div class="col pl-0">
                                        <small class="text-muted d-block mb-2 font-weight-bold uppercase-label">Dokumentasi Foto</small>
                                        @if($activity->documentations && $activity->documentations->count() > 0)
                                            <div class="row">
                                                @foreach($activity->documentations as $doc)
                                                    <div class="col-6 mb-3">
                                                        <div class="position-relative shadow-sm rounded overflow-hidden group-hover-parent">
                                                            <a href="#" class="d-block" data-toggle="modal" data-target="#photoModal" data-src="{{ Storage::url($doc->file_path) }}">
                                                                <div class="bg-light" style="
                                                                    background-image: url('{{ Storage::url($doc->file_path) }}'); 
                                                                    background-size: cover; 
                                                                    background-position: center; 
                                                                    padding-bottom: 75%; /* 4:3 Aspect Ratio */
                                                                "></div>
                                                            </a>
                                                            <div class="position-absolute p-2" style="top: 0; right: 0; z-index: 10;">
                                                                <a href="{{ Storage::url($doc->file_path) }}" download class="btn btn-sm bg-white text-primary shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; padding: 0;" title="Download Foto">
                                                                    <i class="fe fe-download"></i>
                                                                </a>
                                                            </div>
                                                            <div class="position-absolute bottom-0 left-0 w-100 p-1 bg-gradient-dark text-white small text-truncate" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                                                                {{ $doc->caption ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted font-italic small">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Disposition Card -->
                <div class="card mb-4">
                     <div class="card-header d-flex align-items-center">
                        <div class="icon-shape bg-light text-primary rounded-circle mr-3">
                            <i class="fe fe-check-square"></i>
                        </div>
                        <strong class="card-title">Tujuan Disposisi</strong>
                    </div>
                    <div class="card-body">
                         <!-- Narasumber Display (Moved from Main Info) -->
                         @if($activity->type == 'external' && !empty($activity->narasumber))
                            <div class="mb-4 bg-white rounded-lg shadow-sm border border-warning-subtle overflow-hidden" style="border-left: 4px solid #ffc107;">
                                <div class="px-4 py-3 bg-light-warning d-flex align-items-center border-bottom border-warning-subtle" style="background-color: #fffdf5;">
                                    <div class="icon-shape bg-warning text-white rounded-circle mr-3 shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fe fe-mic"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold mb-0 text-dark">Narasumber Kegiatan</h6>
                                        <small class="text-muted">Daftar pembicara untuk kegiatan ini</small>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="d-flex flex-wrap">
                                        @foreach($activity->narasumber as $nara)
                                            <div class="d-flex align-items-center bg-white border rounded-pill px-3 py-2 mr-3 mb-2 shadow-sm" style="border-color: #ffeeba !important;">
                                                <div class="avatar avatar-xs bg-warning text-white rounded-circle mr-2 d-flex align-items-center justify-content-center">
                                                <i class="fe fe-user" style="font-size: 12px;"></i>
                                            </div>
                                                <span class="font-weight-bold text-dark" style="color: #664d03 !important;">{{ $nara }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                         @endif

                         @if(isset($groupedDisposition) && $groupedDisposition->isNotEmpty())
                            <div class="timeline ml-3 timeline-disposition">
                            @foreach($groupedDisposition as $groupName => $users)
                                @php
                                    // $users is a Collection of User objects
                                    $sortedUsers = $users; // Already sorted in controller or by default? We rely on retrieval order.
                                    
                                    // In controller for `show`, sorting inside groups wasn't explicitly done, but retrieved by name?
                                    // Actually we just care about grouping for now.
                                    $attendanceList = $activity->attendance_list ?? [];
                                @endphp
                                <div class="pb-3 timeline-item item-primary">
                                    <div class="pl-4">
                                        <div class="mb-1 font-weight-bold text-dark">{{ $groupName }}</div>
                                        <ul class="list-unstyled mb-0 text-muted small">
                                            @foreach($users as $user)
                                                @php
                                                    $isPresent = in_array($user->name, $attendanceList);
                                                @endphp
                                                <li class="mb-1 d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        @if($isPresent)
                                                            <span class="fe fe-check text-success mr-2 font-weight-bold"></span>
                                                        @else
                                                            <span class="mr-2 text-muted font-weight-bold" style="width: 14px; text-align: center;">-</span>
                                                        @endif
                                                        <span class="{{ $isPresent ? 'text-dark font-weight-bold' : '' }}">{{ $user->name }}</span>
                                                    </div>
                                                    {{-- Representative Display (Imron Rosadi logic) --}}
                                                    @if($user->name === 'Imron Rosadi')
                                                        @php
                                                            $details = $activity->attendance_details ?? [];
                                                            $repName = $details[$user->id]['representative'] ?? null;
                                                        @endphp
                                                        @if($repName)
                                                            <div class="ml-4 pl-1 text-muted small">
                                                                <i class="fe fe-corner-down-right mr-1"></i> Diwakili: <span class="font-weight-bold text-dark">{{ $repName }}</span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @elseif($activity->disposition_to)
                             {{-- Fallback for legacy views pending controller update or cache issues --}}
                            @php
                                $councilStructure = \App\Models\Activity::COUNCIL_STRUCTURE;
                                $selected = $activity->disposition_to;
                            @endphp
                            
                            <div class="timeline ml-3 timeline-disposition">
                            @foreach($councilStructure as $groupName => $members)
                                @php
                                    $groupSelected = array_intersect($members, $selected);
                                    $attendanceList = $activity->attendance_list ?? [];
                                @endphp
                                @if(!empty($groupSelected))
                                    <div class="pb-3 timeline-item item-primary">
                                        <div class="pl-4">
                                            <div class="mb-1 font-weight-bold text-dark">{{ $groupName }}</div>
                                            <ul class="list-unstyled mb-0 text-muted small">
                                                @foreach($groupSelected as $member)
                                                    @php
                                                        $isPresent = in_array($member, $attendanceList);
                                                    @endphp
                                                    <li class="mb-1 d-flex flex-column">
                                                        <div class="d-flex align-items-center">
                                                            @if($isPresent)
                                                                <span class="fe fe-check text-success mr-2 font-weight-bold"></span>
                                                            @else
                                                                <span class="mr-2 text-muted font-weight-bold" style="width: 14px; text-align: center;">-</span>
                                                            @endif
                                                            <span class="{{ $isPresent ? 'text-dark font-weight-bold' : '' }}">{{ $member }}</span>
                                                        </div>
                                                        {{-- Display Representative if applicable (Legacy string logic) --}}
                                                        @if($member === 'Imron Rosadi')
                                                            @php
                                                                $imronUser = \App\Models\User::where('name', 'Imron Rosadi')->first();
                                                                $details = $activity->attendance_details ?? [];
                                                                $repName = null;
                                                                if ($imronUser && isset($details[$imronUser->id]['representative'])) {
                                                                    $repName = $details[$imronUser->id]['representative'];
                                                                }
                                                            @endphp
    
                                                            @if($repName)
                                                                <div class="ml-4 pl-1 text-muted small">
                                                                    <i class="fe fe-corner-down-right mr-1"></i> Diwakili: <span class="font-weight-bold text-dark">{{ $repName }}</span>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            </div>
                        @else
                             <p class="text-muted mb-0">-</p>
                        @endif
                    </div>
                </div>
                
                @if($activity->dispo_note || $activity->dresscode)
                 <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                         <div class="icon-shape bg-light text-primary rounded-circle mr-3">
                            <i class="fe fe-align-left"></i>
                        </div>
                         <strong class="card-title">Catatan Tambahan</strong>
                    </div>
                    <div class="card-body">
                        @if($activity->dresscode)
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Dresscode</label>
                            <p class="mb-0 font-weight-bold text-dark">{{ $activity->dresscode }}</p>
                        </div>
                        <hr>
                        @endif
                        
                        @if($activity->dispo_note)
                        <div>
                            <label class="text-muted small text-uppercase font-weight-bold">Keterangan</label>
                            <div class="bg-light p-3 rounded markdown-content border">
                                {!! $activity->dispo_note !!}
                            </div>
                        </div>
                        @endif
                    </div>
                 </div>
                 @endif

            </div>
        </div>
    </div>
</div>

<!-- Summary Modal (Keep functionality) -->
<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel">Hasil Rapat Secara Singkat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('activities.update-summary', $activity->id) }}" method="POST" id="summaryForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Masukkan rangkuman hasil rapat:</label>
                        <div id="summary-editor" style="height: 300px;">{!! $activity->summary_content !!}</div>
                        <input type="hidden" name="summary_content" id="summary_content_input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="close text-white position-absolute" data-dismiss="modal" aria-label="Close" style="top: -30px; right: 0; opacity: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                    <span aria-hidden="true">&times;</span>
                </button>
                <img src="" id="photoModalImage" class="img-fluid rounded shadow-lg" alt="Dokumentasi">
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('tinydash/css/quill.snow.css') }}">
<style>
    .uppercase-label {
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .form-label-premium {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #adb5bd;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    /* Fix Timeline Dot Alignment */
    .timeline-disposition .timeline-item::before {
        top: 4px !important; /* Push dot down to align with text center */
    }

    /* Remove excessive line above the first dot */
    .timeline-disposition .timeline-item:first-child::after {
        top: 4px !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('tinydash/js/quill.min.js') }}"></script>
<script>
    // Initialize Quill for Summary
    var summaryQuill = new Quill('#summary-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'header': [1, 2, 3, false] }],
                ['clean']
            ]
        }
    });

    // Update hidden input on submit
    $('#summaryForm').on('submit', function() {
        $('#summary_content_input').val(summaryQuill.root.innerHTML);
    });
    
    // Photo Modal Script
    $('#photoModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var src = button.data('src');
        var modal = $(this);
        modal.find('#photoModalImage').attr('src', src);
    });
</script>
@endpush

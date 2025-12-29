@extends('layouts.app')

@section('title', 'Detail Kegiatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header -->
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="h5 page-title">
                    <small class="text-muted text-uppercase">Detail Kegiatan</small><br />
                    {{ $activity->name }}
                </h2>
                 <div class="mt-2">
                    @if($activity->type == 'external')
                        <span class="badge badge-info">Eksternal</span>
                    @else
                        <span class="badge badge-primary">Internal</span>
                    @endif
                    
                     @switch($activity->status)
                        @case(0) <span class="badge badge-success ml-1">On Schedule</span> @break
                        @case(1) <span class="badge badge-warning ml-1">Reschedule</span> @break
                        @case(2) <span class="badge badge-secondary ml-1">Belom ada Dispo</span> @break
                        @case(3) <span class="badge badge-danger ml-1">Tidak Dilaksanakan</span> @break
                    @endswitch
                 </div>
            </div>
            <div class="col-auto">
                 @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-primary"><span class="fe fe-edit"></span> Edit</a>
                @endif
                <a href="{{ route('activities.index') }}" class="btn btn-secondary"><span class="fe fe-arrow-left"></span> Kembali</a>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Main Info -->
            <div class="col-md-7">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title"><span class="fe fe-info mr-2"></span>Informasi Utama</strong>
                    </div>
                    <div class="card-body">
                        <!-- Waktu -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted"><span class="fe fe-calendar mr-1"></span> Waktu</label>
                            <div class="col-sm-9">
                                <p class="form-control-static mb-0 font-weight-bold text-dark">{{ $activity->date_time->format('d F Y') }}</p>
                                <small class="text-muted">{{ $activity->date_time->format('H:i') }} WIB</small>
                            </div>
                        </div>
                        
                        <!-- Lokasi -->
                         <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted"><span class="fe fe-map-pin mr-1"></span> Lokasi</label>
                            <div class="col-sm-9">
                                @if($activity->location_type == 'offline')
                                    <span class="badge badge-secondary mb-1">Offline</span>
                                    <p class="mb-0">{{ $activity->location }}</p>
                                @else
                                    <span class="badge badge-info mb-1">Online</span>
                                    @if($activity->meeting_link)
                                        @if(filter_var($activity->meeting_link, FILTER_VALIDATE_URL))
                                            <p class="mb-0"><a href="{{ $activity->meeting_link }}" target="_blank" class="text-truncate d-block">{{ $activity->meeting_link }}</a></p>
                                        @else
                                            <p class="mb-0">{{ $activity->meeting_link }}</p>
                                        @endif
                                    @else
                                        <p class="mb-0">-</p>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <hr>

                        <!-- Status Undangan -->
                         <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted"><span class="fe fe-mail mr-1"></span> Undangan</label>
                            <div class="col-sm-9">
                                <span class="badge badge-light border mb-1">{{ $activity->invitation_type == 'inbound' ? 'Surat Masuk' : 'Surat Keluar' }}</span>
                                <div>
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
                                </div>
                            </div>
                        </div>

                        <!-- PIC -->
                        @if($activity->pic)
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-muted"><span class="fe fe-users mr-1"></span> PIC</label>
                            <div class="col-sm-9">
                                 @if($activity->type == 'external')
                                    {{ is_array($activity->pic) ? implode(', ', $activity->pic) : $activity->pic }}
                                @else
                                    @foreach($activity->pic as $pic)
                                        @php
                                            $badgeClass = 'badge-info';
                                            if($pic == 'Komjakum') $badgeClass = 'badge-danger';
                                            elseif($pic == 'PME') $badgeClass = 'badge-success';
                                            elseif($pic == 'Sekretariat DJSN') $badgeClass = 'badge-secondary';
                                            elseif($pic == 'Ketua DJSN') $badgeClass = 'badge-primary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} mr-1">{{ $pic }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endif


                    </div>
            </div>

            <div class="card shadow mb-4">
                 <div class="card-body">
                    @livewire('follow-up-manager', ['activity' => $activity])
                 </div>
            </div>

            <!-- Hasil Rapat Card -->
             <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong class="card-title mb-0"><span class="fe fe-file-text mr-2"></span>Hasil Rapat Secara Singkat</strong>
                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->role === 'Sekretariat DJSN'))
                        @if($activity->summary_content)
                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#summaryModal">
                                <span class="fe fe-edit"></span> Edit
                            </button>
                        @endif
                    @endif
                </div>
                <div class="card-body">
                    @if($activity->summary_content)
                        <div class="markdown-content text-justify">{!! $activity->summary_content !!}</div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-3">Belum ada hasil rapat singkat.</p>
                            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->role === 'Sekretariat DJSN'))
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#summaryModal">
                                    <span class="fe fe-plus"></span> Tambahkan Hasil Rapat
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
        </div>

        <!-- Summary Modal -->
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

        @push('styles')
        <link rel="stylesheet" href="{{ asset('tinydash/css/quill.snow.css') }}">

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

            function autoFormatSummary() {
                let text = summaryQuill.getText();
                
                // Pre-process: Force newlines for list patterns that might be inline
                // Case: "end text 2. New Item" -> "end text\n2. New Item"
                
                // 1. Handle "1. ", "2. " but AVOID "No. 1"
                text = text.replace(/([\w\W])\s+(\d+\.)\s/g, function(match, prevChar, numDot) {
                    // Check context: prevChar is the character before the space.
                    // match is the full string "o. 1. " or "t 2. ".
                    
                    // If proper numbering, it usually follows a sentence end or is distinct.
                    // Explicit check for "No."
                    let fullContext = text.substring(text.indexOf(match) - 5, text.indexOf(match)); // Peek behind
                    if (fullContext.includes("No.") || fullContext.includes("no.")) {
                         return match; // Don't split
                    }
                    
                    return prevChar + '\n' + numDot + ' ';
                });

                // 2. Handle "a) ", "b) "
                text = text.replace(/([^\n])\s+([a-z]\))\s/g, '$1\n$2 ');
                
                // 3. Handle "1) ", "2) " (Level 3)
                text = text.replace(/([^\n])\s+(\d+\))\s/g, '$1\n$2 ');

                let lines = text.split('\n');
                let html = '';
                
                // Regex patterns
                const patterns = {
                    level1: /^(\d+)\.\s+(.*)/, // 1. Text
                    level2: /^([a-z])\)\s+(.*)/, // a) Text
                    level3: /^(\d+)\)\s+(.*)/  // 1) Text
                };

                let currentLevel = 0; // 0 = root, 1 = ol, 2 = ol > ol (a), 3 = ol > ol > ol (i)

                function ensureLevel(targetLevel, type = '') {
                    // Close deeper levels
                    while (currentLevel > targetLevel) {
                        html += '</ol>';
                        currentLevel--;
                    }
                    
                    // Open new levels if needed
                    while (currentLevel < targetLevel) {
                        let listType = '';
                        if (currentLevel === 0) listType = ''; // 1, 2, 3
                        if (currentLevel === 1) listType = ' type="a"'; // a, b, c
                        if (currentLevel === 2) listType = ' type="1"'; // 1, 2, 3
                        
                        // Override/Set based on target (simplified for standard 1 -> a -> i hierarchy)
                        html += `<ol${listType}>`;
                        currentLevel++;
                    }
                }

                lines.forEach((line, index) => {
                    line = line.trim();
                    if (!line) return;

                    let match1 = line.match(patterns.level1);
                    let match2 = line.match(patterns.level2);
                    let match3 = line.match(patterns.level3);

                    if (match1) {
                        ensureLevel(1);
                        html += `<li>${match1[2]}`;
                    } else if (match2) {
                        ensureLevel(2);
                        html += `<li>${match2[2]}`;
                    } else if (match3) {
                        ensureLevel(3);
                        html += `<li>${match3[2]}`;
                    } else {
                        // Continuation of previous item
                        if (currentLevel > 0) {
                            html += ` ${line}`;
                        } else {
                            html += `<p>${line}</p>`;
                        }
                    }
                });

                // Close all remaining lists
                while (currentLevel > 0) {
                    html += '</ol>';
                    currentLevel--;
                }
                
                summaryQuill.clipboard.dangerouslyPasteHTML(html);
            }
        </script>
        @endpush

            <!-- Right Column: Details & Disposition -->
            <div class="col-md-5">
                 <!-- Lampiran Card -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title"><span class="fe fe-paperclip mr-2"></span>Lampiran Surat</strong>
                    </div>
                    <div class="card-body text-center">
                         @if($activity->attachment_path)
                            <div class="file-icon mb-2">
                                <span class="fe fe-file-text fe-32 text-primary"></span>
                            </div>
                            <h6 class="mb-1 text-truncate" title="{{ basename($activity->attachment_path) }}">{{ basename($activity->attachment_path) }}</h6>
                            <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2"><span class="fe fe-download"></span> Lihat / Download</a>
                        @else
                            <span class="text-muted font-italic">Tidak ada lampiran.</span>
                        @endif
                    </div>
                </div>

                <!-- Disposition Card -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title"><span class="fe fe-check-square mr-2"></span>Tujuan Disposisi</strong>
                    </div>
                    <div class="card-body">
                         @if($activity->disposition_to)
                            @php
                                $councilStructure = \App\Models\Activity::COUNCIL_STRUCTURE;
                                $selected = $activity->disposition_to;
                            @endphp
                            
                            <div class="timeline">
                            @foreach($councilStructure as $groupName => $members)
                                @php
                                    $groupSelected = array_intersect($members, $selected);
                                @endphp
                                @if(!empty($groupSelected))
                                    <div class="pb-3 timeline-item item-primary">
                                        <div class="pl-5">
                                            <div class="mb-1"><strong>{{ $groupName }}</strong></div>
                                            <ul class="list-unstyled mb-0 text-muted small">
                                                @foreach($groupSelected as $member)
                                                    <li class="mb-1"><span class="fe fe-check text-success mr-1"></span>{{ $member }}</li>
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
                 <div class="card shadow mb-4">
                    <div class="card-header">
                         <strong class="card-title"><span class="fe fe-align-left mr-2"></span>Catatan Tambahan</strong>
                    </div>
                    <div class="card-body">
                        @if($activity->dresscode)
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase font-weight-bold">Dresscode</label>
                            <p class="mb-0">{{ $activity->dresscode }}</p>
                        </div>
                        @endif
                        
                        @if($activity->dispo_note)
                        <div>
                            <label class="text-muted small text-uppercase font-weight-bold">Keterangan</label>
                            <div class="bg-light p-3 rounded markdown-content">
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
@endsection

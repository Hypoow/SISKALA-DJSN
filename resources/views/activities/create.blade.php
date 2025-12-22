@extends('layouts.app')

@section('title', isset($activity) ? 'Edit Kegiatan' : 'Tambah Kegiatan Baru')

@push('styles')
<link rel="stylesheet" href="{{ asset('tinydash/css/select2.css') }}">
<link rel="stylesheet" href="{{ asset('tinydash/css/quill.snow.css') }}">
<link rel="stylesheet" href="{{ asset('tinydash/css/daterangepicker.css') }}">
<style>
    /* Modern Form Styling */
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #343a40;
        margin-bottom: 1.2rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    /* Type Selection Cards */
    .type-card-input {
        display: none;
    }
    .type-card-label {
        display: block;
        cursor: pointer;
        position: relative;
    }
    .type-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background: #fff;
        height: 100%;
    }
    .type-card:hover {
        border-color: #ced4da;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .type-card-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
    }
    .type-card-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .type-card-desc {
        color: #6c757d;
        font-size: 0.85rem;
    }
    
    /* Active States */
    .type-card-input:checked + .type-card-label .type-card.internal {
        border-color: #007bff;
        background-color: #f8f9fa;
        box-shadow: 0 0 0 4px rgba(0,123,255,0.1);
    }
    .type-card-input:checked + .type-card-label .type-card.internal .type-card-icon,
    .type-card-input:checked + .type-card-label .type-card.internal .type-card-title {
        color: #007bff;
    }

    .type-card-input:checked + .type-card-label .type-card.external {
        border-color: #fd7e14;
        background-color: #fffbf7;
        box-shadow: 0 0 0 4px rgba(253,126,20,0.1);
    }
    .type-card-input:checked + .type-card-label .type-card.external .type-card-icon,
    .type-card-input:checked + .type-card-label .type-card.external .type-card-title {
        color: #fd7e14;
    }

    /* Smart Assist Upload */
    .smart-upload-area {
        border: 2px dashed #ced4da;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.2s;
        cursor: pointer;
        position: relative;
    }
    .smart-upload-area:hover {
        border-color: #6c757d;
        background: #e9ecef;
    }
    .smart-upload-icon {
        font-size: 2rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    .custom-file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* Form Controls */
    .form-control-lg {
        border-radius: 8px;
        font-size: 1rem;
    }
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05) !important;
    }
    
    .status-dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mb-5">
    
    <!-- Header -->
    <div class="row mb-4 align-items-center justify-content-center">
        <div class="col-md-8 text-center">
            <h2 class="page-title font-weight-bold">{{ isset($activity) ? 'Edit Kegiatan' : 'Buat Kegiatan Baru' }}</h2>
            <p class="text-muted mb-0">{{ isset($activity) ? 'Perbarui informasi kegiatan dengan mudah.' : 'Pilih jenis kegiatan dan lengkapi informasinya.' }}</p>
        </div>
    </div>

    <form id="activityForm" method="POST" enctype="multipart/form-data" action="{{ isset($activity) ? route('activities.update', $activity->id) : route('activities.store') }}">
        @csrf
        @if(isset($activity))
            @method('PUT')
        @endif

        <!-- 1. Type Selection -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-5 mb-3 mb-md-0">
                <input type="radio" name="activity_type" id="type_internal" value="internal" class="type-card-input" 
                    onchange="updateFormType()" {{ (old('activity_type', $activity->type ?? '') == 'internal') ? 'checked' : '' }}
                    {{ isset($activity) ? 'disabled' : '' }}>
                <label for="type_internal" class="type-card-label h-100">
                    <div class="type-card internal d-flex flex-column justify-content-center align-items-center">
                        <i class="fe fe-briefcase type-card-icon"></i>
                        <h5 class="type-card-title">Kegiatan Internal</h5>
                        <p class="type-card-desc mb-0">Rapat, Koordinasi, dan acara internal DJSN.</p>
                    </div>
                </label>
            </div>
            <div class="col-md-5">
                <input type="radio" name="activity_type" id="type_external" value="external" class="type-card-input" 
                    onchange="updateFormType()" {{ (old('activity_type', $activity->type ?? '') == 'external') ? 'checked' : '' }}
                    {{ isset($activity) ? 'disabled' : '' }}>
                <label for="type_external" class="type-card-label h-100">
                    <div class="type-card external d-flex flex-column justify-content-center align-items-center">
                        <i class="fe fe-mail type-card-icon"></i>
                        <h5 class="type-card-title">Kegiatan Eksternal</h5>
                        <p class="type-card-desc mb-0">Undangan dari Kementerian, Lembaga, atau Mitra.</p>
                    </div>
                </label>
            </div>
            
            <!-- Hidden input to maintain value if disabled (edit mode) -->
            @if(isset($activity))
                <input type="hidden" name="activity_type" id="activity_type_hidden" value="{{ $activity->type }}">
            @endif
            <!-- Hidden Select for JS Compatibility (updateFormType logic looks for Select value, we will shim this) -->
            <select id="activity_type" class="d-none" name="activity_type_shim" disabled>
                 <option value="internal">Internal</option>
                 <option value="external">External</option>
            </select>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- 2. Smart Assist (File Upload) -->
                <div class="card mb-4" id="attachment_group">
                    <div class="card-body p-4">
                        <h5 class="form-section-title" id="smart_assist_title"><i class="fe fe-zap mr-2 text-warning"></i>Surat Undangan (Auto-Fill)</h5>
                        <p class="text-muted small mb-3" id="smart_assist_desc">Upload surat undangan (PDF/Gambar) untuk mengisi form secara otomatis.</p>
                        
                        <div class="smart-upload-area position-relative">
                            <input type="file" class="custom-file-input" id="attachment_path" name="attachment_path" accept="application/pdf, image/*" onchange="handleFileUpload(event)">
                            <div class="d-flex flex-column align-items-center justify-content-center pt-3">
                                <i class="fe fe-upload-cloud smart-upload-icon"></i>
                                <h6 class="font-weight-bold text-dark mb-1" id="file_label">Klik atau Tarik File Di Sini</h6>
                                <p class="text-muted small mb-0">Mendukung file PDF dan Images (JPG, PNG)</p>
                            </div>
                        </div>
                        
                        <!-- File Feedback -->
                        <div class="mt-3 text-center">
                            <div id="ocr-loading" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                                <span class="text-primary font-weight-bold">Sedang memproses surat...</span>
                            </div>
                            
                            @if(isset($activity) && $activity->attachment_path)
                                <div class="alert alert-light border d-inline-block px-4 py-2 mt-2">
                                    <i class="fe fe-file-text mr-2 text-primary"></i> File tersimpan: 
                                    <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank" class="font-weight-bold">{{ basename($activity->attachment_path) }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 3. Core Information -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="form-section-title"><i class="fe fe-info mr-2 text-primary"></i>Informasi Utama</h5>
                        
                        <div class="form-group mb-4">
                            <label for="name" class="font-weight-bold">Nama Kegiatan / Perihal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name', $activity->name ?? '') }}" placeholder="Contoh: Rapat Pleno Pembahasan Anggaran..." required>
                        </div>
                        
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="letter_number" class="font-weight-bold">Nomor Surat</label>
                                <input type="text" class="form-control" id="letter_number" name="letter_number" value="{{ old('letter_number', $activity->letter_number ?? '') }}" placeholder="Masukkan Nomor Surat (Jika ada)">
                            </div>
                            <!-- Organizer (External Only) -->
                            <div class="col-md-6 mb-3" id="organizer_wrapper" style="display: none;">
                                <label for="organizer_name" class="font-weight-bold">Instansi Penyelenggara</label>
                                <input type="text" class="form-control bg-light" id="organizer_name" name="organizer_name" value="{{ old('organizer_name', $activity->organizer_name ?? '') }}" placeholder="Contoh: Kementerian Kesehatan">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 4. Date & Time -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="form-section-title"><i class="fe fe-calendar mr-2 text-success"></i>Waktu Pelaksanaan</h5>
                        
                        <div class="form-row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tanggal</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', isset($activity) ? $activity->start_date->format('Y-m-d') : (isset($date) ? $date : now()->format('Y-m-d'))) }}" required onchange="syncDates()">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text bg-white border-left-0 border-right-0">s/d</span>
                                    </div>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', isset($activity) ? $activity->end_date->format('Y-m-d') : '') }}" placeholder="Selesai">
                                </div>
                                <small class="text-muted">Biarkan tanggal selesai kosong jika hanya 1 hari.</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Jam (WIB)</label>
                                <div class="input-group">
                                    <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', isset($activity) ? \Carbon\Carbon::parse($activity->start_time)->format('H:i') : '09:00') }}" required>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text bg-white border-left-0 border-right-0">s/d</span>
                                    </div>
                                    <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', (isset($activity) && $activity->end_time) ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : '') }}">
                                </div>
                                <small class="text-muted px-2">Kosongkan jam selesai jika "s/d Selesai".</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 5. Location & Type (Status) -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="form-section-title"><i class="fe fe-map-pin mr-2 text-danger"></i>Lokasi & Status</h5>
                        
                        <div class="form-row">
                             <div class="col-md-4 mb-3">
                                <label for="location_type" class="font-weight-bold">Tipe Lokasi <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="location_type" name="location_type" onchange="updateLocationInput()" required>
                                    <option value="offline" {{ (old('location_type', $activity->location_type ?? '') == 'offline') ? 'selected' : '' }}>Offline (Tatap Muka)</option>
                                    <option value="online" {{ (old('location_type', $activity->location_type ?? '') == 'online') ? 'selected' : '' }}>Online (Daring)</option>
                                    <option value="hybrid" {{ (old('location_type', $activity->location_type ?? '') == 'hybrid') ? 'selected' : '' }}>Hybrid (Kombinasi)</option>
                                </select>
                             </div>
                             
                             <div class="col-md-8">
                                 <!-- Location Input for Offline -->
                                 <div class="form-group mb-3" id="location_input_group">
                                    <label for="location" class="font-weight-bold">Nama Lokasi / Gedung</label>
                                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $activity->location ?? '') }}" placeholder="Contoh: Ruang Rapat Utama DJSN / Hotel Mulia">
                                </div>

                                <!-- Online Inputs -->
                                <div id="media_online_group" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label for="media_online" class="font-weight-bold">Platform Online</label>
                                        <select class="form-control select2" id="media_online" name="media_online">
                                            <option value="" disabled selected>-- Pilih Platform --</option>
                                            <option value="Zoom" {{ (old('media_online', $activity->media_online ?? '') == 'Zoom') ? 'selected' : '' }}>Zoom Meeting</option>
                                            <option value="Google Meet" {{ (old('media_online', $activity->media_online ?? '') == 'Google Meet') ? 'selected' : '' }}>Google Meet</option>
                                            <option value="Microsoft Teams" {{ (old('media_online', $activity->media_online ?? '') == 'Microsoft Teams') ? 'selected' : '' }}>Microsoft Teams</option>
                                            <option value="Lainnya" {{ (old('media_online', $activity->media_online ?? '') == 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="link_input_group" style="display: none;" class="mb-3">
                                    <label class="font-weight-bold">Link Meeting</label>
                                    <input type="text" class="form-control text-primary" id="meeting_link" name="meeting_link" value="{{ old('meeting_link', $activity->meeting_link ?? '') }}" placeholder="https://zoom.us/j/...">
                                </div>
                                <div class="form-row" id="meeting_details_group" style="display: none;">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Meeting ID</label>
                                        <input type="text" class="form-control" id="meeting_id" name="meeting_id" value="{{ old('meeting_id', $activity->meeting_id ?? '') }}" placeholder="823 456 789">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Passcode</label>
                                        <input type="text" class="form-control" id="passcode" name="passcode" value="{{ old('passcode', $activity->passcode ?? '') }}" placeholder="123456">
                                    </div>
                                </div>
                             </div>
                        </div>

                        <hr class="my-4">

                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="font-weight-bold">Status Pelaksanaan</label>
                                <select class="form-control select2" id="status" name="status" required>
                                    <option value="0" data-color="#28a745" {{ (old('status', $activity->status ?? '') == 0) ? 'selected' : '' }}>On Schedule</option>
                                    <option value="1" data-color="#ffc107" {{ (old('status', $activity->status ?? '') == 1) ? 'selected' : '' }}>Reschedule</option>
                                    <option value="2" data-color="#6c757d" {{ (old('status', $activity->status ?? '') == 2) ? 'selected' : '' }}>Belum Ada Disposisi</option>
                                    <option value="3" data-color="#dc3545" {{ (old('status', $activity->status ?? '') == 3) ? 'selected' : '' }}>Tidak Dilaksanakan</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="invitation_status" class="font-weight-bold">Status Undangan</label>
                                <select class="form-control select2" id="invitation_status" name="invitation_status" required>
                                    <!-- Options populated by JS via updateFormType() -->
                                </select>
                            </div>
                            
                            <!-- Hidden or Auto-managed Type -->
                            <div class="col-12" style="display:none;">
                                 <select class="form-control" id="invitation_type" name="invitation_type">
                                    <option value="inbound">Surat Masuk</option>
                                    <option value="outbound">Surat Keluar</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 6. Participants (PIC & Dispo) -->
                 <div class="card mb-4" id="pic_group">
                    <div class="card-body p-4">
                        <h5 class="form-section-title"><i class="fe fe-users mr-2 text-info"></i>Peserta & Disposisi</h5>
                        
                        <div class="mb-4">
                            <label class="font-weight-bold d-block">PIC Kegiatan / Unit Kerja <span class="text-danger" id="pic_asterisk">*</span></label>
                            
                            <!-- Internal Checkboxes -->
                            <div id="pic_internal_wrapper" class="row">
                                @php
                                    $internalPics = \App\Models\Activity::INTERNAL_PICS;
                                    $selectedPics = isset($activity) && $activity->pic ? $activity->pic : [];
                                @endphp
                                @foreach($internalPics as $pic)
                                    @php
                                        $colorClass = 'bg-secondary';
                                        if($pic == 'Komisi Komjakum') $colorClass = 'bg-danger';
                                        elseif($pic == 'Komisi PME') $colorClass = 'bg-success';
                                        elseif($pic == 'Ketua DJSN') $colorClass = 'bg-primary';
                                    @endphp
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox p-3 border rounded">
                                            <input type="checkbox" class="custom-control-input pic-checkbox" id="pic_{{ Str::slug($pic) }}" name="pic[]" value="{{ $pic }}" {{ in_array($pic, $selectedPics) ? 'checked' : '' }} data-target-group="{{ $pic }}">
                                            <label class="custom-control-label font-weight-bold" for="pic_{{ Str::slug($pic) }}">
                                                <span class="status-dot {{ $colorClass }} mr-2"></span> {{ $pic }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- External Input -->
                            <div id="pic_external_wrapper" style="display: none;">
                                <input type="text" class="form-control" id="pic_external" name="pic_external" value="{{ (isset($activity) && $activity->type == 'external') ? ($activity->pic[0] ?? '') : '' }}" placeholder="Masukkan Nama PIC Eksternal">
                            </div>
                        </div>
                        
                        <!-- Disposition Accordion -->
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Target Disposisi / Undangan</label>
                             <div class="accordion" id="accordionDewan">
                                 @php $groupIndex = 0; @endphp
                                 @foreach($dewanUsers as $groupName => $members)
                                     @php $groupIndex++; @endphp
                                     <div class="card mb-2 shadow-none border">
                                         <div class="card-header bg-light d-flex justify-content-between align-items-center py-2" id="heading{{ $groupIndex }}">
                                             <h2 class="mb-0">
                                                 <button class="btn btn-link btn-block text-left text-dark font-weight-bold collapsed text-decoration-none" type="button" data-toggle="collapse" data-target="#collapse{{ $groupIndex }}">
                                                     {{ $groupName }}
                                                 </button>
                                             </h2>
                                             <div class="custom-control custom-checkbox mr-3">
                                                 <input type="checkbox" class="custom-control-input group-check-all" id="checkAll{{ $groupIndex }}" data-target=".group-{{ $groupIndex }}">
                                                 <label class="custom-control-label" for="checkAll{{ $groupIndex }}">All</label>
                                             </div>
                                         </div>
                                         <div id="collapse{{ $groupIndex }}" class="collapse show" aria-labelledby="heading{{ $groupIndex }}">
                                             <div class="card-body py-2">
                                                 <div class="row">
                                                     @foreach($members as $member)
                                                     @php 
                                                         $selectedDewan = (isset($activity) && is_array($activity->disposition_to)) ? $activity->disposition_to : [];
                                                     @endphp
                                                     <div class="col-md-6">
                                                         <div class="custom-control custom-checkbox mb-2">
                                                             <input type="checkbox" class="custom-control-input dewan-checkbox group-{{ $groupIndex }}" id="dewan_{{ $member->id }}" name="disposition_to[]" value="{{ $member->name }}" {{ in_array($member->name, $selectedDewan) ? 'checked' : '' }}>
                                                             <label class="custom-control-label" for="dewan_{{ $member->id }}">
                                                                 {{ $member->name }}
                                                                 @if($groupName === 'Sekretariat DJSN')
                                                                     <br><small class="text-muted">{{ $member->divisi }}</small>
                                                                 @endif
                                                             </label>
                                                         </div>
                                                     </div>
                                                     @endforeach
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                        </div>

                    </div>
                 </div>
                 
                <!-- 7. Additional Info (Dresscode & Note) -->
                <div class="card mb-5">
                    <div class="card-body p-4">
                         <h5 class="form-section-title"><i class="fe fe-align-left mr-2 text-secondary"></i>Tambahan</h5>
                         <div class="form-group mb-3">
                             <label for="dresscode" class="font-weight-bold">Dresscode</label>
                             <input type="text" class="form-control" id="dresscode" name="dresscode" value="{{ old('dresscode', $activity->dresscode ?? '') }}" placeholder="Contoh: Batik Lengan Panjang / Bebas Rapi">
                         </div>
                         <div class="form-group mb-0">
                             <label for="dispo_note" class="font-weight-bold">Keterangan / Catatan</label>
                             <div id="quill-editor" style="height: 150px; border-radius: 0 0 8px 8px;">{!! old('dispo_note', $activity->dispo_note ?? '') !!}</div>
                             <input type="hidden" name="dispo_note" id="dispo_note">
                         </div>
                    </div>
                </div>

                <!-- Action Buttons (Sticky Bottom or Just Bottom) -->
                <div class="d-flex justify-content-end mb-5">
                     <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-lg mr-3 px-5 rounded-pill">Batal</a>
                     <button class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg" type="submit" id="submitBtn"><i class="fe fe-save mr-2"></i> Simpan Kegiatan</button>
                </div>

            </div> <!-- col-lg-10 -->
        </div> <!-- row -->
    </form>
</div>

@endsection

@push('scripts')
<script src="{{ asset('tinydash/js/select2.min.js') }}"></script>
<script src="{{ asset('tinydash/js/quill.min.js') }}"></script>
<script src="{{ asset('tinydash/js/daterangepicker.js') }}"></script>
<!-- OCR Dependencies -->
<script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Set worker for PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
</script>
<script>
    // Quill Editor
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Tambahkan catatan tambahan disini...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }]
            ]
        }
    });

    // Form Submission
    $('#activityForm').on('submit', function() {
        $('#dispo_note').val(quill.root.innerHTML);
    });

    // Select2 with Color
    function formatState (state) {
        if (!state.id) { return state.text; }
        var color = $(state.element).data('color');
        if(color) {
             var $state = $(
                '<span><span class="status-dot" style="background-color:' + color + '"></span> ' + state.text + '</span>'
            );
            return $state;
        }
        return state.text;
    };

    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        templateResult: formatState,
        templateSelection: formatState
    });

    function syncDates() {
        // Did user want auto-sync? No, leaving as logic present in original file
    }

    function updateLocationInput() {
        const type = document.getElementById('location_type').value;
        const locInput = document.getElementById('location_input_group');
        const linkInput = document.getElementById('link_input_group');
        const mediaGroup = document.getElementById('media_online_group');
        const meetingDetailsGroup = document.getElementById('meeting_details_group');

        if (type === 'online') {
            locInput.style.display = 'none';
            linkInput.style.display = 'block';
            mediaGroup.style.display = 'block';
            meetingDetailsGroup.style.display = 'flex';
        } else if (type === 'offline') {
            locInput.style.display = 'block';
            linkInput.style.display = 'none';
            mediaGroup.style.display = 'none';
            meetingDetailsGroup.style.display = 'none';
        } else if (type === 'hybrid') {
            locInput.style.display = 'block';
            linkInput.style.display = 'block';
            mediaGroup.style.display = 'block';
            meetingDetailsGroup.style.display = 'flex';
        }
    }

    function updateFormType() {
        // Get value from checked radio button
        const selectedRadio = document.querySelector('input[name="activity_type"]:checked');
        const type = selectedRadio ? selectedRadio.value : '';
        
        // Shim for shim select just in case
        if(type) $('#activity_type').val(type);

        const picGroup = document.getElementById('pic_group');
        const picInternalWrapper = document.getElementById('pic_internal_wrapper');
        const picExternalWrapper = document.getElementById('pic_external_wrapper');
        const organizerWrapper = document.getElementById('organizer_wrapper');
        const invStatus = $('#invitation_status');
        const attachmentInput = document.getElementById('attachment_path');
        const invTypeSelect = $('#invitation_type');
        
        // Use PHP to inject value
        const currentInvStatus = {{ isset($activity) ? $activity->invitation_status : '0' }};
        
        const attachmentGroup = document.getElementById('attachment_group');

        // Enable file input if type is selected
        if (attachmentInput) {
            if (type) {
                attachmentInput.disabled = false;
            } else {
                attachmentInput.disabled = true;
            }
        }

        const picAsterisk = document.getElementById('pic_asterisk');

        if (type === 'external') {
            picInternalWrapper.style.display = 'none';
            picExternalWrapper.style.display = 'block';
            if(organizerWrapper) organizerWrapper.style.display = 'block';
            if(picAsterisk) picAsterisk.style.display = 'none'; // Optional for External
            
            // Show Attachment Group for External (Upload Only)
            if(attachmentGroup) attachmentGroup.style.display = 'block';
            
            // UI Updates for External (No Smart Assist)
            $('#smart_assist_title').html('<i class="fe fe-paperclip mr-2 text-secondary"></i>Upload Lampiran');
            $('#smart_assist_desc').text('Upload surat undangan (PDF/Gambar) sebagai lampiran.');
            $('#ocr_loading').hide(); // Ensure loading is hidden

            // Auto-set Invitation Type to "Surat Masuk" (inbound)
            invTypeSelect.val('inbound').trigger('change');

            // External Invitation Status Options
            let options = [
                {id: 0, text: 'Proses Disposisi', color: '#ffc107'}, 
                {id: 1, text: 'Sudah ada Disposisi', color: '#28a745'}, 
                {id: 2, text: 'Untuk Diketahui Ketua', color: '#dc3545'}, 
                {id: 3, text: 'Terjadwal Hadir', color: '#007bff'} 
            ];
            populateSelect(invStatus, options, currentInvStatus);

        } else if (type === 'internal') {
            picInternalWrapper.style.display = 'flex'; // Use flex for row
            picExternalWrapper.style.display = 'none';
            if(organizerWrapper) organizerWrapper.style.display = 'none';
            if(picAsterisk) picAsterisk.style.display = 'inline'; // Required for Internal
            
            // Show Smart Assist for Internal
            if(attachmentGroup) attachmentGroup.style.display = 'block';
            
            // UI Updates for Internal (With Smart Assist)
            $('#smart_assist_title').html('<i class="fe fe-zap mr-2 text-warning"></i>Surat Undangan (Auto-Fill)');
            $('#smart_assist_desc').text('Upload surat undangan (PDF/Gambar) untuk mengisi form secara otomatis.');

            // Auto-set Invitation Type to "Surat Keluar" (outbound)
            invTypeSelect.val('outbound').trigger('change');

            // Internal Invitation Status Options
            let options = [
                {id: 0, text: 'Proses Terkirim', color: '#28a745'}, 
                {id: 1, text: 'Proses TTD', color: '#007bff'}, 
                {id: 2, text: 'Proses Drafting dan Acc', color: '#dc3545'} 
            ];
            populateSelect(invStatus, options, currentInvStatus);
        }
    }

    function populateSelect(selectElement, options, selectedValue) {
        selectElement.empty();
        options.forEach(function(option) {
            let selected = (selectedValue == option.id) ? 'selected' : '';
            let newOption = new Option(option.text, option.id, false, selected);
            $(newOption).attr('data-color', option.color);
            selectElement.append(newOption);
        });
        selectElement.trigger('change');
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateFormType();
        updateLocationInput();
        
        // Custom file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $('#file_label').html(fileName ? fileName : 'Klik atau Tarik File Di Sini');
            if(fileName) {
                $('.smart-upload-area').addClass('border-primary bg-white');
                $('.smart-upload-icon').removeClass('text-muted').addClass('text-primary');
            }
        });

        // Group Select All Logic
        $('.group-check-all').change(function() {
            let targetClass = $(this).data('target');
            $(targetClass).prop('checked', $(this).prop('checked'));
        });
        
        // Update Group Select All state if individual checkboxes change
        $('.dewan-checkbox').change(function() {
            // Find which group this checkbox belongs to
            let groupClass = Array.from(this.classList).find(cls => cls.startsWith('group-'));
            if (groupClass) {
                let allInGroup = $('.' + groupClass);
                let checkedInGroup = $('.' + groupClass + ':checked');
                let selectAllBtn = $('.group-check-all[data-target=".' + groupClass + '"]');
                
                selectAllBtn.prop('checked', allInGroup.length === checkedInGroup.length);
            }
        });

        // PIC Checkbox Auto-Select Disposition Logic
        $('.pic-checkbox').change(function() {
            let targetGroup = $(this).data('target-group');
            let isChecked = $(this).prop('checked');
            
            $('.card-header button').each(function() {
                let groupName = $(this).text().trim();
                if (groupName === targetGroup) {
                    let selectAllCheckbox = $(this).closest('.card-header').find('.group-check-all');
                    if (selectAllCheckbox.prop('checked') !== isChecked) {
                        selectAllCheckbox.prop('checked', isChecked).trigger('change');
                    }
                }
            });
        });
    });
</script>
<script>
    // OCR Logic
    async function handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Get value from radio
        const selectedRadio = document.querySelector('input[name="activity_type"]:checked');
        const activityType = selectedRadio ? selectedRadio.value : '';

        // Only run automation for Internal Activities or just run it generally?
        // Original code limited to Internal. Let's keep it generally open or check user hint?
        // User hint in UI was "Smart Assist" for all. But original logic was strict.
        // Let's allow it for both but maybe prioritize fields.
        
        const loading = document.getElementById('ocr-loading');
        loading.style.display = 'block';

        try {
            let text = '';
            if (file.type === 'application/pdf') {
                text = await extractTextFromPDF(file);
            } else if (file.type.startsWith('image/')) {
                text = await extractTextFromImage(file);
            }

            console.log("Extracted Text:", text);
            if (text) {
                parseAndFillForm(text);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Form terisi otomatis dari surat!',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        } catch (error) {
            console.error("OCR Error:", error);
            Swal.fire({
                title: 'Info',
                text: 'Tidak dapat membaca teks otomatis, silakan isi manual.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        } finally {
            loading.style.display = 'none';
        }
    }

    async function extractTextFromImage(file) {
        const worker = await Tesseract.createWorker('ind');
        const ret = await worker.recognize(file);
        await worker.terminate();
        return ret.data.text;
    }

    async function extractTextFromPDF(file) {
        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument(arrayBuffer).promise;
        let fullText = '';

        for (let i = 1; i <= pdf.numPages; i++) {
            const page = await pdf.getPage(i);
            const viewport = page.getViewport({ scale: 2.0 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            await page.render({ canvasContext: context, viewport: viewport }).promise;
            
            const blob = await new Promise(resolve => canvas.toBlob(resolve));
            const pageText = await extractTextFromImage(blob);
            fullText += pageText + '\n\n';
        }
        
        return fullText;
    }

    function parseAndFillForm(text) {
        console.log("Raw OCR Text:", text); 

        // 0. Nomor Surat
        const noMatch = text.match(/(?:Nomor|No\.?)\s*:\s*(.*?)(?=\s{2,}|\s+Jakarta|\n|$)/i);
        if (noMatch && noMatch[1]) {
            $('#letter_number').val(noMatch[1].trim());
        }

        // 1. Nama Kegiatan
        const halMatch = text.match(/(?:Hal|Perihal)\s*:\s*([\s\S]*?)(?=\n\s*(?:Yth|Lampiran)|$)/i);
        if (halMatch && halMatch[1]) {
            const hal = halMatch[1].replace(/\n/g, ' ').replace(/\s+/g, ' ').trim();
            $('#name').val(hal);
        }

        // 2. Tanggal
        function parseIndoDate(dateStr) {
             const months = {
                'januari': '01', 'februari': '02', 'maret': '03', 'april': '04', 'mei': '05', 'juni': '06',
                'juli': '07', 'agustus': '08', 'september': '09', 'oktober': '10', 'november': '11', 'desember': '12',
                'jan': '01', 'feb': '02', 'mar': '03', 'apr': '04', 'may': '05', 'jun': '06',
                'jul': '07', 'aug': '08', 'sep': '09', 'oct': '10', 'nov': '11', 'dec': '12'
            };
            
            let cleanStr = dateStr.replace(/^[a-zA-Z]+,\s*/, '').trim();
            
            let rangeMatch = cleanStr.match(/(\d+)\s*[-–]\s*(\d+)\s+([a-zA-Z]+)\s+(\d+)/);
            if (rangeMatch) {
               let startDay = rangeMatch[1].padStart(2, '0');
               let endDay = rangeMatch[2].padStart(2, '0');
               let month = months[rangeMatch[3].toLowerCase()];
               let year = rangeMatch[4];
               if (month) {
                   return { start: `${year}-${month}-${startDay}`, end: `${year}-${month}-${endDay}` };
               }
            }
            
            let singleMatch = cleanStr.match(/(\d+)\s+([a-zA-Z]+)\s+(\d+)/);
            if (singleMatch) {
                let day = singleMatch[1].padStart(2, '0');
                let month = months[singleMatch[2].toLowerCase()];
                let year = singleMatch[3];
                if (month) {
                     let date = `${year}-${month}-${day}`;
                     return { start: date, end: date };
                }
            }
            return null;
        }

        const dateMatch = text.match(/(?:Hari|Tanggal)\s*:\s*(.*?)(?=\n|$)/i);
        if (dateMatch && dateMatch[1]) {
            const parsed = parseIndoDate(dateMatch[1].trim());
            if (parsed) {
                $('#start_date').val(parsed.start);
                $('#end_date').val(parsed.end);
            }
        }

        // 3. Waktu
        const timeMatch = text.match(/(?:Waktu|Pukul|Jam)\s*:\s*(.*)/i);
        if (timeMatch && timeMatch[1]) {
            const times = timeMatch[1].match(/(\d{1,2}[.:]\d{2})/g);
            if (times && times.length > 0) {
                let start = times[0].replace('.', ':');
                if (start.length === 4) start = '0' + start;
                $('#start_time').val(start);
                
                if (times.length > 1) {
                    let end = times[1].replace('.', ':');
                    if (end.length === 4) end = '0' + end;
                    $('#end_time').val(end);
                }
            }
        }

        // 3. Tipe Lokasi, Media, & Lokasi
        const mediaMatch = text.match(/(?:Media|Tempat)\s*:\s*(.*)/i);
        let detectedType = 'offline';
        let mediaName = '';

        if (mediaMatch && mediaMatch[1]) {
            const content = mediaMatch[1].toLowerCase();
            if (content.includes('zoom')) { detectedType = 'online'; mediaName = 'Zoom'; }
            else if (content.includes('meet') || content.includes('google')) { detectedType = 'online'; mediaName = 'Google Meet'; }
            else if (content.includes('teams')) { detectedType = 'online'; mediaName = 'Microsoft Teams'; }
            else if (content.includes('online')) { detectedType = 'online'; mediaName = 'Lainnya'; }
        }
        
        if (detectedType === 'offline' && /zoom|google meet|microsoft teams|daring/i.test(text)) {
             detectedType = 'online';
             if (/zoom/i.test(text)) mediaName = 'Zoom';
        }

        $('#location_type').val(detectedType).trigger('change');

        if (detectedType === 'online' || detectedType === 'hybrid') {
            if (mediaName) $('#media_online').val(mediaName).trigger('change');
            
            const meetingIdMatch = text.match(/Meeting ID\s*[:.]?\s*([\d\s]+)/i);
            const passcodeMatch = text.match(/Passcode\s*[:.]?\s*([\w]+)/i);
            const linkMatch = text.match(/https?:\/\/[^\s]+/i);

            if (meetingIdMatch) $('#meeting_id').val(meetingIdMatch[1].trim());
            if (passcodeMatch) $('#passcode').val(passcodeMatch[1].trim());
            if (linkMatch) $('#meeting_link').val(linkMatch[0]);
        } else {
            const placeMatch = text.match(/(?:Tempat|Lokasi)\s*:\s*(.*)/i);
            if (placeMatch && !/zoom|meet|online/i.test(placeMatch[1])) {
                 $('#location').val(placeMatch[1].trim());
            }
        }

        // 4. PIC Extraction (Simplistic Keyword Match)
        const councilKeywords = {
            'Nunung Nuryartono': ['Nuryartono', 'Nunung Nuryartono'],
            'Muttaqien': ['Muttaqien', 'MPH'],
            'Nikodemus Beriman Purba': ['Nikodemus', 'Beriman Purba'],
            'Sudarto': ['Sudarto'],
            'Robben Rico': ['Robben Rico', 'Robben'],
            'Mahesa Paranadipa Maykel': ['Mahesa Paranadipa', 'Paranadipa'],
            'Syamsul Hidayat Pasaribu': ['Syamsul Hidayat', 'Pasaribu'],
            'Hermansyah': ['Hermansyah'],
            'Paulus Agung Pambudhi': ['Paulus Agung', 'Pambudhi'],
            'Agus Taufiqurrohman': ['Agus', 'Taufiqurrohman', 'Agus Taufiqurrohman'],
            'Kunta Wibawa Dasa Nugraha': ['Kunta Wibawa', 'Dasa Nugraha'],
            'Indah Anggoro Putri': ['Indah Anggoro', 'Anggoro Putri'],
            'Rudi Purwono': ['Rudi Purwono'],
            'Mickael Bobby Hoelman': ['Mickael Bobby', 'Bobby Hoelman'],
            'Royanto Purba': ['Royanto Purba', 'Royanto'],
            // Sekretariat
            'Imron Rosadi': ['Imron Rosadi'],
            'Dwi Janatun Rahayu': ['Dwi Janatun', 'Janatun Rahayu'],
            'Wenny Kartika Ayunungtyas': ['Wenny Kartika', 'Ayunungtyas'],
            'Annisa': ['Annisa'],
            'Eko Sudarmawan': ['Eko Sudarmawan', 'Sudarmawan']
        };

        let foundPersons = [];
        
        // Scope text for Disposition Search
        // Attempt to find "DAFTAR UNDANGAN" or "Lampiran" to avoid matching signers on Page 1
        let dispoText = text;
        const dispoStartMatch = text.match(/(?:DAFTAR UNDANGAN|Lampiran\s+[IVX]+)/i);
        
        if (dispoStartMatch) {
            console.log("Found Disposition Section starting at:", dispoStartMatch.index);
            dispoText = text.substring(dispoStartMatch.index);
        } else {
            console.log("No specific Disposition section found, searching full text (risk of false positives).");
        }
        
        for (const [fullName, keywords] of Object.entries(councilKeywords)) {
            // Check if any keyword exists in the scoped text
            const isMatch = keywords.some(keyword => new RegExp(keyword, 'i').test(dispoText));
            
            if (isMatch) {
                foundPersons.push(fullName);
                
                // Find Checkbox with value = fullName
                const checkbox = $(`.dewan-checkbox[value="${fullName}"]`);
                if (checkbox.length > 0) {
                    checkbox.prop('checked', true).trigger('change');
                }
            }
        }
        
        // Special Check for "Sekretariat DJSN" Group
        if (/Sekretariat DJSN/i.test(dispoText)) {
             console.log("Found 'Sekretariat DJSN' in text, selecting group...");
             
             // 1. Select PIC Checkbox (ID: pic_sekretariat-djsn)
             $('#pic_sekretariat-djsn').prop('checked', true).trigger('change');
             
             // 2. Select All in Sekretariat Disposition Group
             // Find grouping based on header text "Sekretariat DJSN"
             $('.card-header button').each(function() {
                if ($(this).text().trim() === 'Sekretariat DJSN') {
                    // Trigger the "Select All" checkbox in this header
                    $(this).closest('.card-header').find('.group-check-all').prop('checked', true).trigger('change');
                }
             });
        }

        if (foundPersons.length > 0) {
             console.log("Auto-filled Disposition for:", foundPersons);
        }
    }
</script>
@endpush

@extends('layouts.app')

@section('title', 'Edit Kegiatan')

@push('styles')
<link rel="stylesheet" href="{{ asset('tinydash/css/select2.css') }}">
<link rel="stylesheet" href="{{ asset('tinydash/css/quill.snow.css') }}">
<link rel="stylesheet" href="{{ asset('tinydash/css/daterangepicker.css') }}">
<style>
    body {
        background-color: #f6f7fa; /* Subtle background for contrast */
    }
    .page-title {
        color: #343a40;
    }
    /* Enhanced Card Styling */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03) !important;
        transition: transform 0.2s;
    }
    .card-header {
        background-color: #fff;
        border-bottom: 2px solid #f1f3f5;
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
        border-radius: 12px 12px 0 0 !important;
    }
    .card-title {
        font-weight: 700;
        color: #495057;
        font-size: 1rem;
        margin-bottom: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-body {
        padding: 1.5rem;
    }
    
    /* Input Enhancements */
    .form-control, .custom-select {
        border-radius: 8px;
        border-color: #dee2e6;
        padding: 0.6rem 1rem;
        height: auto;
        font-size: 0.95rem;
    }
    .form-control:focus, .custom-select:focus {
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        border-color: #80bdff;
    }
    .form-label-premium {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    /* Fixed alignment for custom checkbox */
    .custom-control-label {
        padding-top: 2px;
    }
    .custom-control-input:checked ~ .custom-control-label::before {
        border-color: #007bff;
        background-color: #007bff;
    }
    .custom-control-label::before {
        top: 0.2rem;
    }
    .custom-control-label::after {
        top: 0.2rem;
    }

    /* Select2 Dots & Alignment Fixes */
    .status-dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    .select2-container--bootstrap4 .select2-selection {
        border-radius: 8px;
        border-color: #dee2e6;
        height: calc(1.5em + 0.75rem + 2px) !important; /* Bootstrap standard height */
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
        margin-top: 0 !important;
        padding-left: 0.75rem !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 0 !important;
        display: flex;
        align-items: center;
    }
    
    /* Custom File Input */
    .custom-file-label {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        height: auto;
    }
    .custom-file-label::after {
        height: auto;
        padding: 0.6rem 1rem;
        border-radius: 0 8px 8px 0;
    }

    /* Premium Select2 Tags for Narasumber */
    .narasumber-container .select2-selection--multiple {
        border: 1px solid #e9ecef !important;
        background-color: #ffffff !important;
        min-height: 50px !important;
        border-radius: 12px !important;
        padding: 8px 12px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
    }
    
    .narasumber-container .select2-container--bootstrap4.select2-container--focus .select2-selection--multiple {
        border-color: #ffc107 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
    }

    .narasumber-container .select2-selection--multiple .select2-selection__choice {
        background: linear-gradient(45deg, #fffbcc, #fff3cd) !important;
        border: 1px solid #ffeeba !important;
        color: #856404 !important;
        border-radius: 30px !important;
        padding: 6px 15px !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        margin-top: 5px !important;
        margin-right: 8px !important;
        box-shadow: 0 2px 4px rgba(133, 100, 4, 0.05) !important;
        display: inline-flex;
        align-items: center;
    }

    .narasumber-container .select2-selection--multiple .select2-selection__choice__remove {
        color: #856404 !important;
        margin-right: 8px !important;
        font-weight: bold !important;
        float: none !important;
        padding-right: 0 !important;
        opacity: 0.6;
        transition: opacity 0.2s;
        background-color: transparent !important;
        border: none !important;
    }
    
    .narasumber-container .select2-selection--multiple .select2-selection__choice__remove:hover {
        opacity: 1;
        color: #d39e00 !important;
    }

    /* Custom Dropdown Styling */
    .select2-container--bootstrap4 .select2-dropdown {
        border-radius: 12px !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        border: 0 none !important;
        overflow: hidden !important;
        margin-top: 8px;
    }

    .select2-results__group {
        background-color: #fffbf2;
        color: #d39e00;
        padding: 10px 15px;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid #fff3cd;
    }

    .select2-container--bootstrap4 .select2-results__option {
        padding: 10px 15px !important;
        font-size: 0.95rem;
        color: #495057;
        border-bottom: 1px solid #f8f9fa;
        transition: all 0.2s;
    }

    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #fff3cd !important;
        color: #856404 !important;
        font-weight: 600;
        padding-left: 20px !important;
    }
    
    .select2-container--bootstrap4 .select2-results__option[aria-selected="true"] {
        background-color: #e2e6ea !important;
        color: #212529 !important;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-11"> 
        <form action="{{ route('activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data" id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="activity_type" value="{{ $activity->type }}">

            <!-- Sticky Header for Actions -->
            <div class="row align-items-center mb-4 p-3 border-bottom sticky-action-header">
                <div class="col">
                    <h2 class="h4 font-weight-bold mb-0 text-dark">
                        Edit Kegiatan
                    </h2>
                    <div class="d-flex align-items-center mt-1">
                        @if($activity->type == 'external')
                            <span class="badge badge-pill badge-info px-3 py-2 mr-2 text-white">Kegiatan Eksternal</span>
                        @else
                            <span class="badge badge-pill badge-primary px-3 py-2 mr-2" style="background-color: #004085;">Kegiatan Internal</span>
                        @endif
                         <span class="text-muted small">
                             Update terakhir: {{ $activity->updated_at->diffForHumans() }}
                             @if($activity->lastEditor)
                                 oleh <span class="font-weight-bold">{{ $activity->lastEditor->name }}</span>
                             @endif
                         </span>
                    </div>
                </div>
                <div class="col-auto">
                    <a href="{{ route('activities.show', $activity->id) }}" class="btn btn-outline-secondary rounded-pill px-4 mr-2">
                        <span class="fe fe-x mr-1"></span> Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <span class="fe fe-save mr-1"></span> Simpan Perubahan
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Main Info -->
                <div class="col-md-7">
                    
                    <!-- Title/Name Input -->
                     <div class="card mb-4" style="border-left: 5px solid #007bff;">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="form-label-premium">Nama Kegiatan</label>
                                <input type="text" class="form-control form-control-lg font-weight-bold" name="name" value="{{ old('name', $activity->name) }}" placeholder="Nama Kegiatan" style="font-size: 1.2rem;">
                            </div>
                        </div>
                     </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <div class="icon-shape bg-light text-primary rounded-circle mr-3">
                                <i class="fe fe-info"></i>
                            </div>
                            <strong class="card-title">Informasi Utama</strong>
                        </div>
                        <div class="card-body">
                            <!-- Nomor Surat -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-muted font-weight-bold">Nomor Surat</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="letter_number" value="{{ old('letter_number', $activity->letter_number) }}" placeholder="Nomor Surat (Opsional)">
                                </div>
                            </div>

                            @if($activity->type == 'external')
                            <!-- Organizer Name (External Only) -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-muted font-weight-bold">Penyelenggara</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="organizer_name" value="{{ old('organizer_name', $activity->organizer_name) }}" placeholder="Instansi Penyelenggara">
                                </div>
                            </div>
                            @endif

                            <!-- Waktu -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-muted font-weight-bold">Waktu</label>
                                <div class="col-sm-9">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <input type="date" class="form-control mb-2" name="start_date" value="{{ old('start_date', $activity->start_date->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" class="form-control mb-2" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($activity->start_time)->format('H:i')) }}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Sampai Tanggal (Opsional)</small>
                                            <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $activity->end_date->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-6">
                                             <small class="text-muted d-block mb-1">Sampai Jam (Opsional)</small>
                                            <input type="time" class="form-control" name="end_time" value="{{ old('end_time', $activity->end_time ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Lokasi -->
                             <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-muted font-weight-bold">Lokasi</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2 mb-2" id="location_type" name="location_type">
                                        <option value="offline" {{ old('location_type', $activity->location_type) == 'offline' ? 'selected' : '' }}>Offline</option>
                                        <option value="online" {{ old('location_type', $activity->location_type) == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="hybrid" {{ old('location_type', $activity->location_type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                    </select>
                                    
                                    <!-- Offline Input -->
                                    <div id="location_input_group" class="mb-2">
                                        <input type="text" class="form-control" name="location" value="{{ old('location', $activity->location) }}" placeholder="Nama Lokasi / Gedung">
                                    </div>

                                    <!-- Online Inputs -->
                                    <div id="online_inputs_wrapper" style="display: none;">
                                        <div class="card bg-light border-0 p-3 mt-2">
                                            <select class="form-control select2 mb-2" name="media_online">
                                                <option value="" selected disabled>-- Pilih Platform --</option>
                                                <option value="Zoom" {{ old('media_online', $activity->media_online) == 'Zoom' ? 'selected' : '' }}>Zoom</option>
                                                <option value="Google Meet" {{ old('media_online', $activity->media_online) == 'Google Meet' ? 'selected' : '' }}>Google Meet</option>
                                                <option value="Microsoft Teams" {{ old('media_online', $activity->media_online) == 'Microsoft Teams' ? 'selected' : '' }}>Microsoft Teams</option>
                                                <option value="Lainnya" {{ old('media_online', $activity->media_online) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control mb-2" name="meeting_link" value="{{ old('meeting_link', $activity->meeting_link) }}" placeholder="Link Meeting">
                                            <div class="form-row">
                                                <div class="col">
                                                    <input type="text" class="form-control" name="meeting_id" value="{{ old('meeting_id', $activity->meeting_id) }}" placeholder="Meeting ID">
                                                </div>
                                                <div class="col">
                                                    <input type="text" class="form-control" name="passcode" value="{{ old('passcode', $activity->passcode) }}" placeholder="Passcode">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <hr class="my-4">
    
                            <!-- Status Undangan & Kegiatan -->
                             <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-muted font-weight-bold">Status</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="small text-muted font-weight-bold text-uppercase">Tipe Undangan</label>
                                            <input type="text" class="form-control bg-light" value="{{ $activity->invitation_type == 'inbound' ? 'Surat Masuk' : 'Surat Keluar' }}" readonly>
                                            <input type="hidden" name="invitation_type" value="{{ $activity->invitation_type }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="small text-muted font-weight-bold text-uppercase">Status Undangan</label>
                                            <select class="form-control select2" name="invitation_status" id="invitation_status">
                                                @php
                                                    $isExternal = $activity->type == 'external';
                                                @endphp
                                                @if($isExternal)
                                                    <option value="0" data-color="#28a745" {{ $activity->invitation_status == 0 ? 'selected' : '' }}>Sudah ada Disposisi</option>
                                                    <option value="1" data-color="#ffc107" {{ $activity->invitation_status == 1 ? 'selected' : '' }}>Proses Disposisi</option>
                                                    <option value="2" data-color="#dc3545" {{ $activity->invitation_status == 2 ? 'selected' : '' }}>Untuk Diketahui Ketua</option>
                                                    <option value="3" data-color="#007bff" {{ $activity->invitation_status == 3 ? 'selected' : '' }}>Terjadwal Hadir</option>
                                                @else
                                                    <option value="0" data-color="#28a745" {{ $activity->invitation_status == 0 ? 'selected' : '' }}>Proses Terkirim</option>
                                                    <option value="1" data-color="#007bff" {{ $activity->invitation_status == 1 ? 'selected' : '' }}>Proses TTD</option>
                                                    <option value="2" data-color="#dc3545" {{ $activity->invitation_status == 2 ? 'selected' : '' }}>Proses Drafting dan Acc</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="small text-muted font-weight-bold text-uppercase">Status Kegiatan</label>
                                            <select class="form-control select2" name="status">
                                                <option value="0" data-color="#28a745" {{ $activity->status == 0 ? 'selected' : '' }}>On Schedule</option>
                                                <option value="2" data-color="#ffc107" {{ $activity->status == 2 ? 'selected' : '' }}>Belum ada Disposisi</option>
                                                <option value="3" data-color="#dc3545" {{ $activity->status == 3 ? 'selected' : '' }}>Tidak Dilaksanakan</option>
                                                <option value="1" data-color="#6c757d" {{ $activity->status == 1 ? 'selected' : '' }}>Reschedule</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <!-- PIC -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-muted font-weight-bold">PIC</label>
                                <div class="col-sm-9">
                                    @if($activity->type == 'internal')
                                        <div class="alert alert-light border-0 shadow-sm d-flex align-items-center mb-0">
                                            <i class="fe fe-info text-info mr-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <h6 class="font-weight-bold mb-1">Unit Kerja Otomatis</h6>
                                                <p class="mb-0 small text-muted">Unit kerja ditentukan otomatis berdasarkan nama-nama dalam Disposisi.</p>
                                            </div>
                                        </div>
                                        <!-- Hidden inputs if needed, or just nothing as backend handles it -->
                                    @else
                                        <input type="text" class="form-control" name="pic_external" value="{{ $activity->pic[0] ?? '' }}" placeholder="Nama PIC Eksternal">
                                    @endif
                                </div>
                            </div>

                            @if($activity->type == 'external')
                            <!-- Narasumber (External Only) -->
                            <div class="mt-4 mb-3 narasumber-container">
                                <div class="p-3 bg-white border rounded shadow-sm" style="border-left: 4px solid #ffc107 !important;">
                                    <div class="d-flex align-items-center mb-2">
                                         <div class="icon-shape bg-warning text-white rounded-circle mr-2 shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fe fe-mic"></i>
                                        </div>
                                        <label class="font-weight-bold mb-0 text-dark">Narasumber Kegiatan</label>
                                    </div>
                                    <select class="form-control select2-tags" name="narasumber[]" multiple="multiple">
                                        @foreach($dewanUsers as $groupName => $members)
                                            <optgroup label="{{ $groupName }}">
                                                @foreach($members as $member)
                                                    <option value="{{ $member->name }}" {{ (is_array($activity->narasumber) && in_array($member->name, $activity->narasumber)) ? 'selected' : '' }}>{{ $member->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                        <!-- Add existing manual tags -->
                                        @if(is_array($activity->narasumber))
                                            @foreach($activity->narasumber as $nara)
                                                @php
                                                    $exists = false;
                                                    foreach($dewanUsers as $group) {
                                                        if($group->contains('name', $nara)) { $exists = true; break; }
                                                    }
                                                @endphp
                                                @if(!$exists)
                                                    <option value="{{ $nara }}" selected>{{ $nara }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    <small class="form-text text-muted mt-2"><i class="fe fe-info mr-1"></i> Pilih dari daftar atau ketik nama baru lalu tekan <strong>Enter</strong>.</small>
                                </div>
                            </div>
                            @endif
    
                        </div>
                </div>
    
                <!-- Hasil Rapat (Summary) -->
                 <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <div class="icon-shape bg-light text-warning rounded-circle mr-3">
                            <i class="fe fe-file-text"></i>
                        </div>
                        <strong class="card-title mb-0">Hasil Rapat Singkat</strong>
                    </div>
                    <div class="card-body">
                         <div id="summary-editor" style="height: 200px; border-radius: 0 0 8px 8px;">{!! old('summary_content', $activity->summary_content) !!}</div>
                         <input type="hidden" name="summary_content" id="summary_content">
                    </div>
                </div>
    
            </div>
    
                <!-- Right Column: Details & Disposition -->
                <div class="col-md-5">
                    <!-- Dokumen Pendukung Card -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <div class="icon-shape bg-light text-success rounded-circle mr-3">
                                <i class="fe fe-folder"></i>
                            </div>
                            <strong class="card-title">Dokumen Pendukung</strong>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <!-- 1. Surat Undangan -->
                                <div class="list-group-item">
                                    <div class="form-group mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="small text-muted font-weight-bold mb-0">Surat Undangan</label>
                                            @if($activity->attachment_path)
                                                <div>
                                                    <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank" class="badge badge-success text-white mr-1"><i class="fe fe-eye"></i> Lihat</a>
                                                    <button type="button" class="btn badge badge-danger text-white border-0" onclick="confirmDelete('{{ route('activities.delete-attachment', $activity->id) }}')"><i class="fe fe-trash-2"></i></button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="attachment_path" name="attachment_path" accept="application/pdf, image/*">
                                            <label class="custom-file-label text-truncate" for="attachment_path">{{ $activity->attachment_path ? basename($activity->attachment_path) : 'Ganti/Upload File...' }}</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 2. Notulensi -->
                                <div class="list-group-item">
                                    <div class="form-group mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="small text-muted font-weight-bold mb-0">Notulensi</label>
                                            @if($activity->minutes_path)
                                                <div>
                                                    <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="badge badge-success text-white mr-1"><i class="fe fe-eye"></i> Lihat</a>
                                                    <button type="button" class="btn badge badge-danger text-white border-0" onclick="confirmDelete('{{ route('activities.delete-minutes', $activity->id) }}')"><i class="fe fe-trash-2"></i></button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="minutes_path" name="minutes_path" accept="application/pdf">
                                            <label class="custom-file-label text-truncate" for="minutes_path">{{ $activity->minutes_path ? basename($activity->minutes_path) : 'Ganti/Upload File...' }}</label>
                                        </div>
                                    </div>
                                </div>
    
                                <!-- 3. Surat Tugas -->
                                <div class="list-group-item">
                                     <div class="form-group mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="small text-muted font-weight-bold mb-0">Surat Tugas</label>
                                             @if($activity->assignment_letter_path)
                                                <div>
                                                    <a href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank" class="badge badge-success text-white mr-1"><i class="fe fe-eye"></i> Lihat</a>
                                                    <button type="button" class="btn badge badge-danger text-white border-0" onclick="confirmDelete('{{ route('activities.delete-assignment', $activity->id) }}')"><i class="fe fe-trash-2"></i></button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="assignment_letter_path" name="assignment_letter_path" accept="application/pdf">
                                            <label class="custom-file-label text-truncate" for="assignment_letter_path">{{ $activity->assignment_letter_path ? basename($activity->assignment_letter_path) : 'Ganti/Upload File...' }}</label>
                                        </div>
                                    </div>
                                </div>
    
                            </div>
                        </div>
                    </div>

                    <!-- Bahan Materi List & Upload -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <div class="icon-shape bg-light text-primary rounded-circle mr-3">
                                <i class="fe fe-layers"></i>
                            </div>
                            <strong class="card-title">Bahan Materi</strong>
                        </div>
                        <div class="card-body p-0">
                             <div class="list-group list-group-flush mb-3">
                                @forelse($activity->materials as $material)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-truncate mr-2" style="max-width: 200px;" title="{{ $material->title }}">{{ $material->title }}</span>
                                        <div>
                                            <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="badge badge-info text-white mr-1"><i class="fe fe-download"></i></a>
                                            <button type="button" class="btn badge badge-danger text-white border-0" onclick="confirmDelete('{{ route('activities.delete-material', $material->id) }}')"><i class="fe fe-trash-2"></i></button>
                                        </div>
                                    </div>
                                @empty
                                <br>
                                    <div class="list-group-item text-center text-muted small">Belum ada bahan materi</div>
                                @endforelse
                             </div>
                             
                             <!-- Upload Form (handled externally via JS form submission or separate form) -->
                             <!-- To keep it simple inside the main form, we can just point to a separate modal or form, 
                                  but since we are inside one big UPDATE form, multiple file uploads are tricky if we want instant feedback.
                                  Ideally, these should be separate forms or handled via ajax. 
                                  For now, to strictly follow "edit page", we might need a separate small form. 
                                  Let's use a small collapsable form that POSTs to the new endpoints we made. 
                             -->
                             <div class="p-3 border-top">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-block" data-toggle="collapse" data-target="#collapseMaterial">
                                    <i class="fe fe-plus"></i> Tambah Materi
                                </button>
                                <div class="collapse mt-3" id="collapseMaterial">
                                    <div class="form-group">
                                        <input type="text" id="material_title" class="form-control form-control-sm mb-2" placeholder="Judul Materi">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="material_file">
                                            <label class="custom-file-label" text-truncate>Pilih File...</label>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary mt-3 btn-block" onclick="uploadMaterial()">Upload</button>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>

                    <!-- Dokumentasi List & Upload -->
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                             <div class="icon-shape bg-light text-warning rounded-circle mr-3">
                                <i class="fe fe-image"></i>
                            </div>
                            <strong class="card-title">Dokumentasi</strong>
                        </div>
                         <div class="card-body p-0">
                             <div class="row p-3">
                                @forelse($activity->documentations as $doc)
                                    <div class="col-6 mb-3 position-relative">
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank">
                                            <img src="{{ Storage::url($doc->file_path) }}" class="img-fluid rounded shadow-sm" style="height: 100px; width: 100%; object-fit: cover;">
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 5px; right: 20px; padding: 2px 6px;" onclick="confirmDelete('{{ route('activities.delete-documentation', $doc->id) }}')">
                                            <i class="fe fe-trash-2"></i>
                                        </button>
                                        @if($doc->caption)
                                        <small class="d-block text-muted mt-1 text-truncate">{{ $doc->caption }}</small>
                                        @endif
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted small py-3">Belum ada dokumentasi</div>
                                @endforelse
                             </div>

                             <div class="p-3 border-top">
                                <button type="button" class="btn btn-sm btn-outline-warning btn-block" onclick="$('#doc_file').click()">
                                    <i class="fe fe-plus"></i> Tambah Dokumentasi
                                </button>
                                <input type="file" id="doc_file" accept="image/*" multiple style="display: none;" onchange="uploadDocumentation()">
                             </div>
                         </div>
                    </div>
    
                    <!-- Disposition Card -->
                    <div class="card mb-4" style="max-height: 500px; overflow-y: auto;">
                        <div class="card-header d-flex align-items-center sticky-top bg-white" style="z-index: 10;">
                             <div class="icon-shape bg-light text-info rounded-circle mr-3">
                                <i class="fe fe-check-square"></i>
                            </div>
                            <strong class="card-title">Tujuan Disposisi</strong>
                        </div>
                        <div class="card-body p-0">
                            <!-- Accordion Style from Create Page -->
                            <div class="accordion" id="accordionDewan">
                                 @php $groupIndex = 0; @endphp
                                 @foreach($dewanUsers as $groupName => $members)
                                     @php $groupIndex++; @endphp
                                     <div class="card shadow-none border-bottom mb-0 rounded-0">
                                         <div class="card-header bg-light d-flex justify-content-between align-items-center py-2" id="heading{{ $groupIndex }}" style="cursor: pointer;" data-toggle="collapse" data-target="#collapse{{ $groupIndex }}">
                                             <h2 class="mb-0 h6 font-weight-bold text-dark">
                                                 {{ $groupName }}
                                             </h2>
                                             <div class="custom-control custom-checkbox mr-3" onclick="event.stopPropagation();">
                                                 <input type="checkbox" class="custom-control-input group-check-all" id="checkAll{{ $groupIndex }}" data-target=".group-{{ $groupIndex }}">
                                                 <label class="custom-control-label small" for="checkAll{{ $groupIndex }}">All</label>
                                             </div>
                                         </div>
                                         <div id="collapse{{ $groupIndex }}" class="collapse {{ in_array('Sekretariat DJSN', $activity->disposition_to ?? []) && $groupName == 'Sekretariat DJSN' ? 'show' : '' }}" aria-labelledby="heading{{ $groupIndex }}">
                                             <div class="card-body py-2">
                                                 <div class="row">
                                                     @foreach($members as $member)
                                                     @php 
                                                         $selectedDewan = (isset($activity) && is_array($activity->disposition_to)) ? $activity->disposition_to : [];
                                                     @endphp
                                                     <div class="col-12">
                                                         <div class="custom-control custom-checkbox mb-2">
                                                             <input type="checkbox" class="custom-control-input dewan-checkbox group-{{ $groupIndex }}" id="dewan_{{ $member->id }}" name="disposition_to[]" value="{{ $member->name }}" data-group-name="{{ $groupName }}" {{ in_array($member->name, $selectedDewan) ? 'checked' : '' }}>
                                                             <label class="custom-control-label text-dark" for="dewan_{{ $member->id }}">
                                                                 {{ $member->name }}
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
                    
                    <!-- Notes -->
                     <div class="card mb-4">
                        <div class="card-header d-flex align-items-center">
                            <div class="icon-shape bg-light text-secondary rounded-circle mr-3">
                                <i class="fe fe-align-left"></i>
                            </div>
                             <strong class="card-title">Catatan Tambahan</strong>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label-premium">Dresscode</label>
                                <input type="text" class="form-control" name="dresscode" value="{{ old('dresscode', $activity->dresscode) }}">
                            </div>
                            
                            <div class="form-group mb-0">
                                <label class="form-label-premium">Keterangan</label>
                                <div id="dispo-editor" style="height: 150px;">{!! old('dispo_note', $activity->dispo_note) !!}</div>
                                <input type="hidden" name="dispo_note" id="dispo_note">
                            </div>
                        </div>
                     </div>
    
    
                </div>
            </div>
             <div class="mb-5"></div> <!-- Bottom Spacer -->
        </form>
        
        <!-- Hidden Forms for Upload/Delete Operations -->
        <form id="deleteForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        
        <form id="materialUploadForm" method="POST" action="{{ route('activities.upload-material', $activity->id) }}" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="text" name="title" id="hidden_material_title">
            <input type="file" name="file_path" id="hidden_material_file">
        </form>

        <form id="docUploadForm" method="POST" action="{{ route('activities.upload-documentation', $activity->id) }}" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="file" name="file_path[]" id="hidden_doc_file" multiple>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('tinydash/js/select2.min.js') }}"></script>
<script src="{{ asset('tinydash/js/quill.min.js') }}"></script>
<script>
    // Delete Confirmation with SweetAlert
    function confirmDelete(url) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "File yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var form = document.getElementById('deleteForm');
                form.action = url;
                form.submit();
            }
        })
    }

    // Material Upload
    function uploadMaterial() {
        var title = $('#material_title').val();
        var file = $('#material_file')[0].files[0];
        
        if(!title || !file) {
            Swal.fire('Error', 'Mohon isi Judul Materi dan pilih file.', 'error');
            return;
        }
        
        $('#hidden_material_title').val(title);
        // We need to transfer the file or clone the input. 
        // Cloning is safer.
        var fileInput = $('#material_file').clone();
        fileInput.attr('id', 'hidden_material_file');
        fileInput.attr('name', 'file_path');
        $('#hidden_material_file').replaceWith(fileInput);
        
        $('#materialUploadForm').submit();
    }

    // Documentation Upload
    function uploadDocumentation() {
        var file = $('#doc_file')[0].files[0];
        
        if(file) {
            // Check file count
            var files = $('#doc_file')[0].files;
            if(files.length > 4) {
                 Swal.fire('Error', 'Maksimal upload 4 foto sekaligus.', 'error');
                 return;
            }

            var fileInput = $('#doc_file').clone();
            fileInput.attr('id', 'hidden_doc_file');
            fileInput.attr('name', 'file_path[]'); // Set name as array
            $('#hidden_doc_file').replaceWith(fileInput);
            
            $('#docUploadForm').submit();
        }
    }
    // Editors
    var summaryQuill = new Quill('#summary-editor', {
        theme: 'snow',
        modules: { toolbar: [ ['bold', 'italic', 'underline'], [{ 'list': 'ordered'}, { 'list': 'bullet' }] ] }
    });
    
    var dispoQuill = new Quill('#dispo-editor', {
        theme: 'snow',
        modules: { toolbar: [ ['bold', 'italic', 'underline'], [{ 'list': 'ordered'}, { 'list': 'bullet' }] ] }
    });

    // Handle Form Submit
    $('#editForm').on('submit', function() {
        $('#summary_content').val(summaryQuill.root.innerHTML);
        $('#dispo_note').val(dispoQuill.root.innerHTML);
    });

    // Select2 with Color Customization
    function formatState (state) {
        if (!state.id) { return state.text; }
        // Use .attr() to get data-color because Select2 objects might be complex, 
        // but typically state.element gives the DOM element.
        var color = $(state.element).attr('data-color'); 
        
        if(color) {
             var $state = $(
                '<span class="d-flex align-items-center"><span class="status-dot" style="background-color:' + color + '"></span><span>' + state.text + '</span></span>'
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

    // Initialize Select2 with Tags
    $('.select2-tags').select2({
        theme: 'bootstrap4',
        tags: true,
        width: '100%',
        placeholder: 'Pilih atau ketik narasumber...',
        tokenSeparators: [',']
    });

    // File Inputs
     $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Location Toggle
    function updateLocationInput() {
        const type = document.getElementById('location_type').value;
        const locInput = document.getElementById('location_input_group');
        const onlineWrapper = document.getElementById('online_inputs_wrapper');

        if (type === 'offline') {
            locInput.style.display = 'block';
            onlineWrapper.style.display = 'none';
        } else if (type === 'online') {
            locInput.style.display = 'none';
            onlineWrapper.style.display = 'block';
        } else if (type === 'hybrid') {
            locInput.style.display = 'block';
            onlineWrapper.style.display = 'block';
        }
    }

    // Call on load
    updateLocationInput();
    
    // Bind change event for Select2
    $('#location_type').on('change', updateLocationInput);

    // Group Select All Logic
    $('.group-check-all').change(function() {
        let targetClass = $(this).data('target');
        $(targetClass).prop('checked', $(this).prop('checked'));
    });
    
    $('.dewan-checkbox').change(function() {
        let groupClass = Array.from(this.classList).find(cls => cls.startsWith('group-'));
        if (groupClass) {
            let allInGroup = $('.' + groupClass);
            let checkedInGroup = $('.' + groupClass + ':checked');
            let selectAllBtn = $('.group-check-all[data-target=".' + groupClass + '"]');
            selectAllBtn.prop('checked', allInGroup.length === checkedInGroup.length);
        }
    });

    // PIC Auto-Select REMOVED (Handled automatically by backend)

</script>
@endpush

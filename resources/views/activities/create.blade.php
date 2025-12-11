@extends('layouts.app')

@section('title', isset($activity) ? 'Edit Kegiatan' : 'Tambah Kegiatan Baru')

@push('styles')
<link rel="stylesheet" href="{{ asset('tinydash/css/select2.css') }}">
<link rel="stylesheet" href="{{ asset('tinydash/css/quill.snow.css') }}">
<link rel="stylesheet" href="{{ asset('tinydash/css/daterangepicker.css') }}">
<style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px) !important;
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
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="page-title">{{ isset($activity) ? 'Edit Kegiatan' : 'Tambah Kegiatan Baru' }}</h2>
        <p class="text-muted">{{ isset($activity) ? 'Perbarui data kegiatan.' : 'Tambahkan kegiatan eksternal atau internal baru.' }}</p>
        
        <div class="card shadow mb-4">
            <div class="card-header">
                <strong class="card-title">Form Kegiatan</strong>
            </div>
            <div class="card-body">
                <form id="activityForm" method="POST" enctype="multipart/form-data" action="{{ isset($activity) ? route('activities.update', $activity->id) : route('activities.store') }}">
                    @csrf
                    @if(isset($activity))
                        @method('PUT')
                    @endif
                    
                    <div class="form-group mb-3">
                        <label for="activity_type">Jenis Kegiatan</label>
                        <select class="form-control select2" id="activity_type" name="activity_type" onchange="updateFormType()" {{ isset($activity) ? 'disabled' : '' }}>
                            <option value="" disabled {{ !isset($activity) ? 'selected' : '' }}>-- Pilih Jenis Kegiatan --</option>
                            <option value="external" {{ (old('activity_type', $activity->type ?? '') == 'external') ? 'selected' : '' }}>Kegiatan Eksternal</option>
                            <option value="internal" {{ (old('activity_type', $activity->type ?? '') == 'internal') ? 'selected' : '' }}>Kegiatan Internal</option>
                        </select>
                        @if(isset($activity))
                            <input type="hidden" name="activity_type" value="{{ $activity->type }}">
                        @endif
                    </div>

                    {{-- Attachment Field Moved Here --}}
                    <div class="form-group mb-3" id="attachment_group">
                        <label for="attachment_path">Lampiran Surat (PDF/Image) <small class="text-muted" id="ocr_label_hint">- Upload untuk Auto-Fill Form</small></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="attachment_path" name="attachment_path" accept="application/pdf, image/*" onchange="handleFileUpload(event)" disabled>
                            <label class="custom-file-label" for="attachment_path">
                                {{ isset($activity) && $activity->attachment_path ? basename($activity->attachment_path) : 'Pilih file...' }}
                            </label>
                        </div>
                        <div id="ocr-loading" class="mt-2" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <span class="text-primary ml-2">Sedang memproses surat... Mohon tunggu.</span>
                        </div>
                        <small class="form-text text-muted" id="ocr_info_hint">Format: PDF atau Gambar. Sistem akan mencoba membaca isi surat.</small>
                        @if(isset($activity) && $activity->attachment_path)
                            <small class="form-text text-muted">File saat ini: <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank">Lihat File</a></small>
                        @endif
                    </div>



                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="name">Nama Kegiatan</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $activity->name ?? '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="letter_number">Nomor Surat</label>
                            <input type="text" class="form-control" id="letter_number" name="letter_number" value="{{ old('letter_number', $activity->letter_number ?? '') }}" placeholder="Masukkan Nomor Surat">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', isset($activity) ? $activity->start_date->format('Y-m-d') : (isset($date) ? $date : now()->format('Y-m-d'))) }}" required onchange="syncDates()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date">Tanggal Selesai <small class="text-muted">(Opsional)</small></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', isset($activity) ? $activity->end_date->format('Y-m-d') : '') }}">
                            <small class="form-text text-muted">Abaikan jika 1 hari.</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time">Jam Mulai</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', isset($activity) ? \Carbon\Carbon::parse($activity->start_time)->format('H:i') : '09:00') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time">Jam Selesai <small class="text-muted">(Opsional)</small></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', (isset($activity) && $activity->end_time) ? \Carbon\Carbon::parse($activity->end_time)->format('H:i') : '') }}">
                            <small class="form-text text-muted">Kosongkan jika "s/d Selesai".</small>
                        </div>
                    </div>



                    {{-- ... Status and Invitation Status sections remain same ... --}}

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="status">Status Pelaksanaan Kegiatan</label>
                            <select class="form-control select2" id="status" name="status" required>
                                <option value="0" data-color="#28a745" {{ (old('status', $activity->status ?? '') == 0) ? 'selected' : '' }}>On Schedule</option>
                                <option value="1" data-color="#ffc107" {{ (old('status', $activity->status ?? '') == 1) ? 'selected' : '' }}>Reschedule</option>
                                <option value="2" data-color="#6c757d" {{ (old('status', $activity->status ?? '') == 2) ? 'selected' : '' }}>Belom ada Dispo</option>
                                <option value="3" data-color="#dc3545" {{ (old('status', $activity->status ?? '') == 3) ? 'selected' : '' }}>Tidak Dilaksanakan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="invitation_status">Status Undangan Kegiatan</label>
                            <select class="form-control select2" id="invitation_status" name="invitation_status" required>
                                <!-- Options populated by JS -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="invitation_type">Tipe Undangan</label>
                        <select class="form-control select2" id="invitation_type" name="invitation_type" required>
                            <option value="inbound" {{ (old('invitation_type', $activity->invitation_type ?? '') == 'inbound') ? 'selected' : '' }}>Surat Masuk</option>
                            <option value="outbound" {{ (old('invitation_type', $activity->invitation_type ?? '') == 'outbound') ? 'selected' : '' }}>Surat Keluar</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="location_type">Tipe Lokasi</label>
                            <select class="form-control select2" id="location_type" name="location_type" onchange="updateLocationInput()" required>
                                <option value="offline" {{ (old('location_type', $activity->location_type ?? '') == 'offline') ? 'selected' : '' }}>Offline</option>
                                <option value="online" {{ (old('location_type', $activity->location_type ?? '') == 'online') ? 'selected' : '' }}>Online</option>
                                <option value="hybrid" {{ (old('location_type', $activity->location_type ?? '') == 'hybrid') ? 'selected' : '' }}>Hybrid (Offline & Online)</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3" id="media_online_group" style="display: none;">
                                <label for="media_online">Media Online</label>
                                <select class="form-control select2" id="media_online" name="media_online">
                                    <option value="" disabled selected>-- Pilih Media Online --</option>
                                    <option value="Zoom" {{ (old('media_online', $activity->media_online ?? '') == 'Zoom') ? 'selected' : '' }}>Zoom</option>
                                    <option value="Google Meet" {{ (old('media_online', $activity->media_online ?? '') == 'Google Meet') ? 'selected' : '' }}>Google Meet</option>
                                    <option value="Microsoft Teams" {{ (old('media_online', $activity->media_online ?? '') == 'Microsoft Teams') ? 'selected' : '' }}>Microsoft Teams</option>
                                    <option value="Lainnya" {{ (old('media_online', $activity->media_online ?? '') == 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group mb-3" id="location_input_group">
                                <label for="location">Lokasi Kegiatan</label>
                                <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $activity->location ?? '') }}">
                            </div>

                            <div class="form-group mb-3" id="link_input_group" style="display: none;">
                                <label for="meeting_link">Link Meeting / ID & Passcode</label>
                                <input type="text" class="form-control" id="meeting_link" name="meeting_link" value="{{ old('meeting_link', $activity->meeting_link ?? '') }}" placeholder="Contoh: https://zoom.us/... atau ID: 123 Pass: abc">
                            </div>
                        </div>
                    </div>



                    <div class="form-group mb-3">
                        <label for="dresscode">Dresscode</label>
                        <input type="text" class="form-control" id="dresscode" name="dresscode" value="{{ old('dresscode', $activity->dresscode ?? '') }}" placeholder="Contoh: Batik Lengan Panjang">
                    </div>

                    {{-- PIC Section --}}
                    <div class="form-group mb-3" id="pic_group">
                        <label>PIC Kegiatan</label>
                        
                        {{-- Internal PIC (Checkboxes) --}}
                        <div id="pic_internal_wrapper">
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
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input pic-checkbox" id="pic_{{ Str::slug($pic) }}" name="pic[]" value="{{ $pic }}" {{ in_array($pic, $selectedPics) ? 'checked' : '' }} data-target-group="{{ $pic }}">
                                    <label class="custom-control-label" for="pic_{{ Str::slug($pic) }}">
                                        <span class="status-dot {{ $colorClass }}"></span> {{ $pic }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        {{-- External PIC (Text) --}}
                        <div id="pic_external_wrapper" style="display: none;">
                            <input type="text" class="form-control" id="pic_external" name="pic_external" value="{{ (isset($activity) && $activity->type == 'external') ? ($activity->pic[0] ?? '') : '' }}" placeholder="Nama PIC Eksternal">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                            <label>Tujuan Disposisi</label>
                            
                            
                            {{-- Disposition Groups --}}
                            <div class="accordion" id="accordionDewan">
                                @php $groupIndex = 0; @endphp
                                @foreach($dewanUsers as $groupName => $members)
                                    @php $groupIndex++; @endphp
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-header d-flex justify-content-between align-items-center" id="heading{{ $groupIndex }}">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left text-dark font-weight-bold collapsed" type="button" data-toggle="collapse" data-target="#collapse{{ $groupIndex }}" aria-expanded="true" aria-controls="collapse{{ $groupIndex }}">
                                                    {{ $groupName }}
                                                </button>
                                            </h2>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input group-check-all" id="checkAll{{ $groupIndex }}" data-target=".group-{{ $groupIndex }}">
                                                <label class="custom-control-label" for="checkAll{{ $groupIndex }}">Pilih Semua</label>
                                            </div>
                                        </div>
                                
                                        {{-- Remove data-parent to allow multiple sections to be open --}}
                                        <div id="collapse{{ $groupIndex }}" class="collapse show" aria-labelledby="heading{{ $groupIndex }}">
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($members as $member)
                                                    @php 
                                                        $selectedDewan = (isset($activity) && is_array($activity->disposition_to)) ? $activity->disposition_to : [];
                                                    @endphp
                                                    <div class="col-md-12">
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

                    {{-- Attachment Field Moved to Top --}}


                    <div class="form-group mb-3">
                        <label for="dispo_note">Keterangan</label>
                        <button type="button" class="btn btn-sm btn-info float-right mb-2" onclick="syncToDescription()">
                            <i class="fe fe-refresh-cw"></i> Sinkronisasi Info ke Keterangan
                        </button>
                        <div id="quill-editor" style="height: 150px;">{!! old('dispo_note', $activity->dispo_note ?? '') !!}</div>
                        <input type="hidden" name="dispo_note" id="dispo_note">
                    </div>

                    <button class="btn btn-primary" type="submit" id="submitBtn">Simpan Kegiatan</button>
                    <a href="{{ route('activities.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div> <!-- /.card-body -->
        </div> <!-- /.card -->
    </div> <!-- /.col -->
</div> <!-- .row -->

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
        // Did user want auto-sync?
        // User said: "saya mau untuk tanggal selesai nya di -- dlu aja... kalo semisalkan di patokin jadi tgl sekarang , saya kudu ngeubah tgl selesai nya juga"
        // So we do NOT auto-fill end date.
    }

    function updateLocationInput() {
        const type = document.getElementById('location_type').value;
        const locInput = document.getElementById('location_input_group');
        const linkInput = document.getElementById('link_input_group');
        const mediaGroup = document.getElementById('media_online_group');

        if (type === 'online') {
            locInput.style.display = 'none';
            linkInput.style.display = 'block';
            mediaGroup.style.display = 'block';
        } else if (type === 'offline') {
            locInput.style.display = 'block';
            linkInput.style.display = 'none';
            mediaGroup.style.display = 'none';
        } else if (type === 'hybrid') {
            locInput.style.display = 'block';
            linkInput.style.display = 'block';
            mediaGroup.style.display = 'block';
        }
    }

    function updateFormType() {
        const type = document.getElementById('activity_type').value;
        const picGroup = document.getElementById('pic_group');
        const picInternalWrapper = document.getElementById('pic_internal_wrapper');
        const picExternalWrapper = document.getElementById('pic_external_wrapper');
        const invStatus = $('#invitation_status');
        const attachmentInput = document.getElementById('attachment_path');
        const invTypeSelect = $('#invitation_type');
        
        const ocrLabelHint = document.getElementById('ocr_label_hint');
        const ocrInfoHint = document.getElementById('ocr_info_hint');
        
        const currentInvStatus = {{ isset($activity) ? $activity->invitation_status : 'null' }};

        // Enable file input if type is selected
        if (attachmentInput) {
            if (type) {
                attachmentInput.disabled = false;
            } else {
                attachmentInput.disabled = true;
            }
        }

        if (type === 'external') {
            picGroup.style.display = 'block';
            picInternalWrapper.style.display = 'none';
            picExternalWrapper.style.display = 'block';
            
            // Auto-set Invitation Type to "Surat Masuk" (inbound)
            invTypeSelect.val('inbound').trigger('change');

            // External Invitation Status Options
            let options = [
                {id: 0, text: 'Proses Disposisi', color: '#28a745'}, // Hijau
                {id: 1, text: 'Sudah ada Disposisi', color: '#795548'}, // Coklat
                {id: 2, text: 'Untuk Diketahui Ketua', color: '#dc3545'}, // Merah
                {id: 3, text: 'Terjadwal Hadir', color: '#007bff'} // Biru
            ];
            populateSelect(invStatus, options, currentInvStatus);

            // Hide OCR Hints for External
            if(ocrLabelHint) ocrLabelHint.style.display = 'none';
            if(ocrInfoHint) ocrInfoHint.style.display = 'none';

        } else if (type === 'internal') {
            picGroup.style.display = 'block';
            picInternalWrapper.style.display = 'block';
            picExternalWrapper.style.display = 'none';
            
            // Show OCR Hints for Internal
            if(ocrLabelHint) ocrLabelHint.style.display = 'inline';
            if(ocrInfoHint) ocrInfoHint.style.display = 'block';

            // Auto-set Invitation Type to "Surat Keluar" (outbound)
            invTypeSelect.val('outbound').trigger('change');

            // Internal Invitation Status Options
            let options = [
                {id: 0, text: 'Proses Terkirim', color: '#28a745'}, // Hijau
                {id: 1, text: 'Proses TTD', color: '#007bff'}, // Biru
                {id: 2, text: 'Proses Drafting dan Acc', color: '#dc3545'} // Merah
            ];
            populateSelect(invStatus, options, currentInvStatus);
        }
    }

    function populateSelect(selectElement, options, selectedValue) {
        selectElement.empty();
        options.forEach(function(option) {
            let selected = (selectedValue === option.id) ? 'selected' : '';
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
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
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
            
            // Map PIC names to Disposition Group names if they differ slightly
            // In Activity.php: INTERNAL_PICS = ['Ketua DJSN', 'Komisi PME', 'Komisi Komjakum', 'Sekretariat DJSN']
            // In Activity.php: COUNCIL_STRUCTURE keys = ['Ketua DJSN', 'Komisi PME', 'Komisi Komjakum']
            // 'Sekretariat DJSN' has no direct mapping to a council group, so we ignore it or handle if needed.

            // Find the "Select All" checkbox for this group
            // We need to match the group name in the accordion headers
            
            // Iterate through group headers to find matching text
            $('.card-header button').each(function() {
                let groupName = $(this).text().trim();
                if (groupName === targetGroup) {
                    // Found the group header, find the associated "Select All" checkbox in the same header div
                    let selectAllCheckbox = $(this).closest('.card-header').find('.group-check-all');
                    
                    // Trigger click to toggle (if state doesn't match) or just set prop
                    // Better to set prop and trigger change to update children
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

        const activityType = document.getElementById('activity_type').value;

        // Only run automation for Internal Activities
        if (activityType !== 'internal') {
            console.log("Automation skipped for non-internal activity.");
            return;
        }
        
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
            }
        } catch (error) {
            console.error("OCR Error:", error);
            Swal.fire({
                title: 'Gagal!',
                text: 'Gagal membaca file. Silakan isi form secara manual.',
                icon: 'error',
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

        // Iterate through all pages
        for (let i = 1; i <= pdf.numPages; i++) {
            const page = await pdf.getPage(i);
            const viewport = page.getViewport({ scale: 2.0 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            await page.render({ canvasContext: context, viewport: viewport }).promise;
            
            // Convert canvas to image blob for Tesseract
            const blob = await new Promise(resolve => canvas.toBlob(resolve));
            const pageText = await extractTextFromImage(blob);
            fullText += pageText + '\n\n';
            
            // Update loading status
            const loadingMsg = document.getElementById('ocr-loading-msg'); // Assuming this ID exists or just use generic
            if (loadingMsg) loadingMsg.innerText = `Memproses halaman ${i} dari ${pdf.numPages}...`;
        }
        
        return fullText;
    }

    function parseAndFillForm(text) {
        console.log("Raw OCR Text:", text); // Debugging

        // 0. Nomor Surat
        // Capture until 2+ spaces or 'Jakarta' or Newline
        const noMatch = text.match(/(?:Nomor|No\.?)\s*:\s*(.*?)(?=\s{2,}|\s+Jakarta|\n|$)/i);
        if (noMatch && noMatch[1]) {
            const noSurat = noMatch[1].trim();
            $('#letter_number').val(noSurat);
        }

        // 1. Nama Kegiatan (Hal / Perihal)
        // Capture multiline text until "Yth" or "Lampiran" or double newline
        const halMatch = text.match(/(?:Hal|Perihal)\s*:\s*([\s\S]*?)(?=\n\s*(?:Yth|Lampiran)|$)/i);
        if (halMatch && halMatch[1]) {
            // Clean up newlines and extra spaces
            const hal = halMatch[1].replace(/\n/g, ' ').replace(/\s+/g, ' ').trim();
            $('#name').val(hal);
        }

        // 2. Tanggal (Hari, tanggal : ...)
        function parseIndoDate(dateStr) {
             const months = {
                'januari': '01', 'februari': '02', 'maret': '03', 'april': '04', 'mei': '05', 'juni': '06',
                'juli': '07', 'agustus': '08', 'september': '09', 'oktober': '10', 'november': '11', 'desember': '12',
                'jan': '01', 'feb': '02', 'mar': '03', 'apr': '04', 'may': '05', 'jun': '06',
                'jul': '07', 'aug': '08', 'sep': '09', 'oct': '10', 'nov': '11', 'dec': '12'
            };
            
            let cleanStr = dateStr.replace(/^[a-zA-Z]+,\s*/, '').trim();
            
            // Range: "9-10 Desember 2025"
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
            
            // Single: "9 Desember 2025"
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
            // "08.00 s.d. Selesai" or "08.00 - 10.00"
            const times = timeMatch[1].match(/(\d{1,2}[.:]\d{2})/g);
            if (times && times.length > 0) {
                let start = times[0].replace('.', ':');
                if (start.length === 4) start = '0' + start;
                $('#start_time').val(start);
                
                if (times.length > 1) {
                    let end = times[1].replace('.', ':');
                    if (end.length === 4) end = '0' + end;
                    $('#end_time').val(end);
                } else if (/selesai/i.test(timeMatch[1])) {
                    $('#end_time').val('');
                }
            }
        }

        // 3. Tipe Lokasi & Lokasi
        const isOnline = /zoom|online|daring|meet/i.test(text);
        const locationTypeSelect = $('#location_type');
        
        if (isOnline) {
            locationTypeSelect.val('online').trigger('change');
            
            // Try to find Meeting ID / Link
            const meetingIdMatch = text.match(/Meeting ID\s*:\s*([\d\s]+)/i);
            const passcodeMatch = text.match(/Passcode\s*:\s*(\w+)/i);
            
            let linkVal = '';
            if (meetingIdMatch) linkVal += `Meeting ID: ${meetingIdMatch[1].trim()} `;
            if (passcodeMatch) linkVal += `Passcode: ${passcodeMatch[1].trim()}`;
            
            if (linkVal) {
                $('#meeting_link').val(linkVal);
            }
        } else {
            locationTypeSelect.val('offline').trigger('change');
            
            // Try to find "Tempat : <place>"
            const placeMatch = text.match(/(?:Tempat|Lokasi)\s*:\s*(.*)/i);
            if (placeMatch) {
                $('#location').val(placeMatch[1].trim());
            }
        }

        // 4. PIC (Daftar Undangan)
        // Mapping from Invitation Text -> Internal PIC Value
        const picMapping = {
            'Komisi Kebijakan Umum': 'Komisi Komjakum',
            'Komisi Komjakum': 'Komisi Komjakum',
            'Komisi Monitoring': 'Komisi PME',
            'Komisi PME': 'Komisi PME',
            'Ketua Dewan Jaminan Sosial Nasional': 'Ketua DJSN',
            'Ketua DJSN': 'Ketua DJSN',
            'Sekretariat DJSN': 'Sekretariat DJSN'
        };

        // ROBUST KEYWORD MAPPING
        // Map Full Name (Checkbox Value) -> [Keywords to Search]
        // Keywords should be unique enough to identify the person but simple enough to survive OCR.
        const councilKeywords = {
            'Nunung Nuryartono': ['Nunung', 'Nuryartono'],
            'Muttaqien': ['Muttaqien'],
            'Nikodemus Beriman Purba': ['Nikodemus', 'Beriman'],
            'Sudarto': ['Sudarto'],
            'Robben Rico': ['Robben', 'Rico'],
            'Mahesa Paranadipa Maykel': ['Mahesa', 'Paranadipa'],
            'Syamsul Hidayat Pasaribu': ['Syamsul', 'Hidayat'],
            'Hermansyah': ['Hermansyah'],
            'Paulus Agung Pambudhi': ['Paulus', 'Agung'],
            'Agus Taufiqurrohman': ['Agus', 'Taufiqurrohman'],
            'Kunta Wibawa Dasa Nugraha': ['Kunta', 'Wibawa'],
            'Indah Anggoro Putri': ['Indah', 'Anggoro'],
            'Rudi Purwono': ['Rudi', 'Purwono'],
            'Mickael Bobby Hoelman': ['Mickael', 'Bobby'],
            'Royanto Purba': ['Royanto', 'Purba'],
            // Sekretariat
            'Imron Rosadi': ['Imron', 'Rosadi'],
            'Dwi Janatun Rahayu': ['Dwi', 'Janatun', 'Rahayu'],
            'Wenny Kartika Ayunungtyas': ['Wenny', 'Kartika', 'Ayunungtyas'],
            'Annisa': ['Annisa'],
            'Eko Sudarmawan': ['Eko', 'Sudarmawan']
        };

        // Map Person -> Commission (Internal PIC)
        const personToCommission = {
            'Nunung Nuryartono': 'Ketua DJSN',
            'Muttaqien': 'Komisi PME',
            'Nikodemus Beriman Purba': 'Komisi PME',
            'Sudarto': 'Komisi PME',
            'Robben Rico': 'Komisi PME',
            'Mahesa Paranadipa Maykel': 'Komisi PME',
            'Syamsul Hidayat Pasaribu': 'Komisi PME',
            'Hermansyah': 'Komisi PME',
            'Paulus Agung Pambudhi': 'Komisi Komjakum',
            'Agus Taufiqurrohman': 'Komisi Komjakum',
            'Kunta Wibawa Dasa Nugraha': 'Komisi Komjakum',
            'Indah Anggoro Putri': 'Komisi Komjakum',
            'Rudi Purwono': 'Komisi Komjakum',
            'Rudi Purwono': 'Komisi Komjakum',
            'Mickael Bobby Hoelman': 'Komisi Komjakum',
            'Royanto Purba': 'Komisi Komjakum',
            // Sekretariat
            'Imron Rosadi': 'Sekretariat DJSN',
            'Dwi Janatun Rahayu': 'Sekretariat DJSN',
            'Wenny Kartika Ayunungtyas': 'Sekretariat DJSN',
            'Annisa': 'Sekretariat DJSN',
            'Eko Sudarmawan': 'Sekretariat DJSN'
        };

        // Determine Search Scope
        let peopleSearchText = text;
        // Strictly look for "DAFTAR UNDANGAN" to avoid matching "Lampiran" in the letter header (Page 1)
        // which would include the signature (Signer) in the search scope.
        const scopeMatch = text.match(/DAFTAR UNDANGAN/i);
        if (scopeMatch) {
            console.log(`Found scope marker: ${scopeMatch[0]}`);
            peopleSearchText = text.substring(scopeMatch.index);
        } else {
            console.warn("DAFTAR UNDANGAN not found. Searching full text (risk of false positives from signer).");
        }

        // A. Check for Commission Names (Headers)
        Object.keys(picMapping).forEach(key => {
            if (new RegExp(key, 'i').test(peopleSearchText)) {
                const targetValue = picMapping[key];
                const checkbox = $(`input[name="pic[]"][value="${targetValue}"]`);
                if (checkbox.length && !checkbox.prop('checked')) {
                    // IMPORTANT: Do NOT trigger 'change' here.
                    // Triggering 'change' on .pic-checkbox fires a listener that Auto-Selects ALL members of that group.
                    // We only want to check the Commission box itself.
                    checkbox.prop('checked', true);
                }
            }
        });

        // B. Keyword-Based Person Matching
        Object.keys(councilKeywords).forEach(fullName => {
            const keywords = councilKeywords[fullName];
            let isMatch = false;
            
            for (let keyword of keywords) {
                if (peopleSearchText.toLowerCase().includes(keyword.toLowerCase())) {
                    isMatch = true;
                    console.log(`Match found for ${fullName} (Keyword: ${keyword})`);
                    break; 
                }
            }

            if (isMatch) {
                // 1. Check Disposition Checkbox
                const dispoCheckbox = $(`input[name="disposition_to[]"]`).filter(function() {
                    return $(this).val() === fullName;
                });

                if (dispoCheckbox.length) {
                    if (!dispoCheckbox.prop('checked')) {
                        // For Disposition Checkboxes, we DO want to trigger change
                        // because it updates the "Select All" state (if all are checked)
                        // and might have other UI effects.
                        dispoCheckbox.prop('checked', true).trigger('change');
                        
                        // Expand Accordion
                        const card = dispoCheckbox.closest('.card');
                        const collapse = card.find('.collapse');
                        if (collapse.length && !collapse.hasClass('show')) {
                            collapse.collapse('show');
                        }
                    }
                } else {
                    console.warn(`Checkbox for ${fullName} not found!`);
                }

                // 2. Check Corresponding Commission (Internal PIC)
                const commissionName = personToCommission[fullName];
                if (commissionName) {
                    const picCheckbox = $(`input[name="pic[]"][value="${commissionName}"]`);
                    if (picCheckbox.length && !picCheckbox.prop('checked')) {
                        // AGAIN: Do NOT trigger 'change' to avoid cascading Select All.
                        picCheckbox.prop('checked', true);
                    }
                }
            }
        });
        
        Swal.fire({
            title: 'Berhasil!',
            text: 'Form otomatis terisi! Mohon periksa kembali data yang diisi.',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    }

    // Sync Form to Description
    function syncToDescription() {
        // Gather Values
        let name = $('#name').val();
        let letterNumber = $('#letter_number').val();
        
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        let startTime = $('#start_time').val();
        let endTime = $('#end_time').val();
        
        let locationType = $('#location_type').val();
        let location = $('#location').val();
        let mediaOnline = $('#media_online').val();
        let meetingLink = $('#meeting_link').val();
        let dresscode = $('#dresscode').val();
        
        let dispos = [];
        $('input[name="disposition_to[]"]:checked').each(function() {
            dispos.push($(this).val());
        });

        // Format Date
        let dateStr = formatDate(startDate);
        if (endDate && endDate !== startDate) {
            dateStr += ' s.d. ' + formatDate(endDate);
        }

        // Format Time
        let timeStr = startTime + ' WIB';
        if (endTime) {
            timeStr += ' s.d. ' + endTime + ' WIB';
        } else {
            timeStr += ' s.d. Selesai';
        }

        // Format Location
        let locStr = '';
        if (locationType === 'online') {
            locStr = (mediaOnline ? 'Media: ' + mediaOnline : 'Online') + (meetingLink ? ' (' + meetingLink + ')' : '');
        } else if (locationType === 'offline') {
            locStr = location || '-';
        } else {
            // Hybrid
            let onlinePart = (mediaOnline ? 'Media: ' + mediaOnline : 'Online') + (meetingLink ? ' (' + meetingLink + ')' : ' (Link Menyusul)');
            let offlinePart = location || '-';
            locStr = offlinePart + ' & ' + onlinePart;
        }

        // Helper to strip titles (Generic Regex Approach)
        // Removes common titles like Dr., Ir., Prof., S.E., etc.
        function stripTitles(name) {
            // Remove known titles prefix/suffix
            // Pre-process: split by comma, usually name is first part before titles in Indonesia if strictly "Name, Title"
            // But here names are mixed like "Prof. Dr. Ir. R. Nunung Nuryartono, M.Si."
            
            // Strategy: 
            // 1. Remove generic titles (Prof, Dr, Ir, Drs, Dra)
            // 2. Remove trailing degrees (S.X, M.X, Ph.D, dkk after comma)
            
            let cleanName = name;
            
            // Remove prefixes (case insensitive)
            cleanName = cleanName.replace(/^(Prof\.|Dr\.|Drs\.|Dra\.|Ir\.|H\.|Hj\.|dr\.)\s*/gi, '');
            // Repeat to catch multiple prefixes like "Prof. Dr. Ir."
            cleanName = cleanName.replace(/^(Prof\.|Dr\.|Drs\.|Dra\.|Ir\.|H\.|Hj\.|dr\.)\s*/gi, '');
            cleanName = cleanName.replace(/^(Prof\.|Dr\.|Drs\.|Dra\.|Ir\.|H\.|Hj\.|dr\.)\s*/gi, '');
            
            // Remove suffixes (after comma)
            // Note: Some names might not use comma properly, but standard academic format does.
            if (cleanName.includes(',')) {
                cleanName = cleanName.split(',')[0];
            }
            
            return cleanName.trim();
        }

        // Gather Names Without Titles
        let dispoNames = [];
        $('input[name="disposition_to[]"]:checked').each(function() {
            let originalName = $(this).val();
            dispoNames.push(stripTitles(originalName));
        });

        // Build Text
        let text = '';
        
        // 1. Yth Section
        if (dispoNames.length > 0) {
            text += `<p>Yth,</p>`;
            text += `<p>${dispoNames.join(', ')}</p><br>`;
        }

        // Opening Greeting
        text += `<p>Bersama ini kami sampaikan informasi kegiatan dengah detail sebagai berikut:</p><br>`;
        
        text += `<p><strong>Nama Kegiatan:</strong> ${name}</p>`;
        if (letterNumber) text += `<p><strong>Nomor Surat:</strong> ${letterNumber}</p>`;
        text += `<p><strong>Waktu:</strong> ${dateStr}, Pukul ${timeStr}</p>`;
        text += `<p><strong>Lokasi:</strong> ${locStr}</p>`;
        
        // Dresscode Logic
        let dresscodeStr = dresscode ? dresscode : '-';
        text += `<p><strong>Dresscode:</strong> ${dresscodeStr}</p>`;
        
        // Removed "Tujuan Disposisi" list because user wants it at the top as "Yth"
        // But maybe keep it? User said "template keterangan nya itu seperti ini: Yth, (nama dewan)... lalu isi keterangan..."
        // So Yth replaces the old list.

        text += `<p><br></p><p><strong>Keterangan Tambahan:</strong></p>`;

        // Closing Greeting
        text += `<br><p>Demikian kami sampaikan, atas perhatian dan kehadiran Bapak/Ibu diucapkan terima kasih.</p>`;

        // Set to Quill
        quill.root.innerHTML = text;

        Swal.fire({
            title: 'Tersinkronisasi!',
            text: 'Info kegiatan telah disalin ke  sesuai format.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', options);
    }
</script>
@endpush
@endsection

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
                        <label for="attachment_path">Lampiran Surat (PDF/Image) <small class="text-muted">- Upload untuk Auto-Fill Form</small></label>
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
                        <small class="form-text text-muted">Format: PDF atau Gambar. Sistem akan mencoba membaca isi surat.</small>
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
                            <label for="date_time">Tanggal & Jam Pelaksanaan</label>
                            <div class="input-group">
                                <input type="text" class="form-control drgpicker" id="date_time_picker" value="{{ isset($activity) ? $activity->date_time->format('m/d/Y H:i') : (isset($date) ? \Carbon\Carbon::parse($date)->format('m/d/Y H:i') : now()->format('m/d/Y H:i')) }}" required>
                                <input type="hidden" name="date_time" id="date_time" value="{{ isset($activity) ? $activity->date_time->format('Y-m-d H:i:s') : (isset($date) ? \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')) }}">
                                <div class="input-group-append">
                                    <div class="input-group-text" id="button-addon-date"><span class="fe fe-calendar fe-16"></span></div>
                                </div>
                            </div>
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
                                <option value="online" {{ (old('location_type', $activity->location_type ?? '') == 'online') ? 'selected' : '' }}>Online (Zoom/Meet)</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <div id="location_input_group">
                                <label for="location">Lokasi Kegiatan</label>
                                <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $activity->location ?? '') }}">
                            </div>
                            <div id="link_input_group" style="display: none;">
                                <label for="meeting_link">Link Meeting / ID & Passcode</label>
                                <input type="text" class="form-control" id="meeting_link" name="meeting_link" value="{{ old('meeting_link', $activity->meeting_link ?? '') }}" placeholder="Contoh: https://zoom.us/... atau ID: 123 Pass: abc">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="dispo_note">Keterangan Dispo</label>
                        <div id="quill-editor" style="height: 150px;">{!! old('dispo_note', $activity->dispo_note ?? '') !!}</div>
                        <input type="hidden" name="dispo_note" id="dispo_note">
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
                        <label>Tujuan Disposisi (Dewan)</label>
                        <div class="accordion" id="accordionDewan">
                            @php
                                $councilStructure = \App\Models\Activity::COUNCIL_STRUCTURE;
                                $selectedDewan = isset($activity) && $activity->disposition_to ? $activity->disposition_to : [];
                                $groupIndex = 0;
                            @endphp
                            
                            @foreach($councilStructure as $groupName => $members)
                            <div class="card mb-2 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center" id="heading{{ $groupIndex }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left text-dark font-weight-bold collapsed" type="button" data-toggle="collapse" data-target="#collapse{{ $groupIndex }}" aria-expanded="false" aria-controls="collapse{{ $groupIndex }}">
                                            {{ $groupName }}
                                        </button>
                                    </h2>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input group-check-all" id="checkAll{{ $groupIndex }}" data-target=".group-{{ $groupIndex }}">
                                        <label class="custom-control-label" for="checkAll{{ $groupIndex }}">Pilih Semua</label>
                                    </div>
                                </div>
                        
                                <div id="collapse{{ $groupIndex }}" class="collapse show" aria-labelledby="heading{{ $groupIndex }}" data-parent="#accordionDewan">
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($members as $member)
                                            <div class="col-md-12">
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox" class="custom-control-input dewan-checkbox group-{{ $groupIndex }}" id="dewan_{{ Str::slug($member) }}" name="disposition_to[]" value="{{ $member }}" {{ in_array($member, $selectedDewan) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="dewan_{{ Str::slug($member) }}">{{ $member }}</label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php $groupIndex++; @endphp
                            @endforeach
                        </div>
                    </div>

                    {{-- Attachment Field Moved to Top --}}

                    <button class="btn btn-primary" type="submit" id="submitBtn">Simpan Kegiatan</button>
                    <a href="{{ route('activities.index') }}" class="btn btn-secondary">Cancel</a>
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
        templateResult: formatState,
        templateSelection: formatState
    });

    // Datepicker
    $('.drgpicker').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        showDropdowns: true,
        locale: {
            format: 'MM/DD/YYYY HH:mm'
        }
    }, function(start, end, label) {
        $('#date_time').val(start.format('YYYY-MM-DD HH:mm:ss'));
    });

    // Initial Date Value
    function updateHiddenDate() {
        var rawVal = $('#date_time_picker').val();
        if(rawVal) {
            var formatted = moment(rawVal, 'MM/DD/YYYY HH:mm').format('YYYY-MM-DD HH:mm:ss');
            $('#date_time').val(formatted);
        }
    }

    // Update on manual change/blur
    $('#date_time_picker').on('change blur', function() {
        updateHiddenDate();
    });

    // Initial set
    updateHiddenDate();

    function updateLocationInput() {
        const type = document.getElementById('location_type').value;
        if (type === 'online') {
            document.getElementById('location_input_group').style.display = 'none';
            document.getElementById('link_input_group').style.display = 'block';
        } else {
            document.getElementById('location_input_group').style.display = 'block';
            document.getElementById('link_input_group').style.display = 'none';
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
        
        const currentInvStatus = {{ isset($activity) ? $activity->invitation_status : 'null' }};

        // Enable file input if type is selected
        if (type) {
            attachmentInput.disabled = false;
        } else {
            attachmentInput.disabled = true;
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

        } else if (type === 'internal') {
            picGroup.style.display = 'block';
            picInternalWrapper.style.display = 'block';
            picExternalWrapper.style.display = 'none';

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

        // 1. Nama Kegiatan (Hal / Perihal)
        // Capture multiline text until "Yth" or "Lampiran" or double newline
        const halMatch = text.match(/(?:Hal|Perihal)\s*:\s*([\s\S]*?)(?=\n\s*(?:Yth|Lampiran)|$)/i);
        if (halMatch && halMatch[1]) {
            // Clean up newlines and extra spaces
            const hal = halMatch[1].replace(/\n/g, ' ').replace(/\s+/g, ' ').trim();
            $('#name').val(hal);
        }

        // 2. Tanggal & Jam
        // Flexible matching for Hari/Tanggal (handling spaces around slash)
        const dateMatch = text.match(/(?:Hari\s*[\/|]\s*Tanggal)\s*:\s*(.*)/i);
        const timeMatch = text.match(/(?:Waktu|Pukul|Jam)\s*:\s*(.*)/i);

        if (dateMatch && dateMatch[1]) {
            let dateStr = dateMatch[1].trim(); // "Selasa, 25 November 2025"
            // Remove day name (Selasa,)
            dateStr = dateStr.replace(/^[a-zA-Z]+,\s*/, '');
            
            let timeStr = "09:00"; // Default
            if (timeMatch && timeMatch[1]) {
                // Extract first time "13.30"
                const times = timeMatch[1].match(/(\d{1,2}[.:]\d{2})/);
                if (times) {
                    timeStr = times[1].replace('.', ':');
                }
            }

            // Combine and parse
            const monthsInd = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const monthsEng = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            
            monthsInd.forEach((m, i) => {
                dateStr = dateStr.replace(new RegExp(m, 'i'), monthsEng[i]);
            });

            const fullDateTimeString = `${dateStr} ${timeStr}`;
            const parsedDate = moment(fullDateTimeString, 'D MMMM YYYY HH:mm');
            
            if (parsedDate.isValid()) {
                $('#date_time_picker').data('daterangepicker').setStartDate(parsedDate);
                $('#date_time_picker').data('daterangepicker').setEndDate(parsedDate);
                $('#date_time').val(parsedDate.format('YYYY-MM-DD HH:mm:ss'));
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
            'Prof. Dr. Ir. R. Nunung Nuryartono, M.Si.': ['Nunung', 'Nuryartono'],
            'Muttaqien, S.S., M.P.H., A.A.K.': ['Muttaqien'],
            'Nikodemus Beriman Purba, S.Psi., M.H.': ['Nikodemus', 'Beriman'],
            'Sudarto, S.E., M.B.A., M.Kom., Ph.D., CGEIT., CA.': ['Sudarto'],
            'Robben Rico, A.Md., LLAJ., S.H., S.T., M.Si.': ['Robben', 'Rico'],
            'Dr. dr. Mahesa Paranadipa Maykel, M.H., MARS.': ['Mahesa', 'Paranadipa'],
            'Dr.rer.pol. Syamsul Hidayat Pasaribu, S.E., M.Si.': ['Syamsul', 'Hidayat'],
            'Hermansyah, S.H., AK3.': ['Hermansyah'],
            'Drs. Paulus Agung Pambudhi, M.M.': ['Paulus', 'Agung'],
            'dr. H. Agus Taufiqurrohman, M.Kes., Sp.S.': ['Agus', 'Taufiqurrohman'],
            'Kunta Wibawa Dasa Nugraha, S.E., M.A., Ph.D.': ['Kunta', 'Wibawa'],
            'Dra. Indah Anggoro Putri, M.Bus.': ['Indah', 'Anggoro'],
            'Prof. Dr. Rudi Purwono, S.E., M.SE.': ['Rudi', 'Purwono'],
            'Mickael Bobby Hoelman, S.E., M.Si.': ['Mickael', 'Bobby'],
            'Royanto Purba, S.T.': ['Royanto', 'Purba']
        };

        // Map Person -> Commission (Internal PIC)
        const personToCommission = {
            'Prof. Dr. Ir. R. Nunung Nuryartono, M.Si.': 'Ketua DJSN',
            'Muttaqien, S.S., M.P.H., A.A.K.': 'Komisi PME',
            'Nikodemus Beriman Purba, S.Psi., M.H.': 'Komisi PME',
            'Sudarto, S.E., M.B.A., M.Kom., Ph.D., CGEIT., CA.': 'Komisi PME',
            'Robben Rico, A.Md., LLAJ., S.H., S.T., M.Si.': 'Komisi PME',
            'Dr. dr. Mahesa Paranadipa Maykel, M.H., MARS.': 'Komisi PME',
            'Dr.rer.pol. Syamsul Hidayat Pasaribu, S.E., M.Si.': 'Komisi PME',
            'Hermansyah, S.H., AK3.': 'Komisi PME',
            'Drs. Paulus Agung Pambudhi, M.M.': 'Komisi Komjakum',
            'dr. H. Agus Taufiqurrohman, M.Kes., Sp.S.': 'Komisi Komjakum',
            'Kunta Wibawa Dasa Nugraha, S.E., M.A., Ph.D.': 'Komisi Komjakum',
            'Dra. Indah Anggoro Putri, M.Bus.': 'Komisi Komjakum',
            'Prof. Dr. Rudi Purwono, S.E., M.SE.': 'Komisi Komjakum',
            'Mickael Bobby Hoelman, S.E., M.Si.': 'Komisi Komjakum',
            'Royanto Purba, S.T.': 'Komisi Komjakum'
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
</script>
@endpush
@endsection

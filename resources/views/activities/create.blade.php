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
                            <option value="external" {{ (old('activity_type', $activity->type ?? '') == 'external') ? 'selected' : '' }}>Kegiatan Eksternal</option>
                            <option value="internal" {{ (old('activity_type', $activity->type ?? '') == 'internal') ? 'selected' : '' }}>Kegiatan Internal</option>
                        </select>
                        @if(isset($activity))
                            <input type="hidden" name="activity_type" value="{{ $activity->type }}">
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
                                <input type="text" class="form-control drgpicker" id="date_time_picker" value="{{ isset($activity) ? $activity->date_time->format('m/d/Y H:i') : now()->format('m/d/Y H:i') }}" required>
                                <input type="hidden" name="date_time" id="date_time" value="{{ isset($activity) ? $activity->date_time->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s') }}">
                                <div class="input-group-append">
                                    <div class="input-group-text" id="button-addon-date"><span class="fe fe-calendar fe-16"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3" id="pic_group" style="display: none;">
                        <label>PIC Kegiatan</label>
                        @php
                            $selectedPics = isset($activity) && $activity->pic ? $activity->pic : [];
                        @endphp
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="pic[]" value="Komjakum" id="pic1" {{ in_array('Komjakum', $selectedPics) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pic1"><span class="status-dot bg-danger"></span>Komjakum</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="pic[]" value="PME" id="pic2" {{ in_array('PME', $selectedPics) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pic2"><span class="status-dot bg-success"></span>PME</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="pic[]" value="Sekretariat DJSN" id="pic3" {{ in_array('Sekretariat DJSN', $selectedPics) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pic3"><span class="status-dot bg-secondary"></span>Sekretariat DJSN</label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="status">Status Pelaksanaan Kegiatan</label>
                            <select class="form-control select2" id="status" name="status" required>
                                <option value="0" data-color="#007bff" {{ (old('status', $activity->status ?? '') == 0) ? 'selected' : '' }}>On Schedule</option>
                                <option value="1" data-color="#28a745" {{ (old('status', $activity->status ?? '') == 1) ? 'selected' : '' }}>Reschedule</option>
                                <option value="2" data-color="#ffc107" {{ (old('status', $activity->status ?? '') == 2) ? 'selected' : '' }}>Belom ada Dispo</option>
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
                                <label for="meeting_link">Link Meeting</label>
                                <input type="url" class="form-control" id="meeting_link" name="meeting_link" value="{{ old('meeting_link', $activity->meeting_link ?? '') }}" placeholder="https://zoom.us/...">
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

                    <div class="form-group mb-3">
                        <label>Tujuan Disposisi (Dewan)</label>
                        <div class="card">
                            <div class="card-body">
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" class="custom-control-input" id="checkAllDewan">
                                    <label class="custom-control-label" for="checkAllDewan"><strong>Pilih Semua</strong></label>
                                </div>
                                <div class="row">
                                    @php
                                        $councilMembers = \App\Models\Activity::COUNCIL_MEMBERS;
                                        $selectedDewan = isset($activity) && $activity->disposition_to ? $activity->disposition_to : [];
                                    @endphp
                                    @foreach($councilMembers as $index => $member)
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input dewan-checkbox" id="dewan{{ $index }}" name="disposition_to[]" value="{{ $member }}" {{ in_array($member, $selectedDewan) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="dewan{{ $index }}">{{ $member }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="attachment_path">Lampiran Surat (PDF)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="attachment_path" name="attachment_path" accept="application/pdf">
                            <label class="custom-file-label" for="attachment_path">
                                {{ isset($activity) && $activity->attachment_path ? basename($activity->attachment_path) : 'Pilih file...' }}
                            </label>
                        </div>
                        @if(isset($activity) && $activity->attachment_path)
                            <small class="form-text text-muted">File saat ini: <a href="{{ Storage::url($activity->attachment_path) }}" target="_blank">Lihat PDF</a></small>
                        @endif
                    </div>

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
    $('#date_time').val(moment($('#date_time_picker').val()).format('YYYY-MM-DD HH:mm:ss'));

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
        const invStatus = $('#invitation_status');
        
        const currentInvStatus = {{ isset($activity) ? $activity->invitation_status : 'null' }};

        if (type === 'external') {
            picGroup.style.display = 'none';
            
            // External Invitation Status Options
            let options = [
                {id: 0, text: 'Proses Disposisi', color: '#28a745'}, // Hijau
                {id: 1, text: 'Sudah ada Disposisi', color: '#795548'}, // Coklat
                {id: 2, text: 'Untuk Diketahui Ketua', color: '#dc3545'}, // Merah
                {id: 3, text: 'Terjadwal Hadir', color: '#007bff'} // Biru
            ];
            populateSelect(invStatus, options, currentInvStatus);

        } else {
            picGroup.style.display = 'block';

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

        // Select All Dewan
        $('#checkAllDewan').change(function() {
            $('.dewan-checkbox').prop('checked', $(this).prop('checked'));
        });
        
        // Update Select All state if individual checkboxes change
        $('.dewan-checkbox').change(function() {
            if(false == $(this).prop("checked")){
                $("#checkAllDewan").prop('checked', false);
            }
            if ($('.dewan-checkbox:checked').length == $('.dewan-checkbox').length ){
                $("#checkAllDewan").prop('checked', true);
            }
        });
    });
</script>
@endpush
@endsection

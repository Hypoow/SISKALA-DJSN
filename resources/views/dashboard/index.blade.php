@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('css/select2.css') }}">
<style>
    .fc-event {
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-12">
    <div class="row align-items-center my-3">
      <div class="col">
        <h2 class="page-title">Dashboard & Kalender</h2>
      </div>
    </div>
    
    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <span class="circle circle-lg bg-primary-light">
                                <i class="fe fe-calendar fe-24 text-primary"></i>
                            </span>
                        </div>
                        <div class="col-9">
                            <p class="small text-muted mb-0">Total Kegiatan (Bulan Ini)</p>
                            <span class="h3 mb-0 text-primary font-weight-bold">{{ $totalActivities }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <span class="circle circle-lg bg-success-light" style="background-color: #d4edda;">
                                <i class="fe fe-check-circle fe-24 text-success"></i>
                            </span>
                        </div>
                        <div class="col-9">
                            <p class="small text-muted mb-0">Kegiatan Hari Ini</p>
                            <span class="h3 mb-0 text-success font-weight-bold">{{ $todayActivities }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <span class="circle circle-lg bg-info-light" style="background-color: #d1ecf1;">
                                <i class="fe fe-users fe-24 text-info"></i>
                            </span>
                        </div>
                        <div class="col-9">
                            <p class="small text-muted mb-0">Internal (Bulan Ini)</p>
                            <span class="h3 mb-0 text-info font-weight-bold">{{ $internalActivities }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <span class="circle circle-lg bg-warning-light" style="background-color: #fff3cd;">
                                <i class="fe fe-external-link fe-24 text-warning"></i>
                            </span>
                        </div>
                        <div class="col-9">
                            <p class="small text-muted mb-0">Eksternal (Bulan Ini)</p>
                            <span class="h3 mb-0 text-warning font-weight-bold">{{ $externalActivities }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center my-3">
      <div class="col-12 col-md mb-3 mb-md-0">
        <h4 class="mb-1 text-primary-dark font-weight-bold text-center text-md-left">Kalender</h4>
      </div>
      <div class="col-12 col-md-auto">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-center">
            {{-- Legend Container --}}
            <div class="d-flex flex-wrap justify-content-center mb-3 mb-md-0">
                {{-- Legend for Dewan --}}
                @if(Auth::user()->role === 'Dewan')
                    <div class="d-flex align-items-center mx-2 mb-2 mb-md-0">
                        <span class="status-dot" style="background-color: #007bff;"></span>
                        <span class="small ml-1">Hadir (Internal)</span>
                    </div>
                    <div class="d-flex align-items-center mx-2 mb-2 mb-md-0">
                        <span class="status-dot" style="background-color: #fd7e14;"></span>
                        <span class="small ml-1">Hadir (Eksternal)</span>
                    </div>
                    <div class="d-flex align-items-center mx-2 mb-2 mb-md-0">
                        <span class="status-dot" style="background-color: #6c757d;"></span>
                        <span class="small ml-1">Opsional</span>
                    </div>
                {{-- Legend for Others --}}
                @else
                    <div class="d-flex align-items-center mx-2 mb-2 mb-md-0">
                        <span class="status-dot" style="background-color: #007bff;"></span>
                        <span class="small ml-1">Internal</span>
                    </div>
                    <div class="d-flex align-items-center mx-2 mb-2 mb-md-0">
                        <span class="status-dot" style="background-color: #fd7e14;"></span>
                        <span class="small ml-1">Eksternal</span>
                    </div>
                @endif
            </div>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('activities.create') }}" class="btn btn-primary ml-md-3 w-100 w-md-auto">
                <span class="fe fe-plus fe-16 mr-2"></span>Kegiatan Baru
            </a>
            @endif
        </div>
      </div>
    </div>
    <div id='calendar'></div>
    
    {{-- Event Modal Removed --}}

  </div> <!-- .col-12 -->
</div> <!-- .row -->
@endsection

@push('scripts')
<script src="{{ asset('js/fullcalendar.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  if (calendarEl) {
    var calendar = new FullCalendar.Calendar(calendarEl, {
      plugins: ['dayGrid', 'timeGrid', 'list', 'interaction'],
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      firstDay: 1, // Monday
      locale: 'id',
      // Dynamic Header Format based on screen width
      columnHeaderFormat: window.innerWidth < 768 ? { weekday: 'short' } : { weekday: 'long' },
      locales: [{
        code: 'id',
        week: {
          dow: 1, // Monday is the first day of the week.
          doy: 4  // The week that contains Jan 4th is the first week of the year.
        },
        buttonText: {
          prev: 'Mundur',
          next: 'Maju',
          today: 'Hari Ini',
          month: 'Bulan',
          week: 'Minggu',
          day: 'Hari',
          list: 'Agenda'
        },
        weekLabel: 'Mg',
        allDayHtml: 'Sehari<br/>penuh',
        eventLimitText: 'lebihnya',
        noEventsMessage: 'Tidak ada acara untuk ditampilkan'
      }],
      weekNumbers: false,
      fixedWeekCount: false,
      showNonCurrentDates: true,
      eventLimit: true,
      eventTimeFormat: { // like '14:30'
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
      },
      slotLabelFormat: {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
      },
      events: '{{ route("dashboard.events.get") }}',
      eventClick: function(info) {
        // ... (existing eventClick logic) ...
        // Populate Modal
        $('#eventDetailTitle').text(info.event.title);
        
        // Format Time
        var start = info.event.start;
        var end = info.event.end;
        var timeString = start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        if(end) {
            timeString += ' - ' + end.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        $('#eventDetailTime').text(timeString);

        // Extended Props
        var props = info.event.extendedProps;
        
        // Type Badge
        var typeBadge = props.type === 'external' 
            ? '<span class="badge badge-info mb-2">Eksternal</span>' 
            : '<span class="badge badge-primary mb-2">Internal</span>';
        $('#eventDetailType').html(typeBadge);

        // Location Logic
        var locationHtml = '';
        if (props.location_type === 'online') {
            locationHtml = '<span class="badge badge-info mb-1">Online</span><br>';
            if (props.meeting_link) {
                locationHtml += '<a href="' + props.meeting_link + '" target="_blank" class="text-truncate d-block">' + props.meeting_link + '</a>';
            } else {
                locationHtml += '-';
            }
        } else {
            locationHtml = '<span class="badge badge-secondary mb-1">Offline</span><br>' + (props.location_detail || '-');
        }
        $('#eventDetailLocation').html(locationHtml);

        // PIC Logic
        var picHtml = '';
        if (props.pic) {
            if (Array.isArray(props.pic)) {
                props.pic.forEach(function(p) {
                    var badgeClass = 'badge-info';
                    if(p == 'Komjakum') badgeClass = 'badge-danger';
                    else if(p == 'PME') badgeClass = 'badge-success';
                    else if(p == 'Sekretariat DJSN') badgeClass = 'badge-secondary';
                    else if(p == 'Ketua DJSN') badgeClass = 'badge-primary';
                    
                    picHtml += '<span class="badge ' + badgeClass + ' mr-1">' + p + '</span>';
                });
            } else {
                picHtml = props.pic;
            }
        } else {
            picHtml = '-';
        }
        $('#eventDetailPic').html(picHtml);

        $('#eventDetailDescription').text(props.description || '-');

        // Buttons
        var editBtn = $('#eventDetailEditBtn');
        var detailBtn = $('#eventDetailLinkBtn');
        
        // Set Detail Link
        detailBtn.attr('href', '/activities/' + info.event.id);

        // Set Edit Link (Admin Only)
        @if(Auth::user()->role === 'admin')
            editBtn.attr('href', '/activities/' + info.event.id + '/edit');
            editBtn.show();
        @else
            editBtn.hide();
        @endif

        // Show Modal
        $('#eventDetailModal').modal('show');
      },
      dateClick: function(info) {
        @if(Auth::user()->role === 'admin')
        window.location.href = "{{ route('activities.create') }}?date=" + info.dateStr;
        @endif
      },
      windowResize: function(view) {
        // Update header format on resize
        if (window.innerWidth < 768) {
            calendar.setOption('columnHeaderFormat', { weekday: 'short' });
        } else {
            calendar.setOption('columnHeaderFormat', { weekday: 'long' });
        }
      }
    });
    calendar.render();
  }

  // Handle all day switch
  $('#allDaySwitch').change(function() {
    if ($(this).is(':checked')) {
      $('#startTime, #endTime').prop('disabled', true).val('');
    } else {
      $('#startTime, #endTime').prop('disabled', false);
    }
  });
});
</script>

<!-- Modal Detail Kegiatan -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventDetailModalLabel">Detail Kegiatan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="eventDetailType"></div>
        <h5 id="eventDetailTitle" class="mb-4 font-weight-bold text-dark"></h5>
        
        <div class="card bg-light border-0 mb-3">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-1 text-center"><i class="fe fe-clock text-muted"></i></div>
                    <div class="col-10">
                        <strong class="d-block text-muted small text-uppercase">Waktu</strong>
                        <span id="eventDetailTime" class="text-dark font-weight-bold"></span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-1 text-center"><i class="fe fe-map-pin text-muted"></i></div>
                    <div class="col-10">
                        <strong class="d-block text-muted small text-uppercase">Lokasi</strong>
                        <div id="eventDetailLocation"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-1 text-center"><i class="fe fe-users text-muted"></i></div>
                    <div class="col-10">
                        <strong class="d-block text-muted small text-uppercase">PIC</strong>
                        <div id="eventDetailPic"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="text-muted small text-uppercase font-weight-bold">Keterangan</label>
            <p id="eventDetailDescription" class="mb-0 text-dark"></p>
        </div>
      </div>
      <div class="modal-footer">
        @if(Auth::user()->role === 'admin')
            <a href="#" id="eventDetailEditBtn" class="btn btn-warning">Edit</a>
        @endif
        <a href="#" id="eventDetailLinkBtn" class="btn btn-primary">Detail</a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<br></br>
<br></br>
<br></br>
<br></br>
<br></br>
<br></br>
@endpush


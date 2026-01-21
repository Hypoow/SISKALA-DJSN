@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('css/select2.css') }}">
<style>
    .fc-event {
        cursor: pointer;
        padding: 1px 3px; /* Reduced padding for compact look */
        font-size: 0.8rem; /* Slightly smaller font */
    }
    .fc-toolbar h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #343a40;
    }
    .fc-day-grid-event .fc-content {
        white-space: nowrap; /* Force single line */
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .text-shadow {
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .icon-shape {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .bg-gradient-primary { background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important; }
    .bg-gradient-success { background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important; }
    .bg-gradient-info { background: linear-gradient(87deg, #11cdef 0, #1171ef 100%) !important; }
    .bg-gradient-warning { background: linear-gradient(87deg, #fb6340 0, #fbb140 100%) !important; }
    
    /* Custom Gradient for Internal (Dark Blue) */
    .bg-gradient-dark-blue { background: linear-gradient(87deg, #004085 0, #0056b3 100%) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h2 class="h3 font-weight-bold mb-0 text-dark">Dashboard</h2>
            <p class="text-muted mb-0">Selamat Datang, <span class="text-primary font-weight-bold">{{ Auth::user()->name }}</span>!</p>
        </div>
        <div class="d-none d-md-block text-right">
            <span class="text-muted small text-uppercase font-weight-bold d-block">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats shadow-sm border-0 stat-card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted text-uppercase font-weight-bold mb-0">Total Kegiatan</h6>
                            <span class="h2 font-weight-bold mb-0 text-dark">{{ $totalActivities }}</span>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success mr-2"><i class="fe fe-arrow-up"></i> Bulan Ini</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-shape bg-gradient-primary text-white shadow">
                                <i class="fe fe-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats shadow-sm border-0 stat-card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted text-uppercase font-weight-bold mb-0">Hari Ini</h6>
                            <span class="h2 font-weight-bold mb-0 text-dark">{{ $todayActivities }}</span>
                             <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-nowrap">Agenda aktif</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-shape bg-gradient-success text-white shadow">
                                <i class="fe fe-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats shadow-sm border-0 stat-card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted text-uppercase font-weight-bold mb-0">Internal</h6>
                            <span class="h2 font-weight-bold mb-0 text-dark">{{ $internalActivities }}</span>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-nowrap">Bulan Ini</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-shape bg-gradient-dark-blue text-white shadow">
                                <i class="fe fe-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats shadow-sm border-0 stat-card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted text-uppercase font-weight-bold mb-0">Eksternal</h6>
                            <span class="h2 font-weight-bold mb-0 text-dark">{{ $externalActivities }}</span>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-nowrap">Bulan Ini</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-shape bg-gradient-info text-white shadow">
                                <i class="fe fe-globe"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header bg-white border-0 pt-4 pb-2 d-flex flex-column flex-md-row align-items-center justify-content-between">
                    <h4 class="mb-3 mb-md-0 font-weight-bold text-dark">Kalender & Agenda</h4>
                    
                    <div class="d-flex flex-wrap align-items-center justify-content-center">
                         {{-- Legend --}}
                        <div class="mr-md-4 mb-3 mb-md-0 d-flex">
                             @if(Auth::user()->role === 'Dewan')
                                <div class="badge badge-pill badge-primary mr-2 px-3 py-2" style="background-color: #004085;">Internal</div>
                                <div class="badge badge-pill badge-info text-white px-3 py-2">Eksternal</div>
                             @else
                                <div class="badge badge-pill badge-primary mr-2 px-3 py-2" style="background-color: #004085;">Internal</div>
                                <div class="badge badge-pill badge-info text-white px-3 py-2">Eksternal</div>
                             @endif
                        </div>
                        
                         @if(Auth::user()->role === 'admin')
                        <a href="{{ route('activities.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                            <i class="fe fe-plus-circle mr-2"></i> Buat Kegiatan
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                     <div id='calendar' class="p-3"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Detail Kegiatan -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light border-0">
        <h5 class="modal-title font-weight-bold text-dark" id="eventDetailModalLabel">Detail Kegiatan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div id="eventDetailType" class="mb-2"></div>
        <h4 id="eventDetailTitle" class="mb-4 font-weight-bold text-dark" style="line-height:1.4;"></h4>
        
        <div class="card bg-light border-0 mb-4 rounded-lg">
            <div class="card-body p-3">
                <div class="d-flex mb-3 align-items-start">
                    <div class="mr-3 text-center" style="width: 24px;">
                        <i class="fe fe-clock text-primary fe-16"></i>
                    </div>
                    <div>
                        <small class="text-uppercase text-muted font-weight-bold d-block">Waktu</small>
                        <span id="eventDetailTime" class="text-dark font-weight-bold"></span>
                    </div>
                </div>
                <div class="d-flex mb-3 align-items-start">
                    <div class="mr-3 text-center" style="width: 24px;">
                        <i class="fe fe-map-pin text-danger fe-16"></i>
                    </div>
                    <div>
                        <small class="text-uppercase text-muted font-weight-bold d-block">Lokasi</small>
                        <div id="eventDetailLocation" class="text-dark font-weight-medium"></div>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="mr-3 text-center" style="width: 24px;">
                        <i class="fe fe-users text-info fe-16"></i>
                    </div>
                    <div>
                         <small class="text-uppercase text-muted font-weight-bold d-block mb-1">PIC / Disposisi</small>
                        <div id="eventDetailPic"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mb-0">
            <label class="text-muted small text-uppercase font-weight-bold mb-2">Keterangan Tambahan</label>
            <div class="bg-white p-3 rounded border">
                <p id="eventDetailDescription" class="mb-0 text-secondary"></p>
            </div>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        @if(Auth::user()->role === 'admin')
            <a href="#" id="eventDetailEditBtn" class="btn btn-warning shadow-sm"><i class="fe fe-edit-2 mr-1"></i> Edit</a>
        @endif
        <a href="#" id="eventDetailLinkBtn" class="btn btn-primary shadow-sm"><i class="fe fe-eye mr-1"></i> Lihat Detail</a>
      </div>
    </div>
  </div>
</div>
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
      firstDay: 0, // Sunday
      locale: 'id',
      // Dynamic Header Format based on screen width
      columnHeaderFormat: window.innerWidth < 768 ? { weekday: 'short' } : { weekday: 'long' },
      locales: [{
        code: 'id',
        week: {
          dow: 0, 
          doy: 4 
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
      height: 'auto', // Adjust height automatically
      eventTimeFormat: { 
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
        // Type Badge
        var typeBadge = '';
        if (props.type === 'external') {
            var label = props.organizer_name ? props.organizer_name : 'Eksternal';
            typeBadge = '<span class="badge badge-pill badge-info text-white px-3 py-2">' + label + '</span>';
        } else {
             typeBadge = '<span class="badge badge-pill badge-primary px-3 py-2" style="background-color: #004085;">Internal</span>';
        }
        $('#eventDetailType').html(typeBadge);

        // Location Logic
        // Location Logic
        var locationHtml = '';
        if (props.location_type === 'offline') {
            locationHtml = '<span class="badge badge-secondary mb-1">Offline</span>';
            locationHtml += '<p class="text-dark mb-0"><i class="fe fe-map-pin mr-1 text-muted"></i>' + (props.location || '-') + '</p>';
        } else if (props.location_type === 'online') {
            locationHtml = '<span class="badge badge-secondary mb-1">Online' + (props.media_online ? ' (' + props.media_online + ')' : '') + '</span>';
            
            if (props.meeting_link) {
                var link = props.meeting_link;
                if(link.startsWith('http')) {
                     locationHtml += '<p class="mb-0 mt-1"><a href="' + link + '" target="_blank" class="text-truncate d-block text-primary">' + link + '</a></p>';
                } else {
                     locationHtml += '<p class="mb-0 mt-1">' + link + '</p>';
                }
            }

            if(props.meeting_id || props.passcode) {
                locationHtml += '<div class="mt-1 small text-muted">';
                if (props.meeting_id) {
                    locationHtml += '<strong>ID:</strong> ' + props.meeting_id + '<br>';
                }
                if (props.passcode) {
                    locationHtml += '<strong>Pass:</strong> ' + props.passcode;
                }
                locationHtml += '</div>';
            }
        } else if (props.location_type === 'hybrid') {
            locationHtml = '<span class="badge badge-secondary mb-1">Hybrid</span>';
            
            // Offline part
            locationHtml += '<div class="mb-2">';
            locationHtml += '<small class="text-muted font-weight-bold d-block">Offline:</small>';
            locationHtml += '<p class="text-dark mb-0 pl-2 border-left"><i class="fe fe-map-pin mr-1 text-muted"></i>' + (props.location || '-') + '</p>';
            locationHtml += '</div>';

            // Online part
            locationHtml += '<div class="mb-0">';
            locationHtml += '<small class="text-muted font-weight-bold d-block">Online' + (props.media_online ? ' (' + props.media_online + ')' : '') + ':</small>';
            locationHtml += '<div class="pl-2 border-left">';
            
            if (props.meeting_link) {
                 var link = props.meeting_link;
                if(link.startsWith('http')) {
                     locationHtml += '<p class="mb-1"><a href="' + link + '" target="_blank" class="text-truncate d-block text-primary">' + link + '</a></p>';
                } else {
                     locationHtml += '<p class="mb-1">' + link + '</p>';
                }
            }
            
            if(props.meeting_id || props.passcode) {
                locationHtml += '<div class="small text-muted">';
                if (props.meeting_id) {
                    locationHtml += '<strong>ID:</strong> ' + props.meeting_id + '<br>';
                }
                if (props.passcode) {
                    locationHtml += '<strong>Pass:</strong> ' + props.passcode;
                }
                locationHtml += '</div>';
            }
            locationHtml += '</div></div>';
        } else {
             locationHtml = '-';
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
                    
                    picHtml += '<span class="badge pill ' + badgeClass + ' mr-1 mb-1 px-2 py-1">' + p + '</span>';
                });
            } else {
                picHtml = props.pic;
            }
        } else {
            picHtml = '-';
        }
        $('#eventDetailPic').html(picHtml);

        if (props.description && props.description !== '-') {
             // Basic newline to br if plain text, but if HTML passed assume it's good
             // Since we removed strip_tags, we trust the content.
             // If it's pure markdown, we'd need a parser. But Quill saves HTML.
             $('#eventDetailDescription').html(props.description).addClass('markdown-content').removeClass('text-secondary');
        } else {
             $('#eventDetailDescription').text('-').removeClass('markdown-content').addClass('text-secondary');
        }

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
        if (window.innerWidth < 768) {
            calendar.setOption('columnHeaderFormat', { weekday: 'short' });
        } else {
            calendar.setOption('columnHeaderFormat', { weekday: 'long' });
        }
      }
    });
    calendar.render();
  }
});
</script>
@endpush

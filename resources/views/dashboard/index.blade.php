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
    
    /* Ensure tooltips always appear above full-screen modals */
    .tooltip, .tooltip-inner {
        z-index: 105000 !important;
    }

    .dashboard-event-modal {
        max-width: 760px;
    }

    .dashboard-event-modal .modal-content {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.24);
    }

    .dashboard-event-modal .modal-header {
        align-items: center;
        background: #ffffff;
        border-bottom: 1px solid #e6edf5;
        padding: 1.2rem 1.4rem;
    }

    .dashboard-event-modal .modal-title-wrap {
        align-items: center;
        display: flex;
        min-width: 0;
    }

    .dashboard-event-modal .min-width-0 {
        min-width: 0;
    }

    .dashboard-event-modal .modal-title-icon {
        align-items: center;
        background: #eaf2ff;
        border: 1px solid #d6e5fb;
        border-radius: 8px;
        color: #0b4c9c;
        display: inline-flex;
        flex: 0 0 44px;
        height: 44px;
        justify-content: center;
        margin-right: 0.9rem;
        width: 44px;
    }

    .dashboard-event-modal .modal-eyebrow {
        color: #64748b;
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0;
        line-height: 1.2;
        margin-bottom: 0.2rem;
        text-transform: uppercase;
    }

    .dashboard-event-modal .modal-title {
        color: #0f172a;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.2;
        margin: 0;
    }

    .dashboard-event-modal .close {
        align-items: center;
        background: #f1f5f9;
        border-radius: 999px;
        color: #334155;
        display: inline-flex;
        height: 40px;
        justify-content: center;
        margin: 0;
        opacity: 1;
        padding: 0;
        text-shadow: none;
        width: 40px;
    }

    .dashboard-event-modal .modal-body {
        background: #f8fafc;
        padding: 1.4rem;
    }

    .event-detail-hero {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.25rem;
    }

    .event-detail-type {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-bottom: 0.85rem;
    }

    .event-detail-type .badge {
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0;
        padding: 0.42rem 0.7rem;
    }

    .event-detail-title {
        color: #0f172a;
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.35;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .event-detail-grid {
        display: grid;
        gap: 0.85rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 1rem;
    }

    .event-detail-item {
        align-items: flex-start;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        display: flex;
        min-width: 0;
        padding: 1rem;
    }

    .event-detail-item.event-detail-item-full {
        grid-column: 1 / -1;
    }

    .event-detail-icon {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        flex: 0 0 38px;
        height: 38px;
        justify-content: center;
        margin-right: 0.85rem;
        width: 38px;
    }

    .event-detail-icon-time {
        background: #eff6ff;
        color: #0b63ce;
    }

    .event-detail-icon-location {
        background: #fff1f2;
        color: #e11d48;
    }

    .event-detail-icon-pic {
        background: #ecfeff;
        color: #0891b2;
    }

    .event-detail-label {
        color: #64748b;
        display: block;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
    }

    .event-detail-value {
        color: #0f172a;
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.45;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .event-detail-location-mode {
        border-radius: 999px;
        display: inline-flex;
        font-size: 0.72rem;
        font-weight: 800;
        margin-bottom: 0.45rem;
        padding: 0.3rem 0.55rem;
    }

    .event-detail-location-mode.offline {
        background: #f1f5f9;
        color: #475569;
    }

    .event-detail-location-mode.online {
        background: #e0f2fe;
        color: #0369a1;
    }

    .event-detail-location-mode.hybrid {
        background: #ecfdf5;
        color: #047857;
    }

    .event-detail-meeting {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-top: 0.65rem;
        padding: 0.7rem;
    }

    .event-detail-meeting a {
        font-weight: 700;
        word-break: break-all;
    }

    .event-detail-meeting-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .event-detail-muted {
        color: #64748b;
        font-size: 0.86rem;
    }

    .event-detail-pic-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .event-detail-pic-list .badge {
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 800;
        line-height: 1.1;
        padding: 0.42rem 0.62rem;
    }

    .event-detail-notes {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-top: 1rem;
        padding: 1rem;
    }

    .event-detail-notes-title {
        align-items: center;
        color: #475569;
        display: flex;
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0;
        margin-bottom: 0.65rem;
        text-transform: uppercase;
    }

    .event-detail-notes-title i {
        color: #0b4c9c;
        margin-right: 0.45rem;
    }

    .event-detail-description {
        color: #334155;
        font-size: 0.94rem;
        line-height: 1.6;
        min-height: 44px;
        overflow-wrap: anywhere;
    }

    .event-detail-empty {
        align-items: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        color: #64748b;
        display: flex;
        min-height: 58px;
        padding: 0.9rem;
    }

    .dashboard-event-modal .modal-footer {
        background: #ffffff;
        border-top: 1px solid #e6edf5;
        padding: 1rem 1.4rem;
    }

    .dashboard-event-modal .modal-footer .btn {
        border-radius: 999px;
        font-weight: 800;
        min-width: 132px;
        padding: 0.68rem 1rem;
    }

    .dashboard-event-modal .btn-event-detail {
        background: #0b214a;
        border-color: #071936;
        box-shadow: 0 10px 22px rgba(11, 33, 74, 0.2);
        color: #ffffff;
    }

    .dashboard-event-modal .btn-event-detail:hover {
        background: #071936;
        color: #ffffff;
    }

    .dashboard-event-modal .btn-event-edit {
        background: #f59e0b;
        border-color: #d97706;
        color: #111827;
    }

    @media (max-width: 767.98px) {
        .dashboard-event-modal {
            margin: 0.75rem;
            max-width: none;
        }

        .dashboard-event-modal .modal-header,
        .dashboard-event-modal .modal-body,
        .dashboard-event-modal .modal-footer {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .event-detail-title {
            font-size: 1.15rem;
        }

        .event-detail-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-event-modal .modal-footer {
            align-items: stretch;
            flex-direction: column-reverse;
        }

        .dashboard-event-modal .modal-footer .btn {
            margin: 0.25rem 0;
            width: 100%;
        }
    }
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
                            <span id="dashboard-total-activities" class="h2 font-weight-bold mb-0 text-dark">{{ $totalActivities }}</span>
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
                            <span id="dashboard-today-activities" class="h2 font-weight-bold mb-0 text-dark">{{ $todayActivities }}</span>
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
                            <span id="dashboard-internal-activities" class="h2 font-weight-bold mb-0 text-dark">{{ $internalActivities }}</span>
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
                            <span id="dashboard-external-activities" class="h2 font-weight-bold mb-0 text-dark">{{ $externalActivities }}</span>
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
                             @if(Auth::user()->isDewan())
                                <div class="badge badge-pill badge-primary mr-2 px-3 py-2" style="background-color: #004085;">Internal</div>
                                <div class="badge badge-pill badge-info text-white px-3 py-2">Eksternal</div>
                             @else
                                <div class="badge badge-pill badge-primary mr-2 px-3 py-2" style="background-color: #004085;">Internal</div>
                                <div class="badge badge-pill badge-info text-white px-3 py-2">Eksternal</div>
                             @endif
                        </div>
                        
                         @if(Auth::user()->canManageActivities())
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
  <div class="modal-dialog modal-dialog-centered dashboard-event-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title-wrap">
            <div class="modal-title-icon">
                <i class="fe fe-calendar fe-18"></i>
            </div>
            <div class="min-width-0">
                <span class="modal-eyebrow">Agenda Kalender</span>
                <h5 class="modal-title" id="eventDetailModalLabel">Detail Kegiatan</h5>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fe fe-x fe-18"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <div class="event-detail-hero">
            <div id="eventDetailType" class="event-detail-type"></div>
            <h4 id="eventDetailTitle" class="event-detail-title"></h4>
        </div>
        
        <div class="event-detail-grid">
            <div class="event-detail-item">
                <div class="event-detail-icon event-detail-icon-time">
                    <i class="fe fe-clock fe-16"></i>
                </div>
                <div class="event-detail-value">
                    <span class="event-detail-label">Waktu</span>
                    <span id="eventDetailTime"></span>
                </div>
            </div>

            <div class="event-detail-item">
                <div class="event-detail-icon event-detail-icon-location">
                    <i class="fe fe-map-pin fe-16"></i>
                </div>
                <div class="event-detail-value">
                    <span class="event-detail-label">Lokasi</span>
                    <div id="eventDetailLocation"></div>
                </div>
            </div>

            <div class="event-detail-item event-detail-item-full">
                <div class="event-detail-icon event-detail-icon-pic">
                    <i class="fe fe-users fe-16"></i>
                </div>
                <div class="event-detail-value">
                    <span class="event-detail-label">PIC / Disposisi</span>
                    <div id="eventDetailPic" class="event-detail-pic-list"></div>
                </div>
            </div>
        </div>

        <div class="event-detail-notes">
            <div class="event-detail-notes-title">
                <i class="fe fe-file-text fe-15"></i>
                <span>Keterangan Tambahan</span>
            </div>
            <div id="eventDetailDescription" class="event-detail-description"></div>
        </div>
      </div>
      <div class="modal-footer">
        @if(Auth::user()->canManageActivities())
            <a href="#" id="eventDetailEditBtn" class="btn btn-event-edit"><i class="fe fe-edit-2 mr-1"></i> Edit</a>
        @endif
        <a href="#" id="eventDetailLinkBtn" class="btn btn-event-detail"><i class="fe fe-eye mr-1"></i> Lihat Detail</a>
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
  var calendar = null;

  function updateDashboardStats(summary) {
    if (!summary) {
      return;
    }

    var statMap = {
      totalActivities: 'dashboard-total-activities',
      todayActivities: 'dashboard-today-activities',
      internalActivities: 'dashboard-internal-activities',
      externalActivities: 'dashboard-external-activities'
    };

    Object.keys(statMap).forEach(function(key) {
      var element = document.getElementById(statMap[key]);
      if (element && typeof summary[key] !== 'undefined') {
        element.textContent = summary[key];
      }
    });
  }

  function refreshDashboardState() {
    if (calendar) {
      calendar.refetchEvents();
    }

    var summaryUrl = window.Schedulo?.routes?.dashboardSummary;
    if (!summaryUrl) {
      return;
    }

    fetch(summaryUrl, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Gagal memuat ringkasan dashboard.');
        }

        return response.json();
      })
      .then(updateDashboardStats)
      .catch(function() {
      });
  }

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>"']/g, function(character) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[character];
    });
  }

  function nl2br(value) {
    return escapeHtml(value).replace(/\r?\n/g, '<br>');
  }

  function formatEventDateTime(start, end) {
    if (!start) {
      return '-';
    }

    var dateText = start.toLocaleDateString('id-ID', {
      weekday: 'long',
      day: '2-digit',
      month: 'long',
      year: 'numeric'
    });
    var startText = start.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });

    if (!end) {
      return dateText + ', ' + startText + ' WIB';
    }

    var endText = end.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    });

    return dateText + ', ' + startText + ' - ' + endText + ' WIB';
  }

  function buildTypeBadges(props) {
    if (props.type === 'external') {
      var organizer = props.organizer_name ? escapeHtml(props.organizer_name) : '';
      var organizerBadge = organizer
        ? '<span class="badge badge-light border text-dark">' + organizer + '</span>'
        : '';

      return '<span class="badge badge-info text-white">Eksternal</span>' + organizerBadge;
    }

    return '<span class="badge badge-primary text-white" style="background-color: #004085;">Internal</span>';
  }

  function locationModeBadge(className, label) {
    return '<span class="event-detail-location-mode ' + className + '">' + label + '</span>';
  }

  function buildMeetingHtml(props) {
    var detailRows = [];

    if (props.meeting_id) {
      detailRows.push('<span><strong>ID:</strong> ' + escapeHtml(props.meeting_id) + '</span>');
    }

    if (props.passcode) {
      detailRows.push('<span><strong>Passcode:</strong> ' + escapeHtml(props.passcode) + '</span>');
    }

    if (!props.meeting_link && detailRows.length === 0) {
      return '';
    }

    var html = '<div class="event-detail-meeting">';

    if (props.meeting_link) {
      var link = String(props.meeting_link);
      if (link.startsWith('http')) {
        html += '<a href="' + escapeHtml(link) + '" target="_blank" rel="noopener" class="d-block mb-1">' + escapeHtml(link) + '</a>';
      } else {
        html += '<div class="mb-1">' + escapeHtml(link) + '</div>';
      }
    }

    if (detailRows.length > 0) {
      html += '<div class="event-detail-muted event-detail-meeting-meta">' + detailRows.join('') + '</div>';
    }

    html += '</div>';

    return html;
  }

  function buildLocationHtml(props) {
    var media = props.media_online ? ' (' + escapeHtml(props.media_online) + ')' : '';
    var meetingHtml = buildMeetingHtml(props);

    if (props.location_type === 'offline') {
      return locationModeBadge('offline', 'Offline')
        + '<div>' + nl2br(props.location || '-') + '</div>';
    }

    if (props.location_type === 'online') {
      return locationModeBadge('online', 'Online' + media)
        + (meetingHtml || '<div class="event-detail-muted">Detail media belum tersedia.</div>');
    }

    if (props.location_type === 'hybrid') {
      return locationModeBadge('hybrid', 'Hybrid')
        + '<div class="mb-2">' + nl2br(props.location || '-') + '</div>'
        + (meetingHtml || '<div class="event-detail-muted">Detail media online belum tersedia.</div>');
    }

    return '<span class="event-detail-muted">-</span>';
  }

  function picBadgeClass(label) {
    if (label.includes('Komjakum') || label.includes('Kebijakan')) {
      return 'badge-komjakum';
    }
    if (label.includes('PME') || label.includes('Monitoring')) {
      return 'badge-pme';
    }
    if (label.includes('Sekretariat') || label.includes('Sekretaris')) {
      return 'badge-sekretariat';
    }
    if (label.includes('Ketua')) {
      return 'badge-ketua';
    }
    if (label.includes('Anggota')) {
      return 'badge-djsn';
    }

    return 'badge-info';
  }

  function buildPicHtml(props) {
    if (!props.pic || (Array.isArray(props.pic) && props.pic.length === 0)) {
      return '<span class="event-detail-muted">Belum ada PIC / disposisi.</span>';
    }

    if (!Array.isArray(props.pic)) {
      return escapeHtml(props.pic);
    }

    return props.pic.map(function(p) {
      var label = String(p || '');
      var tooltipContent = props.pic_details && props.pic_details[p] ? props.pic_details[p] : '';
      var tooltipAttrs = tooltipContent
        ? ' data-toggle="tooltip" data-html="true" data-placement="top" title="' + escapeHtml(tooltipContent) + '"'
        : '';

      return '<span class="badge pill ' + picBadgeClass(label) + '"' + tooltipAttrs + '>'
        + escapeHtml(label)
        + '</span>';
    }).join('');
  }

  if (calendarEl) {
    calendar = new FullCalendar.Calendar(calendarEl, {
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
      events: window.Schedulo?.routes?.dashboardEvents || '{{ route("dashboard.events.get") }}',
      eventClick: function(info) {
        $('#eventDetailTitle').text(info.event.title);

        var props = info.event.extendedProps;

        $('#eventDetailTime').text(formatEventDateTime(info.event.start, info.event.end));
        $('#eventDetailType').html(buildTypeBadges(props));
        $('#eventDetailLocation').html(buildLocationHtml(props));
        $('#eventDetailPic').html(buildPicHtml(props));

        if (props.description && String(props.description).trim() !== '-') {
             $('#eventDetailDescription')
                .html(props.description)
                .addClass('markdown-content')
                .removeClass('event-detail-empty text-secondary');
        } else {
             $('#eventDetailDescription')
                .html('<span>Belum ada keterangan tambahan.</span>')
                .removeClass('markdown-content')
                .addClass('event-detail-empty');
        }

        // Buttons
        var editBtn = $('#eventDetailEditBtn');
        var detailBtn = $('#eventDetailLinkBtn');
        
        // Set Detail Link
        detailBtn.attr('href', '/activities/' + info.event.id);

        // Set Edit Link (Admin Only)
        @if(Auth::user()->canManageActivities())
            editBtn.attr('href', '/activities/' + info.event.id + '/edit');
            editBtn.show();
        @else
            editBtn.hide();
        @endif

        // Show Modal
        $('#eventDetailModal').modal('show');
        
        // Re-init tooltips inside modal
        setTimeout(function() {
             $('#eventDetailPic [data-toggle="tooltip"]').tooltip('dispose').tooltip({
                 html: true,
                 container: 'body'
             });
        }, 300);
      },
      dateClick: function(info) {
        @if(Auth::user()->canManageActivities())
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

  window.addEventListener('schedulo:realtime', function(event) {
    if (!window.scheduloRealtime?.matchesAnyTopic(event.detail, ['activities', 'dashboard'])) {
      return;
    }

    window.scheduloRealtime.queueRefresh('dashboard-state', refreshDashboardState, {
      delay: 300
    });
  });
});
</script>
@endpush

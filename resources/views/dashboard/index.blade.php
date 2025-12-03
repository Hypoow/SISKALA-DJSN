@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('css/select2.css') }}">
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-12">
    <div class="row align-items-center my-3">
      <div class="col">
        <h2 class="page-title">Kalender Kegiatan</h2>
      </div>
      <div class="col-auto">
        @if(Auth::user()->role === 'admin')
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#eventModal">
          <span class="fe fe-plus fe-16 mr-3"></span>Kegiatan Baru
        </button>
        @endif
      </div>
    </div>
    <div id='calendar'></div>
    
    <!-- Modal Kegiatan Baru -->
    @if(Auth::user()->role === 'admin')
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="eventModalLabel">Kegiatan Baru</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="eventForm" method="POST" action="{{ route('dashboard.events.store') }}">
            @csrf
            <div class="modal-body p-4">
              <div class="form-group">
                <label for="eventTitle" class="col-form-label">Judul</label>
                <input type="text" class="form-control" id="eventTitle" name="title" placeholder="Masukkan judul kegiatan" required>
              </div>
              <div class="form-group">
                <label for="eventNote" class="col-form-label">Keterangan</label>
                <textarea class="form-control" id="eventNote" name="description" placeholder="Tambahkan keterangan untuk kegiatan"></textarea>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="startDate">Tanggal Mulai</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="fe fe-calendar fe-16"></span></div>
                    </div>
                    <input type="date" class="form-control" id="startDate" name="start_date" required>
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label for="endDate">Tanggal Selesai</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="fe fe-calendar fe-16"></span></div>
                    </div>
                    <input type="date" class="form-control" id="endDate" name="end_date">
                  </div>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="startTime">Waktu Mulai</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="fe fe-clock fe-16"></span></div>
                    </div>
                    <input type="time" class="form-control" id="startTime" name="start_time">
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label for="endTime">Waktu Selesai</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="fe fe-clock fe-16"></span></div>
                    </div>
                    <input type="time" class="form-control" id="endTime" name="end_time">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="allDaySwitch" name="all_day">
                <label class="custom-control-label" for="allDaySwitch">Sepanjang Hari</label>
              </div>
              <button type="submit" class="btn mb-2 btn-primary">Simpan Kegiatan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @endif
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
      firstDay: 1, // Monday
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
        alert('Kegiatan: ' + info.event.title);
      },
      dateClick: function(info) {
        @if(Auth::user()->role === 'admin')
        $('#startDate').val(info.dateStr);
        $('#eventModal').modal('show');
        @endif
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
@endpush


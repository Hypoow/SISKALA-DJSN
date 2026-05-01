<div style="overflow: visible;">
    <div>
        <!-- Stats Widgets -->
        <div class="row mb-4">
            <!-- Total -->
            <div class="col-12 col-sm-6 col-xl-3 fade-in delay-1 mb-3 mb-xl-0">
                <div class="card shadow border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="circle circle-lg bg-primary mr-3">
                                <i class="fe fe-list fe-24 text-white"></i>
                            </span>
                            <div>
                                <h3 class="h2 mb-0 font-bold text-dark">{{ $stats['total'] }}</h3>
                                <p class="text-muted mb-0">Total Tindak Lanjut</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selesai -->
            <div class="col-12 col-sm-6 col-xl-3 fade-in delay-2 mb-3 mb-xl-0">
                <div class="card shadow border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="circle circle-lg bg-success mr-3">
                                <i class="fe fe-check-circle fe-24 text-white"></i>
                            </span>
                            <div>
                                <h3 class="h2 mb-0 font-bold text-dark">{{ $stats['completed'] }}</h3>
                                <p class="text-muted mb-0">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- On Progress -->
            <div class="col-12 col-sm-6 col-xl-3 fade-in delay-3 mb-3 mb-sm-0">
                <div class="card shadow border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="circle circle-lg bg-warning mr-3">
                                <i class="fe fe-loader fe-24 text-white"></i>
                            </span>
                            <div>
                                <h3 class="h2 mb-0 font-bold text-dark">{{ $stats['progress'] }}</h3>
                                <p class="text-muted mb-0">On Progress</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="col-12 col-sm-6 col-xl-3 fade-in delay-4">
                <div class="card shadow border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="circle circle-lg bg-secondary mr-3">
                                <i class="fe fe-clock fe-24 text-white"></i>
                            </span>
                            <div>
                                <h3 class="h2 mb-0 font-bold text-dark">{{ $stats['pending'] }}</h3>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow border-0 rounded-lg mb-0 fade-in delay-3" style="overflow: visible;">
            <!-- Card Header -->
            <div class="card-header bg-primary text-white p-4"
                style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1 text-white font-weight-bold text-uppercase"
                            style="letter-spacing: 1px;">Monitoring Tindak Lanjut</h5>
                        <p class="mb-0 text-white-50 small">Pantau progres dan status arahan kegiatan</p>
                    </div>
                    <div style="width: 300px;">
                        <div class="input-group input-group-merge input-group-premium bg-white shadow-sm"
                            style="border-radius: 20px; overflow: hidden;">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="form-control border-0 pl-4 py-2" placeholder="Cari..."
                                style="font-size: 0.9rem;">
                            <div class="input-group-append">
                                <div class="input-group-text border-0 bg-white pr-4"><i
                                        class="fe fe-search text-muted"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toolbar & Filters -->
            <style>
                .custom-dropdown-menu {
                    padding: 0.5rem !important;
                    border: 1px solid #edf2f7 !important;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                    border-radius: 0.5rem !important;
                }

                .custom-dropdown-item {
                    border-radius: 0.375rem !important;
                    padding: 0.5rem 1rem !important;
                    color: #4a5568 !important;
                    font-size: 0.95rem;
                    transition: all 0.2s ease-in-out;
                    margin-bottom: 2px;
                    background-color: transparent;
                }

                .custom-dropdown-item:last-child {
                    margin-bottom: 0;
                }

                .custom-dropdown-item:hover,
                .custom-dropdown-item:focus {
                    background-color: #f7fafc !important;
                    color: #2b6cb0 !important;
                    transform: translateX(4px);
                }

                .custom-dropdown-item.bg-primary {
                    background-color: #0052cc !important;
                    color: white !important;
                    transform: none;
                }
            </style>
            <div class="bg-light border-bottom p-3" style="position: relative; z-index: 100; overflow: visible;">
                <div class="row align-items-center mx-n1" style="overflow: visible;">

                    <!-- Year -->
                    <div class="col-6 col-md-2 px-1 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                        <div class="position-relative">
                            <button type="button" @click="open = !open"
                                class="btn bg-white w-100 shadow-sm text-center d-flex align-items-center justify-content-center px-1 px-md-2 rounded-pill"
                                style="border: 1px solid #e2e8f0; height: 38px;">
                                <span class="text-truncate font-weight-bold text-dark" style="font-size: 0.9rem;"
                                    x-text="$wire.year"></span>
                            </button>
                            <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1"
                                :class="{ 'd-block': open }" x-show="open" x-transition
                                style="display: none; max-height: 250px; overflow-y: auto; position: absolute; z-index: 1060;">
                                @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                                    <button type="button"
                                        class="dropdown-item custom-dropdown-item text-center w-100 border-0"
                                        :class="{ 'bg-primary text-white': $wire.year == {{ $y }} }"
                                        @click="$wire.set('year', '{{ $y }}'); open = false">{{ $y }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Month -->
                    <div class="col-6 col-md-2 px-1 mb-2 mb-md-0"
                        x-data="{ open: false, getMonthName(m) { return m ? ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][m] : 'Semua Bulan'; } }"
                        @click.away="open = false">
                        <div class="position-relative">
                            <button type="button" @click="open = !open"
                                class="btn bg-white w-100 shadow-sm text-left d-flex align-items-center justify-content-between px-3 rounded-pill"
                                style="border: 1px solid #e2e8f0; height: 38px;">
                                <span class="text-truncate font-weight-bold text-dark" style="font-size: 0.9rem;"
                                    x-text="getMonthName($wire.month)">Semua Bulan</span>
                                <i class="fe fe-chevron-down ml-1 text-muted"
                                    :style="open ? 'transform: rotate(180deg);' : ''"
                                    style="transition: transform 0.2s;"></i>
                            </button>
                            <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1"
                                :class="{ 'd-block': open }" x-show="open" x-transition
                                style="display: none; max-height: 300px; overflow-y: auto; position: absolute; z-index: 1060;">
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '' }"
                                    @click="$wire.set('month', ''); open = false">Semua Bulan</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '1' }"
                                    @click="$wire.set('month', '1'); open = false">Januari</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '2' }"
                                    @click="$wire.set('month', '2'); open = false">Februari</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '3' }"
                                    @click="$wire.set('month', '3'); open = false">Maret</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '4' }"
                                    @click="$wire.set('month', '4'); open = false">April</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '5' }"
                                    @click="$wire.set('month', '5'); open = false">Mei</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '6' }"
                                    @click="$wire.set('month', '6'); open = false">Juni</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '7' }"
                                    @click="$wire.set('month', '7'); open = false">Juli</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '8' }"
                                    @click="$wire.set('month', '8'); open = false">Agustus</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '9' }"
                                    @click="$wire.set('month', '9'); open = false">September</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '10' }"
                                    @click="$wire.set('month', '10'); open = false">Oktober</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '11' }"
                                    @click="$wire.set('month', '11'); open = false">November</button>
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.month == '12' }"
                                    @click="$wire.set('month', '12'); open = false">Desember</button>
                            </div>
                        </div>
                    </div>

                    <!-- Topic -->
                    <div class="col-12 col-md-2 px-1 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                        <div class="position-relative">
                            <button type="button" @click="open = !open"
                                class="btn bg-white w-100 shadow-sm text-left d-flex align-items-center justify-content-between px-3 rounded-pill"
                                style="border: 1px solid #e2e8f0; height: 38px;">
                                <span class="text-truncate font-weight-bold text-dark" style="font-size: 0.9rem;"
                                    x-text="$wire.topic ? $wire.topic : 'Semua Topik'">Semua Topik</span>
                                <i class="fe fe-chevron-down ml-1 text-muted"
                                    :style="open ? 'transform: rotate(180deg);' : ''"
                                    style="transition: transform 0.2s;"></i>
                            </button>
                            <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1"
                                :class="{ 'd-block': open }" x-show="open" x-transition
                                style="display: none; max-height: 300px; overflow-y: auto; position: absolute; z-index: 1060;">
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.topic == '' }"
                                    @click="$wire.set('topic', ''); open = false">Semua Topik</button>
                                @foreach($existingTopics as $t)
                                    <button type="button"
                                        class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                        :class="{ 'bg-primary text-white': $wire.topic == '{{ $t }}' }"
                                        @click="$wire.set('topic', '{{ $t }}'); open = false">{{ $t }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- PIC -->
                    <div class="col-12 col-md-3 px-1 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false">
                        <div class="position-relative">
                            <button type="button" @click="open = !open"
                                class="btn bg-white w-100 shadow-sm text-left d-flex align-items-center justify-content-between px-3 rounded-pill"
                                style="border: 1px solid #e2e8f0; height: 38px;">
                                <span class="text-truncate font-weight-bold text-dark" style="font-size: 0.9rem;"
                                    x-text="$wire.pic ? $wire.pic : 'Semua PIC'">Semua PIC</span>
                                <i class="fe fe-chevron-down ml-1 text-muted"
                                    :style="open ? 'transform: rotate(180deg);' : ''"
                                    style="transition: transform 0.2s;"></i>
                            </button>
                            <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1"
                                :class="{ 'd-block': open }" x-show="open" x-transition
                                style="display: none; max-height: 300px; overflow-y: auto; position: absolute; z-index: 1060;">
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.pic == '' }"
                                    @click="$wire.set('pic', ''); open = false">Semua PIC</button>
                                @foreach($existingPics as $p)
                                    <button type="button"
                                        class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                        :class="{ 'bg-primary text-white': $wire.pic == '{{ $p }}' }"
                                        @click="$wire.set('pic', '{{ $p }}'); open = false">{{ $p }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-3 px-1 mb-0"
                        x-data="{ open: false, statuses: {{ json_encode($statusLabels) }} }" @click.away="open = false">
                        <div class="position-relative">
                            <button type="button" @click="open = !open"
                                class="btn bg-white w-100 shadow-sm text-left d-flex align-items-center justify-content-between px-3 rounded-pill"
                                style="border: 1px solid #e2e8f0; height: 38px;">
                                <span class="text-truncate font-weight-bold text-dark" style="font-size: 0.9rem;"
                                    x-text="$wire.status === 'all' ? 'Semua Status' : (statuses[$wire.status] || 'Status')">Semua
                                    Status</span>
                                <i class="fe fe-chevron-down ml-1 text-muted"
                                    :style="open ? 'transform: rotate(180deg);' : ''"
                                    style="transition: transform 0.2s;"></i>
                            </button>
                            <div class="dropdown-menu custom-dropdown-menu shadow-lg w-100 rounded-lg mt-1 dropdown-menu-md-right"
                                :class="{ 'd-block': open }" x-show="open" x-transition
                                style="display: none; max-height: 300px; overflow-y: auto; position: absolute; z-index: 1060;">
                                <button type="button"
                                    class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                    :class="{ 'bg-primary text-white': $wire.status == 'all' }"
                                    @click="$wire.set('status', 'all'); open = false">Semua Status</button>
                                @foreach($statusLabels as $key => $label)
                                    <button type="button"
                                        class="dropdown-item custom-dropdown-item text-left w-100 border-0"
                                        :class="{ 'bg-primary text-white': $wire.status == '{{ $key }}' }"
                                        @click="$wire.set('status', '{{ $key }}'); open = false">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Data Table -->
            <div class="card-body p-0" style="position: relative; z-index: 1;">
                <div class="table-responsive">
                    <table class="table table-striped table-custom-border mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4"
                                    style="width: 20%;">Agenda & Waktu</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    style="width: 10%;">PIC / Komisi</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    style="width: 30%;">Arahan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    style="width: 20%;">Progres / Tindak Lanjut</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                                    style="width: 10%;">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center"
                                    style="width: 10%;">Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groupedActivities as $month => $monthActivities)
                                <tr class="bg-white">
                                    <td colspan="6" class="py-2 pl-4 border-bottom border-top">
                                        <h6 class="mb-0 text-primary font-weight-bold text-uppercase"
                                            style="letter-spacing: 1px; font-size: 0.8rem;">
                                            <i class="fe fe-calendar mr-2"></i>{{ $month }}
                                        </h6>
                                    </td>
                                </tr>
                                @foreach($monthActivities as $activity)
                                    @php
                                        $followups = $activity->followups;
                                        $rowCount = $followups->isNotEmpty() ? $followups->count() : 1;
                                        // Group by PIC for rendering
                                        $followupsByPic = $followups->groupBy('pic');
                                    @endphp

                                    @if($followups->isEmpty())
                                        <tr style="border-top: 1px solid #e2e8f0;">
                                            <!-- Activity Info -->
                                            <td class="align-top pl-4 py-3 bg-white"
                                                style="border-left: 4px solid {{ $activity->type == 'internal' ? '#004085' : '#17a2b8' }} !important; width: 20%;">
                                                <div class="position-relative">
                                                    <!-- Row 1: Activity Name -->
                                                    <div class="mb-2">
                                                        <a href="{{ route('activities.show', $activity->id) }}"
                                                            class="text-dark font-weight-bold text-decoration-none"
                                                            title="Lihat Detail">{{ $activity->name }}</a>
                                                    </div>

                                                    <!-- Row 2: Time -->
                                                    <div class="mb-2">
                                                        <small class="text-muted font-weight-bold" style="font-size: 0.9em;">
                                                            <i
                                                                class="fe fe-clock mr-1"></i>{{ $activity->date_time->translatedFormat('d F, H:i') }}
                                                        </small>
                                                    </div>

                                                    @php
                                                        $firstWithTopic = $activity->followups->first(function ($val) {
                                                            return !empty($val->topic); });
                                                        $topicName = $firstWithTopic ? $firstWithTopic->topic : null;
                                                    @endphp

                                                    <!-- Row 3: Topic & Type -->
                                                    <div class="d-flex align-items-center mb-2 flex-wrap">
                                                        <!-- Topic -->
                                                        @if($topicName)
                                                            @php $tColor = $this->getTopicColor($topicName); @endphp
                                                            <span class="badge badge-pill px-2 py-1 mr-2 mb-1"
                                                                style="background-color: {{ $tColor }}20; border: 1px solid {{ $tColor }}; color: {{ $tColor }}; font-weight: 600; font-size: 0.7em;">
                                                                <i class="fe fe-tag mr-1"></i>{{ $topicName }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="badge badge-pill badge-light px-2 py-1 mr-2 mb-1 text-muted border"
                                                                style="font-size: 0.7em;">
                                                                No Topic
                                                            </span>
                                                        @endif

                                                        <span class="text-muted mr-2 mb-1" style="font-size: 0.8em;">-</span>

                                                        <!-- Type -->
                                                        <div class="mb-1">
                                                            @if($activity->type == 'internal')
                                                                <span class="badge badge-primary px-2 py-1"
                                                                    style="font-size: 0.7em; background-color: #004085;">Internal</span>
                                                            @else
                                                                <span class="badge badge-info text-white px-2 py-1"
                                                                    style="font-size: 0.7em;">{{ $activity->organizer_name ?? 'Eksternal' }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Attachments (Row 4) -->
                                                    <div>
                                                        @if($activity->minutes_path || $activity->attachment_path || $activity->assignment_letter_path || $activity->moms->isNotEmpty() || $activity->materials->isNotEmpty())
                                                            <div class="dropdown">
                                                                <button
                                                                    class="btn btn-sm d-flex justify-content-between align-items-center w-100 rounded-pill px-3 py-1 mt-1 attachment-btn text-muted"
                                                                    type="button" data-toggle="dropdown" aria-expanded="false"
                                                                    style="font-size: 0.75rem; border: 1px solid #e2e8f0; background: #f8f9fc; transition: all 0.2s;">
                                                                    <span><i class="fe fe-paperclip mr-1 text-primary"></i> <span
                                                                            class="font-weight-bold">Lampiran</span></span>
                                                                    <i class="fe fe-chevron-down ml-2" style="font-size: 0.7rem;"></i>
                                                                </button>
                                                                <div class="dropdown-menu shadow-lg border-0 mt-2"
                                                                    style="border-radius: 0.8rem; min-width: 220px; padding: 0.5rem;">
                                                                    @if($activity->minutes_path)
                                                                        <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                            href="{{ Storage::url($activity->minutes_path) }}"
                                                                            target="_blank" style="transition: background 0.2s;">
                                                                            <div class="icon-circle bg-primary-light mr-2 d-flex align-items-center justify-content-center"
                                                                                style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe;">
                                                                                <i class="fe fe-file-text text-primary"
                                                                                    style="font-size: 0.85rem;"></i>
                                                                            </div>
                                                                            <span class="text-dark font-weight-bold"
                                                                                style="font-size: 0.8rem;">Notulensi</span>
                                                                        </a>
                                                                    @endif
                                                                    @if($activity->attachment_path)
                                                                        <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                            href="{{ Storage::url($activity->attachment_path) }}"
                                                                            target="_blank" style="transition: background 0.2s;">
                                                                            <div class="icon-circle bg-primary-light mr-2 d-flex align-items-center justify-content-center"
                                                                                style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe;">
                                                                                <i class="fe fe-paperclip text-primary"
                                                                                    style="font-size: 0.85rem;"></i>
                                                                            </div>
                                                                            <span class="text-dark font-weight-bold"
                                                                                style="font-size: 0.8rem;">Surat Undangan</span>
                                                                        </a>
                                                                    @endif
                                                                    @if($activity->assignment_letter_path)
                                                                        <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                            href="{{ Storage::url($activity->assignment_letter_path) }}"
                                                                            target="_blank" style="transition: background 0.2s;">
                                                                            <div class="icon-circle bg-primary-light mr-2 d-flex align-items-center justify-content-center"
                                                                                style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe;">
                                                                                <i class="fe fe-file-text text-primary"
                                                                                    style="font-size: 0.85rem;"></i>
                                                                            </div>
                                                                            <span class="text-dark font-weight-bold"
                                                                                style="font-size: 0.8rem;">Surat Tugas</span>
                                                                        </a>
                                                                    @endif
                                                                    @foreach($activity->moms as $mom)
                                                                        <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                            href="{{ Storage::url($mom->file_path) }}" target="_blank"
                                                                            style="transition: background 0.2s;">
                                                                            <div class="icon-circle bg-success-light mr-2 d-flex align-items-center justify-content-center"
                                                                                style="width: 28px; height: 28px; border-radius: 50%; background: #dcfce7;">
                                                                                <i class="fe fe-file-text text-success"
                                                                                    style="font-size: 0.85rem;"></i>
                                                                            </div>
                                                                            <span class="text-dark font-weight-bold"
                                                                                style="font-size: 0.8rem;"
                                                                                title="{{ $mom->title ?? basename($mom->file_path) }}">{{ \Illuminate\Support\Str::limit($mom->title ?? basename($mom->file_path), 20) }}</span>
                                                                        </a>
                                                                    @endforeach
                                                                    @foreach($activity->materials as $mat)
                                                                        <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                            href="{{ Storage::url($mat->file_path) }}" target="_blank"
                                                                            style="transition: background 0.2s;">
                                                                            <div class="icon-circle bg-info-light mr-2 d-flex align-items-center justify-content-center"
                                                                                style="width: 28px; height: 28px; border-radius: 50%; background: #e0f6ff;">
                                                                                <i class="fe fe-book-open text-info"
                                                                                    style="font-size: 0.85rem;"></i>
                                                                            </div>
                                                                            <span class="text-dark font-weight-bold"
                                                                                style="font-size: 0.8rem;"
                                                                                title="{{ $mat->title ?? basename($mat->file_path) }}">{{ \Illuminate\Support\Str::limit($mat->title ?? basename($mat->file_path), 20) }}</span>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td colspan="5" class="text-center text-muted small font-italic py-3 align-middle">
                                                Belum ada tindak lanjut.
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($followupsByPic as $pic => $items)
                                            @foreach($items as $item)
                                                <tr id="followup-row-{{ $item->id }}"
                                                    style="{{ ($loop->parent->first && $loop->first) ? 'border-top: 2px solid #6c757d;' : '' }}">
                                                    <!-- Activity Info (Rowspan for ALL followups) -->
                                                    @if($loop->parent->first && $loop->first)
                                                        <td rowspan="{{ $rowCount }}" class="align-top pl-4 py-3"
                                                            style="border-left: 2px solid {{ $activity->type == 'internal' ? '#004085' : '#17a2b8' }}; background-color: #fff; width: 20%; border-right: 2px solid rgba(184, 194, 204, 1);">
                                                            <div class="position-relative">
                                                                <!-- Row 1: Activity Name -->
                                                                <div class="mb-2">
                                                                    <a href="{{ route('activities.show', $activity->id) }}"
                                                                        class="text-dark font-weight-bold text-decoration-none"
                                                                        title="Lihat Detail">{{ $activity->name }}</a>
                                                                </div>

                                                                <!-- Row 2: Time -->
                                                                <div class="mb-2">
                                                                    <small class="text-muted font-weight-bold" style="font-size: 0.9em;">
                                                                        <i
                                                                            class="fe fe-clock mr-1"></i>{{ $activity->date_time->translatedFormat('d F, H:i') }}
                                                                    </small>
                                                                </div>

                                                                @php
                                                                    $firstWithTopic = $activity->followups->first(function ($val) {
                                                                        return !empty($val->topic); });
                                                                    $topicName = $firstWithTopic ? $firstWithTopic->topic : null;
                                                                @endphp

                                                                <!-- Row 3: Topic & Type -->
                                                                <div class="d-flex align-items-center mb-2 flex-wrap">
                                                                    <!-- Topic -->
                                                                    @if($topicName)
                                                                        @php $tColor = $this->getTopicColor($topicName); @endphp
                                                                        <span class="badge badge-pill px-2 py-1 mr-2 mb-1"
                                                                            style="background-color: {{ $tColor }}20; border: 1px solid {{ $tColor }}; color: {{ $tColor }}; font-weight: 600; font-size: 0.7em;">
                                                                            <i class="fe fe-tag mr-1"></i>{{ $topicName }}
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="badge badge-pill badge-light px-2 py-1 mr-2 mb-1 text-muted border"
                                                                            style="font-size: 0.7em;">
                                                                            No Topic
                                                                        </span>
                                                                    @endif

                                                                    <span class="text-muted mr-2 mb-1" style="font-size: 0.8em;">-</span>

                                                                    <!-- Type -->
                                                                    <div class="mb-1">
                                                                        @if($activity->type == 'internal')
                                                                            <span class="badge badge-primary px-2 py-1"
                                                                                style="font-size: 0.7em; background-color: #004085;">Internal</span>
                                                                        @else
                                                                            <span class="badge badge-info text-white px-2 py-1"
                                                                                style="font-size: 0.7em;">{{ $activity->organizer_name ?? 'Eksternal' }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <!-- Attachments (Row 4) -->
                                                                <div>
                                                                    @if($activity->minutes_path || $activity->attachment_path || $activity->assignment_letter_path || $activity->moms->isNotEmpty() || $activity->materials->isNotEmpty())
                                                                        <div class="dropdown">
                                                                            <button
                                                                                class="btn btn-sm d-flex justify-content-between align-items-center w-100 rounded-pill px-3 py-1 mt-1 attachment-btn text-muted"
                                                                                type="button" data-toggle="dropdown" aria-expanded="false"
                                                                                style="font-size: 0.75rem; border: 1px solid #e2e8f0; background: #f8f9fc; transition: all 0.2s;">
                                                                                <span><i class="fe fe-paperclip mr-1 text-primary"></i> <span
                                                                                        class="font-weight-bold">Lampiran</span></span>
                                                                                <i class="fe fe-chevron-down ml-2" style="font-size: 0.7rem;"></i>
                                                                            </button>
                                                                            <div class="dropdown-menu shadow-lg border-0 mt-2"
                                                                                style="border-radius: 0.8rem; min-width: 220px; padding: 0.5rem;">
                                                                                @if($activity->minutes_path)
                                                                                    <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                                        href="{{ Storage::url($activity->minutes_path) }}"
                                                                                        target="_blank" style="transition: background 0.2s;">
                                                                                        <div class="icon-circle bg-primary-light mr-2 d-flex align-items-center justify-content-center"
                                                                                            style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe;">
                                                                                            <i class="fe fe-file-text text-primary"
                                                                                                style="font-size: 0.85rem;"></i>
                                                                                        </div>
                                                                                        <span class="text-dark font-weight-bold"
                                                                                            style="font-size: 0.8rem;">Notulensi</span>
                                                                                    </a>
                                                                                @endif
                                                                                @if($activity->attachment_path)
                                                                                    <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                                        href="{{ Storage::url($activity->attachment_path) }}"
                                                                                        target="_blank" style="transition: background 0.2s;">
                                                                                        <div class="icon-circle bg-primary-light mr-2 d-flex align-items-center justify-content-center"
                                                                                            style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe;">
                                                                                            <i class="fe fe-paperclip text-primary"
                                                                                                style="font-size: 0.85rem;"></i>
                                                                                        </div>
                                                                                        <span class="text-dark font-weight-bold"
                                                                                            style="font-size: 0.8rem;">Surat Undangan</span>
                                                                                    </a>
                                                                                @endif
                                                                                @if($activity->assignment_letter_path)
                                                                                    <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                                        href="{{ Storage::url($activity->assignment_letter_path) }}"
                                                                                        target="_blank" style="transition: background 0.2s;">
                                                                                        <div class="icon-circle bg-primary-light mr-2 d-flex align-items-center justify-content-center"
                                                                                            style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe;">
                                                                                            <i class="fe fe-file-text text-primary"
                                                                                                style="font-size: 0.85rem;"></i>
                                                                                        </div>
                                                                                        <span class="text-dark font-weight-bold"
                                                                                            style="font-size: 0.8rem;">Surat Tugas</span>
                                                                                    </a>
                                                                                @endif
                                                                                @foreach($activity->moms as $mom)
                                                                                    <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                                        href="{{ Storage::url($mom->file_path) }}" target="_blank"
                                                                                        style="transition: background 0.2s;">
                                                                                        <div class="icon-circle bg-success-light mr-2 d-flex align-items-center justify-content-center"
                                                                                            style="width: 28px; height: 28px; border-radius: 50%; background: #dcfce7;">
                                                                                            <i class="fe fe-file-text text-success"
                                                                                                style="font-size: 0.85rem;"></i>
                                                                                        </div>
                                                                                        <span class="text-dark font-weight-bold"
                                                                                            style="font-size: 0.8rem;"
                                                                                            title="{{ $mom->title ?? basename($mom->file_path) }}">{{ \Illuminate\Support\Str::limit($mom->title ?? basename($mom->file_path), 20) }}</span>
                                                                                    </a>
                                                                                @endforeach
                                                                                @foreach($activity->materials as $mat)
                                                                                    <a class="dropdown-item rounded d-flex align-items-center py-2 mb-1"
                                                                                        href="{{ Storage::url($mat->file_path) }}" target="_blank"
                                                                                        style="transition: background 0.2s;">
                                                                                        <div class="icon-circle bg-info-light mr-2 d-flex align-items-center justify-content-center"
                                                                                            style="width: 28px; height: 28px; border-radius: 50%; background: #e0f6ff;">
                                                                                            <i class="fe fe-book-open text-info"
                                                                                                style="font-size: 0.85rem;"></i>
                                                                                        </div>
                                                                                        <span class="text-dark font-weight-bold"
                                                                                            style="font-size: 0.8rem;"
                                                                                            title="{{ $mat->title ?? basename($mat->file_path) }}">{{ \Illuminate\Support\Str::limit($mat->title ?? basename($mat->file_path), 20) }}</span>
                                                                                    </a>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @endif

                                                    <!-- PIC Column (Rowspan per PIC Group) -->
                                                    @if($loop->first)
                                                        <td rowspan="{{ $items->count() }}" class="align-top py-3 text-center"
                                                            style="border-right: 1px solid #e9ecef;">
                                                            @if($pic)
                                                                @php
                                                                    $displayName = $pic;
                                                                    if ($pic === 'Komisi Komjakum') {
                                                                        $displayName = 'Komjakum';
                                                                    }
                                                                @endphp
                                                                <span class="badge badge-pill badge-{{ $this->getPicColor($pic) }} px-2 mb-1"
                                                                    style="white-space: normal; line-height: 1.4; display: inline-block; cursor: help; user-select: none; -webkit-user-select: none;"
                                                                    data-toggle="tooltip" data-placement="top" data-offset="0, 5"
                                                                    title="{{ $activity->getDispositionGroupMembers($pic) }}">
                                                                    {{ $displayName }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted small">-</span>
                                                            @endif
                                                        </td>
                                                    @endif

                                                    <!-- Instruction -->
                                                    <td class="align-top py-2 pr-3">
                                                        <div class="markdown-content">
                                                            {!! \Illuminate\Support\Str::markdown($item->instruction) !!}
                                                        </div>
                                                    </td>

                                                    <!-- Progress -->
                                                    <td class="align-top py-2">
                                                        @if(auth()->user()->canManageFollowUp() && $editingProgressId === $item->id)
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" wire:model="progressNote" class="form-control"
                                                                    placeholder="Update...">
                                                                <div class="input-group-append">
                                                                    <button wire:click="saveProgress({{ $item->id }})"
                                                                        class="btn btn-primary px-2" type="button"><i
                                                                            class="fe fe-check"></i></button>
                                                                    <button wire:click="$set('editingProgressId', null)"
                                                                        class="btn btn-light px-2" type="button"><i
                                                                            class="fe fe-x"></i></button>
                                                                </div>
                                                            </div>
                                                        @elseif(auth()->user()->canManageFollowUp())
                                                            <div class="cursor-pointer d-flex align-items-center justify-content-between"
                                                                wire:click="editProgress({{ $item->id }})">
                                                                @if($item->progress_notes)
                                                                    <span class="small text-dark text-truncate d-inline-block"
                                                                        style="max-width: 150px;"
                                                                        title="{{ $item->progress_notes }}">{{ $item->progress_notes }}</span>
                                                                @else
                                                                    <span class="small text-muted font-italic opacity-50">Update...</span>
                                                                @endif
                                                                <i class="fe fe-edit-2 text-muted ml-1"
                                                                    style="font-size: 10px; opacity: 0.5;"></i>
                                                            </div>
                                                        @else
                                                            @if($item->progress_notes)
                                                                <span class="small text-dark text-truncate d-inline-block" style="max-width: 150px;"
                                                                    title="{{ $item->progress_notes }}">{{ $item->progress_notes }}</span>
                                                            @else
                                                                <span class="small text-muted font-italic opacity-50">-</span>
                                                            @endif
                                                        @endif
                                                    </td>

                                                    <!-- Status -->
                                                    <td class="align-top py-2 text-center">
                                                        @if(auth()->user()->canManageFollowUp())
                                                            <div class="dropdown">
                                                                <button
                                                                    class="btn btn-xs dropdown-toggle btn-{{ $statusColors[$item->status] ?? 'secondary' }} text-white shadow-sm py-0 px-2"
                                                                    type="button" data-toggle="dropdown" style="font-size: 0.7rem;">
                                                                    {{ $statusLabels[$item->status] ?? 'Pending' }}
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                                    @foreach($statusLabels as $key => $label)
                                                                        <a class="dropdown-item small" href="#"
                                                                            wire:click.prevent="updateStatus({{ $item->id }}, {{ $key }})">{{ $label }}</a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span
                                                                class="badge badge-{{ $statusColors[$item->status] ?? 'secondary' }}">{{ $statusLabels[$item->status] ?? 'Pending' }}</span>
                                                        @endif
                                                    </td>

                                                    <!-- Deadline -->
                                                    <td class="align-top py-2 text-center">
                                                        @if($item->deadline)
                                                            <span
                                                                class="small {{ $item->deadline->isPast() && $item->status < 2 ? 'text-danger font-weight-bold' : 'text-muted' }}"
                                                                style="font-size: 0.75rem;">
                                                                {{ $item->deadline->format('d M') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif
                                @endforeach

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fe fe-clipboard text-muted display-4"></i>
                                            </div>
                                            <h5 class="text-muted font-weight-bold">Tidak ada tindak lanjut ditemukan</h5>
                                            <p class="text-muted small">Coba ubah filter atau kata kunci pencarian Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top bg-light">
                    {{ $activities->links('vendor.livewire.custom-pagination') }}
                </div>
            </div>
        </div>

        <!-- Styles for Highlight Animation and Tables -->
        <style>
            .table-custom-border {
                border-collapse: separate;
                border-spacing: 0;
            }

            .table-custom-border th {
                border-bottom: 2px solid #e2e8f0 !important;
                border-top: none !important;
                border-left: none !important;
                border-right: none !important;
                padding: 1rem 0.75rem !important;
                color: #6c757d !important;
                font-weight: 700 !important;
                letter-spacing: 0.5px;
            }

            .table-custom-border td {
                border-top: 1px solid #edf2f7 !important;
                border-bottom: 1px solid #edf2f7 !important;
                border-left: none !important;
                border-right: none !important;
            }

            .table-custom-border tbody tr:hover td {
                background-color: #f8f9fc !important;
            }

            .table-custom-border thead .bg-light {
                background-color: #f8f9fa !important;
            }

            @keyframes kf-highlight {
                0% {
                    background-color: #fff3cd;
                }

                /* Warning color light */
                50% {
                    background-color: #fff3cd;
                }

                100% {
                    background-color: transparent;
                }
            }

            .row-highlight {
                animation: kf-highlight 3s ease-out forwards;
            }
        </style>

        <!-- Scripts for Tooltips and Highlight -->
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Re-init tooltips safely
                const initTooltips = () => {
                    $('[data-toggle="tooltip"]').tooltip('dispose');
                    $('[data-toggle="tooltip"]').tooltip({
                        html: true,
                        container: 'body'
                    });
                };
                initTooltips();

                // Re-init on Livewire updates
                Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    succeed(({ snapshot, effect }) => {
                        setTimeout(() => { initTooltips(); }, 100);
                    });
                });

                // Check for highlight_id in URL
                const urlParams = new URLSearchParams(window.location.search);
                const highlightId = urlParams.get('highlight_id');

                if (highlightId) {
                    // Find the element
                    setTimeout(() => {
                        const element = document.getElementById('followup-row-' + highlightId);
                        if (element) {
                            // Scroll into view
                            element.scrollIntoView({ behavior: 'smooth', block: 'center' });

                            // Add highlight class
                            element.classList.add('row-highlight');

                            // Clean URL to prevent re-highlighting on reload (Optional, keeping commented for now)
                            // const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            // window.history.replaceState({path: newUrl}, '', newUrl);
                        }
                    }, 500);
                }
            });
        </script>

        <style>
            /* Fix for tooltip flickering: prevent tooltip from capturing mouse events */
            .tooltip {
                pointer-events: none !important;
            }

            /* Attachment Button Styling */
            .attachment-btn:hover {
                background-color: #e2e8f0 !important;
                border-color: #cbd5e0 !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            }

            .attachment-btn:active {
                transform: translateY(0);
            }

            .dropdown-item.rounded {
                border-bottom: 1px solid #f1f5f9;
            }

            .dropdown-item.rounded:last-child {
                border-bottom: none;
            }

            .dropdown-item.rounded:hover {
                background-color: #f8fafc !important;
                padding-left: 1.25rem !important;
            }

            /* Markdown Styling inside the table */
            .markdown-content {
                font-size: 0.75rem;
                line-height: 1.35;
                color: #334155;
            }

            .markdown-content p {
                margin-bottom: 0.35rem;
            }

            .markdown-content p:last-child {
                margin-bottom: 0;
            }

            .markdown-content strong {
                font-weight: 700;
                color: #0f172a;
            }

            .markdown-content em {
                color: #64748b;
            }

            .markdown-content ul,
            .markdown-content ol {
                padding-left: 1rem;
                margin-bottom: 0.35rem;
            }

            .markdown-content li {
                margin-bottom: 0.15rem;
            }

            .markdown-content li::marker {
                color: #94a3b8;
            }

            .markdown-content a {
                color: #2563eb;
                text-decoration: none;
                font-weight: 500;
            }

            .markdown-content a:hover {
                text-decoration: underline;
            }

            .markdown-content blockquote {
                border-left: 2px solid #cbd5e1;
                padding-left: 0.5rem;
                margin-left: 0;
                color: #64748b;
                font-style: italic;
                background: #f8fafc;
                padding-top: 0.25rem;
                padding-bottom: 0.25rem;
                border-radius: 0 0.25rem 0.25rem 0;
            }

            .markdown-content code {
                background-color: #f1f5f9;
                color: #ef4444;
                padding: 0.05rem 0.2rem;
                border-radius: 0.2rem;
                font-size: 0.85em;
                font-family: inherit;
            }
        </style>
    </div>
</div>

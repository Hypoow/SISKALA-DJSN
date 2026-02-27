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
        <div class="card-header bg-primary text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 text-white font-weight-bold text-uppercase" style="letter-spacing: 1px;">Monitoring Tindak Lanjut</h5>
                    <p class="mb-0 text-white-50 small">Pantau progres dan status arahan kegiatan</p>
                </div>
                <div style="width: 300px;">
                    <div class="input-group input-group-merge input-group-premium bg-white shadow-sm" style="border-radius: 20px; overflow: hidden;">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 pl-4 py-2" placeholder="Cari..." style="font-size: 0.9rem;">
                        <div class="input-group-append">
                            <div class="input-group-text border-0 bg-white pr-4"><i class="fe fe-search text-muted"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar & Filters -->
        <div class="bg-light border-bottom p-3" style="position: relative; z-index: 100; overflow: visible;">
            <div class="row align-items-center" style="overflow: visible;">
                <div class="col-md-2 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false" style="position: relative; z-index: 1050; overflow: visible;">
                    <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                            <span class="text-truncate" x-text="$wire.year"></span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                         <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 250px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1060;">
                             @foreach(range(date('Y')-1, date('Y')+1) as $y)
                                 <div class="dropdown-item-premium" :class="{ 'active': $wire.year == {{ $y }} }" @click="$wire.set('year', '{{ $y }}'); open = false">{{ $y }}</div>
                             @endforeach
                         </div>
                    </div>
                </div>
                <div class="col-md-2 mb-2 mb-md-0" x-data="{ open: false, getMonthName(m) { return m ? ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][m] : 'Semua Bulan'; } }" @click.away="open = false" style="position: relative; z-index: 1049; overflow: visible;">
                     <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                            <span class="text-truncate" x-text="getMonthName($wire.month)">Semua Bulan</span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                        <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 300px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1060;">
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '' }" @click="$wire.set('month', ''); open = false">Semua Bulan</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '1' }" @click="$wire.set('month', '1'); open = false">Januari</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '2' }" @click="$wire.set('month', '2'); open = false">Februari</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '3' }" @click="$wire.set('month', '3'); open = false">Maret</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '4' }" @click="$wire.set('month', '4'); open = false">April</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '5' }" @click="$wire.set('month', '5'); open = false">Mei</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '6' }" @click="$wire.set('month', '6'); open = false">Juni</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '7' }" @click="$wire.set('month', '7'); open = false">Juli</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '8' }" @click="$wire.set('month', '8'); open = false">Agustus</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '9' }" @click="$wire.set('month', '9'); open = false">September</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '10' }" @click="$wire.set('month', '10'); open = false">Oktober</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '11' }" @click="$wire.set('month', '11'); open = false">November</div>
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.month == '12' }" @click="$wire.set('month', '12'); open = false">Desember</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false" style="position: relative; z-index: 1048; overflow: visible;">
                    <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                            <span class="text-truncate" x-text="$wire.topic ? $wire.topic : 'Semua Topik'">Semua Topik</span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                        <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 300px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1060;">
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.topic == '' }" @click="$wire.set('topic', ''); open = false">Semua Topik</div>
                            @foreach($existingTopics as $t)
                                <div class="dropdown-item-premium" :class="{ 'active': $wire.topic == '{{ $t }}' }" @click="$wire.set('topic', '{{ $t }}'); open = false">{{ $t }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-2 mb-md-0" x-data="{ open: false }" @click.away="open = false" style="position: relative; z-index: 1048; overflow: visible;">
                    <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                            <span class="text-truncate" x-text="$wire.pic ? $wire.pic : 'Semua PIC'">Semua PIC</span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                        <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 300px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1060;">
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.pic == '' }" @click="$wire.set('pic', ''); open = false">Semua PIC</div>
                            @foreach($existingPics as $p)
                                <div class="dropdown-item-premium" :class="{ 'active': $wire.pic == '{{ $p }}' }" @click="$wire.set('pic', '{{ $p }}'); open = false">{{ $p }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-2 mb-md-0" x-data="{ open: false, statuses: {{ json_encode($statusLabels) }} }" @click.away="open = false" style="position: relative; z-index: 1047; overflow: visible;">
                    <div class="position-relative">
                        <button type="button" @click="open = !open" class="form-control-premium shadow-sm text-left d-flex align-items-center justify-content-between" style="background-image: none; height: auto;">
                            <span class="text-truncate" x-text="$wire.status === 'all' ? 'Semua Status' : (statuses[$wire.status] || 'Status')">Semua Status</span>
                            <i class="fe fe-chevron-down ml-2 header-arrow" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;"></i>
                        </button>
                        <div class="dropdown-menu-premium shadow-lg w-100" x-show="open" x-transition style="display: none; max-height: 300px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1060;">
                            <div class="dropdown-item-premium" :class="{ 'active': $wire.status == 'all' }" @click="$wire.set('status', 'all'); open = false">Semua Status</div>
                            @foreach($statusLabels as $key => $label)
                                <div class="dropdown-item-premium" :class="{ 'active': $wire.status == '{{ $key }}' }" @click="$wire.set('status', '{{ $key }}'); open = false">{{ $label }}</div>
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
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4" style="width: 20%;">Agenda & Waktu</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 10%;">PIC / Komisi</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 30%;">Arahan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 20%;">Progres / Tindak Lanjut</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 10%;">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 10%;">Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedActivities as $month => $monthActivities)
                            <tr class="bg-white">
                                <td colspan="6" class="py-2 pl-4 border-bottom border-top">
                                    <h6 class="mb-0 text-primary font-weight-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">
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
                                    <tr style="border-top: 2px solid #6c757d;">
                                        <!-- Activity Info -->
                                        <td class="align-top pl-4 py-3" style="border-left: 4px solid {{ $activity->type == 'internal' ? '#004085' : '#17a2b8' }}; background-color: #fff; width: 20%; border-right: 2px solid #b8c2cc;">
                                            <div style="max-height: 200px; overflow-y: auto;">
                                                <!-- Row 1: Activity Name -->
                                                <div class="mb-2">
                                                    <a href="{{ route('activities.show', $activity->id) }}" class="text-dark font-weight-bold text-decoration-none" title="Lihat Detail">{{ $activity->name }}</a>
                                                </div>

                                                <!-- Row 2: Time -->
                                                <div class="mb-2">
                                                    <small class="text-muted font-weight-bold" style="font-size: 0.9em;">
                                                        <i class="fe fe-clock mr-1"></i>{{ $activity->date_time->translatedFormat('d F, H:i') }}
                                                    </small>
                                                </div>

                                                @php
                                                    $firstWithTopic = $activity->followups->first(function($val) { return !empty($val->topic); });
                                                    $topicName = $firstWithTopic ? $firstWithTopic->topic : null;
                                                @endphp

                                                <!-- Row 3: Topic & Type -->
                                                <div class="d-flex align-items-center mb-2 flex-wrap">
                                                    <!-- Topic -->
                                                    @if($topicName)
                                                        @php $tColor = $this->getTopicColor($topicName); @endphp
                                                        <span class="badge badge-pill px-2 py-1 mr-2 mb-1" style="background-color: {{ $tColor }}20; border: 1px solid {{ $tColor }}; color: {{ $tColor }}; font-weight: 600; font-size: 0.7em;">
                                                            <i class="fe fe-tag mr-1"></i>{{ $topicName }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-pill badge-light px-2 py-1 mr-2 mb-1 text-muted border" style="font-size: 0.7em;">
                                                            No Topic
                                                        </span>
                                                    @endif

                                                    <span class="text-muted mr-2 mb-1" style="font-size: 0.8em;">-</span>

                                                    <!-- Type -->
                                                    <div class="mb-1">
                                                        @if($activity->type == 'internal')
                                                            <span class="badge badge-primary px-2 py-1" style="font-size: 0.7em; background-color: #004085;">Internal</span>
                                                        @else
                                                            <span class="badge badge-info text-white px-2 py-1" style="font-size: 0.7em;">{{ $activity->organizer_name ?? 'Eksternal' }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                    <!-- Attachments (Right) -->
                                                <!-- Attachments (Row 4) -->
                                                <div>
                                                    @if($activity->minutes_path || $activity->attachment_path || $activity->assignment_letter_path)
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-2 btn-block text-left" type="button" data-toggle="dropdown" aria-expanded="false" style="font-size: 0.75em;">
                                                                <i class="fe fe-paperclip mr-1"></i> Lampiran
                                                            </button>
                                                            <div class="dropdown-menu shadow-sm dropdown-menu-right">
                                                                @if($activity->minutes_path)
                                                                    <a class="dropdown-item small" href="{{ Storage::url($activity->minutes_path) }}" target="_blank">
                                                                        <i class="fe fe-file-text mr-2 text-primary"></i> Notulensi
                                                                    </a>
                                                                @endif
                                                                @if($activity->attachment_path)
                                                                    <a class="dropdown-item small" href="{{ Storage::url($activity->attachment_path) }}" target="_blank">
                                                                        <i class="fe fe-paperclip mr-2 text-primary"></i> Surat Undangan
                                                                    </a>
                                                                @endif
                                                                @if($activity->assignment_letter_path)
                                                                    <a class="dropdown-item small" href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank">
                                                                        <i class="fe fe-file-text mr-2 text-primary"></i> Surat Tugas
                                                                    </a>
                                                                @endif
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
                                            <tr id="followup-row-{{ $item->id }}" style="{{ ($loop->parent->first && $loop->first) ? 'border-top: 2px solid #6c757d;' : '' }}">
                                                <!-- Activity Info (Rowspan for ALL followups) -->
                                                @if($loop->parent->first && $loop->first)
                                                    <td rowspan="{{ $rowCount }}" class="align-top pl-4 py-3" style="border-left: 2px solid {{ $activity->type == 'internal' ? '#004085' : '#17a2b8' }}; background-color: #fff; width: 20%; border-right: 2px solid rgba(184, 194, 204, 1);">
                                                        <div style="max-height: 200px; overflow-y: auto;">
                                                            <!-- Row 1: Activity Name -->
                                                            <div class="mb-2">
                                                                <a href="{{ route('activities.show', $activity->id) }}" class="text-dark font-weight-bold text-decoration-none" title="Lihat Detail">{{ $activity->name }}</a>
                                                            </div>

                                                            <!-- Row 2: Time -->
                                                            <div class="mb-2">
                                                                <small class="text-muted font-weight-bold" style="font-size: 0.9em;">
                                                                    <i class="fe fe-clock mr-1"></i>{{ $activity->date_time->translatedFormat('d F, H:i') }}
                                                                </small>
                                                            </div>

                                                            @php
                                                                $firstWithTopic = $activity->followups->first(function($val) { return !empty($val->topic); });
                                                                $topicName = $firstWithTopic ? $firstWithTopic->topic : null;
                                                            @endphp

                                                            <!-- Row 3: Topic & Type -->
                                                            <div class="d-flex align-items-center mb-2 flex-wrap">
                                                                <!-- Topic -->
                                                                @if($topicName)
                                                                    @php $tColor = $this->getTopicColor($topicName); @endphp
                                                                    <span class="badge badge-pill px-2 py-1 mr-2 mb-1" style="background-color: {{ $tColor }}20; border: 1px solid {{ $tColor }}; color: {{ $tColor }}; font-weight: 600; font-size: 0.7em;">
                                                                        <i class="fe fe-tag mr-1"></i>{{ $topicName }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge badge-pill badge-light px-2 py-1 mr-2 mb-1 text-muted border" style="font-size: 0.7em;">
                                                                        No Topic
                                                                    </span>
                                                                @endif

                                                                <span class="text-muted mr-2 mb-1" style="font-size: 0.8em;">-</span>

                                                                <!-- Type -->
                                                                <div class="mb-1">
                                                                    @if($activity->type == 'internal')
                                                                        <span class="badge badge-primary px-2 py-1" style="font-size: 0.7em; background-color: #004085;">Internal</span>
                                                                    @else
                                                                        <span class="badge badge-info text-white px-2 py-1" style="font-size: 0.7em;">{{ $activity->organizer_name ?? 'Eksternal' }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Attachments (Row 4) -->
                                                            <div>
                                                                @if($activity->minutes_path || $activity->attachment_path || $activity->assignment_letter_path)
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-2 btn-block text-left" type="button" data-toggle="dropdown" aria-expanded="false" style="font-size: 0.75em;">
                                                                            <i class="fe fe-paperclip mr-1"></i> Lampiran
                                                                        </button>
                                                                        <div class="dropdown-menu shadow-sm dropdown-menu-right">
                                                                            @if($activity->minutes_path)
                                                                                <a class="dropdown-item small" href="{{ Storage::url($activity->minutes_path) }}" target="_blank">
                                                                                    <i class="fe fe-file-text mr-2 text-primary"></i> Notulensi
                                                                                </a>
                                                                            @endif
                                                                            @if($activity->attachment_path)
                                                                                <a class="dropdown-item small" href="{{ Storage::url($activity->attachment_path) }}" target="_blank">
                                                                                    <i class="fe fe-paperclip mr-2 text-primary"></i> Surat Undangan
                                                                                </a>
                                                                            @endif
                                                                            @if($activity->assignment_letter_path)
                                                                                <a class="dropdown-item small" href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank">
                                                                                    <i class="fe fe-file-text mr-2 text-primary"></i> Surat Tugas
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                @endif

                                                <!-- PIC Column (Rowspan per PIC Group) -->
                                                @if($loop->first)
                                                    <td rowspan="{{ $items->count() }}" class="align-top py-3 text-center" style="border-right: 1px solid #e9ecef;">
                                                        @if($pic)
                                                            @php
                                                                $displayName = $pic;
                                                                if ($pic === 'Komisi Komjakum') {
                                                                    $displayName = 'Komjakum';
                                                                }
                                                            @endphp
                                                            <span class="badge badge-pill badge-{{ $this->getPicColor($pic) }} px-2 mb-1" 
                                                                  style="white-space: normal; line-height: 1.4; display: inline-block; cursor: help; user-select: none; -webkit-user-select: none;"
                                                                  data-toggle="tooltip" 
                                                                  data-placement="top" 
                                                                  data-offset="0, 5"
                                                                  title="{{ $activity->getDispositionGroupMembers($pic) }}">
                                                                {{ $displayName }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                @endif

                                                <!-- Instruction -->
                                                <td class="align-top py-2">
                                                    <div class="markdown-content" style="font-size: 0.9em;">
                                                        {!! \Illuminate\Support\Str::markdown($item->instruction) !!}
                                                    </div>
                                                </td>

                                                <!-- Progress -->
                                                <td class="align-top py-2">
                                                    @if($editingProgressId === $item->id)
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" wire:model="progressNote" class="form-control" placeholder="Update...">
                                                            <div class="input-group-append">
                                                                <button wire:click="saveProgress({{ $item->id }})" class="btn btn-primary px-2" type="button"><i class="fe fe-check"></i></button>
                                                                <button wire:click="$set('editingProgressId', null)" class="btn btn-light px-2" type="button"><i class="fe fe-x"></i></button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="cursor-pointer d-flex align-items-center justify-content-between" wire:click="editProgress({{ $item->id }})">
                                                            @if($item->progress_notes)
                                                                <span class="small text-dark text-truncate d-inline-block" style="max-width: 150px;" title="{{ $item->progress_notes }}">{{ $item->progress_notes }}</span>
                                                            @else
                                                                <span class="small text-muted font-italic opacity-50">Update...</span>
                                                            @endif
                                                            <i class="fe fe-edit-2 text-muted ml-1" style="font-size: 10px; opacity: 0.5;"></i>
                                                        </div>
                                                    @endif
                                                </td>

                                                <!-- Status -->
                                                <td class="align-top py-2 text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs dropdown-toggle btn-{{ $statusColors[$item->status] ?? 'secondary' }} text-white shadow-sm py-0 px-2" type="button" data-toggle="dropdown" style="font-size: 0.7rem;">
                                                            {{ $statusLabels[$item->status] ?? 'Pending' }}
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                                                            @foreach($statusLabels as $key => $label)
                                                                <a class="dropdown-item small" href="#" wire:click.prevent="updateStatus({{ $item->id }}, {{ $key }})">{{ $label }}</a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Deadline -->
                                                <td class="align-top py-2 text-center">
                                                    @if($item->deadline)
                                                        <span class="small {{ $item->deadline->isPast() && $item->status < 2 ? 'text-danger font-weight-bold' : 'text-muted' }}" style="font-size: 0.75rem;">
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
            <div class="p-3">
                {{ $activities->links() }}
            </div>
        </div>
    </div>

    <!-- Styles for Highlight Animation -->
    <style>
        .table-custom-border th,
        .table-custom-border td {
            border: 1px solid #adb5bd !important;
        }
        .table-custom-border thead th {
            border-bottom: 2px solid #6c757d !important;
        }
        
        @keyframes kf-highlight {
            0% { background-color: #fff3cd; } /* Warning color light */
            50% { background-color: #fff3cd; }
            100% { background-color: transparent; }
        }
        
        .row-highlight {
            animation: kf-highlight 3s ease-out forwards;
        }
    </style>

    <!-- Scripts for Auto-Refresh and Highlight -->
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

            let lastActive = Date.now();
            const updateLastActive = () => { lastActive = Date.now(); };
            ['mousemove', 'click', 'scroll', 'keydown', 'touchstart'].forEach(evt => 
                document.addEventListener(evt, updateLastActive)
            );

            setInterval(() => {
                if (Date.now() - lastActive > 10000) {
                    @this.$refresh();
                }
            }, 5000);

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
    </style>
    </div>
</div>

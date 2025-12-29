<div>
    <!-- Stats Widgets -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <span class="circle circle-lg bg-primary">
                                <i class="fe fe-list fe-24 text-white"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h3 class="h2 mb-0 mt-2 font-bold">{{ $stats['total'] }}</h3>
                            <p class="text-muted mb-0">Total Tindak Lanjut</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <span class="circle circle-lg bg-success">
                                <i class="fe fe-check-circle fe-24 text-white"></i>
                            </span>
                        </div>
                        <div class="col">
                             <h3 class="h2 mb-0 mt-2 font-bold">{{ $stats['completed'] }}</h3>
                            <p class="text-muted mb-0">Selesai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <span class="circle circle-lg bg-warning">
                                <i class="fe fe-clock fe-24 text-white"></i>
                            </span>
                        </div>
                        <div class="col">
                             <h3 class="h2 mb-0 mt-2 font-bold">{{ $stats['pending'] }}</h3>
                            <p class="text-muted mb-0">Outstanding / Pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 rounded-lg overflow-hidden mb-4">
        <!-- Card Header -->
        <div class="card-header bg-primary text-white p-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1 text-white font-weight-bold text-uppercase" style="letter-spacing: 1px;">Monitoring Tindak Lanjut</h5>
                    <p class="mb-0 text-white-50 small">Pantau progres dan status arahan kegiatan</p>
                </div>
            </div>
        </div>

        <!-- Toolbar & Filters -->
        <div class="bg-light border-bottom p-3">
            <div class="row align-items-center">
                <div class="col-md-2 mb-2 mb-md-0">
                    <select wire:model.live="year" class="form-control border-0 shadow-sm" style="background-image: none;">
                        @foreach(range(date('Y')-1, date('Y')+1) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select wire:model.live="status" class="form-control border-0 shadow-sm" style="background-image: none;">
                        <option value="all">Semua Status</option>
                         @foreach($statusLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-7">
                    <div class="input-group input-group-merge shadow-sm">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-0 pl-4" placeholder="Cari Agenda atau PIC...">
                        <div class="input-group-append">
                            <div class="input-group-text border-0 bg-white pr-4"><i class="fe fe-search text-muted"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 pl-4" style="width: 25%;">Agenda & Waktu</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 30%;">Arahan / Tindak Lanjut</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 25%;">Proses Capaian</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 10%;">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 10%;">Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedActivities as $month => $monthActivities)
                            <tr class="bg-white">
                                <td colspan="5" class="py-2 pl-4 border-bottom border-top">
                                    <h6 class="mb-0 text-primary font-weight-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">
                                        <i class="fe fe-calendar mr-2"></i>{{ $month }}
                                    </h6>
                                </td>
                            </tr>
                            @foreach($monthActivities as $activity)
                                @php
                                    $followups = $activity->followups;
                                    $rowCount = $followups->isNotEmpty() ? $followups->count() : 1;
                                @endphp

                                @foreach($followups->isNotEmpty() ? $followups : [null] as $index => $item)
                                <tr style="{{ $loop->first ? 'border-top: 1px solid #dee2e6;' : '' }}">
                                    <!-- Activity Info (Rowspan) -->
                                    @if($loop->first)
                                    <td rowspan="{{ $rowCount }}" class="align-top pl-4" style="border-left: 4px solid {{ $activity->type == 'internal' ? '#007bff' : '#fd7e14' }}; background-color: #fff;">
                                        <strong>{{ $activity->name }}</strong><br>
                                        <small class="text-muted"><i class="fe fe-clock mr-1"></i>{{ $activity->date_time->format('d M Y, H:i') }}</small>
                                        
                                        <div class="mt-2">
                                            @if($activity->type == 'internal')
                                                <span class="badge badge-primary px-2 py-1">Internal</span>
                                            @else
                                                <span class="badge badge-warning text-white px-2 py-1">Eksternal</span>
                                            @endif
                                        </div>

                                        @if($activity->minutes_path)
                                            <div class="mt-2">
                                                <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="text-primary small">
                                                    <i class="fe fe-file-text mr-1"></i> Lihat Notulensi
                                                </a>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2">
                                            <a href="{{ route('activities.show', $activity->id) }}" class="btn btn-xs btn-outline-secondary">Lihat Detail</a>
                                        </div>
                                    </td>
                                    @endif

                                    <!-- Follow-up Items -->
                                    @if($item)
                                        <td class="align-top">
                                            <div class="p-2 rounded bg-light border border-light" style="border-left: 3px solid #007bff !important;">
                                                <!-- Prioritas / PIC -->
                                                @if($item->pic)
                                                    <div class="mb-2">
                                                        <span class="badge badge-pill badge-primary px-2" style="font-size: 0.7rem;">
                                                            <i class="fe fe-user mr-1"></i> {{ $item->pic }}
                                                        </span>
                                                    </div>
                                                @endif
                                                
                                                <!-- Instruction Content -->
                                                <div class="text-dark small" style="line-height: 1.6;">
                                                    @php
                                                        $lines = collect(explode("\n", $item->instruction))->filter(function($line) { return trim($line) !== ''; });
                                                    @endphp
                                                    
                                                    @if($lines->count() > 1)
                                                        <ul class="pl-3 mb-0" style="list-style-type: disc;">
                                                            @foreach($lines as $line)
                                                                <li class="mb-1">{{ trim($line) }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        {{ $item->instruction }}
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-top">
                                            <!-- Editable Progress Input -->
                                            @if($editingProgressId === $item->id)
                                                <div class="bg-white p-2 border rounded shadow-sm">
                                                    <label class="small font-weight-bold mb-1 d-block text-primary">Capaian: {{ $percentage }}%</label>
                                                    <input type="range" wire:model.live="percentage" class="custom-range mb-2" min="0" max="100" step="5">
                                                    
                                                    <div class="input-group input-group-sm">
                                                        <textarea wire:model="progressNote" class="form-control" rows="2" placeholder="Update progress..."></textarea>
                                                        <div class="input-group-append">
                                                            <button wire:click="saveProgress({{ $item->id }})" class="btn btn-primary" type="button"><i class="fe fe-check"></i></button>
                                                            <button wire:click="$set('editingProgressId', null)" class="btn btn-light" type="button"><i class="fe fe-x"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="cursor-pointer" wire:click="editProgress({{ $item->id }})" style="min-height: 20px;">
                                                    <!-- Progress Bar -->
                                                    <div class="progress mb-2" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $item->percentage == 100 ? 'success' : 'primary' }}" role="progressbar" style="width: {{ $item->percentage }}%" aria-valuenow="{{ $item->percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                                        <span>{{ $item->percentage }}% Completed</span>
                                                    </div>

                                                    @if($item->progress_notes)
                                                        <span class="small text-dark d-block">{{ $item->progress_notes }}</span>
                                                    @else
                                                        <span class="small text-muted font-italic">Klik untuk update...</span>
                                                    @endif
                                                    <i class="fe fe-edit-2 text-muted small mt-1" style="opacity: 0.5;"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="align-top text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-xs dropdown-toggle btn-{{ $statusColors[$item->status] ?? 'secondary' }} text-white shadow-sm" type="button" data-toggle="dropdown">
                                                    {{ $statusLabels[$item->status] ?? 'Pending' }}
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    @foreach($statusLabels as $key => $label)
                                                        <a class="dropdown-item small" href="#" wire:click.prevent="updateStatus({{ $item->id }}, {{ $key }})">{{ $label }}</a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-top text-center">
                                            @if($item->deadline)
                                                <span class="small {{ $item->deadline->isPast() && $item->status < 2 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                                                    {{ $item->deadline->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    @else
                                        <!-- No Follow-ups -->
                                        <td colspan="4" class="text-center text-muted small font-italic py-3">
                                            Belum ada tindak lanjut.
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan untuk filter ini.</td>
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
</div>
</div>

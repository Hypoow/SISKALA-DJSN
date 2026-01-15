<tr class="{{ $activity->type == 'internal' ? 'row-internal' : 'row-external' }}" wire:key="row-{{ $activity->id }}">
    <td style="min-width: 140px;">
        <div class="d-flex align-items-center">
            <div class="text-center bg-white border border-secondary shadow-sm rounded" style="width: 50px; overflow: hidden;">
                <div class="bg-secondary text-white small font-weight-bold py-0" style="font-size: 10px; line-height: 1.2;">
                    {{ $activity->date_time->format('M') }}
                </div>
                <div class="h4 mb-0 font-weight-bold text-dark py-2">
                    {{ $activity->date_time->format('d') }}
                </div>
            </div>
            <div class="ml-3">
                <span class="h5 mb-0 font-weight-bold text-secondary">{{ $activity->date_time->format('H:i') }}</span>
                <div class="small text-muted font-weight-bold text-uppercase">{{ $activity->date_time->isoFormat('dddd') }}</div>
            </div>
        </div>
    </td>
    <td>
        <a href="{{ route('activities.show', $activity->id) }}" class="text-dark font-weight-bold">{{ $activity->name }}</a>
        <br>
        <small class="text-muted">{{ $activity->location_type == 'online' ? 'Online' : $activity->location }}</small>
    </td>
    <td class="text-center">
        @switch($activity->status)
            @case(0) <span class="badge badge-success">Terlaksana</span> @break
            @case(1) <span class="badge badge-secondary">Reschedule</span> @break
            @case(2) <span class="badge badge-warning">Belum ada Disposisi</span> @break
            @case(3) <span class="badge badge-danger">Batal</span> @break
        @endswitch
    </td>
    <td>
        {{-- Minutes Upload --}}
        @if($activity->minutes_path)
            <div class="btn-group btn-block">
                <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary text-truncate" title="Lihat Notulensi">
                    <i class="fe fe-eye"></i> Lihat
                </a>
                @if(auth()->check() && auth()->user()->isAdmin())
                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('minutes_{{ $activity->id }}').click()">
                        <i class="fe fe-upload mr-2"></i> Ganti File
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDeleteRow('{{ $this->getId() }}', 'minutes')">
                        <i class="fe fe-trash-2 mr-2"></i> Hapus File
                    </a>
                </div>
                @endif
            </div>
            @if(auth()->check() && auth()->user()->isAdmin())
                <input type="file" id="minutes_{{ $activity->id }}" wire:model.live="minutesFile" class="d-none" accept="application/pdf">
                <div wire:loading wire:target="minutesFile" class="text-center mt-1">
                     <small class="text-muted"><span class="spinner-border spinner-border-sm"></span> Uploading...</small>
                </div>
            @endif
        @else
            @if(auth()->check() && auth()->user()->isAdmin())
            <input type="file" id="minutes_{{ $activity->id }}" wire:model.live="minutesFile" class="d-none" accept="application/pdf">
            <button type="button" class="btn btn-sm btn-primary btn-block w-100" style="width: 100% !important; border-radius: 50px !important; display: block;" onclick="document.getElementById('minutes_{{ $activity->id }}').click()">
                <span wire:loading.remove wire:target="minutesFile"><i class="fe fe-upload"></i> Upload</span>
                <span wire:loading wire:target="minutesFile" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
            @else
            <span class="text-muted small font-italic">Belum ada</span>
            @endif
        @endif
        @error('minutesFile') <span class="text-danger small">{{ $message }}</span> @enderror
    </td>
    <td>
        {{-- Assignment Upload --}}
        @if($activity->assignment_letter_path)
            <div class="btn-group btn-block">
                <a href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank" class="btn btn-sm btn-outline-primary text-truncate" title="Lihat Surat Tugas">
                    <i class="fe fe-eye"></i> Lihat
                </a>
                @if(auth()->check() && auth()->user()->isAdmin())
                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('assignment_{{ $activity->id }}').click()">
                        <i class="fe fe-upload mr-2"></i> Ganti File
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDeleteRow('{{ $this->getId() }}', 'assignment')">
                        <i class="fe fe-trash-2 mr-2"></i> Hapus File
                    </a>
                </div>
                @endif
            </div>
            @if(auth()->check() && auth()->user()->isAdmin())
                <input type="file" id="assignment_{{ $activity->id }}" wire:model.live="assignmentFile" class="d-none" accept="application/pdf">
                <div wire:loading wire:target="assignmentFile" class="text-center mt-1">
                     <small class="text-muted"><span class="spinner-border spinner-border-sm"></span> Uploading...</small>
                </div>
            @endif
        @else
            @if(auth()->check() && auth()->user()->isAdmin())
            <input type="file" id="assignment_{{ $activity->id }}" wire:model.live="assignmentFile" class="d-none" accept="application/pdf">
            <button type="button" class="btn btn-sm btn-primary btn-block w-100" style="width: 100% !important; border-radius: 50px !important; display: block;" onclick="document.getElementById('assignment_{{ $activity->id }}').click()">
                <span wire:loading.remove wire:target="assignmentFile"><i class="fe fe-upload"></i> Upload</span>
                <span wire:loading wire:target="assignmentFile" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
            @else
            <span class="text-muted small font-italic">Belum ada</span>
            @endif
        @endif
        @error('assignmentFile') <span class="text-danger small">{{ $message }}</span> @enderror
    </td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary" type="button" id="dropdownMenuButton-row-{{ $activity->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fe fe-more-horizontal"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-row-{{ $activity->id }}">
                <a class="dropdown-item" href="{{ route('activities.show', $activity->id) }}">
                    <i class="fe fe-eye mr-2"></i> Detail
                </a>
                @if(auth()->check() && auth()->user()->isAdmin())
                <a class="dropdown-item" href="{{ route('activities.edit', $activity->id) }}">
                    <i class="fe fe-edit mr-2"></i> Edit
                </a>
                @endif
            </div>
        </div>
    </td>
</tr>

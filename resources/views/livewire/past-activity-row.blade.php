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
    <td>
        @switch($activity->status)
            @case(0) <span class="badge badge-success">Terlaksana</span> @break
            @case(1) <span class="badge badge-warning">Reschedule</span> @break
            @case(2) <span class="badge badge-secondary">Belom ada Dispo</span> @break
            @case(3) <span class="badge badge-danger">Batal</span> @break
        @endswitch
    </td>
    <td>
        {{-- Minutes Upload --}}
        @if($activity->minutes_path)
            <div class="mb-2">
                <a href="{{ Storage::url($activity->minutes_path) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block">
                    <i class="fe fe-eye"></i> Lihat
                </a>
            </div>
            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="row no-gutters">
                <div class="col-6 pr-1">
                    <input type="file" id="minutes_{{ $activity->id }}" wire:model.live="minutesFile" class="d-none" accept="application/pdf">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-block" onclick="document.getElementById('minutes_{{ $activity->id }}').click()">
                        <span wire:loading.remove wire:target="minutesFile">Ganti</span>
                        <span wire:loading wire:target="minutesFile" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                </div>
                <div class="col-6 pl-1">
                    <button type="button" class="btn btn-sm btn-danger btn-block" onclick="confirmDeleteRow('{{ $this->getId() }}', 'minutes')">
                        Hapus
                    </button>
                </div>
            </div>
            @endif
        @else
            @if(auth()->check() && auth()->user()->isAdmin())
            <span class="text-muted small font-italic d-block mb-2">Belum ada</span>
            <input type="file" id="minutes_{{ $activity->id }}" wire:model.live="minutesFile" class="d-none" accept="application/pdf">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="document.getElementById('minutes_{{ $activity->id }}').click()">
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
            <div class="mb-2">
                <a href="{{ Storage::url($activity->assignment_letter_path) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block">
                    <i class="fe fe-eye"></i> Lihat
                </a>
            </div>
            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="row no-gutters">
                <div class="col-6 pr-1">
                    <input type="file" id="assignment_{{ $activity->id }}" wire:model.live="assignmentFile" class="d-none" accept="application/pdf">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-block" onclick="document.getElementById('assignment_{{ $activity->id }}').click()">
                        <span wire:loading.remove wire:target="assignmentFile">Ganti</span>
                        <span wire:loading wire:target="assignmentFile" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                </div>
                <div class="col-6 pl-1">
                    <button type="button" class="btn btn-sm btn-danger btn-block" onclick="confirmDeleteRow('{{ $this->getId() }}', 'assignment')">
                        Hapus
                    </button>
                </div>
            </div>
            @endif
        @else
            @if(auth()->check() && auth()->user()->isAdmin())
            <span class="text-muted small font-italic d-block mb-2">Belum ada</span>
            <input type="file" id="assignment_{{ $activity->id }}" wire:model.live="assignmentFile" class="d-none" accept="application/pdf">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="document.getElementById('assignment_{{ $activity->id }}').click()">
                <span wire:loading.remove wire:target="assignmentFile"><i class="fe fe-upload"></i> Upload</span>
                <span wire:loading wire:target="assignmentFile" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
            @else
            <span class="text-muted small font-italic">Belum ada</span>
            @endif
        @endif
        @error('assignmentFile') <span class="text-danger small">{{ $message }}</span> @enderror
    </td>
    @if(auth()->check() && auth()->user()->isAdmin())
    <td>
        <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-sm btn-primary">Edit</a>
    </td>
    @endif
</tr>

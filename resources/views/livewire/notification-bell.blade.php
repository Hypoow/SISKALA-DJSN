<li class="nav-item dropdown">
    <a class="nav-link text-muted my-2" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="fe fe-bell fe-16"></span>
        @if($notifications->count() > 0)
            <span class="dot dot-md bg-danger"></span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink" style="width: 350px; max-height: 400px; overflow-y: auto;">
        <div class="dropdown-header">Notifikasi ({{ $notifications->count() }})</div>
        
        @if($notifications->isEmpty())
            <a class="dropdown-item text-center small text-muted">Tidak ada notifikasi baru</a>
        @else
            @foreach($notifications as $notification)
                <div class="dropdown-item px-3 py-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">{{ $notification->start_date->isoFormat('D MMM') }}</small>
                        <span class="badge badge-pill badge-{{ $notification->type == 'external' ? 'primary' : 'success' }}">{{ ucfirst($notification->type) }}</span>
                    </div>
                    <div class="font-weight-bold mt-1 text-truncate" style="max-width: 300px;">{{ $notification->name }}</div>
                    
                    @if(auth()->user()->isAdmin())
                        <small class="text-danger d-block mt-1"><i class="fe fe-alert-circle"></i> Butuh Disposisi!</small>
                        <a href="{{ route('activities.index', ['search' => $notification->name]) }}" class="btn btn-xs btn-outline-danger mt-2 btn-block">Proses Disposisi</a>
                    @else
                        {{-- Generic message for Dewan --}}
                        <small class="text-info d-block mt-1"><i class="fe fe-info"></i> Disposisi untuk Anda</small>
                        {{-- Just view detail --}}
                        <button type="button" class="btn btn-xs btn-outline-primary mt-2 btn-block" data-toggle="modal" data-target="#detailModal" onclick="@this.dispatch('show-detail', { id: {{ $notification->id }} })">Lihat Detail</button>
                         {{-- Note: dispatching to ActivityList component might be tricky from here if check detail is needed. 
                            Ideally, link to dashboard with filter or open modal. 
                            For now, link to dashboard search --}}
                         <a href="{{ route('activities.index', ['search' => $notification->name]) }}" class="btn btn-xs btn-outline-primary mt-2 btn-block">Lihat Kegiatan</a>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</li>

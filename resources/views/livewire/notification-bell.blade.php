<li class="nav-item dropdown">
    <a class="nav-link text-muted my-2" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="fe fe-bell fe-16"></span>
        @if($unreadCount > 0)
            <span class="dot dot-md bg-danger"></span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 notif-dropdown" aria-labelledby="navbarDropdownMenuLink" style="max-height: 480px; overflow-y: auto; border-radius: 12px;">
        <div class="dropdown-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4 sticky-top">
            <span class="font-weight-bold text-dark mb-0" style="font-size: 0.95rem;">Notifikasi</span>
            @if($unreadCount > 0)
                <span class="badge badge-pill badge-danger">{{ $unreadCount }} Baru</span>
            @endif
        </div>
        
        <div class="list-group list-group-flush">
            @if($notifications->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-2 text-muted-light"><i class="fe fe-bell-off mb-2" style="font-size: 2rem;"></i></div>
                    <p class="small text-muted mb-0">Tidak ada notifikasi baru</p>
                </div>
            @else
                @foreach($notifications as $notification)
                    @php
                        $isFollowUp = $notification->type === 'followup';
                        $uniqueId = $isFollowUp ? 'followup-' . $notification->id : 'activity-' . $notification->id;
                        $isRead = in_array($uniqueId, $readNotifications);
                        
                        // Redirect follow-ups to dashboard with highlight, others to show page
                        $url = $isFollowUp 
                            ? route('followup.dashboard', ['highlight_id' => $notification->id]) 
                            : route('activities.show', $notification->id);
                            
                        $title = $isFollowUp ? $notification->instruction : $notification->name;
                        $date = $isFollowUp ? $notification->deadline : $notification->start_date;
                        $typeName = $isFollowUp ? 'Tindak Lanjut' : ucfirst($notification->type);
                        $badgeClass = $isFollowUp ? 'badge-danger' : ($notification->type == 'internal' ? 'badge-primary' : 'badge-warning text-white');
                        $icon = $isFollowUp ? 'fe-check-square' : ($notification->type == 'activities.external' || $notification->type == 'external' ? 'fe-mail' : 'fe-briefcase');
                        $iconBg = $isFollowUp ? 'bg-danger-light text-danger' : ($notification->type == 'internal' ? 'bg-primary-light text-primary' : 'bg-warning-light text-warning');
                    @endphp
                    <a href="javascript:void(0)" 
                       wire:click="markAsRead('{{ $uniqueId }}', '{{ $url }}')"
                       class="list-group-item list-group-item-action border-bottom py-3 px-4 d-flex align-items-start {{ $isRead ? 'bg-white' : 'bg-light' }}">
                        
                        <!-- Icon Column -->
                        <div class="mr-3">
                            <div class="avatar avatar-sm">
                                <span class="avatar-title rounded-circle shadow-sm {{ $iconBg }} {{ $isRead ? 'opacity-50' : '' }}">
                                    <i class="fe {{ $icon }}"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Content Column -->
                        <div class="flex-fill">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge badge-pill {{ $badgeClass }} px-2 {{ $isRead ? 'opacity-50' : '' }}" style="font-size: 10px;">{{ $typeName }}</span>
                                <small class="text-muted {{ $isRead ? 'opacity-50' : '' }}"><i class="fe fe-clock mr-1"></i>{{ $date->isoFormat('D MMM') }}</small>
                            </div>
                            
                            <h6 class="mb-1 {{ $isRead ? 'font-weight-normal text-muted' : 'font-weight-bold text-dark' }} text-truncate" style="max-width: 200px;">{{ $title }}</h6>
                            
                            @if($isFollowUp)
                                <p class="mb-0 small text-danger font-weight-bold {{ $isRead ? 'opacity-50' : '' }}">
                                    <i class="fe fe-alert-triangle mr-1"></i> Deadline H-3!
                                </p>
                            @elseif(auth()->user()->isAdmin())
                                <p class="mb-0 small text-danger font-weight-bold {{ $isRead ? 'opacity-50' : '' }}">
                                    <i class="fe fe-alert-circle mr-1"></i> Butuh Disposisi!
                                </p>
                            @else
                                <p class="mb-0 small text-secondary {{ $isRead ? 'opacity-50' : '' }}">
                                    <i class="fe fe-info mr-1"></i> Disposisi Baru
                                </p>
                            @endif
                        </div>
                        
                        @if(!$isRead)
                            <span class="ml-2" title="Belum Dibaca">
                                <span class="d-inline-block rounded-circle bg-primary" style="width: 10px; height: 10px;"></span>
                            </span>
                        @endif
                    </a>
                @endforeach
            @endif
        </div>
        
        @if($notifications->isNotEmpty())
        <div class="dropdown-footer text-center bg-light border-top py-2 d-flex justify-content-between px-4">
            <a href="javascript:void(0)" wire:click="clearHistory" class="small font-weight-bold text-muted text-decoration-none">
                <i class="fe fe-trash-2 mr-1"></i>Hapus Riwayat
            </a>
            <a href="javascript:void(0)" wire:click="markAllAsRead" class="small font-weight-bold text-primary text-decoration-none">
                <i class="fe fe-check-circle mr-1"></i>Tandai Semua Dibaca
            </a>
        </div>
        @endif
    </div>
</li>

<script>
    document.addEventListener('livewire:initialized', () => {
        let lastActive = Date.now();
        const updateLastActive = () => { lastActive = Date.now(); };
        ['mousemove', 'click', 'scroll', 'keydown', 'touchstart'].forEach(evt => 
            document.addEventListener(evt, updateLastActive)
        );

        setInterval(() => {
            const idleTime = Date.now() - lastActive;
            if (idleTime > 10000 && idleTime < 300000) { // Refresh only when idle > 10s but < 5 mins to prevent server overload
                @this.$refresh();
            }
        }, 15000);
    });
</script>

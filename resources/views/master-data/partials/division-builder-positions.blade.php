<div class="card shadow-sm border-0 rounded-xl">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
            <div>
                <h4 class="font-weight-bold text-dark mb-1">Master Jabatan</h4>
                <p class="text-muted small mb-0">Jabatan bisa dipindah lintas kelompok agar pola role dan disposisi mudah disusun ulang.</p>
            </div>
            <span class="badge badge-light border px-3 py-2 mt-3 mt-md-0">Drag lintas kolom aktif</span>
        </div>

        <div class="row">
            @foreach($structureGroups as $groupKey => $groupLabel)
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="builder-column">
                        <div class="builder-column-header">
                            <div>
                                <h5 class="mb-1">{{ $groupLabel }}</h5>
                                <small>{{ $groupedPositions[$groupKey]->count() }} jabatan</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill" onclick="openPositionModalForGroup('{{ $groupKey }}')">
                                <i class="fe fe-plus"></i>
                            </button>
                        </div>

                        <div class="builder-dropzone position-dropzone" data-structure-group="{{ $groupKey }}">
                            @forelse($groupedPositions[$groupKey] as $position)
                                <div class="builder-item" data-id="{{ $position->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="pr-2">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="drag-handle mr-2"><i class="fe fe-menu"></i></span>
                                                <strong class="text-dark">{{ $position->name }}</strong>
                                            </div>
                                            <div class="d-flex flex-wrap">
                                                <span class="badge badge-primary-soft mr-2 mb-2">{{ $position->access_profile ? \App\Models\User::accessProfileLabel($position->access_profile) : 'Ikuti unit' }}</span>
                                                <span class="badge {{ $position->receives_disposition ? 'badge-success-soft' : 'badge-light border' }} mr-2 mb-2">{{ $position->receives_disposition ? 'Masuk disposisi' : 'Tanpa disposisi' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="button" class="btn btn-sm btn-link text-warning p-0 mr-2" onclick="editPosition({{ $position->id }}, @js($position->name), @js($position->structure_group), @js($position->access_profile), {{ $position->order }}, @js($position->getRawOriginal('receives_disposition')), @js($position->disposition_group_label), @js($position->report_target_label))">Edit</button>
                                            <form action="{{ route('master-data.positions.destroy', $position->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-btn">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="small text-muted d-flex justify-content-between">
                                        <span>{{ $position->users_count }} akun</span>
                                        <span>Urutan {{ $position->order }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="builder-empty">Belum ada jabatan.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

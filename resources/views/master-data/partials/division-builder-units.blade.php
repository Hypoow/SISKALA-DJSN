<div class="card shadow-sm border-0 rounded-xl mb-5">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
            <div>
                <h4 class="font-weight-bold text-dark mb-1">Kelompok Akun &amp; Unit Kerja</h4>
                <p class="text-muted small mb-0">Tarik kartu unit ke grup yang tepat. Tandai unit sebagai komisi Dewan jika ingin dipakai sebagai acuan komisi dinamis.</p>
            </div>
            <span class="badge badge-light border px-3 py-2 mt-3 mt-md-0">Drag lintas kolom aktif</span>
        </div>

        <div class="row">
            @foreach($divisionStructureGroups as $groupKey => $groupLabel)
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="builder-column">
                        <div class="builder-column-header">
                            <div>
                                <h5 class="mb-1">{{ $groupLabel }}</h5>
                                <small>{{ $groupedDivisions[$groupKey]->count() }} unit</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill" onclick="openDivisionModalForGroup('{{ $groupKey }}')">
                                <i class="fe fe-plus"></i>
                            </button>
                        </div>

                        <div class="builder-dropzone division-dropzone" data-structure-group="{{ $groupKey }}">
                            @forelse($groupedDivisions[$groupKey] as $division)
                                <div class="builder-item" data-id="{{ $division->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="pr-2">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="drag-handle mr-2"><i class="fe fe-menu"></i></span>
                                                <strong class="text-dark">{{ $division->name }}</strong>
                                            </div>
                                            <div class="d-flex flex-wrap">
                                                @if($division->short_label)
                                                    <span class="badge badge-light border mr-2 mb-2">{{ $division->short_label }}</span>
                                                @endif
                                                <span class="badge badge-primary-soft mr-2 mb-2">{{ \App\Models\User::accessProfileLabel($division->access_profile) }}</span>
                                                @if($division->is_commission)
                                                    <span class="badge badge-warning-soft mr-2 mb-2">Komisi</span>
                                                @elseif($division->commission_code)
                                                    <span class="badge badge-info-soft mr-2 mb-2">{{ \App\Models\Division::commissionLabel($division->commission_code) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="button" class="btn btn-sm btn-link text-warning p-0 mr-2" onclick="editDivision({{ $division->id }}, @js($division->name), @js($division->short_label), @js($division->structure_group), @js($division->access_profile), @js($division->commission_code), @js($division->is_commission), @js($division->description), {{ $division->order }})">Edit</button>
                                            <form action="{{ route('master-data.divisions.destroy', $division->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-btn">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-2">{{ $division->description ?: 'Belum ada deskripsi khusus.' }}</p>
                                    <div class="small text-muted d-flex justify-content-between">
                                        <span>{{ $division->users_count }} akun</span>
                                        <span>Urutan {{ $division->order }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="builder-empty">Belum ada unit.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

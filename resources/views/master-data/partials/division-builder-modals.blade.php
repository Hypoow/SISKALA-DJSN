<div class="modal fade" id="divisionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-xl shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold" id="divisionModalLabel">Tambah Unit</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="divisionForm" method="POST" action="{{ route('master-data.divisions.store') }}">
                @csrf
                <input type="hidden" id="divisionMethod" name="_method" value="POST">
                <div class="modal-body pt-0">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Nama Unit</label>
                        <input type="text" class="form-control shadow-sm" id="divisionName" name="name" required>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Kelompok Struktur</label>
                                <select class="form-control shadow-sm" id="divisionStructureGroup" name="structure_group" required>
                                    @foreach($divisionStructureGroups as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Profil Akses</label>
                                <select class="form-control shadow-sm" id="divisionAccessProfile" name="access_profile" required>
                                    @foreach($accessProfiles as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Urutan</label>
                                <input type="number" class="form-control shadow-sm" id="divisionOrder" name="order" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input" id="divisionIsCommission" name="is_commission" value="1">
                        <label class="custom-control-label font-weight-bold text-dark" for="divisionIsCommission">Ini komisi Dewan</label>
                    </div>
                    <div class="form-group" id="divisionCommissionGroup">
                        <label class="small font-weight-bold text-muted">Komisi Acuan</label>
                        <select class="form-control shadow-sm" id="divisionCommissionCode" name="commission_code">
                            <option value="">Tidak spesifik komisi</option>
                            @foreach($commissionOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-light border" id="divisionCommissionAuto" style="display: none;">
                        <div class="small text-muted mb-0">Kode komisi akan dibuat otomatis dari nama unit.</div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted">Deskripsi</label>
                        <textarea class="form-control shadow-sm" id="divisionDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="positionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-xl shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold" id="positionModalLabel">Tambah Jabatan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="positionForm" method="POST" action="{{ route('master-data.positions.store') }}">
                @csrf
                <input type="hidden" id="positionMethod" name="_method" value="POST">
                <div class="modal-body pt-0">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Nama Jabatan</label>
                        <input type="text" class="form-control shadow-sm" id="positionName" name="name" required>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Kelompok</label>
                                <select class="form-control shadow-sm" id="positionStructureGroup" name="structure_group" required>
                                    @foreach($structureGroups as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Akses Default</label>
                                <select class="form-control shadow-sm" id="positionAccessProfile" name="access_profile">
                                    <option value="">Ikuti unit kerja</option>
                                    @foreach($accessProfiles as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Masuk Disposisi</label>
                                <select class="form-control shadow-sm" id="positionReceivesDisposition" name="receives_disposition" required>
                                    <option value="1">Ya</option>
                                    <option value="0">Tidak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted">Urutan</label>
                                <input type="number" class="form-control shadow-sm" id="positionOrder" name="order" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted">Label Grup Disposisi</label>
                        <input type="text" class="form-control shadow-sm" id="positionDispositionGroupLabel" name="disposition_group_label">
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted">Label Target Laporan</label>
                        <input type="text" class="form-control shadow-sm" id="positionReportTargetLabel" name="report_target_label">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Jabatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

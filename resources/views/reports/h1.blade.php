@extends('layouts.app')

@section('title', 'Mutasi Pelaporan Kegiatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="mb-4">
            <h2 class="h3 page-title mb-1 text-dark" style="font-weight: 700;">Mutasi Pelaporan Kegiatan</h2>
            <p class="text-muted mb-3">Generate laporan kegiatan berdasarkan rentang mutasi.</p>
            
            <div class="d-inline-block mt-2">
                <div class="bg-white rounded-pill shadow-sm border border-light p-2 d-flex align-items-center">
                    <form action="{{ route('report.h1') }}" method="GET" class="d-flex align-items-center m-0">
                        <!-- Start Date -->
                        <div class="d-flex align-items-center px-3 border-right">
                            <i class="fe fe-calendar text-primary mr-2" style="font-size: 1.1rem;"></i>
                            <div class="d-flex flex-column">
                                <label class="text-uppercase text-muted mb-0" style="font-size:0.65rem; font-weight:700; letter-spacing:0.5px;" for="start_date">Mulai</label>
                                <input type="date" class="form-control form-control-sm border-0 p-0 text-dark font-weight-bold" id="start_date" name="start_date" value="{{ $startDateStr }}" style="box-shadow:none; background:transparent;">
                            </div>
                        </div>
                        <!-- End Date -->
                        <div class="d-flex align-items-center px-3 border-right">
                            <div class="d-flex flex-column">
                                <label class="text-uppercase text-muted mb-0" style="font-size:0.65rem; font-weight:700; letter-spacing:0.5px;" for="end_date">Selesai</label>
                                <input type="date" class="form-control form-control-sm border-0 p-0 text-dark font-weight-bold" id="end_date" name="end_date" value="{{ $endDateStr }}" style="box-shadow:none; background:transparent;">
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="px-2 pl-3">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 py-2 font-weight-bold shadow-sm" style="transition: all 0.2s;">
                                <i class="fe fe-search mr-1"></i> Tampilkan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(isset($reportText))
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title text-dark font-weight-bold mb-0">Hasil Report (Format WhatsApp)</h5>
                    <small class="text-muted">Salin kotak di bawah ini lalu tempel (paste) ke chat WhatsApp.</small>
                </div>
                <button class="btn btn-success rounded-pill px-3 shadow-sm" onclick="copyToClipboard()" style="transition: all 0.2s; font-weight: 600;">
                    <i class="fe fe-copy mr-1"></i> Salin Teks
                </button>
            </div>
            <div class="card-body p-4">
                <div class="form-group mb-0">
                    <textarea class="form-control bg-light border-0 rounded" id="reportText" rows="22" readonly style="font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace; font-size: 0.95rem; resize: none; padding: 1.5rem; color: #334155;">{{ $reportText }}</textarea>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard() {
        var copyText = document.getElementById("reportText");
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */
        document.execCommand("copy");
        
        // Alert or Toast
        Swal.fire({
            icon: 'success',
            title: 'Teks Berhasil Disalin!',
            showConfirmButton: false,
            timer: 1500
        });
    }
</script>
@endpush
@endsection

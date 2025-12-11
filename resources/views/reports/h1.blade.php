@extends('layouts.app')

@section('title', 'Pelaporan Admin H-1')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="page-title">Pelaporan Admin H-1</h2>
        <p class="text-muted">Generate laporan kegiatan untuk di-share ke grup WhatsApp.</p>
        
        <div class="card shadow mb-4">
            <div class="card-header">
                <strong class="card-title">Filter Tanggal</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('report.h1') }}" method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="date" class="sr-only">Tanggal</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ $dateStr }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </form>
            </div>
        </div>

        @if(isset($reportText))
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong class="card-title">Hasil Report (WhatsApp Format)</strong>
                <button class="btn btn-success btn-sm" onclick="copyToClipboard()">
                    <span class="fe fe-copy"></span> Salin Teks
                </button>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <textarea class="form-control" id="reportText" rows="20" readonly style="font-family: monospace;">{{ $reportText }}</textarea>
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

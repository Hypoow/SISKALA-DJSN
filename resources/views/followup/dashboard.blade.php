@extends('layouts.app')

@section('title', 'Dashboard Tindak Lanjut')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="mb-2 page-title">Dashboard Tindak Lanjut Kegiatan</h2>
        <p class="text-muted mb-4">Monitoring status tindak lanjut dan arahan hasil kegiatan.</p>

        @livewire('follow-up-dashboard')
    </div>
</div>
@endsection

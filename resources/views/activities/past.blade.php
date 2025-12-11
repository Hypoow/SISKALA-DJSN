@extends('layouts.app')

@section('title', 'Kegiatan Lampau')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="mb-2 page-title">Kegiatan Lampau</h2>
        <p class="card-text">Daftar kegiatan yang sudah lewat.</p>
        
        <div class="row my-4">
            <div class="col-md-12">
                <livewire:past-activity-list />
            </div> <!-- simple table -->
        </div> <!-- end section -->
    </div> <!-- .col-12 -->
</div> <!-- .row -->
@endsection

@extends('layouts.app')

@section('title', 'Kegiatan Selesai')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        
        <div class="row my-4">
            <div class="col-md-12">
                <livewire:past-activity-list />
            </div> <!-- simple table -->
        </div> <!-- end section -->
    </div> <!-- .col-12 -->
</div> <!-- .row -->
@endsection

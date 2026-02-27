@extends('layouts.app')

@section('title', 'Daftar Kegiatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        
        <livewire:activity-list />

    </div> <!-- .col-12 -->
</div> <!-- .row -->
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data kegiatan yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush

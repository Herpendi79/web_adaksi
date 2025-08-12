@extends('layouts.admin_layout')
@section('title', 'Tambah Rekening')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Tambah Rekening</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Rekening</a></li>
                <li class="breadcrumb-item active">Tambah Rekening</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <form action="{{ route('rekening.store') }}" method="POST"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="row">
                    {{-- Nama Bank --}}
                    <div class="mb-2 col-md-4">
                        <label for="nama_bank" class="form-label m-0">Nama Bank</label>
                        <input type="text" class="form-control @error('nama_bank') is-invalid @enderror"
                            id="nama_bank" name="nama_bank" placeholder="Isi Nama Rekening"
                            value="{{ old('nama_bank') }}">
                        @error('nama_bank')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    {{-- Nomor Rekening --}}
                    <div class="mb-2 col-md-4">
                        <label for="no_rek" class="form-label m-0">Nomor Rekening</label>
                        <input type="number" class="form-control @error('no_rek') is-invalid @enderror"
                            id="no_rek" name="no_rek" placeholder="Isi Nomor Rekening"
                            value="{{ old('no_rek') }}">
                        @error('no_rek')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- Atas Nama --}}
                    <div class="mb-2 col-md-4">
                        <label for="atas_nama" class="form-label m-0">Atas Nama</label>
                        <input type="text" class="form-control @error('atas_nama') is-invalid @enderror"
                            id="atas_nama" name="atas_nama" placeholder="Isi Atas Nama"
                            value="{{ old('atas_nama') }}">
                        @error('atas_nama')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    
                </div>

                
                <div class="d-flex justify-content-start mb-3 gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ url('admin/rekening') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        confirmButtonText: 'OK'
    });
</script>
@endif
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        confirmButtonText: 'OK'
    });
</script>
@endif
@endsection
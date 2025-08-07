@extends('layouts.anggota_layout')
@section('title', 'Tambah Aduan')
@section('content')
    <div class="container-fluid">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Edit Aduan</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Aduan</a></li>
                    <li class="breadcrumb-item active">Edit Aduan</li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <a href="{{ url('anggota/aduan') }}" class="btn btn-secondary me-2">Kembali</a>
                    </div>
                @endif
                <form action="{{ route('aduan.update', $aduan->id_ad) }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="row">
                        {{-- Kategori --}}
                        <div class="mb-2 col-md-12">
                            <label for="kategori" class="form-label m-0">Kategori</label>
                            <input type="text" id="kategori" name="kategori"
                                class="form-control @error('kategori') is-invalid @enderror"
                                placeholder="Masukkan beberapa kategori (pisah dengan koma)" value="{{ $aduan->kategori ?? old('kategori') }}">
                            @error('kategori')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var input = document.querySelector('#kategori');
                                new Tagify(input, {
                                    whitelist: [],
                                    dropdown: {
                                        enabled: 0 // set to 1+ if you want dropdown suggestions
                                    }
                                });
                            });
                        </script>

                    </div>
                    <div class="row">
                        {{-- Judul --}}
                        <div class="mb-2 col-md-12">
                            <label for="nama" class="form-label m-0">Judul Aduan</label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul"
                                name="judul" placeholder="Tuliskan Judul Aduan Anda" value="{{ $aduan->judul ?? old('judul') }}">
                            @error('judul')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        {{-- Deskripsi --}}
                        <div class="mb-2 col-md-12">
                            <label for="deskripsi" class="form-label m-0">Deskripsi Aduan Anda</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5"
                                placeholder="Deskripsi Webinar">{{ $aduan->deskripsi ?? old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        {{-- CKEditor Script --}}
                        <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
                        <script>
                            CKEDITOR.replace('deskripsi');
                        </script>
                    </div>
                    <div class="row">
                        {{-- Hari --}}
                        <div class="mb-2 col-md-4">
                            <label for="lampiran" class="form-label m-0">Foto / File Lampiran</label>
                            <input type="file" class="form-control @error('lampiran') is-invalid @enderror"
                                id="lampiran" name="lampiran[]" multiple>
                            @error('lampiran')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                            @error('lampiran.*')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-start mb-3 gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ url('admin/aduan') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

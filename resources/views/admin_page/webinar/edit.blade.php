@extends('layouts.admin_layout')
@section('title', 'Edit Profile')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Edit Webinar</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Profil</a></li>
                <li class="breadcrumb-item active">Edit Webinar</li>
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
            <form action="{{ route('webinar.update', $webinar->id_wb) }}" method="POST"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Judul --}}
                    <div class="mb-2 col-md-12">
                        <label for="nama" class="form-label m-0">Judul</label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror"
                            id="judul" name="judul" placeholder="Judul Webinar"
                            value="{{ $webinar->judul ?? old('judul') }}">
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
                        <label for="deskripsi" class="form-label m-0">Deskripsi (Detail Kegiatan, Sub Tema, Narsum dll)</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                            id="deskripsi" name="deskripsi" rows="5"
                            placeholder="Deskripsi Webinar">{{ $webinar->deskripsi ?? old('deskripsi') }}</textarea>
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
                        <label for="nama" class="form-label m-0">Hari</label>
                        <input type="text" class="form-control @error('hari') is-invalid @enderror"
                            id="hari" name="hari" placeholder="Misal: Senin sd. Rabu"
                            value="{{ $webinar->hari ?? old('hari') }}">
                        @error('hari')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="mb-2 col-md-4">
                        @php
                        $today = date('Y-m-d');
                        @endphp

                        <label for="tanggal_mulai" class="form-label m-0">Tanggal Mulai</label>
                        <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                            id="tanggal_mulai" name="tanggal_mulai"
                            min="{{ $today }}"
                            value="{{ old('tanggal_mulai', $webinar->tanggal_mulai ?? '') }}">
                        @error('tanggal_mulai')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    {{-- Tanggal Selesai --}}
                    <div class="mb-2 col-md-4">
                        @php
                        $today = date('Y-m-d');
                        @endphp

                        <label for="tanggal_selesai" class="form-label m-0">Tanggal Selesai</label>
                        <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror"
                            id="tanggal_selesai" name="tanggal_selesai"
                            min="{{ $today }}"
                            value="{{ old('tanggal_selesai', $webinar->tanggal_selesai ?? '') }}">
                        @error('tanggal_selesai')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>

                <div class="row">
                    {{-- Pukul --}}
                    <div class="mb-2 col-md-4">
                        <label for="pukul" class="form-label m-0">Pukul</label>
                        <input type="text" class="form-control @error('pukul') is-invalid @enderror"
                            id="pukul" name="pukul" placeholder="Misal : 19.00 - 21.00 WIB"
                            value="{{ $webinar->pukul ?? old('pukul') }}">
                        @error('pukul')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- Link Zoom --}}
                    <div class="mb-2 col-md-4">
                        <label for="link_zoom" class="form-label m-0">Link Zoom</label>
                        <input type="text" class="form-control @error('link_zoom') is-invalid @enderror"
                            id="link_zoom" name="link_zoom" placeholder="https://zoom...."
                            value="{{ $webinar->link_zoom ?? old('link_zoom') }}">
                        @error('link_zoom')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    {{-- Kepesertaan --}}
                    <div class="mb-2 col-md-4">
                        <label class="form-label mb-1">Kepesertaan</p></label>
                        @php
                        $selectedKepesertaan = old('bayar_free', $webinar->bayar_free ?? '');
                        @endphp
                        <div class="form-check">
                            <input class="form-check-input @error('bayar_free') is-invalid @enderror" type="radio"
                                name="bayar_free" id="bayar" value="bayar"
                                {{ $selectedKepesertaan == 'bayar' ? 'checked' : '' }}>
                            <label class="form-check-label" for="bayar">Berbayar</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input @error('bayar_free') is-invalid @enderror" type="radio"
                                name="bayar_free" id="free" value="free"
                                {{ $selectedKepesertaan == 'free' ? 'checked' : '' }}>
                            <label class="form-check-label" for="free">Free</label>
                        </div>

                        @error('bayar_free')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>


                    <div class="row">
                        {{-- Anggota Aktif --}}
                        <div class="mb-2 col-md-4">
                            <label for="biaya_anggota_aktif_display" class="form-label m-0">Biaya Anggota Aktif</label>
                            <input type="text" class="form-control format-rupiah" id="biaya_anggota_aktif_display" placeholder="Rp....">
                            <input type="hidden" name="biaya_anggota_aktif" id="biaya_anggota_aktif"
                                value="{{ old('biaya_anggota_aktif', $webinar->biaya_anggota_aktif ?? 0) }}"
                                data-default="{{ $webinar->biaya_anggota_aktif ?? 0 }}">
                        </div>

                        {{-- Anggota Non Aktif --}}
                        <div class="mb-2 col-md-4">
                            <label for="biaya_anggota_non_aktif_display" class="form-label m-0">Biaya Anggota Non Aktif</label>
                            <input type="text" class="form-control format-rupiah" id="biaya_anggota_non_aktif_display" placeholder="Rp....">
                            <input type="hidden" name="biaya_anggota_non_aktif" id="biaya_anggota_non_aktif"
                                value="{{ old('biaya_anggota_non_aktif', $webinar->biaya_anggota_non_aktif ?? 0) }}"
                                data-default="{{ $webinar->biaya_anggota_non_aktif ?? 0 }}">
                        </div>

                        {{-- Non Anggota --}}
                        <div class="mb-2 col-md-4">
                            <label for="biaya_non_anggota_display" class="form-label m-0">Biaya Non Anggota</label>
                            <input type="text" class="form-control format-rupiah" id="biaya_non_anggota_display" placeholder="Rp....">
                            <input type="hidden" name="biaya_non_anggota" id="biaya_non_anggota"
                                value="{{ old('biaya_non_anggota', $webinar->biaya_non_anggota ?? 0) }}"
                                data-default="{{ $webinar->biaya_non_anggota ?? 0 }}">
                        </div>

                    </div>

                    <script>
                        function formatRupiah(angka) {
                            if (!angka) return 'Rp0';
                            return 'Rp' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }

                        function unformatRupiah(rpString) {
                            return rpString.replace(/[^\d]/g, '');
                        }

                        function updateRupiahDisplay(id) {
                            const inputDisplay = document.getElementById(id + '_display');
                            const inputHidden = document.getElementById(id);

                            // Format saat load
                            inputDisplay.value = formatRupiah(inputHidden.value);

                            // Format saat diketik
                            inputDisplay.addEventListener('input', function() {
                                const clean = unformatRupiah(inputDisplay.value);
                                inputHidden.value = clean;
                                inputDisplay.value = formatRupiah(clean);
                            });
                        }

                        function toggleBiayaInputs() {
                            const isFree = document.getElementById('free').checked;

                            const biayaFields = [
                                'biaya_anggota_aktif',
                                'biaya_anggota_non_aktif',
                                'biaya_non_anggota'
                            ];

                            biayaFields.forEach(id => {
                                const inputDisplay = document.getElementById(id + '_display');
                                const inputHidden = document.getElementById(id);
                                if (isFree) {
                                    inputHidden.value = '0';
                                    inputDisplay.value = formatRupiah(0);
                                    inputDisplay.setAttribute('readonly', true);
                                } else {
                                    const defaultVal = inputHidden.dataset.default || '0';
                                    inputHidden.value = defaultVal;
                                    inputDisplay.value = formatRupiah(defaultVal);
                                    inputDisplay.removeAttribute('readonly');
                                }
                            });
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            // Init rupiah formatting
                            updateRupiahDisplay('biaya_anggota_aktif');
                            updateRupiahDisplay('biaya_anggota_non_aktif');
                            updateRupiahDisplay('biaya_non_anggota');

                            // Apply logic when free/bayar toggled
                            toggleBiayaInputs();
                            document.getElementById('bayar').addEventListener('change', toggleBiayaInputs);
                            document.getElementById('free').addEventListener('change', toggleBiayaInputs);
                        });
                    </script>




                    <div class="row">
                        {{-- Moderator --}}
                        <div class="mb-2 col-md-4">
                            <label for="moderator" class="form-label m-0">Moderator</label>
                            <input type="text" class="form-control @error('moderator') is-invalid @enderror"
                                id="moderator" name="moderator" placeholder="Misal: Jhon & Harsi"
                                value="{{ $webinar->pukul ?? old('moderator') }}">
                            @error('moderator')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- Sertifikat Depan --}}
                        <div class="mb-1 col-md-2">
                            <label for="sertifikat_depan" class="form-label mb-1">Sertifikat Depan</label>
                            <input type="file"
                                class="form-control @error('sertifikat_depan') is-invalid @enderror"
                                id="sertifikat_depan"
                                name="sertifikat_depan">
                            @error('sertifikat_depan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        {{-- Sertifikat Belakang --}}
                        <div class="mb-1 col-md-2">
                            <label for="sertifikat_belakang" class="form-label mb-1">Sertifikat Belakang</label>
                            <input type="file"
                                class="form-control @error('sertifikat_belakang') is-invalid @enderror"
                                id="sertifikat_belakang"
                                name="sertifikat_belakang">
                            @error('sertifikat_belakang')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        {{-- Flyer --}}
                        <div class="mb-2 col-md-4">
                            <label for="flyer" class="form-label mb-1">Flyer</label>
                            <input type="file"
                                class="form-control @error('flyer') is-invalid @enderror"
                                id="flyer"
                                name="flyer">
                            @error('flyer')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        {{-- Label dan Tombol Tambah --}}
                        <label for="fasilitas" class="form-label mb-1">Link Fasilitas (Misal :link PPT, link rekaman, link undangan, link presensi dll)</label>
                        <div class="d-flex justify-content-start mb-3">
                            <button type="button" id="add-fasilitas" class="btn btn-info">+</button>
                        </div>

                        {{-- Container untuk Input Fasilitas --}}
                        <div id="fasilitas-container">
                            @foreach ($webinar->fasilitas as $i => $fas)
                            <div class="row mb-2 fasilitas-item">
                                <input type="hidden" name="fasilitas[{{ $i }}][id_fas]" value="{{ $fas->id_fas }}">

                                <div class="col-md-5">
                                    <label class="form-label m-0">Nama Fasilitas {{ $i+1 }}</label>
                                    <input type="text" class="form-control @error(" fasilitas.$i.nama") is-invalid @enderror" name="fasilitas[{{ $i }}][nama]" value="{{ old("fasilitas.$i.nama", $fas->nama) }}">
                                    @error("fasilitas.$i.nama")
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label m-0">Link Fasilitas {{ $i+1 }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error(" fasilitas.$i.link") is-invalid @enderror" name="fasilitas[{{ $i }}][link]" value="{{ old("fasilitas.$i.link", $fas->link) }}"
                                            placeholder="Contoh -> https://www.adaksi.org/">
                                        <button type="button" class="btn btn-danger remove-fasilitas">Hapus</button>
                                    </div>
                                    @error("fasilitas.$i.link")
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>



                    {{-- SCRIPT --}}
                    <script>
                        let fasilitasIndex = 1;

                        document.getElementById('add-fasilitas').addEventListener('click', function() {
                            const container = document.getElementById('fasilitas-container');

                            const row = document.createElement('div');
                            row.classList.add('row', 'mb-2', 'fasilitas-item');
                            row.innerHTML = `
                                <div class="col-md-5">
                                    <label class="form-label m-0">Nama Fasilitas ${fasilitasIndex + 1}</label>
                                    <input type="text" class="form-control" name="fasilitas[${fasilitasIndex}][nama]" placeholder="Nama Fasilitas">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label m-0">Link Fasilitas ${fasilitasIndex + 1}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="fasilitas[${fasilitasIndex}][link]" placeholder="Link Fasilitas">
                                        <button type="button" class="btn btn-danger remove-fasilitas">Batal</button>
                                    </div>
                                </div>
                            `;
                            container.appendChild(row);
                            fasilitasIndex++;
                        });

                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-fasilitas')) {
                                const item = e.target.closest('.fasilitas-item');
                                item.remove();
                            }
                        });
                    </script>



                </div>

                <hr>
                <div class="d-flex justify-content-start mb-3">
                    <a href="{{ url('admin/webinar') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>
</div>


@endsection
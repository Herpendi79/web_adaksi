@extends('layouts.admin_layout')
@section('title', 'Tambah Webinar')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Tambah Webinar</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Webinar</a></li>
                <li class="breadcrumb-item active">Tambah Webinar</li>
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
            <form action="{{ route('webinar.store') }}" method="POST"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                     <div class="row">
                        {{-- Judul --}}
                       <div class="mb-2 col-md-12">
                            <label for="nama" class="form-label m-0">Judul</label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                id="judul" name="judul" placeholder="Judul Webinar"
                                value="{{ old('judul') }}">
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
                                  placeholder="Deskripsi Webinar">{{ old('deskripsi') }}</textarea>
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
                            value="{{ old('hari') }}">
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
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const tanggalMulai = document.getElementById('tanggal_mulai');
                                const tanggalSelesai = document.getElementById('tanggal_selesai');
                        
                                function setTanggalSelesaiMin() {
                                    tanggalSelesai.min = tanggalMulai.value;
                                    if (tanggalSelesai.value < tanggalMulai.value) {
                                        tanggalSelesai.value = tanggalMulai.value;
                                    }
                                }
                        
                                tanggalMulai.addEventListener('change', setTanggalSelesaiMin);
                        
                                // Jalankan saat pertama kali halaman dimuat
                                setTanggalSelesaiMin();
                            });
                        </script>
                
                    <div class="row">
                    {{-- Pukul --}}
                    <div class="mb-2 col-md-4">
                        <label for="pukul" class="form-label m-0">Pukul</label>
                        <input type="text" class="form-control @error('pukul') is-invalid @enderror"
                            id="pukul" name="pukul" placeholder="Misal : 19.00 - 21.00 WIB"
                            value="{{ old('pukul') }}">
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
                                value="{{ old('link_zoom') }}">
                            @error('link_zoom')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    {{-- Kepesertaan --}}
                            <div class="mb-2 col-md-4">
                                <label class="form-label mb-1">Kepesertaan</label>
                            
                                <div class="form-check">
                                    <input class="form-check-input @error('bayar_free') is-invalid @enderror" type="radio"
                                        name="bayar_free" id="bayar" value="bayar"
                                        {{ old('bayar_free') == 'Bayar' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bayar">Berbayar</label>
                                </div>
                            
                                <div class="form-check">
                                    <input class="form-check-input @error('bayar_free') is-invalid @enderror" type="radio"
                                        name="bayar_free" id="free" value="free"
                                        {{ old('bayar_free') == 'Free' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="free">Free</label>
                                </div>
                            </div>
              
                    <div class="row">
                        {{-- Anggota Aktif --}}
                        <div class="mb-2 col-md-4">
                            <label for="biaya_anggota_aktif" class="form-label m-0">Biaya Anggota Aktif</label>
                            <input type="text" class="form-control" id="biaya_anggota_aktif" name="biaya_anggota_aktif"
                                   placeholder="Rp...." value="{{ old('biaya_anggota_aktif') }}">
                        </div>
                    
                        {{-- Anggota Non Aktif --}}
                        <div class="mb-2 col-md-4">
                            <label for="biaya_anggota_non_aktif" class="form-label m-0">Biaya Anggota Non Aktif</label>
                            <input type="text" class="form-control" id="biaya_anggota_non_aktif"
                                   name="biaya_anggota_non_aktif" placeholder="Rp...."
                                   value="{{ old('biaya_anggota_non_aktif') }}">
                        </div>
                    
                        {{-- Non Anggota --}}
                        <div class="mb-2 col-md-4">
                            <label for="biaya_non_anggota" class="form-label m-0">Biaya Non Anggota</label>
                            <input type="text" class="form-control" id="biaya_non_anggota"
                                   name="biaya_non_anggota" placeholder="Rp...."
                                   value="{{ old('biaya_non_anggota') }}">
                        </div>
                    </div>

                    {{-- SCRIPT --}}
                    <script>
                        // Fungsi format rupiah
                        function formatRupiah(angka) {
                            if (!angka) return '';
                            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                                split   	 = number_string.split(','),
                                sisa     	 = split[0].length % 3,
                                rupiah     	 = split[0].substr(0, sisa),
                                ribuan     	 = split[0].substr(sisa).match(/\d{3}/gi);
                    
                            if(ribuan){
                                let separator = sisa ? '.' : '';
                                rupiah += separator + ribuan.join('.');
                            }
                    
                            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                            return rupiah;
                        }
                    
                        // Fungsi untuk menghandle input dan format otomatis
                        function inputRupiahHandler(event) {
                            const input = event.target;
                            const cursorPos = input.selectionStart;
                            const oldLength = input.value.length;
                    
                            input.value = formatRupiah(input.value);
                    
                            // Mengatur ulang posisi cursor agar tidak lompat-lompat
                            const newLength = input.value.length;
                            const diff = newLength - oldLength;
                            input.selectionEnd = cursorPos + diff;
                        }
                    
                        // Fungsi toggle disable input
                        function toggleBiayaInputs() {
                            const isFree = document.getElementById('free').checked;
                            const fields = ['biaya_anggota_aktif', 'biaya_anggota_non_aktif', 'biaya_non_anggota'];
                            fields.forEach(id => {
                                const el = document.getElementById(id);
                                el.disabled = isFree;
                                if (isFree) {
                                    el.value = 0;
                                }
                            });
                        }
                    
                        document.addEventListener('DOMContentLoaded', function () {
                            // Pasang event listener untuk tiap input biaya
                            ['biaya_anggota_aktif', 'biaya_anggota_non_aktif', 'biaya_non_anggota'].forEach(id => {
                                const el = document.getElementById(id);
                                el.addEventListener('input', inputRupiahHandler);
                            });
                    
                            toggleBiayaInputs();
                    
                            document.getElementById('bayar').addEventListener('change', toggleBiayaInputs);
                            document.getElementById('free').addEventListener('change', toggleBiayaInputs);
                        });
                    </script>


                    <div class="row">
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
                         {{-- Sertifikat Depan --}}
                        <div class="mb-1 col-md-4">
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
                        <div class="mb-1 col-md-4">
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
                         
                    </div>

                    <div class="row">
                        {{-- Moderator --}}
                        <div class="mb-2 col-md-4">
                            <label for="moderator" class="form-label m-0">Moderator</label>
                            <input type="text" class="form-control @error('moderator') is-invalid @enderror"
                                id="moderator" name="moderator" placeholder="Misal: Jhon & Harsi"
                                value="{{ old('moderator') }}">
                            @error('moderator')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        {{-- No Surat --}}
                        <div class="mb-2 col-md-2">
                            <label for="no_surat" class="form-label m-0">Nomor Surat</label>
                            <input type="text" class="form-control @error('no_surat') is-invalid @enderror"
                                id="no_surat" name="no_surat" placeholder="No. Surat Terakhir"
                                value="{{ old('no_surat') }}">
                            @error('no_surat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        {{-- angakatan --}}
                        <div class="mb-2 col-md-2">
                            <label for="angkatan" class="form-label m-0">Angakatan ADAKSI</label>
                             <select class="form-select @error('angkatan') is-invalid @enderror" id="angkatan" name="angkatan">
                                        <option value="" disabled selected>-- Pilih Angkatan --</option>
                                        <option value="I">I</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                    </select>
                            @error('angkatan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        {{-- unit --}}
                        <div class="mb-2 col-md-4">
                            <label for="unit" class="form-label m-0">Unit</label>
                             <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit">
                                        <option value="" disabled selected>-- Pilih Unit --</option>
                                        <option value="DPP">DPP</option>
                                        <option value="DPW">DPW</option>
                                        <option value="DPC">DPC</option>
                                    </select>
                            @error('unit')
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
                        @php
                            $oldFasilitas = old('fasilitas') ?? [['nama' => '', 'link' => '']];
                        @endphp
                    
                        @foreach ($oldFasilitas as $i => $fas)
                            <div class="row mb-2 fasilitas-item">
                                <div class="col-md-5">
                                    <label class="form-label m-0">Nama Fasilitas {{ $i + 1 }}</label>
                                    <input type="text" class="form-control @error("fasilitas.$i.nama") is-invalid @enderror" 
                                           name="fasilitas[{{ $i }}][nama]" value="{{ old("fasilitas.$i.nama") }}"
                                           placeholder="Nama Fasilitas">
                                    @error("fasilitas.$i.nama")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label m-0">Link Fasilitas {{ $i + 1 }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error("fasilitas.$i.link") is-invalid @enderror"
                                               name="fasilitas[{{ $i }}][link]" value="{{ old("fasilitas.$i.link") }}"
                                               placeholder="Contoh -> https://www.adaksi.org/">
                                        <button type="button" class="btn btn-danger remove-fasilitas">Batal</button>
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
                        let fasilitasIndex = {{ count(old('fasilitas') ?? [['nama'=>'','link'=>'']]) }};
                    
                        document.getElementById('add-fasilitas').addEventListener('click', function () {
                            const container = document.getElementById('fasilitas-container');
                    
                            const row = document.createElement('div');
                            row.classList.add('row', 'mb-2', 'fasilitas-item');
                            row.innerHTML = `
                                <div class="col-md-5">
                                    <label class="form-label m-0">Nama Fasilitas ${fasilitasIndex + 1}</label>
                                    <input type="text" class="form-control" name="fasilitas[${fasilitasIndex}][nama]" placeholder="Nama Fasilitas">
                                    <div class="invalid-feedback d-block"></div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label m-0">Link Fasilitas ${fasilitasIndex + 1}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="fasilitas[${fasilitasIndex}][link]" placeholder="Link Fasilitas">
                                        <button type="button" class="btn btn-danger remove-fasilitas">Batal</button>
                                    </div>
                                    <div class="invalid-feedback d-block"></div>
                                </div>
                            `;
                            container.appendChild(row);
                            fasilitasIndex++;
                        });
                    
                        document.addEventListener('click', function (e) {
                            if (e.target.classList.contains('remove-fasilitas')) {
                                const item = e.target.closest('.fasilitas-item');
                                item.remove();
                            }
                        });
                    </script>

                   
                    
                    <hr>
                <div class="d-flex justify-content-start mb-3 gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ url('admin/webinar') }}" class="btn btn-secondary">Kembali</a>
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
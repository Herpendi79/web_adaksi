@extends('layouts.admin_layout')
@section('title', 'Tambah Rakernas')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Tambah Rakernas</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Rakernas</a></li>
                <li class="breadcrumb-item active">Tambah Rakernas</li>
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
            <form action="{{ route('rakernas.store') }}" method="POST"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="row">
                    {{-- Judul --}}
                    <div class="mb-2 col-md-12">
                        <label for="tema" class="form-label m-0">Tema</label>
                        <input type="text" class="form-control @error('tema') is-invalid @enderror"
                            id="tema" name="tema" placeholder="Tema Rakernas"
                            value="{{ old('tema') }}">
                        @error('tema')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Tempat --}}
                    <div class="mb-2 col-md-4">
                        <label for="tempat" class="form-label m-0">Tempat</label>
                        <input type="text" class="form-control @error('tempat') is-invalid @enderror"
                            id="tempat" name="tempat" placeholder="Misal: Jakarta"
                            value="{{ old('tempat') }}">
                        @error('tempat')
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
                            value="{{ old('tanggal_mulai') }}">
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
                            value="{{ old('tanggal_selesai') }}">
                        @error('tanggal_selesai')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
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
                    {{-- Biaya --}}
                    <div class="mb-2 col-md-4">
                        <label for="biaya" class="form-label m-0">Biaya Kepesertaan</label>
                        <input type="text" class="form-control" id="biaya" name="biaya"
                            placeholder="Rp...." value="{{ old('biaya') }}">
                        @error('biaya')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <script>
                        document.getElementById('biaya').addEventListener('input', function(e) {
                            let value = e.target.value.replace(/[^,\d]/g, '');
                            if (value) {
                                let formatted = formatRupiah(value, 'Rp ');
                                e.target.value = formatted;
                            } else {
                                e.target.value = '';
                            }
                        });

                        function formatRupiah(angka, prefix) {
                            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                                split = number_string.split(','),
                                sisa = split[0].length % 3,
                                rupiah = split[0].substr(0, sisa),
                                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                            if (ribuan) {
                                let separator = sisa ? '.' : '';
                                rupiah += separator + ribuan.join('.');
                            }

                            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                            return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
                        }
                    </script>

                    {{-- Fasilitas--}}
                    <div class="mb-2 col-md-8">
                        <label for="fasilitas" class="form-label m-0">Fasilitas</label>
                        <input type="text" class="form-control @error('fasilitas') is-invalid @enderror"
                            id="fasilitas" name="fasilitas" placeholder="Misal: Starter KIT, Baju dll"
                            value="{{ old('fasilitas') }}">
                        @error('fasilitas')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <hr>
                <div class="row">
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
                    {{-- angkatan --}}
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
                    <div class="mb-2 col-md-2">
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
                </div>
                <div class="d-flex justify-content-start mb-3 gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ url('admin/rakernas') }}" class="btn btn-secondary">Kembali</a>
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
@extends('layouts.admin_layout')
@php use Illuminate\Support\Str; @endphp
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

<?php
$main_data = 'Biaya Anggota';
$url = '/admin/biaya';
?>
@section('title', $main_data)
@section('content')
    <div class="container-fluid">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">
                    {{ $main_data }}
                </h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ url($url) }}">
                            Pengguna
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url($url) }}">
                            {{ $main_data }}
                        </a>
                    </li>
                </ol>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div class="d-md-flex align-items-center">
                            <div>
                                <h5 class="card-title mb-1">Biaya Pendaftaran & Iuran Keanggotaan</h5>
                                <p class="card-text text-muted mb-0">Berikut adalah daftar Biaya Pendaftaran & Iuran
                                    Keanggotaan yang terdaftar dalam
                                    sistem.</p>
                            </div>
                            <form class="d-flex flex-stack flex-wrap gap-1 justify-content-md-end ms-auto" method="GET"
                                action="{{ url($url) }}">

                                <div class="position-relative topbar-search">
                                    <input name="search" type="text"
                                        class="form-control bg-light-subtle ps-4 py-1 border fs-14" placeholder="Search..."
                                        value="{{ request('search') }}">
                                    <i
                                        class="mdi mdi-magnify fs-16 position-absolute text-dark top-50 translate-middle-y ms-2"></i>
                                </div>
                                {{-- Filter --}}
                                <button type="submit"
                                    class="btn btn-outline-info btn-sm d-flex align-items-center gap-1 ms-2">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                            </form>
                            <a href="/admin/biaya" class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </a>
                            <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-1 ms-2"
                                data-bs-toggle="modal" data-bs-target="#modalTambahBiaya">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                            <!-- Modal Tambah Biaya -->
                            <div class="modal fade" id="modalTambahBiaya" tabindex="-1"
                                aria-labelledby="modalTambahBiayaLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('biaya.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalTambahBiayaLabel">Tambah Biaya Anggota
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="keterangan" class="form-label">Keterangan</label>
                                                    <select class="form-select @error('keterangan') is-invalid @enderror"
                                                        id="keterangan" name="keterangan" required>
                                                        <option value="">-- Pilih Keterangan --</option>
                                                        @foreach ($biayamodal as $item)
                                                            <option value="{{ $item }}"
                                                                {{ old('keterangan') == $item ? 'selected' : '' }}>
                                                                {{ $item }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('keterangan')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="nominal" class="form-label m-0">Nominal</label>
                                                    <input type="text" class="form-control" id="nominal" name="nominal"
                                                        placeholder="Rp...." value="{{ old('nominal') }}">
                                                    @error('nominal')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <script>
                                                    document.getElementById('nominal').addEventListener('input', function(e) {
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
                                                <div class="mb-3">
                                                    <label for="berlaku_mulai" class="form-label">Berlaku Mulai</label>
                                                    <input type="date"
                                                        class="form-control @error('berlaku_mulai') is-invalid @enderror"
                                                        id="berlaku_mulai" name="berlaku_mulai"
                                                        value="{{ old('berlaku_mulai') }}">
                                                    @error('berlaku_mulai')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="berlaku_sampai" class="form-label">Berlaku
                                                        Sampai</label>
                                                    <input type="date"
                                                        class="form-control @error('berlaku_sampai') is-invalid @enderror"
                                                        id="berlaku_sampai" name="berlaku_sampai"
                                                        value="{{ old('berlaku_sampai') }}">
                                                    @error('berlaku_sampai')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const tanggalMulai = document.getElementById('berlaku_mulai');
                                                        const tanggalSelesai = document.getElementById('berlaku_sampai');

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
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-dark btn-sm"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-traffic mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Keterangan</th>
                                        <th>Nominal Biaya</th>
                                        <th>Berlaku Mulai</th>
                                        <th>Berlaku Sampai</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($biaya as $key => $data)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <p class="mb-0 fw-medium fs-14">{{ $data->keterangan }}</p>
                                            </td>
                                            <td>
                                                <p class="mb-0 fw-medium fs-14">Rp
                                                    {{ number_format($data->nominal, 0, ',', '.') }}</p>
                                            </td>
                                            <td>
                                                <p class="mb-0 fw-medium fs-14">
                                                    {{ $data->berlaku_mulai ? \Carbon\Carbon::parse($data->berlaku_mulai)->translatedFormat('d F Y') : '-' }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="mb-0 fw-medium fs-14">
                                                    {{ $data->berlaku_sampai ? \Carbon\Carbon::parse($data->berlaku_sampai)->translatedFormat('d F Y') : '-' }}
                                                </p>
                                            </td>
                                            <td>
                                                <button type="button" aria-label="anchor"
                                                    class="btn btn-icon btn-sm bg-info-subtle me-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalHapus{{ $data->id }}"
                                                    data-bs-original-title="Hapus">
                                                    <i class="mdi mdi-pencil fs-14 text-info"></i>
                                                </button>

                                                <!-- Modal Validasi -->
                                                <div class="modal fade" id="modalHapus{{ $data->id }}"
                                                    tabindex="-1" aria-labelledby="modalHapus{{ $data->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form action="{{ url($url . '/update/' . $data->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('POST')
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="modalHapus{{ $data->id }}">
                                                                        Edit Biaya Anggota
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="keterangan_edit_{{ $data->id }}"
                                                                            class="form-label">Keterangan</label>
                                                                        <select class="form-select"
                                                                            id="keterangan_edit_{{ $data->id }}"
                                                                            name="keterangan" required>
                                                                            <option value="">-- Pilih Keterangan --
                                                                            </option>
                                                                            @foreach ($biayamodal as $item)
                                                                                <option value="{{ $item }}"
                                                                                    {{ $data->keterangan == $item ? 'selected' : '' }}>
                                                                                    {{ $item }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="nominal_edit_{{ $data->id }}"
                                                                            class="form-label m-0">Nominal</label>
                                                                        <input type="text" class="form-control"
                                                                            id="nominal_edit_{{ $data->id }}"
                                                                            name="nominal" placeholder="Rp...."
                                                                            value="{{ 'Rp ' . number_format($data->nominal, 0, ',', '.') }}">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label
                                                                            for="berlaku_mulai_edit_{{ $data->id }}"
                                                                            class="form-label">Berlaku Mulai</label>
                                                                        <input type="date" class="form-control"
                                                                            id="berlaku_mulai_edit_{{ $data->id }}"
                                                                            name="berlaku_mulai"
                                                                            value="{{ $data->berlaku_mulai ? \Carbon\Carbon::parse($data->berlaku_mulai)->format('Y-m-d') : '' }}">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label
                                                                            for="berlaku_sampai_edit_{{ $data->id }}"
                                                                            class="form-label">Berlaku Sampai</label>
                                                                        <input type="date" class="form-control"
                                                                            id="berlaku_sampai_edit_{{ $data->id }}"
                                                                            name="berlaku_sampai"
                                                                            value="{{ $data->berlaku_sampai ? \Carbon\Carbon::parse($data->berlaku_sampai)->format('Y-m-d') : '' }}">
                                                                    </div>
                                                                    <script>
                                                                        document.addEventListener('DOMContentLoaded', function() {
                                                                            const nominalInput = document.getElementById('nominal_edit_{{ $data->id }}');
                                                                            if (nominalInput) {
                                                                                nominalInput.addEventListener('input', function(e) {
                                                                                    let value = e.target.value.replace(/[^,\d]/g, '');
                                                                                    if (value) {
                                                                                        let formatted = formatRupiah(value, 'Rp ');
                                                                                        e.target.value = formatted;
                                                                                    } else {
                                                                                        e.target.value = '';
                                                                                    }
                                                                                });
                                                                            }

                                                                            // Validasi tanggal berlaku_sampai >= berlaku_mulai
                                                                            const mulai = document.getElementById('berlaku_mulai_edit_{{ $data->id }}');
                                                                            const sampai = document.getElementById('berlaku_sampai_edit_{{ $data->id }}');
                                                                            if (mulai && sampai) {
                                                                                function setSampaiMin() {
                                                                                    sampai.min = mulai.value;
                                                                                    if (sampai.value < mulai.value) {
                                                                                        sampai.value = mulai.value;
                                                                                    }
                                                                                }
                                                                                mulai.addEventListener('change', setSampaiMin);
                                                                                // Jalankan saat pertama kali modal dibuka
                                                                                setSampaiMin();
                                                                            }
                                                                        });
                                                                    </script>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-dark btn-sm"
                                                                        data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary btn-sm">Simpan</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer pt-3 pb-0 border-top">
                        {{ $biaya->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Script Notifikasi Validasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK'
            });
        </script>
    @endif



@endsection

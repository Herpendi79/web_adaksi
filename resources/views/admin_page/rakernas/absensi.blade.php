@extends('layouts.admin_layout')
@php use Illuminate\Support\Str; @endphp

<?php
$main_data = 'Absensi Rakernas';
$url = '/admin/rakernas';
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
                                <div>
                                    <h5>Total Absensi Berdasarkan Jenis Pengurus</h5>
                                    <ul>
                                        <li>DPP: {{ $totalAbsensiByPengurus['DPP'] ?? 0 }}</li>
                                        <li>DPW: {{ $totalAbsensiByPengurus['DPW'] ?? 0 }}</li>
                                        <li>DPC: {{ $totalAbsensiByPengurus['DPC'] ?? 0 }}</li>
                                        <li>Anggota Biasa: {{ $totalAbsensiByPengurus['Anggota Biasa'] ?? 0 }}</li>
                                    </ul>


                                </div>
                            </div>
                            <form class="d-flex flex-stack flex-wrap gap-1 justify-content-md-end ms-auto">
                                {{-- Selection Status --}}
                                @php
                                    use Carbon\Carbon;
                                @endphp

                                <select name="kehadiran"
                                    class="form-select form-select-sm bg-light-subtle border fw-medium me-2"
                                    style="width: auto;">
                                    <option value="">Pilih Tanggal</option>

                                    @if ($rakernas)
                                        @php
                                            $start = Carbon::parse($rakernas->tanggal_mulai);
                                            $end = Carbon::parse($rakernas->tanggal_selesai);
                                        @endphp

                                        @for ($date = $start->copy(); $date->lte($end); $date->addDay())
                                            <option value="{{ $date->format('Y-m-d') }}"
                                                {{ request('kehadiran') == $date->format('Y-m-d') ? 'selected' : '' }}>
                                                {{ $date->translatedFormat('l, d F Y') }}
                                            </option>
                                        @endfor
                                    @else
                                        <option disabled>Tidak ada tanggal tersedia</option>
                                    @endif

                                </select>


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

                                <a href="{{ route('rakernas.absensi', $rakernas->id_rk) }}"
                                    class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                    <i class="mdi mdi-refresh"></i> Refresh
                                </a>
                                <a href="{{ route('admin.rakernas.absensi_create', $rakernas->id_rk) }}"
                                    class="btn btn-success btn-sm d-flex align-items-center gap-1 ms-2" target="_blank">
                                    <i class="mdi mdi-plus"></i> Absen
                                </a>

                            </form>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-traffic mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Kehadiran</th>
                                        <th>ID ADAKSI</th>
                                        <th>Nama</th>
                                        <th>Home Base</th>
                                        <th>Provinsi</th>
                                        <th>Perwakilan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($absensi_rakernas as $index => $absen)
                                        @php
                                            $anggota = $absen->AbsenPendaftar->anggota ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($absen->kehadiran)->translatedFormat('d F Y') }}
                                            </td>
                                            <td>{{ $anggota->id_card ?? '-' }}</td>
                                            <td>{{ $anggota->nama_anggota ?? '-' }}</td>
                                            <td>{{ $anggota->homebase_pt ?? '-' }}</td>
                                            <td>{{ $anggota->provinsi ?? '-' }}</td>
                                            <td>{{ $absen->AbsenPendaftar->pengurus ?? '-' }}</td> {{-- ✅ Diperbaiki --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data absensi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>

                    <div class="card-footer pt-3 pb-0 border-top">
                        {{ $absensi_rakernas->links('vendor.pagination.bootstrap-5') }}
                        {{-- <div class="row align-items-center">
                            <div class="col-sm">
                                <div class="text-block text-center text-sm-start">
                                    <span class="fw-medium">1 of 3</span>
                                </div>
                            </div>
                            <div class="col-sm-auto mt-3 mt-sm-0">
                                <div class="pagination gap-2 justify-content-center py-3 ps-0 pe-3">
                                    <ul class="pagination mb-0">
                                        <li class="page-item disabled">
                                            <a class="page-link me-2 rounded-2" href="javascript:void(0);"> Prev </a>
                                        </li>
                                        <li class="page-item active">
                                            <a class="page-link rounded-2 me-2" href="#" data-i="1"
                                                data-page="5">1</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link me-2 rounded-2" href="#" data-i="2"
                                                data-page="5">2</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link text-primary rounded-2" href="javascript:void(0);">Next
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div> --}}
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
                title: 'success!',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK'
            });
        </script>
    @endif


@endsection

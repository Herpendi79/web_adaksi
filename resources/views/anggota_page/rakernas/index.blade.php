@extends('layouts.anggota_layout')
@php use Illuminate\Support\Str; @endphp
@php
use Carbon\Carbon;
Carbon::setLocale('id');
@endphp

<?php
$main_data = 'Rakernas';
$url = '/anggota/rakernas';
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
                        Kegiatan
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
                            <h5 class="card-title mb-1">Daftar Kegiatan Rakernas</h5>
                        </div>
                        <form class="d-flex flex-stack flex-wrap gap-1 justify-content-md-end ms-auto">

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

                            <a href="/anggota/webinar"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </a>

                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Tema</th>
                                    <th>Tempat</th>
                                    <th>Tanggal</th>
                                    <th>Biaya</th>
                                    <th>Fasilitas</th>
                                    <th>QR Code</th>
                                    <th>Kepesertaan</th>
                                    <th>Status Acara</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rakernas as $key => $data)
                                <tr>
                                    <td>{{ $rakernas->firstItem() + $key }}</td>
                                    <td>{{ Str::limit($data->tema, 30) }}</td>
                                    <td>{{ $data->tempat }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($data->tanggal_mulai)->translatedFormat('d F Y') }}
                                        s.d.
                                        {{ \Carbon\Carbon::parse($data->tanggal_selesai)->translatedFormat('d F Y') }}
                                    </td>
                                    <td>Rp {{ number_format($data->biaya) }}</td>
                                    <td>{{ $data->fasilitas }}</td>
                                    <td>
                                        @php
                                        $pendaftar = $data->pendaftar->first();
                                        @endphp
                                        @if($pendaftar && filled($pendaftar->qrcode))
                                        <button type="button" aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-info-subtle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalValidasi{{ $data->id_prk }}"
                                            data-bs-original-title="Validasi">
                                            <i class="mdi mdi-eye fs-14 text-info"></i>
                                        </button>
                                        <!-- Modal Validasi -->
                                        <div class="modal fade" id="modalValidasi{{ $data->id_prk }}"
                                            tabindex="-1"
                                            aria-labelledby="modalValidasiLabel{{ $data->id_prk }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form
                                                    action=""
                                                    method="">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalValidasiLabel{{ $data->id_prk }}">
                                                                QR Code Peserta
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3 w-100">
                                                                @if ($pendaftar->qrcode)
                                                                <div class="mb-2 d-flex justify-content-center">
                                                                    <img src="{{ asset('uploads/qrcode/' . $pendaftar->qrcode) }}"
                                                                        alt="QR Code"
                                                                        class="img-fluid rounded"
                                                                        style="max-width: 300px;">
                                                                </div>

                                                                <div class="text-center mb-2">
                                                                    <a href="{{ asset('uploads/qrcode/' . $pendaftar->qrcode) }}"
                                                                        download="QRCode_Rakernas_{{ $pendaftar->id_prk }}.png"
                                                                        class="btn btn-success btn-sm">
                                                                        <i class="mdi mdi-download"></i> Download QR Code
                                                                    </a>
                                                                </div>

                                                                <div class="text-center">
                                                                    <a href="{{ asset('uploads/qrcode/' . $pendaftar->qrcode) }}"
                                                                        target="_blank"
                                                                        class="btn btn-primary btn-sm">
                                                                        <i class="mdi mdi-eye"></i> Lihat QR Code
                                                                    </a>
                                                                </div>
                                                                @else
                                                                <p class="text-muted mb-2">QR Code belum tampil, masih proses validasi Admin</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    <td>

                                        @php
                                        $today = \Carbon\Carbon::today();
                                        $tanggalMulai = \Carbon\Carbon::parse($data->tanggal_mulai);
                                        $tanggalSelesai = \Carbon\Carbon::parse($data->tanggal_selesai);
                                        @endphp

                                        @if(in_array($data->id_rk, $rakernas_terdaftar_ids))
                                        <span class="badge bg-primary-subtle text-primary fw-semibold text-uppercase">
                                            Terdaftar
                                        </span>
                                        @else
                                        <span class="badge bg-danger-subtle text-danger fw-semibold text-uppercase">
                                            Belum Terdaftar
                                        </span>
                                        @if($today->lt($tanggalMulai))
                                        <button type="button" aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-warning-subtle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalValidasi{{ $data->id_rk }}"
                                            data-bs-original-title="Daftar Rakernas">
                                            <i class="mdi mdi-check-decagram-outline fs-14 text-warning"></i>
                                        </button>
                                        <!-- Modal Validasi -->
                                        <div class="modal fade" id="modalValidasi{{ $data->id_rk }}"
                                            tabindex="-1"
                                            aria-labelledby="modalValidasiLabel{{ $data->id_rk }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form
                                                    action="{{ route('store_registrasi_rakernas') }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalValidasiLabel{{ $data->id_rk }}">
                                                                Daftar Rakernas
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            {{-- Keterangan Rekening Pengirim --}}
                                                            <div class="mb-3">
                                                                <label for="keterangan" class="form-label mb-1">Informasi Rekening Pengirim</label>
                                                                <input type="text" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                                                    placeholder="Misal: Budi Karya - BRI 03178261829256" value="{{ old('keterangan') }}">
                                                                @error('keterangan')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                            {{-- Bukti Transfer --}}
                                                            <div class="mb-3">
                                                                <label for="bukti_transfer" class="form-label mb-1">Bukti Transfer</label>
                                                                <input type="file"
                                                                    class="form-control @error('bukti_transfer') is-invalid @enderror"
                                                                    id="bukti_transfer"
                                                                    name="bukti_transfer">
                                                                <input type="hidden" name="id_rk" value="{{ $data->id_rk }}">
                                                                <input type="hidden" name="biaya" value="{{ $data->biaya_unik }}">
                                                                <input type="hidden" name="id_user" value="{{ Auth::id() }}">

                                                                <br>
                                                                <small class="form-text text-danger">
                                                                    *Transfer senilai <strong class="text-success">Rp. {{ number_format($data->biaya_unik ?? 0, 0, ',', '.') }}
                                                                    </strong> (pastikan persis hingga 3 digit terakhir)
                                                                </small><br>
                                                                <small class="form-text text-danger">
                                                                    *Transfer ke <strong class="text-success">312601035654536</strong> , Rek BRI, a.n <strong class="text-success">Nindya Adiasti</strong> (Bendahara DPP ADAKSI)
                                                                </small>

                                                                @error('bukti_transfer')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-dark btn-sm"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Daftar</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                        @endif
                                    </td>
                                    <td>

                                        @if($today->lt($tanggalMulai))
                                        <span class="badge bg-info">PENDAFTARAN</span>
                                        @elseif($today->gt($tanggalSelesai))
                                        <span class="badge bg-secondary">SELESAI</span>
                                        @else
                                        <span class="badge bg-success">SEDANG BERLANGSUNG</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data Rakernas yang tersedia.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer pt-3 pb-0 border-top">
                    {{ $rakernas->links('vendor.pagination.bootstrap-5') }}
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


@endsection
@extends('layouts.admin_layout')
@php use Illuminate\Support\Str; @endphp
@php
use Carbon\Carbon;
Carbon::setLocale('id');
@endphp

<?php
$main_data = 'Webinar';
$url = '/admin/webinar';
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
                            <h5 class="card-title mb-1">Daftar Kegiatan Webinar</h5>
                            <p class="card-text text-muted mb-0">Berikut adalah daftar kegiatan Webinar yang terdaftar dalam
                                sistem.</p>
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

                            <a href="/admin/webinar"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </a>
                            <a href="{{ route('webinar.create') }}"
                                class="btn btn-success btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-plus"></i> Tambah
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
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Detil</th>
                                    <th>Flyer</th>
                                    <th>Sertifikat Depan</th>
                                    <th>Sertifikat Belakang</th>
                                    <th>Pendaftar</th>
                                    <th>Dana</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($webinar as $key => $data)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-medium fs-14">{{ Str::limit($data->judul, 25) }}</p>

                                        <button type="button" class="btn btn-sm btn-info mt-1 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalFasilitas{{ $data->id_wb }}">
                                            Fasilitas
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="modalFasilitas{{ $data->id_wb }}" tabindex="-1" aria-labelledby="modalLabel{{ $data->id_wb }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalLabel{{ $data->id_wb }}">Fasilitas Webinar</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if ($data->fasilitas->isNotEmpty())
                                                        <ul class="list-group">
                                                            @foreach ($data->fasilitas as $fasilitas)
                                                            <li class="list-group-item">
                                                                <strong>{{ $fasilitas->nama }}</strong><br>
                                                                @if($fasilitas->link)
                                                                <a href="{{ $fasilitas->link }}" target="_blank">{{ $fasilitas->link }}</a>
                                                                @else
                                                                <em>Tidak ada link</em>
                                                                @endif
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                        @else
                                                        <p class="text-muted">Tidak ada fasilitas tersedia untuk webinar ini.</p>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <p class="mb-0 fw-medium fs-14">
                                            {{ Str::limit(strip_tags($data->deskripsi), 30) }}
                                        </p>
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-info mt-1 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalDetil{{ $data->id_wb }}">
                                            Detil
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="modalDetil{{ $data->id_wb }}" tabindex="-1" aria-labelledby="modalLabel{{ $data->id_wb }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalLabel{{ $data->id_wb }}">Detil Webinar</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <p class="mb-0 fw-medium fs-14"><strong>Hari</strong>: {{ $data->hari }}</p>
                                                        <p class="mb-0 fw-medium fs-14">
                                                            <strong>Tanggal</strong>:
                                                            {{ \Carbon\Carbon::parse($data->tanggal_mulai)->translatedFormat('d F Y') }}
                                                            s.d.
                                                            {{ \Carbon\Carbon::parse($data->tanggal_selesai)->translatedFormat('d F Y') }}
                                                        </p>
                                                        <p class="mb-0 fw-medium fs-14"><strong>Pukul</strong>: {{ $data->pukul }}</p>
                                                        <p class="mb-0 fw-medium fs-14"><strong>Link Zoom</strong>: <a href="{{ $data->link_zoom }}" target="blank"> Klik</a></p>
                                                        <p class="mb-0 fw-medium fs-14"><strong>Kepesertaan</strong>: {{ $data->bayar_free }}</p>
                                                        <p class="mb-0 fw-medium fs-14">
                                                            <strong>Biaya</strong>: Anggota Aktif ({{ 'Rp. ' . number_format($data->biaya_anggota_aktif, 0, ',', '.') }}),
                                                            Anggota Non Aktif ({{ 'Rp. ' . number_format($data->biaya_anggota_non_aktif, 0, ',', '.') }}),
                                                            Non Anggota ({{ 'Rp. ' . number_format($data->biaya_non_anggota, 0, ',', '.') }})
                                                        </p>
                                                        <p class="mb-0 fw-medium fs-14"><strong>Moderator</strong>: {{ $data->moderator }}</p>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ asset('uploads/webinar/' . $data->flyer) }}" target="_blank">Lihat</a>
                                    </td>
                                    <td>
                                        <a href="{{ asset('uploads/webinar/' . $data->sertifikat_depan) }}" target="_blank">Lihat</a>
                                    </td>
                                    <td>
                                        <a href="{{ asset('uploads/webinar/' . $data->sertifikat_belakang) }}" target="_blank">Lihat</a>
                                    </td>
                                    <td>
                                        @if(($jumlahPendaftarPerWebinar[$data->id_wb] ?? 0) > 0)
                                        <p class="mb-0 fw-medium fs-14"> Valid: {{ $valid[$data->id_wb] ?? 0 }}</p>
                                        <p class="mb-0 fw-medium fs-14"> Pending: {{ $pendaftarBelumTokenPerWebinar[$data->id_wb] ?? 0 }}</p>
                                        @if ($data->status != 'draft')
                                        <a href="{{ url('admin/webinar/pendaftar/' . $data->id_wb) }}"
                                            target="_blank"
                                            aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-info-subtle me-1"
                                            title="Lihat">
                                            <i class="mdi mdi-eye-outline fs-14 text-info"></i>
                                        </a>

                                        @endif
                                        @else
                                        <p class="mb-0 text-danger fw-medium fs-14">Tidak Ada Pendaftar</p>
                                        @endif


                                    </td>
                                    <td>
                                        <span class="text-danger">Rp. {{ number_format($total_biaya[$data->id_wb] ?? 0, 0, ',', '.') }}</span>
                                    </td>

                                    <td>
                                        @if ($data->status == 'publish')
                                        <span
                                            class="badge bg-primary-subtle text-primary fw-semibold text-uppercase">
                                            {{ $data->status }}
                                        </span>
                                        <button type="button" aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-primary-subtle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalSelesai{{ $data->id_wb }}"
                                            data-bs-original-title="Selesai">
                                            <i class="mdi mdi-check-decagram-outline fs-14 text-primary"></i>
                                        </button>

                                        <!-- Modal Validasi -->
                                        <div class="modal fade" id="modalSelesai{{ $data->id_wb }}"
                                            tabindex="-1"
                                            aria-labelledby="modalSelesai{{ $data->id_wb }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form
                                                    action="{{ url($url . '/selesai/' . $data->id_wb) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalValidasiLabel{{ $data->id_wb }}">
                                                                Selesaikan Webinar
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menyelesaikan webinar? Dengan ini maka peserta dapat mengunduh
                                                                semua fasilitas kegiatan
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-dark btn-sm"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Selesai</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @elseif ($data->status == 'draft')
                                        <span
                                            class="badge bg-danger-subtle text-danger fw-semibold text-uppercase">
                                            {{ $data->status }}
                                        </span>

                                        <button type="button" aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-warning-subtle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalValidasi{{ $data->id_wb }}"
                                            data-bs-original-title="Publish">
                                            <i class="mdi mdi-check-decagram-outline fs-14 text-warning"></i>
                                        </button>

                                        <!-- Modal Validasi -->
                                        <div class="modal fade" id="modalValidasi{{ $data->id_wb }}"
                                            tabindex="-1"
                                            aria-labelledby="modalValidasiLabel{{ $data->id_wb }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form
                                                    action="{{ url($url . '/publish/' . $data->id_wb) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalValidasiLabel{{ $data->id_wb }}">
                                                                Publish Webinar
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin publish webinar?
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-dark btn-sm"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Publish</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @elseif ($data->status == 'selesai')
                                        <span
                                            class="badge bg-success-subtle text-success fw-semibold text-uppercase">
                                            {{ $data->status }}
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('admin/webinar/edit/' . $data->id_wb) }}"
                                            aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-info-subtle" data-bs-toggle="tooltip"
                                            data-bs-original-title="Edit">
                                            <i class="mdi mdi-pencil fs-14 text-info"></i>
                                        </a>
                                        @if ($data->status == 'draft')
                                        <button type="button" aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-danger-subtle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalHapus{{ $data->id_wb }}"
                                            data-bs-original-title="Hapus">
                                            <i class="mdi mdi-trash-can fs-14 text-danger"></i>
                                        </button>

                                        <!-- Modal Validasi -->
                                        <div class="modal fade" id="modalHapus{{ $data->id_wb }}"
                                            tabindex="-1"
                                            aria-labelledby="modalHapus{{ $data->id_wb }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form
                                                    action="{{ url($url . '/hapus/' . $data->id_wb) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalHapus{{ $data->id_wb }}">
                                                                Hapus Webinar
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menghapus webinar?
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-dark btn-sm"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Hapus</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer pt-3 pb-0 border-top">
                    {{ $webinar->links('vendor.pagination.bootstrap-5') }}
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
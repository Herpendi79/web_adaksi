@extends('layouts.admin_layout')
@php use Illuminate\Support\Str; @endphp

<?php
$main_data = 'Pendaftar Webinar';
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
                            <h5 class="card-title mb-1">
                                Total Dana Diterima
                                <span class="text-danger">
                                    Rp.
                                    {{ isset($data) && isset($data->id_rk) && isset($total_masuk[$data->id_rk]) 
                                        ? number_format($total_masuk[$data->id_wb], 0, ',', '.') 
                                        : '0' 
                                    }}
                                </span>
                            </h5>

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

                            <a href="{{ url('admin/webinar/pendaftar/' . $id) }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </a>
                            <a href="{{ url('admin/webinar/') }}"
                                class="btn btn-success btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-arrow-left"></i> Kembali
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
                                    <th>Nama</th>
                                    <th>HP / WA</th>
                                    <th>NIP / ID</th>
                                    <th>Dosen</th>
                                    <th>Home Base</th>
                                    <th>Provinsi</th>
                                    <th>Validasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($webinar as $key => $data)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-medium fs-14">{{ $data->nama}}</p>
                                    </td>

                                    <td>
                                        <p class="mb-0 fw-medium fs-14">{{ $data->no_hp}}</p>
                                    </td>

                                    <td>
                                        <p class="mb-0 fw-medium fs-14">{{ $data->nip}}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-medium fs-14">{{ $data->status}}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-medium fs-14">{{ $data->home_base}}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-medium fs-14">{{ $data->provinsi}}</p>
                                    </td>
                                    <td>
                                        @if ($data->token == '')
                                        <!-- Tombol Validasi -->
                                        <button type="button" aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-info-subtle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalValidasi{{ $data->id_pwe }}"
                                            data-bs-original-title="Validasi">
                                            <i class="mdi mdi-check-decagram-outline fs-14 text-info"></i>
                                        </button>
                                        <!-- Modal Validasi -->
                                        <div class="modal fade" id="modalValidasi{{ $data->id_pwe }}"
                                            tabindex="-1"
                                            aria-labelledby="modalValidasiLabel{{ $data->id_pwe }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form
                                                    action="{{ url($url . '/validasiPendaftar/' . $data->id_pwe) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalValidasiLabel{{ $data->id_pwe }}">
                                                                Validasi Pendaftaran
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3 w-100">
                                                                <label class="form-label">Bukti Transfer
                                                                    :</label>
                                                                <a href="{{ asset('uploads/bukti_tf_pendaftaran/' . $data->bukti_tf) }}"
                                                                    target="_blank">Lihat Bukti Transfer</a>
                                                                @if ($data->bukti_tf)
                                                                <div
                                                                    class="mb-2 d-flex justify-content-center">
                                                                    <img src="{{ asset('uploads/bukti_tf_pendaftaran/' . $data->bukti_tf) }}"
                                                                        alt="Bukti Transfer"
                                                                        class="img-fluid rounded"
                                                                        style="max-width: 300px;">
                                                                </div>
                                                                <!-- <p>Jumlah Transfer : <strong>Rp. {{ number_format($data->biaya, 0, ',', '.') }}</strong></p> -->
                                                                <p>Keterangan Transfer : <strong>{{ $data->keterangan }}</strong></p>
                                                                @else
                                                                <p class="text-muted mb-2">Belum ada bukti
                                                                    transfer.</p>
                                                                @endif
                                                            </div>
                                                            <div class="mb-3">
                                                                <label
                                                                    for="status_anggota{{ $data->id_wb }}"
                                                                    class="form-label">Bukti Pendaftaran : </label>
                                                                <select class="form-select"
                                                                    id="status_anggota{{ $data->id_wb }}"
                                                                    name="valid" required>
                                                                    <option value="valid">Valid</option>
                                                                    <option value="nonvalid">Tidak Valid</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="keterangan" class="form-label">Keterangan Validasi(Max 250 karakter) Khusus untuk <strong>Tidak Valid</strong> :</label>
                                                                <input type="text" class="form-control" id="keterangan"
                                                                    name="keterangan" value="" placeholder="Wajib tuliskan keterangan untuk verifikasi yang tidak valid">
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-dark btn-sm"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Validasi</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @else
                                        <span
                                            class="badge bg-primary-subtle text-primary fw-semibold text-uppercase">
                                            Valid
                                        </span>
                                        <button type="button" aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-primary-subtle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalView{{ $data->id_pwe }}"
                                            data-bs-original-title="Validasi">
                                            <i class="mdi mdi-eye-outline fs-14 text-primary"></i>
                                        </button>
                                        <!-- Modal Validasi -->
                                        <div class="modal fade" id="modalView{{ $data->id_pwe }}"
                                            tabindex="-1"
                                            aria-labelledby="modalView{{ $data->id_pwe }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form
                                                    action=""
                                                    method="">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalView{{ $data->id_pwe }}">
                                                                Validasi Pendaftaran
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3 w-100">
                                                                @if ($data->bukti_tf)
                                                                <label class="form-label">Bukti Transfer
                                                                    :</label>
                                                                <a href="{{ asset('uploads/bukti_tf_pendaftaran/' . $data->bukti_tf) }}"
                                                                    target="_blank">Lihat Bukti Transfer</a>
                                                                <div
                                                                    class="mb-2 d-flex justify-content-center">
                                                                    <img src="{{ asset('uploads/bukti_tf_pendaftaran/' . $data->bukti_tf) }}"
                                                                        alt="Bukti Transfer"
                                                                        class="img-fluid rounded"
                                                                        style="max-width: 300px;">
                                                                </div>
                                                                <p>Jumlah Transfer : <strong>Rp. {{ number_format($data->biaya, 0, ',', '.') }}</strong></p>
                                                                <p>Keterangan Transfer : <strong>{{ $data->keterangan }}</strong></p>
                                                                @else
                                                                <p class="text-muted mb-2">Kegiatan gratis tanpa biaya</p>
                                                                @endif
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-dark btn-sm"
                                                                data-bs-dismiss="modal">Keluar</button>
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
        title: 'success!',
        text: "{{ session('success') }}",
        confirmButtonText: 'OK'
    });
</script>
@endif


@endsection
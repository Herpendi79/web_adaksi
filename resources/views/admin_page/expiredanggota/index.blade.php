@extends('layouts.admin_layout')
<?php
$main_data = 'Calon Anggota Expired';
$url = '/admin/expiredanggota';
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
                            <h5 class="card-title mb-1">Daftar Calon Anggota Expired Midtrans</h5>
                            <p class="card-text text-muted mb-0">Berikut adalah daftar calon anggota yang perlu anda validasi</p>
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
                                <i class="mdi mdi-search"></i> Cari
                            </button>

                            <a href="/admin/expiredanggota"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-refresh"></i> Refresh
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
                                    <th>NIP/NIPPPK</th>
                                    <th>No HP</th>
                                    <th>Dosen</th>
                                    <th>PT</th>
                                    <th>Status</th>
                                 <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expired as $data)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="d-flex align-items-center">
                                        <img src="{{ asset('uploads/foto_anggota/' . ($data->foto ?? 'foto.jpg')) }}"
                                            class="avatar avatar-sm rounded-circle me-3">
                                         <div>
                                            <p class="mb-0 fw-medium fs-14">
                                                {{ $data->nama_anggota }}
                                            </p>
                                            <p class="text-muted fs-13 mb-0">{{ $data->email ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">
                                            {{ $data->nip_nipppk }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">{{ $data->no_hp }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">{{ $data->status_dosen }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">{{ $data->homebase_pt }}</p>
                                    </td>
                                    <td>
                                        @if ($data->status_anggota == 'aktif')
                                        <span
                                            class="badge bg-primary-subtle text-primary fw-semibold text-uppercase">
                                            {{ $data->status_anggota }}
                                        </span>
                                        @elseif ($data->status_anggota == 'nonaktif')
                                        <span
                                            class="badge bg-danger-subtle text-danger fw-semibold text-uppercase">{{ $data->status_anggota }}</span
                                            @else <span
                                            class="badge bg-warning-subtle text-warning fw-semibold text-uppercase">{{ $data->status_anggota }}</span>
                                        @endif
                                    </td>
                               <td>
                                  @if ($data->status_anggota == 'pending')
                                        <!-- Tombol Validasi -->
                                        <button type="button" aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-info-subtle me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalValidasi{{ $data->id_anggota }}"
                                            data-bs-original-title="Validasi">
                                            <i class="mdi mdi-check-decagram-outline fs-14 text-info"></i>
                                        </button>
                                        <!-- Modal Validasi -->
                                        <div class="modal fade" id="modalValidasi{{ $data->id_anggota }}"
                                            tabindex="-1"
                                            aria-labelledby="modalValidasiLabel{{ $data->id_anggota }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form
                                                    action="{{ url($url . '/validasiExpired/' . $data->id_anggota) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalValidasiLabel{{ $data->id_anggota }}">
                                                                Validasi Anggota
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin memvalidasi anggota
                                                                <strong>{{ $data->nama_anggota }}</strong>?
                                                            </p>
                                                            <div class="mb-3 w-100">
                                                                <label class="form-label">Bukti Transfer
                                                                    :</label>
                                                                <a href="{{ asset('uploads/bukti_tf_pendaftaran/' . $data->bukti_tf_pendaftaran) }}"
                                                                    target="_blank">Lihat Bukti Transfer</a>
                                                                @if ($data->bukti_tf_pendaftaran)
                                                                <div
                                                                    class="mb-2 d-flex justify-content-center">
                                                                    <img src="{{ asset('uploads/bukti_tf_pendaftaran/' . $data->bukti_tf_pendaftaran) }}"
                                                                        alt="Bukti Transfer"
                                                                        class="img-fluid rounded"
                                                                        style="max-width: 300px;">
                                                                </div>
                                                                @else
                                                                <p class="text-muted mb-2">Belum ada bukti
                                                                    transfer.</p>
                                                                @endif
                                                            </div>
                                                            <div class="mb-3">
                                                                <label
                                                                    for="status_anggota{{ $data->id_anggota }}"
                                                                    class="form-label">Status Pendaftaran</label>
                                                                <select class="form-select"
                                                                    id="status_anggota{{ $data->id_anggota }}"
                                                                    name="status_anggota" required>
                                                                    <option value="aktif"
                                                                        {{ $data->status_anggota == 'aktif' ? 'selected' : '' }}>
                                                                        Valid</option>
                                                                   <!-- <option value="pending"
                                                                        {{ $data->status_anggota == 'pending' ? 'selected' : '' }}>
                                                                        Pending</option>-->
                                                                    <option value="nonaktif"
                                                                        {{ $data->status_anggota == 'nonaktif' ? 'selected' : '' }}>
                                                                        Tidak Valid</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="keterangan{{ $data->id_anggota }}" class="form-label">Keterangan (Max 250 karakter)</label>
                                                                <input type="text" class="form-control" id="keterangan{{ $data->id_anggota }}"
                                                                       name="keterangan" value="{{ old('keterangan', $data->keterangan ?? '') }}"
                                                                       placeholder="Tuliskan keterangan jika perlu">
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
                                        <div aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-primary-subtle me-1"
                                            data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                            <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                        </div>
                                        <a aria-label="anchor" class="btn btn-icon btn-sm bg-danger-subtle"
                                            data-bs-toggle="tooltip" data-bs-original-title="Delete">
                                            <i class="mdi mdi-delete fs-14 text-danger"></i>
                                        </a>
                                        @endif


                                    </td>
                               
                                
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer pt-3 pb-0 border-top">
                    {{ $expired->links('vendor.pagination.bootstrap-5') }}
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


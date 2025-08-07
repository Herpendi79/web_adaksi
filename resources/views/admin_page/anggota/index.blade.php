@extends('layouts.admin_layout')
<?php
$main_data = 'Anggota';
$url = '/admin/anggota';
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
                            <h5 class="card-title mb-1">Daftar Anggota Tetap</h5>
                            <p class="card-text text-muted mb-0">Berikut adalah daftar Anggota Tetap yang terdaftar dalam
                                sistem.</p>
                        </div>
                        <form class="d-flex flex-stack flex-wrap gap-1 justify-content-md-end ms-auto">
                            {{-- Selection Status --}}
                            <select name="status_anggota"
                                class="form-select form-select-sm bg-light-subtle border fw-medium me-2"
                                style="width: auto;">
                                <option value="">All Status</option>
                                <option value="aktif" {{ request('status_anggota') == 'aktif' ? 'selected' : '' }}>
                                    Aktif</option>
                                <!--<option value="pending" {{ request('status_anggota') == 'pending' ? 'selected' : '' }}>
                                    Pending</option>-->
                                <option value="nonaktif"
                                    {{ request('status_anggota') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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

                            <a href="/admin/anggota"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </a>
                            <a href="{{ route('export.anggota') }}"
                                class="btn btn-success btn-sm d-flex align-items-center gap-1 ms-2">
                                <i class="mdi mdi-export"></i> Export
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
                                    <th>Nama Anggota</th>
                                    <th>NIP/NIPPPK</th>
                                    <th>No HP</th>
                                    <th>Dosen</th>
                                    <th>PT</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($anggota as $key => $data)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="d-flex align-items-center">
                                        <img src="{{ asset('uploads/foto_anggota/' . ($data->foto ?: 'foto.jpg')) }}"
                                            class="avatar avatar-sm rounded-circle me-3">
                                        <div>
                                            <p class="mb-0 fw-medium fs-14">
                                                {{ $data->nama_anggota }}
                                            </p>
                                            <p class="text-muted fs-13 mb-0">{{ $data->user->email }}</p>
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
                                        <p class="mb-0 text-muted">{{ $data->keterangan }}</p>
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
                                            @else
                                            class="badge bg-warning-subtle text-warning fw-semibold text-uppercase">{{ $data->status_anggota }}</>
                                        @endif
                                    </td>
                                    <td>


                                        <a href="{{ url('/admin/anggota/edit/' . $data->id_anggota) }}"
                                            aria-label="anchor"
                                            class="btn btn-icon btn-sm bg-info-subtle" data-bs-toggle="tooltip"
                                            data-bs-original-title="Edit">
                                            <i class="mdi mdi-pencil fs-14 text-info"></i>
                                        </a>
                                        <a aria-label="anchor" class="btn btn-icon btn-sm bg-danger-subtle"
                                            data-bs-toggle="tooltip" data-bs-original-title="Delete">
                                            <i class="mdi mdi-delete fs-14 text-danger"></i>
                                        </a>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer pt-3 pb-0 border-top">
                    {{ $anggota->links('vendor.pagination.bootstrap-5') }}
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
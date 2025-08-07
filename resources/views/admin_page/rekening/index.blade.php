@extends('layouts.admin_layout')
@php use Illuminate\Support\Str; @endphp
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

<?php
$main_data = 'Rekening';
$url = '/admin/rekening';
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
                                <h5 class="card-title mb-1">Daftar Rekening</h5>
                                <p class="card-text text-muted mb-0">Berikut adalah daftar Rekening yang terdaftar dalam
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

                                <a href="/admin/rekening"
                                    class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                    <i class="mdi mdi-refresh"></i> Refresh
                                </a>
                                <a href="{{ route('rekening.create') }}"
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
                                        <th>Bank</th>
                                        <th>Nomor Rekening</th>
                                        <th>Atas Nama</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rekening as $key => $data)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <p class="mb-0 fw-medium fs-14">{{ $data->nama_bank }}</p>
                                            </td>
                                            <td>
                                                <p class="mb-0 fw-medium fs-14">{{ $data->no_rek }}</p>
                                            </td>
                                            <td>
                                                <p class="mb-0 fw-medium fs-14">{{ $data->atas_nama }}</p>
                                            </td>
                                            <td>
                                                <a href="{{ url('admin/rekening/edit/' . $data->id_rek) }}"
                                                    aria-label="anchor" class="btn btn-icon btn-sm bg-info-subtle"
                                                    data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                    <i class="mdi mdi-pencil fs-14 text-info"></i>
                                                </a>
                                                <button type="button" aria-label="anchor"
                                                    class="btn btn-icon btn-sm bg-danger-subtle me-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalHapus{{ $data->id_rek }}"
                                                    data-bs-original-title="Hapus">
                                                    <i class="mdi mdi-trash-can fs-14 text-danger"></i>
                                                </button>

                                                <!-- Modal Validasi -->
                                                <div class="modal fade" id="modalHapus{{ $data->id_rek }}" tabindex="-1"
                                                    aria-labelledby="modalHapus{{ $data->id_rek }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form action="{{ url($url . '/hapus/' . $data->id_rek) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="modalHapus{{ $data->id_rek }}">
                                                                        Hapus Rekening
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Apakah Anda yakin ingin menghapus Rekening?
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
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer pt-3 pb-0 border-top">
                        {{ $rekening->links('vendor.pagination.bootstrap-5') }}
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
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK'
            });
        </script>
    @endif



@endsection

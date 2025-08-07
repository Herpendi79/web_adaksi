@extends('layouts.admin_layout')
@php use Illuminate\Support\Str; @endphp
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

<?php
$main_data = 'Aduan';
$url = '/anggota/aduan';
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
                                <h5 class="card-title mb-1">Daftar Aduan Anggota</h5>
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

                                <a href="/admin/aduan" class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
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
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Homebase</th>
                                        <th>Kategori</th>
                                        <th>Judul Aduan</th>
                                        <th>Deskripsi</th>
                                        <th>Detil</th>
                                        <th>Status Aduan</th>
                                        <th>Tanggapan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($aduan as $key => $data)
                                        <tr>
                                            <td>{{ $aduan->firstItem() + $key }}</td>
                                            <td> {{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y') }}
                                            </td>
                                            <td>
                                                {{ $data->anggota->nama_anggota }}
                                                ({{ $data->user->no_hp }})

                                            </td>
                                            <td>{{ $data->anggota->homebase_pt }}</td>
                                            <td>{{ $data->kategori }}</td>
                                            <td>{{ $data->judul }}</td>
                                            <td>
                                                {!! Str::limit($data->deskripsi, 50) !!}
                                            </td>
                                            <td>
                                                <button type="button" aria-label="anchor"
                                                    class="btn btn-icon btn-sm bg-info-subtle me-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalValidasi{{ $data->id_ad }}"
                                                    data-bs-original-title="Validasi">
                                                    <i class="mdi mdi-eye fs-14 text-info"></i>
                                                </button>
                                                <!-- Modal Validasi -->
                                                <div class="modal fade" id="modalValidasi{{ $data->id_ad }}"
                                                    tabindex="-1" aria-labelledby="modalValidasiLabel{{ $data->id_ad }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form action="" method="">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="modalValidasiLabel{{ $data->id_ad }}">
                                                                        Detil Aduan
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3 w-100">
                                                                        <p class="text-center">
                                                                            <strong>{{ $data->judul }}</strong>
                                                                        </p>
                                                                        <p>{!! $data->deskripsi !!}</p>
                                                                        @foreach ($data->lampiran as $file)
                                                                            @php
                                                                                $ext = pathinfo(
                                                                                    $file->lampiran,
                                                                                    PATHINFO_EXTENSION,
                                                                                );
                                                                                $isImage = in_array(strtolower($ext), [
                                                                                    'jpg',
                                                                                    'jpeg',
                                                                                    'png',
                                                                                    'gif',
                                                                                    'bmp',
                                                                                    'webp',
                                                                                ]);
                                                                            @endphp

                                                                            <a href="{{ asset('uploads/lampiran/' . $file->lampiran) }}"
                                                                                target="_blank">
                                                                                Lihat Lampiran ({{ strtoupper($ext) }})
                                                                            </a>

                                                                            @if ($isImage)
                                                                                <div
                                                                                    class="mb-2 d-flex justify-content-center">
                                                                                    <img src="{{ asset('uploads/lampiran/' . $file->lampiran) }}"
                                                                                        alt="Lampiran Gambar"
                                                                                        class="img-fluid rounded"
                                                                                        style="max-width: 300px;">
                                                                                </div>
                                                                            @endif
                                                                        @endforeach

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </td>
                                            <td>{{ $data->status }}</td>
                                            <td>

                                                <button type="button" aria-label="anchor"
                                                    class="btn btn-icon btn-sm bg-info-subtle me-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalTanggapan{{ $data->id_ad }}"
                                                    data-bs-original-title="Tanggapan">
                                                    <i class="mdi mdi-eye fs-14 text-info"></i>
                                                </button>
                                                <!-- Modal Tanggapan -->
                                                <div class="modal fade" id="modalTanggapan{{ $data->id_ad }}"
                                                    tabindex="-1" aria-labelledby="modalTanggapanLabel{{ $data->id_ad }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <div class="mb-3 w-100">
                                                                    {{-- Loop untuk menampilkan tanggapan yang sudah ada --}}
                                                                    @if ($data->tanggapan->isEmpty())
                                                                        <p>Belum ada tanggapan</p>
                                                                    @else
                                                                        @foreach ($data->tanggapan as $obrolan)
                                                                            <p class="text-center">
                                                                                @if ($obrolan->pemilik === 'admin')
                                                                                    <strong>Tim Hukum & Advokasi / Admin
                                                                                        ({{ \Carbon\Carbon::parse($obrolan->created_at)->translatedFormat('d F Y') }})</strong>
                                                                                @else
                                                                                    <strong>Anggota
                                                                                        ({{ \Carbon\Carbon::parse($obrolan->created_at)->translatedFormat('d F Y') }})</strong>
                                                                                @endif
                                                                            </p>
                                                                            <p>{{ $obrolan->isi_tanggapan }}</p>
                                                                            <a href="{{ asset('uploads/lampiran/' . $obrolan->lampiran ?? '-') }}"
                                                                                target="_blank">
                                                                                <p>{{ $obrolan->lampiran }}</p>
                                                                            </a>
                                                                            <hr>
                                                                        @endforeach
                                                                    @endif
                                                                    @if ( $data->status != 'selesai')
                                                                    {{-- Form untuk mengirim tanggapan baru (DI LUAR LOOP) --}}
                                                                    <form action="{{ route('aduan.store_tanggapan_admin') }}"
                                                                        method="POST" enctype="multipart/form-data"
                                                                        class="needs-validation" novalidate>
                                                                        @csrf
                                                                        {{-- Menggunakan $data dari parent loop, bukan $obrolan --}}
                                                                        <input type="hidden" name="id_ad"
                                                                            value="{{ $data->id_ad }}">
                                                                        <input type="hidden" name="id_user"
                                                                            value="{{ Auth::id() }}">

                                                                        <div class="mb-2 col-md-12">
                                                                            <label for="isi_tanggapan"
                                                                                class="form-label m-0">Balas</label>
                                                                            <textarea class="form-control @error('isi_tanggapan') is-invalid @enderror" name="isi_tanggapan" rows="5"
                                                                                placeholder="Isi Balasan...">{{ old('isi_tanggapan') }}</textarea>
                                                                            @error('isi_tanggapan')
                                                                                <div class="invalid-feedback">
                                                                                    {{ $message }}</div>
                                                                            @enderror
                                                                        </div>

                                                                        <div class="mb-2 col-md-12">
                                                                            <label for="lampiran"
                                                                                class="form-label m-0">Foto / File
                                                                                Lampiran</label>
                                                                            <input type="file"
                                                                                class="form-control @error('lampiran') is-invalid @enderror"
                                                                                id="lampiran" name="lampiran">
                                                                            @error('lampiran')
                                                                                <div class="invalid-feedback d-block">
                                                                                    {{ $message }}</div>
                                                                            @enderror
                                                                        </div>

                                                                        <div class="d-flex justify-content-end mb-3 gap-2">
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Kirim</button>
                                                                        </div>
                                                                    </form>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                            <td>
                                                @if ($data->status != 'selesai')
                                                    <button type="button" aria-label="anchor"
                                                        class="btn btn-icon btn-sm bg-info-subtle me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalSelesai{{ $data->id_ad }}"
                                                        data-bs-original-title="Selesai">
                                                        <i class="mdi mdi-check fs-14 text-info"></i>
                                                    </button>

                                                    <!-- Modal Validasi -->
                                                    <div class="modal fade" id="modalSelesai{{ $data->id_ad }}"
                                                        tabindex="-1" aria-labelledby="modalSelesai{{ $data->id_ad }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form action="{{ route('selesai', $data->id_ad) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="modalSelesai{{ $data->id_ad }}">
                                                                            Selesaikan Aduan
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Apakah Anda yakin ingin mengubah status aduan
                                                                            menjadi selesai?
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-dark btn-sm"
                                                                            data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary btn-sm">Ya</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data Aduan yang tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer pt-3 pb-0 border-top">
                        {{ $aduan->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Script Notifikasi Validasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection

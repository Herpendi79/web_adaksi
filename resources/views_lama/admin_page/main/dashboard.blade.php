@extends('layouts.admin_layout')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
     <div class="alert alert-primary mt-4" role="alert">
            <h5 class="alert-heading">Selamat Datang!</h5>
            <p>Halo, selamat datang di Dashboard Admin. Silakan gunakan menu di samping untuk mengakses fitur-fitur yang
                tersedia.</p>
                
              <h4 class="mb-3">Jumlah Anggota per Provinsi</h4>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Provinsi</th>
                <th>Jumlah</th>
                <th>Provinsi</th>
                <th>Jumlah</th>
                <th>Provinsi</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jumlahPerProvinsi->chunk(3) as $chunk)
                <tr>
                    @foreach ($chunk as $item)
                        <td>{{ $item->provinsi ?? '-' }}</td>
                        <td>{{ number_format($item->total, 0, ',', '.') }}</td>
                    @endforeach

                    {{-- Tambah kolom kosong jika baris tidak lengkap --}}
                    @for ($i = $chunk->count(); $i < 3; $i++)
                        <td></td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


              </div>

    <!-- <form action="{{ url('admin/dashboard/edit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="id_pengaturan" value="{{ $pengaturan->id_pengaturan }}">

        {{-- Pesan Error --}}

        <div class="row">
            {{-- no_awal --}}
            <div class="mb-3 col-md-6">
                <label for="no_awal" class="form-label m-0">Nomor Awal KTA</label>
                <p class="text-muted mb-1" style="font-size: 0.775rem;">Nomor awal KTA yang digunakan untuk
                    pembuatan KTA digital</p>
                <input type="number" class="form-control @error('no_awal') is-invalid @enderror" id="no_awal" name="no_awal" placeholder="Masukkan nomor awal KTA" value="{{ old('no_awal', $pengaturan->no_awal) }}" required>
                @error('no_awal')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            {{-- no_tengah --}}
            <div class="mb-3 col-md-6">
                <label for="no_tengah" class="form-label m-0">Nomor Tengah KTA</label>
                <p class="text-muted mb-1" style="font-size: 0.775rem;">Nomor tengah KTA yang digunakan untuk
                    pembuatan KTA digital</p>
                <input type="number" class="form-control @error('no_tengah') is-invalid @enderror" id="no_tengah" name="no_tengah" placeholder="Masukkan nomor tengah KTA" value="{{ old('no_tengah', $pengaturan->no_tengah) }}" required>
                @error('no_tengah')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ url('/anggota/profile') }}" class="btn btn-light">Kembali</a>
            </div>
        </div>
    </form> -->

    <!-- input pengaturan -->
    <!-- <form action="{{ url('admin/dashboard/edit') }}" method="POST">
        {{-- Pesan Error --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @csrf
        <input type="hidden" name="id_pengaturan" value="{{ $pengaturan->id_pengaturan }}">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h5 class="fs-16 fw-semibold mb-3">Pengaturan Dashboard</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="no_awal" class="form-label">Nomor Awal KTA</label>
                            <input type="number" class="form-control @error('no_awal') is-invalid @enderror" id="no_awal" name="no_awal" placeholder="Masukkan nomor awal KTA" value="{{ old('no_awal', $pengaturan->no_awal) }}" required>
                            @error('no_awal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="no_tengah" class="form-label">Nomor Tengah KTA</label>
                            <input type="number" class="form-control @error('no_tengah') is-invalid @enderror" id="no_tengah" name="no_tengah" placeholder="Masukkan nomor tengah KTA" value="{{ old('no_tengah', $pengaturan->no_tengah) }}" required>
                            @error('no_tengah')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </div>
    </form> -->
</div>
@endsection
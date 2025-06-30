@extends('layouts.anggota_layout')
@section('title', 'Edit Profile')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Edit Profile</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Profil</a></li>
                <li class="breadcrumb-item active">Edit Profile</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ url('anggota/profile/edit') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="id_anggota" value="{{ $user->anggota->id_anggota }}">
                <input type="hidden" name="id_user" value="{{ $user->id_user }}">

                {{-- Pesan Error --}}
                <div class="row">
                    {{-- Nama Lengkap --}}
                    <div class="mb-3 col-md-6">
                        <label for="nama" class="form-label m-0">Nama Lengkap</label>
                        <p class="text-muted mb-1" style="font-size: 0.775rem;">Nama Lengkap dengan gelar.
                            Pastikan
                            tidak ada kesalahan.</p>
                        <input type="text" class="form-control @error('nama_anggota') is-invalid @enderror"
                            id="nama" name="nama_anggota" placeholder="Masukkan nama lengkap Anda"
                            value="{{ old('nama_anggota', $user->anggota->nama_anggota) }}" required>
                        @error('nama_anggota')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3 col-md-6">
                        <label for="email" class="form-label m-0">Email</label>
                        <p class="text-muted mb-1" style="font-size: 0.775rem;">Email yang valid untuk
                            pengiriman informasi.</p>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                            placeholder="Masukkan email Anda" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- NIP/NIPPPK --}}
                    <div class="mb-3 col-md-6">
                        <label for="nip_nippk" class="form-label mb-1">NIP/NIPPPK</label>
                        <input type="tel" class="form-control @error('nip_nipppk') is-invalid @enderror" id="nip_nippk" name="nip_nipppk"
                            placeholder="Masukkan NIP/NIPPPK Anda" value="{{ old('nip_nipppk', $user->anggota->nip_nipppk) }}" required>
                        @error('nip_nipppk')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- Nomor HP / WA --}}
                    <div class="mb-3 col-md-6">
                        <label for="no_hp" class="form-label mb-1">Nomor HP / WA</label>
                        <input type="tel" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp"
                            placeholder="Masukkan nomor HP/WA Anda" value="{{ old('no_hp', $user->no_hp) }}" required>
                        @error('no_hp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Status Dosen --}}
                    <div class="mb-3 col-md-6">
                        <label for="status_dosen" class="form-label mb-1">Status Dosen</label>
                        @php
                            $daftar_status_dosen = ['Dosen PTN', 'Dosen DPK'];
                            $status_terpilih = old('status_dosen', $user->anggota->status_dosen ?? '');
                        @endphp
                    
                        <select class="form-select @error('status_dosen') is-invalid @enderror" id="status_dosen" name="status_dosen">
                            <option value="" disabled {{ $status_terpilih ? '' : 'selected' }}>Pilih status dosen Anda</option>
                            @foreach ($daftar_status_dosen as $status)
                                <option value="{{ $status }}" {{ $status_terpilih == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    
                        @error('status_dosen')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                    {{-- Homebase PT --}}
                    <div class="mb-3 col-md-6">
                        <label for="homebase_pt" class="form-label mb-1">Homebase PT</label>
                        <input type="text" class="form-control" id="homebase_pt" name="homebase_pt"
                            placeholder="Masukkan nama PT Anda" value="{{ $user->anggota->homebase_pt }}">
                        @error('homebase_pt')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Provinsi --}}
                    <div class="mb-3 col-md-6">
                        <label for="provinsi" class="form-label mb-1">Provinsi</label>
                        <select class="form-select @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi">
                            <option value="">-- Pilih Provinsi --</option>
                            @php
                                $daftar_provinsi = [
                                     'Aceh',
                                    'Bali',
                                    'Bangka Belitung',
                                    'Banten',
                                    'Bengkulu',
                                    'Daerah Istimewa Yogyakarta',
                                    'DKI Jakarta',
                                    'Gorontalo',
                                    'Jambi',
                                    'Jawa Barat',
                                    'Jawa Tengah',
                                    'Jawa Timur',
                                    'Kalimantan Barat',
                                    'Kalimantan Selatan',
                                    'Kalimantan Tengah',
                                    'Kalimantan Timur',
                                    'Kalimantan Utara',
                                    'Kepulauan Riau',
                                    'Lampung',
                                    'Maluku',
                                    'Maluku Utara',
                                    'Nusa Tenggara Barat',
                                    'Nusa Tenggara Timur',
                                    'Papua',
                                    'Papua Barat',
                                    'Papua Barat Daya',
                                    'Papua Pegunungan',
                                    'Papua Selatan',
                                    'Papua Tengah',
                                    'Riau',
                                    'Sulawesi Barat',
                                    'Sulawesi Selatan',
                                    'Sulawesi Tengah',
                                    'Sulawesi Tenggara',
                                    'Sulawesi Utara',
                                    'Sumatera Barat',
                                    'Sumatera Selatan',
                                    'Sumatera Utara'
                                ];
                    
                                $provinsi_terpilih = old('provinsi', $user->anggota->provinsi ?? '');
                            @endphp
                    
                            @foreach($daftar_provinsi as $prov)
                                <option value="{{ $prov }}" {{ $provinsi_terpilih == $prov ? 'selected' : '' }}>
                                    {{ $prov }}
                                </option>
                            @endforeach
                        </select>
                        @error('provinsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ganti Foto --}}
                    <div class="mb-3 col-md-6">
                        <label for="foto" class="form-label mb-1">Pas Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ url('/anggota/profile') }}" class="btn btn-light">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
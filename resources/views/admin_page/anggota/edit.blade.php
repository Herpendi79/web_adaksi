@extends('layouts.admin_layout')
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
            <form action="{{ url('admin/anggota/edit/' . $anggota->id_anggota) }}" method="POST"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                 <div class="row">
                {{-- Nama Lengkap --}}
               <div class="mb-3 col-md-6">
                    <label for="nama" class="form-label m-0">Nama Lengkap</label>
                    <input type="text" class="form-control @error('nama_anggota') is-invalid @enderror"
                        id="nama" name="nama_anggota" placeholder="Masukkan nama lengkap Anda"
                        value="{{ $anggota->nama_anggota ?? old('nama_anggota') }}">
                    @error('nama_anggota')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3 col-md-6">
                    <label for="email" class="form-label m-0">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                        placeholder="Masukkan email Anda" value="{{ $anggota->email ?? old('email') }}">
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
                            placeholder="Masukkan NIP/NIPPPK Anda" value="{{ $anggota->nip_nipppk ?? old('nip_nipppk') }}">
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
                            placeholder="Masukkan nomor HP/WA Anda" value="{{ $anggota->no_hp ?? old('no_hp') }}">
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
                        <select class="form-select @error('status_dosen') is-invalid @enderror" id="status_dosen" name="status_dosen">
                            <option value="" disabled {{ old('status_dosen', $anggota->status_dosen) ? '' : 'selected' }}>
                                Pilih status dosen Anda
                            </option>
                            <option value="Dosen PTN" {{ old('status_dosen', $anggota->status_dosen) == 'Dosen PTN' ? 'selected' : '' }}>
                                Dosen PTN
                            </option>
                            <option value="Dosen DPK" {{ old('status_dosen', $anggota->status_dosen) == 'Dosen DPK' ? 'selected' : '' }}>
                                Dosen DPK
                            </option>
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
                        <input type="text" class="form-control @error('homebase_pt') is-invalid @enderror" id="homebase_pt" name="homebase_pt"
                            placeholder="Masukkan nama PT Anda" value="{{ $anggota->homebase_pt ?? old('homebase_pt') }}">
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
                    
                                $provinsi_terpilih = old('provinsi', $anggota->provinsi ?? '');
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
                    
                     {{-- Keterangan --}}
                    <div class="mb-3 col-md-6">
                        <label for="homebase_pt" class="form-label mb-1">Keterangan</label>
                        <input type="text" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                            placeholder="Keterangan Tambahan" value="{{ $anggota->keterangan ?? old('keterangan') }}">
                        @error('homebase_pt')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    </div>
                <!-- button -->
                <!-- kembali -->
                <input type="hidden" name="id_anggota" value="{{ $anggota->id_anggota}}">
                <input type="hidden" name="id_user" value="{{ $anggota->id_user }}">

                {{-- Foto Profil --}}
                <div class="d-flex justify-content-start mb-3">
                    <a href="{{ url('admin/anggota') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
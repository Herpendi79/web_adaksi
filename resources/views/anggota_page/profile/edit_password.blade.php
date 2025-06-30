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
            <form action="{{ url('anggota/profile/edit_password') }}" method="POST">
                @csrf

                <input type="hidden" name="id_anggota" value="{{ $user->anggota->id_anggota }}">
                <input type="hidden" name="id_user" value="{{ $user->id_user }}">

                {{-- Pesan Error --}}
                <div class="row">
                    {{-- Password lama--}}
                    <div class="mb-3 col-md-12">
                        <label for="password_lama" class="form-label m-0">Password Lama</label>
                        <p class="text-muted mb-1" style="font-size: 0.775rem;">Masukkan password lama Anda untuk verifikasi</p>
                        <input type="password" class="form-control @error('password_lama') is-invalid @enderror"
                            id="password_lama" name="password_lama" placeholder="Masukkan password lama Anda" required>
                        @error('password_lama')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div class="mb-3 col-md-12">
                        <label for="password_baru" class="form-label m-0">Password Baru</label>
                        <p class="text-muted mb-1" style="font-size: 0.775rem;">Masukkan password baru Anda</p>
                        <input type="password" class="form-control @error('password_baru') is-invalid @enderror"
                            id="password_baru" name="password_baru" placeholder="Masukkan password baru Anda" required>
                        @error('password_baru')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- konfirmasi password -->
                    <div class="mb-3 col-md-12">
                        <label for="konfirmasi_password" class="form-label m-0">Konfirmasi Password</label>
                        <p class="text-muted mb-1" style="font-size: 0.775rem;">Masukkan kembali password baru Anda</p>
                        <input type="password" class="form-control @error('konfirmasi_password') is-invalid @enderror"
                            id="konfirmasi_password" name="konfirmasi_password" placeholder="Masukkan konfirmasi password"
                            required>
                        @error('konfirmasi_password')
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
            </form>
        </div>
    </div>
</div>
@endsection
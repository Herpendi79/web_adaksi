@extends('layouts.anggota_layout')
@section('title', 'Profile')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Profile</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ Auth::user()->anggota && Auth::user()->anggota->foto && file_exists(public_path('uploads/foto_anggota/' . Auth::user()->anggota->foto)) 
    ? asset('uploads/foto_anggota/' . Auth::user()->anggota->foto) 
    : asset('uploads/foto_anggota/foto.jpg') }}"
                        alt="Profile Photo"
                        class="rounded-circle mb-3"
                        width="120"
                        height="120">

                    <h5 class="card-title mb-1">
                        {{ Auth::user()->anggota->nama_anggota }}
                    </h5>
                    <p class="text-muted mb-2">{{ Auth::user()->email }}</p>
                    @if (Auth::user()->anggota->status_anggota == 'aktif')
                    <span class="badge bg-primary">STATUS AKUN : AKTIF</span>
                    @elseif (Auth::user()->anggota->status_anggota == 'nonaktif')
                    <span class="badge bg-secondary">STATUS AKUN : NONAKTIF</span>
                    @else
                    <span class="badge bg-warning">STATUS AKUN : PENDING</span>
                    @endif
                    <div class="border-top pt-3 mt-3">
                        <a href="{{ url('anggota/profile/edit') }}" class="btn btn-primary w-100 mt-2">
                            <i class="bi bi-pencil-square"></i> Edit Profil
                        </a>
                        <a href="{{ url('anggota/profile/edit_password') }}" class="btn btn-warning w-100 mt-2">
                            <i class="bi bi-lock"></i> Ganti Password
                        </a>
                        @if (Auth::user()->anggota->status_anggota == 'aktif')
                        <a href="{{ url('anggota/download-kta') }}" class="btn btn-danger w-100 mt-2" target="_blank">
                            <i class="bi bi-download"></i> Download KTA
                        </a>
                        @else
                        <button class="btn btn-danger w-100 mt-2" disabled>
                            <i class="bi bi-download"></i> Download KTA (Tidak Tersedia)
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Detail Profil</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ID ANGGOTA</dt>
                        <dd class="col-sm-8">{{ Auth::user()->anggota->id_card }}</dd>

                        <dt class="col-sm-4">Nama Lengkap</dt>
                        <dd class="col-sm-8">{{ Auth::user()->anggota->nama_anggota }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ Auth::user()->email }}</dd>

                        <dt class="col-sm-4">No. Telepon</dt>
                        <dd class="col-sm-8">{{ $user->no_hp }}</dd>

                        <dt class="col-sm-4">NIP/NIPPPK</dt>
                        <dd class="col-sm-8">{{ Auth::user()->anggota->nip_nipppk }}</dd>

                        <dt class="col-sm-4">Status Dosen</dt>
                        <dd class="col-sm-8">{{ Auth::user()->anggota->status_dosen }}</dd>

                        <dt class="col-sm-4">Homebase PT</dt>
                        <dd class="col-sm-8">{{ Auth::user()->anggota->homebase_pt }}</dd>

                        <dt class="col-sm-4">Provinsi</dt>
                        <dd class="col-sm-8">{{ Auth::user()->anggota->provinsi }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
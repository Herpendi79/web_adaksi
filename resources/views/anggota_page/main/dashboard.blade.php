@extends('layouts.anggota_layout')
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
            <p>Halo, selamat datang di Dashboard Anggota. Silakan gunakan menu di samping untuk mengakses fitur-fitur yang
                tersedia.</p>
            <p>Klik tautan berikut untuk bergabung di WAG ANGGOTA TETAP ADAKSI --><a href="https://chat.whatsapp.com/L0GbxwfVsLi3VFr0cuPw2s" target="blank"> Klik</a> atau <a href="https://chat.whatsapp.com/J36cYGoe9DT8V5bssP7Ymt" target="blank"> Klik</a></p>
            <p>Klik tautan berikut untuk bergabung di Saluran WA ADAKSI --><a href="https://whatsapp.com/channel/0029VbAU8q47DAX5TaAZhJ30" target="blank"> Klik</a></p>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <a href="/anggota/profile" class="card text-decoration-none text-dark h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-person-circle fs-2 me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Profil</h5>
                            <p class="card-text mb-0">Lihat dan edit profil Anda.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="/anggota/download-kta" class="card text-decoration-none text-dark h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-card-heading fs-2 me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Download KTA</h5>
                            <p class="card-text mb-0">Unduh Kartu Tanda Anggota Anda.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

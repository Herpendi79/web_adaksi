<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8" />
    <title>
        {{ config('app.name', 'Laravel') }} - Daftar Anggota
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    {{-- Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    @notifyCss

    <style>
        body {
            background-color: #a02929 !important;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        .card.pendaftaran {
            margin: 20px auto;
            max-width: 700px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .card .card-title-desc {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 20px;
        }


        /* Responsive */
        @media (max-width: 576px) {
            .card.pendaftaran {
                margin: 10px;
                padding: 15px;
            }

            .card .card-title {
                font-size: 1rem;
                font-weight: 600;
                margin-bottom: 10px;
            }

            .card .card-title-desc {
                font-size: 0.7rem;
                color: #6c757d;
                margin-bottom: 15px;
            }
        }
    </style>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ url('/') }}/assets/images/fav.ico">

    <!-- App css -->
    <link href="{{ url('/') }}/assets-template/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    {{-- Custom CSS --}}
    <link href="{{ url('/') }}/assets/css/custom.css" rel="stylesheet" type="text/css" />

    <!-- Icons -->
    <link href="{{ url('/') }}/assets-template/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="{{ url('/') }}/assets-template/js/head.js"></script>


</head>

<!-- body start -->

<body data-menu-color="light">

    <!-- Begin page -->
    <div>

        <!-- Topbar Start -->
        {{-- <div class="topbar-custom" style="left: 0; right: 0; top: 0; z-index: 1030;">
            <div class="container-fluid">
                <div class="d-flex justify-content-between">
                    Daftar Anggota
                </div>
            </div>
        </div> --}}
        <!-- end Topbar -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page" style="margin: 0">
            <div class="content">
                <div class="card pendaftaran">
                    <div class="card-body">
                        <div>
                            <div class="">
                                <img src="{{ url('/') }}/assets/images/logo.png" alt="Logo"
                                    class="img-fluid mb-3" style="max-width: 150px; display: block; margin: 0 auto;">

                            </div>
                            <h4 class="card-title text-center">Form Pendataan Anggota Tetap ADAKSI
                            </h4>
                          <p class="card-title-desc text-center"> Panduan cara pendaftaran dapat dilihat pada video berikut: 
                                <strong><a href="https://youtu.be/MjG9OuX2mvE" target="blank">Klik disini</a></strong></p>
                        </div>

                        <form action="{{ url('anggota/daftar') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Nama Lengkap --}}
                            <div class="mb-3">
                                <label for="nama" class="form-label m-0">Nama Lengkap</label>
                                <p class="text-muted mb-1" style="font-size: 0.775rem;">Nama Lengkap dengan gelar</p>
                                <input type="text" class="form-control @error('nama_anggota') is-invalid @enderror"
                                    id="nama" name="nama_anggota" placeholder="Masukkan nama lengkap Anda"
                                    value="{{ old('nama_anggota') }}">
                                @error('nama_anggota')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label m-0">Email</label>
                                <p class="text-muted mb-1" style="font-size: 0.775rem;">Email yang valid untuk
                                    pengiriman informasi</p>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                    placeholder="Masukkan email Anda" value="{{ old('email') }}">
                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- NIP/NIPPPK --}}
                                <div class="mb-3 col-md-6">
                                    <label for="nip_nippk" class="form-label mb-1">NIP/NIPPPK</label>
                                    <input type="tel" class="form-control @error('nip_nipppk') is-invalid @enderror" id="nip_nippk" name="nip_nipppk"
                                        placeholder="Masukkan NIP/NIPPPK Anda" value="{{ old('nip_nipppk') }}">
                                    @error('nip_nipppk')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                {{-- Nomor HP / WA --}}
                                <div class="mb-3 col-md-6">
                                   <label for="no_hp" class="form-label mb-1">Nomor HP (<strong>Wajib WA</strong>)</label>
                                    <input type="tel" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp"
                                        placeholder="Masukkan nomor HP/WA Anda" value="{{ old('no_hp') }}">
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
                                        <option value="" disabled selected>-- Pilih status dosen Anda --</option>
                                        <option value="Dosen PTN">Dosen PTN</option>
                                        <option value="Dosen DPK">Dosen DPK</option>
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
                                        placeholder="Masukkan nama PT Anda" value="{{ old('homebase_pt') }}">
                                    @error('homebase_pt')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Provinsi --}}
                            <div class="mb-3">
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
                                    @endphp

                                    @foreach($daftar_provinsi as $prov)
                                    <option value="{{ $prov }}" {{ old('provinsi') == $prov ? 'selected' : '' }}>
                                        {{ $prov }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('provinsi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            {{-- Bukti Transfer --}}
                            <?php $angka_format = rand(100, 999); // generate angka 3 digit acak

                            ?>
                            <div class="mb-3">
                                <label for="bukti_transfer" class="form-label mb-1">Bukti Transfer</label>
                                <input type="file"
                                    class="form-control @error('bukti_transfer') is-invalid @enderror"
                                    id="bukti_transfer"
                                    name="bukti_transfer">

                                <small class="form-text text-danger">
                                    *Transfer senilai <strong>Rp. 100.<?= $angka_format ?></strong> (pastikan persis hingga 3 digit terakhir)
                                </small><br>
                                <small class="form-text text-danger">
                                    *Transfer ke <strong>312601035654536</strong> , Rek BRI, a.n <strong>Nindya Adiasti</strong> (Bendahara DPP ADAKSI)
                                </small>

                                @error('bukti_transfer')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>


                            <button type="submit" class="btn btn-dark w-100">Daftar Anggota</button>
                            <a href="{{ url('/') }}" class="btn btn-light w-100 mt-2">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer Start -->
            <footer class="footer" style="left: 0; right: 0;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col fs-13 text-muted text-center">
                            &copy; Copyright
                            <script>
                                document.write(new Date().getFullYear())
                            </script> by <a class="text-reset fw-semibold">
                                {{ config('app.name') ?? 'Your Company Name' }}
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->

        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Vendor -->
    <script src="{{ url('/') }}/assets-template/libs/jquery/jquery.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/simplebar/simplebar.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/node-waves/waves.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/feather-icons/feather.min.js"></script>

    <!-- Apexcharts JS -->
    <script src="{{ url('/') }}/assets-template/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Widgets Init Js -->
    <script src="{{ url('/') }}/assets-template/js/pages/crm-dashboard.init.js"></script>

    <!-- App js-->
    <script src="{{ url('/') }}/assets-template/js/app.js"></script>
    
    <!-- Tambahkan CDN SweetAlert -->
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




    <x-notify::notify />
    @notifyJs
</body>

</html>
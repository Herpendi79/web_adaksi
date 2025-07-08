<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8" />
    <title>
        {{ config('app.name', 'Laravel') }} - Akses Fasilitas
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ADAKSI" />
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
                    Akses Fasilitas
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
                            <h4 class="card-title text-center">Fasilitas Kegiatan ADAKSI
                            </h4>
                            <p class="card-title-desc text-center">Selamat Datang, <strong>{{ $pendaftar->nama ?? '-' }}</strong>!</p>
                        </div>
                        <div>
                            <p class="card-title-desc text-center"><strong>{{ $pendaftar->webinar->judul ?? '-' }}</strong>!</p>
                        </div>
                    </div>
                    <div>
                        <p class="card-title-desc text-center">Sertifikat -> <a href="{{ url('fasilitas/sertifikat/' . $pendaftar->id_wb) }}" target="_blank">DOWNLOAD</a></p>
                        @foreach ($fasilitas as $item)
                        <p class="card-title-desc text-center">{{ $item->nama }} -> <a href="{{ $item->link }}" target="_blank">DOWNLOAD</a></p>
                        @endforeach
                    </div>
                    <form action="{{ route('fasilitas.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-dark w-100 mt-2">
                            Belum Daftar Anggota? Klik untuk Daftar
                        </button>
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
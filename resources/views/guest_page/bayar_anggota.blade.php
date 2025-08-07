<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8" />
    <title>
        {{ config('app.name', 'Laravel') }} - Daftar Anggota
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
        <div class="content-page" style="margin: 0">
            <div class="content">
                <div class="card pendaftaran">
                    <div class="card-body">
                        <div>
                            <div class="">
                                <img src="{{ url('/') }}/assets/images/logo.png" alt="Logo"
                                    class="img-fluid mb-3" style="max-width: 150px; display: block; margin: 0 auto;">

                            </div>
                            @if ($status_anggota === 'aktif')
                                <h2 class="card-title text-center">Halo, {{ $nama }}
                                </h2>
                                <h4 class="text-center">
                                    Tagihan anda sebesar <strong>Rp {{ number_format($biaya, 0, ',', '.') }}</strong>
                                    telah berhasil dibayar
                                </h4>
                                <p class="text-center">
                                    Anda telah resmi menjadi anggota ADAKSI
                                </p>
                                <button type="button" class="btn btn-dark w-100"
                                    onclick="window.location.href='{{ url('/login') }}'">
                                    Login
                                </button>
                            @elseif ($status_anggota === 'pending' && $order_id === null)
                                <h2 class="card-title text-center">Halo, {{ $nama }}
                                </h2>
                                <p class="text-center">
                                    Jika halaman ini tidak dapat diakses lagi, anda dapat mengaksesnya melalui link yang
                                    dikirim via WA (Whatsapp).
                                </p>
                                <hr>
                                <h5 class="text-center">
                                    <strong>Pilih Metode Pembayaran yang Diinginkan:</strong>
                                </h5>
                                <div class="flex flex-wrap justify-center gap-4">
                                    @foreach ($channels as $channel)
                                        <form action="{{ route('transaction.store_pembayaran') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="id_user" value="{{ $id_user }}">
                                            <input type="hidden" name="method" value="{{ $channel->code }}">
                                            <button type="submit"
                                                class="bg-white p-5 h-32 w-36 rounded-md shadow-md flex flex-col items-center justify-center hover:shadow-lg transition">
                                                <img src="https://developers.google.com/identity/images/g-logo.png"
                                                    alt="Logo"
                                                    style="width: 30px; height: 30px; margin-bottom: 10px;">
                                                <p class="text-sm text-center text-gray-700">Pay With
                                                    {{ $channel->name }}</p>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @elseif ($status_anggota === 'pending' && $order_id != null)
                                <script>
                                    window.location.href = "{{ route('bayar_anggota_show', $order_id) }}";
                                </script>
                            @else
                                <h2 class="card-title text-center">Halo, {{ $nama }}
                                </h2>
                                <h4 class="text-center">
                                    Tagihan anda sudah tidak berlaku, silakan melakukan pendaftaran ulang
                                </h4>
                                <p class="text-center">
                                    Salam ADAKSI!
                                </p>
                                <button type="button" class="btn btn-dark w-100"
                                    onclick="window.location.href='{{ url('/daftar-anggota-adaksi') }}'">
                                    Ke Halaman Daftar
                                </button>
                            @endif
                        </div>
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
    </script>

    <!-- Vendor -->
    <script src="{{ url('/') }}/assets-template/libs/jquery/jquery.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/simplebar/simplebar.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/node-waves/waves.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="{{ url('/') }}/assets-template/libs/feather-icons/feather.min.js"></script>

    <!-- App js-->
    <script src="{{ url('/') }}/assets-template/js/app.js"></script>

    <!-- Tambahkan CDN SweetAlert -->
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
    <x-notify::notify />
    @notifyJs
</body>

</html>

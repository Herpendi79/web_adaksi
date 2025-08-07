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
    <!-- Di <head> -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

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

        .my-confirm-button {
            background-color: #e74c3c !important;
            /* Merah */
            color: white !important;
            border: none;
            box-shadow: none;
            transition: background-color 0.3s ease;
        }

        .my-confirm-button:hover {
            background-color: #9b59b6 !important;
            /* Ungu saat hover */
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
                                    Tagihan Anda telah berhasil dibayar
                                </h4>
                                <p class="text-center">
                                    Anda telah resmi menjadi anggota ADAKSI
                                </p>
                                <button type="button" class="btn btn-dark w-100"
                                    onclick="window.location.href='{{ url('/login') }}'">
                                    Login
                                </button>
                            @elseif ($status_anggota === 'pending')
                                <h2 class="card-title text-center">Halo, {{ $nama }}
                                </h2>
                                <h4 class="text-center">
                                    Total yang harus dibayar:
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-red-500 text-white text-sm font-semibold">
                                        Rp. {{ number_format($detail->amount ?? 0, 0, ',', '.') }}
                                    </span>
                                </h4>
                                <h4 class="text-center">
                                    Status:
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-red-500 text-white text-sm font-semibold">
                                        {{ $detail->status ?? '-' }}
                                    </span>
                                </h4>
                                <h4 class="text-center">
                                    Nomor VA:
                                    <span id="vaNumber"
                                        class="inline-block px-3 py-1 rounded-full bg-red-500 text-white text-sm font-semibold select-all">
                                        {{ $detail->pay_code ?? '-' }}
                                    </span>

                                    <!-- Tombol Copy -->
                                    <button onclick="copyVANumber()"
                                        class="ml-2 inline-flex items-center px-2 py-1 bg-gray-200 hover:bg-gray-300 text-sm rounded-full text-gray-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16h8m-8-4h8m-8-4h8M4 6h16M4 18h16M4 12h16" />
                                        </svg>
                                        Copy
                                    </button>
                                </h4>
                                @php
                                    $expiredTimestamp = $detail->expired_time ?? null;
                                    $reference = $detail->reference ?? null;
                                    //  $id_user = $detail->id_user; // Pastikan kamu punya ini dari controller
                                @endphp

                                <h4 class="text-center">
                                    Batas Pembayaran:
                                    <span id="countdownTimer"
                                        class="inline-block px-3 py-1 rounded-full bg-red-500 text-white text-sm font-semibold"
                                        data-expired="{{ $expiredTimestamp }}" data-reference="{{ $reference }}">
                                        Loading...
                                    </span>
                                </h4>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const timerEl = document.getElementById('countdownTimer');
                                        const expiredTime = timerEl.getAttribute('data-expired');
                                        const reference = timerEl.getAttribute('data-reference');

                                        if (!expiredTime) {
                                            timerEl.textContent = 'Tidak tersedia';
                                            return;
                                        }

                                        const targetTime = new Date(parseInt(expiredTime) * 1000);
                                        let countdownInterval;

                                        function updateCountdown() {
                                            const now = new Date();
                                            const diff = targetTime - now;

                                            if (diff <= 0) {
                                                timerEl.textContent = "Waktu habis";
                                                timerEl.classList.remove('bg-red-500');
                                                timerEl.classList.add('bg-gray-400');

                                                clearInterval(countdownInterval); // 🔁 Hentikan interval agar tidak looping

                                                // Redirect otomatis sekali saja
                                                window.location.href = `/cek-status/${reference}`;
                                                return;
                                            }

                                            const hours = Math.floor(diff / 1000 / 60 / 60);
                                            const minutes = Math.floor((diff / 1000 / 60) % 60);
                                            const seconds = Math.floor((diff / 1000) % 60);

                                            timerEl.textContent = `${hours}j ${minutes}m ${seconds}d`;
                                        }

                                        updateCountdown(); // Jalankan pertama kali
                                        countdownInterval = setInterval(updateCountdown, 1000); // Jalankan setiap detik
                                    });
                                </script>
                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const reference = document.getElementById('countdownTimer')?.getAttribute('data-reference');
                                        const idUser = "{{ $id_user }}"; // Pastikan $id_user dikirim ke view

                                        function checkStatus() {
                                            fetch(`/status-transaksi/${reference}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.status === 'PAID' || data.status === 'paid') {
                                                        window.location.href = `/cek-status/${reference}`;
                                                    } else if (data.status === 'EXPIRED' || data.status === 'expired') {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Pembayaran Gagal',
                                                            text: 'Pembayaran telah expired, silakan daftar ulang.',
                                                            confirmButtonText: 'OK',
                                                            allowOutsideClick: false,
                                                            customClass: {
                                                                confirmButton: 'my-confirm-button'
                                                            }
                                                        }).then(() => {
                                                            window.location.href = `/cek-status/${reference}`;
                                                        });
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error("Gagal cek status:", error);
                                                });
                                        }
                                        setInterval(checkStatus, 5000);
                                    });
                                </script>




                                <p class="text-center">
                                    Jika halaman ini tidak dapat diakses lagi, anda dapat mengaksesnya melalui link yang
                                    dikirim via WA (Whatsapp).
                                </p>
                                <hr>
                                <h5 class="text-center">
                                    <strong>Instruksi Pembayaran:</strong>
                                </h5>
                                @foreach ($detail->instructions as $instructions)
                                    <div class="flex flex-wrap justify-center gap-4">
                                        <span>{{ $instructions->title }}</span>
                                    </div>
                                    <div class="collapse-content">
                                        <ul>
                                            @foreach ($instructions->steps as $steps)
                                                <li class="text-sm">{{ $loop->iteration }}. {!! $steps !!}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
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

    <script>
        function copyVANumber() {
            const vaNumber = document.getElementById("vaNumber").innerText;

            navigator.clipboard.writeText(vaNumber).then(function() {
                alert("Nomor VA berhasil disalin!");
            }, function(err) {
                alert("Gagal menyalin nomor VA");
            });
        }
    </script>


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
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <x-notify::notify />
    @notifyJs
</body>

</html>

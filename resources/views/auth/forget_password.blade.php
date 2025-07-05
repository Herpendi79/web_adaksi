<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>
        @if (isset($title))
        {{ $title }} - {{ config('app.name') }}
        @else
        Masuk - {{ config('app.name') }}
        @endif
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ADAKSI" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    @notifyCss

    {{-- Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ url('/') }}/assets/images/fav.ico">

    <!-- App css -->
    <link href="{{ url('/') }}/assets-template/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ url('/') }}/assets/css/custom.css" rel="stylesheet" type="text/css" />

    <!-- Icons -->
    <link href="{{ url('/') }}/assets-template/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="{{ url('/') }}/assets-template/js/head.js"></script>

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
    </style>

</head>

<body>
    <!-- Begin page -->
    <div class="account-page">
        <div class="container-fluid p-0">
            <div class="flex g-0 px-3 py-3 vh-100">

                <div class="col-xl-12" style="max-width: 700px; margin: auto;">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-0 p-0 p-lg-3">
                                        <div class="mb-0 border-0 p-md-4 p-lg-0">
                                            <div class="mb-4 p-0 text-lg-start text-center">
                                                <div class="auth-brand d-flex justify-content-center mb-4">
                                                    <a href="index.html" class="logo">
                                                        <span class="logo-lg">
                                                            <img src="{{ url('/') }}/assets/images/logo.png"
                                                                alt="" height="30" style="width: 70px;">
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="auth-title-section mb-4 text-lg-start text-center">
                                                <h3 class="text-dark fw-semibold text-center mb-3">FORGOT PASSWORD</h3>
                                                <p class="text-muted fs-14 mb-0">Masukkan email Anda untuk melanjutkan.</p>
                                            </div>
                                            <div class="pt-0">
                                                <form action="{{ url('/forget-password') }}" class="my-4"
                                                    method="POST">
                                                    @csrf
                                                    <div class="form-group mb-3">
                                                        <label for="emailaddress" class="form-label">Email
                                                            address</label>
                                                        <input class="form-control @error('email') is-invalid @enderror"
                                                            type="email" id="emailaddress" name="email"
                                                            placeholder="Enter your email" value="{{ old('email') }}">
                                                        @error('email')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label for="no_hp" class="form-label">Nomor HP</label>
                                                        <input class="form-control @error('no_hp') is-invalid @enderror"
                                                            type="text" id="no_hp" name="no_hp"
                                                            placeholder="Masukkan nomor HP Anda"
                                                            value="{{ old('no_hp') }}">
                                                        @error('no_hp')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group mb-0 row">
                                                        <div class="col-12">
                                                            <div class="d-grid">
                                                                <button class="btn btn-primary fw-semibold"
                                                                    type="submit">
                                                                    Reset </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                <div class="text-center text-muted">
                                                    <p class="mb-0">
                                                        Belum punya akun?
                                                        <a class='text-primary fw-medium'
                                                            href='/daftar-anggota'>Daftar</a>
                                                    </p>
                                                </div>
                                                <div class="text-center text-muted">
                                                    <p class="mb-0">
                                                        Kembali?
                                                        <a class='text-primary fw-medium'
                                                            href='{{ url('/') }}'>Klik Disini</a>
                                                    </p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    <x-notify::notify />
    {{-- <x:notify-messages /> --}}
    @notifyJs
</body>

</html>
@extends('layouts.anggota_layout')
@php use Illuminate\Support\Str; @endphp
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

<?php
$main_data = 'Rakernas';
$url = '/anggota/rakernas';
?>
@section('title', $main_data)
@section('content')
    <div class="container-fluid">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">
                    {{ $main_data }}
                </h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ url($url) }}">
                            Kegiatan
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url($url) }}">
                            {{ $main_data }}
                        </a>
                    </li>
                </ol>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div class="text-center">
                            <h5 class="card-title mb-3">Pembayaran Kegiatan Rakernas</h5>

                            <h2 class="card-title mb-2">Halo, {{ $nama }}</h2>
                            @if ($status === 'pending')
                                <h4 class="mb-3">
                                    Total yang harus dibayar: <strong>Rp {{ number_format($biaya, 0, ',', '.') }}</strong>
                                </h4>
                                <p class="mb-4">
                                    Jika halaman ini tidak dapat diakses lagi, anda dapat mengaksesnya melalui halaman
                                    Rakernas.
                                </p>
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-4">
                                        <button type="submit" class="btn btn-success w-100" id="pay-button">Bayar</button>
                                    </div>
                                </div>
                            @elseif ($status === 'valid')
                                <h4 class="mb-3">
                                    Tagihan anda sebesar <strong>Rp {{ number_format($biaya, 0, ',', '.') }}</strong>
                                    telah berhasil dibayar
                                </h4>
                                <p class="mb-4">
                                    Anda telah terdaftar di kegiatan Rakernas ADAKSI
                                </p>
                            @else
                                <h4 class="mb-3">
                                    Tagihan anda sudah tidak berlaku, silakan melakukan pendaftaran ulang
                                </h4>
                                <p class="mb-4">
                                    Salam ADAKSI!
                                </p>
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-4">
                                        <button type="submit" class="btn btn-success w-100" id=""
                                            onclick="window.location.href='{{ url('/anggota/rakernas') }}'">
                                            Ke Halaman Rakernas
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEYS') }}">
    </script>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    // Redirect ke route validasi, passing ID PRK
                    window.location.href = "/validasi-pembayaran-rakernas/{{ $snapToken }}";
                },
                onPending: function(result) {
                    alert("Pembayaran Anda sedang diproses.");
                },
                onError: function(result) {
                    alert("Pembayaran gagal. Silakan coba lagi.");
                }
            });
        };
        // ⏱ Cek apakah pembayaran sudah expired setelah 5 detik
        setTimeout(function() {
            fetch('/cek-expired-rakernas/{{ $snapToken }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'deleted') {
                        alert('Pembayaran Anda telah expired, silakan daftar ulang.');
                        window.location.href = "{{ url('/anggota/rakernas') }}";
                    }
                });
        }, 5000);
    </script>





    {{-- Script Notifikasi Validasi --}}
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


@endsection

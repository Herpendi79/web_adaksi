@extends('layouts.anggota_layout')
@php use Illuminate\Support\Str; @endphp
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

<?php
$main_data = 'Webinar';
$url = '/anggota/webinar';
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
                    <div class="card-body p-4">
                        @if ($token != null && $no_urut != null)
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="mdi mdi-check-circle-outline text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h2 class="card-title fw-bold">Halo, {{ $nama }}</h2>
                                <h4 class="fw-semibold text-success mb-2">Pembayaran Berhasil!</h4>
                                <div class="mb-2">
                                    <span class="badge bg-success fs-5">Rp {{ number_format($biaya, 0, ',', '.') }}</span>
                                </div>
                                <p class="mb-3">Anda telah resmi menjadi peserta Webinar.<br>Selamat bergabung!</p>
                                <button type="button" class="btn btn-success w-50 mt-2"
                                    onclick="window.location.href='{{ url('anggota/webinar') }}'">
                                    <i class="mdi mdi-home"></i> Ke Halaman Webinar
                                </button>
                            </div>
                        @elseif ($token === null && $no_urut === null)
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="mdi mdi-credit-card-outline text-danger" style="font-size: 3rem;"></i>
                                </div>
                                <h2 class="card-title fw-bold">Halo, {{ $nama }}</h2>
                                <h4 class="fw-semibold text-danger mb-2">- Menunggu Pembayaran -</h4>
                                <div class="mb-2">
                                    <span class="badge bg-danger text-white fs-5">Rp
                                        {{ number_format($biaya, 0, ',', '.') }}</span>
                                </div>
                                <p class="mb-3">Tekan tombol <b>Klik Bayar</b> untuk melanjutkan pembayaran.<br>Jika halaman ini
                                    tidak dapat diakses lagi, silakan buka menu Webinar.</p>
                                <button type="submit" class="btn btn-danger w-50 mt-2" id="pay-button">
                                    <i class="mdi mdi-cash"></i>Klik Bayar<i class="mdi mdi-cash"></i>
                                </button>
                            </div>
                        @else
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="mdi mdi-close-circle-outline text-danger" style="font-size: 3rem;"></i>
                                </div>
                                <h2 class="card-title fw-bold">Halo, {{ $nama }}</h2>
                                <h4 class="fw-semibold text-danger mb-2">Tagihan Tidak Berlaku</h4>
                                <p class="mb-3">Tagihan anda sudah tidak berlaku, silakan melakukan pendaftaran
                                    ulang.<br>Salam ADAKSI!</p>
                                <button type="button" class="btn btn-danger w-50 mt-2"
                                    onclick="window.location.href='{{ url('/anggota/webinar') }}'">
                                    <i class="mdi mdi-refresh"></i> Daftar Ulang
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.clientKey') }}"></script>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    // Redirect ke route validasi, passing ID anggota
                    window.location.href = "/validasi-pembayaran-webinar/{{ $snapToken }}";
                },
                onPending: function(result) {
                    alert("Pembayaran Anda sedang diproses.");
                },
                onError: function(result) {
                    alert("Pembayaran gagal. Silakan coba lagi.");
                }
            });
        };
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/cek-expired/{{ $snapToken }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'redirect') {
                        window.location.href = data.url;
                    } else if (data.status === 'expire' || data.status === null) {
                        // langsung arahkan jika expired
                        window.location.href = '/hapus-jika-expired/{{ $snapToken }}';
                    }
                });
        });
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

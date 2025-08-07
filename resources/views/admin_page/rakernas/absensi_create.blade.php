@extends('layouts.admin_layout')
@section('title', 'Tambah Absensi')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Tambah Absensi</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Absensi</a></li>
                <li class="breadcrumb-item active">Tambah Absensi</li>
            </ol>
        </div>
    </div>

    <div class="card col-md-6">
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <form action="{{ route('rakernas.store_absensi') }}" method="POST"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="row">
                    {{-- Judul --}}
                    <div class="mb-2">
                        <label for="scan" class="form-label m-0">Scan / ID Anggota</label>
                        <input type="text" class="form-control @error('scan') is-invalid @enderror"
                            id="scan" name="scan" placeholder="Scan QRCode / Tulis ID Anggota"
                            value="{{ old('scan') }}">
                        @error('scan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="modal fade" id="modalDetailPendaftar" tabindex="-1" aria-labelledby="modalDetailPendaftarLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalDetailPendaftarLabel">Detail Peserta</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <input type="hidden" id="idPrk">
                                <img id="fotoAnggota" src="" alt="Foto Anggota" class="img-thumbnail mb-2" style="max-height: 250px;">
                                <p><strong>ID Card:</strong> <span id="idCard"></span></p>
                                <p><strong>Nama Anggota:</strong> <span id="namaAnggota"></span></p>
                                <p><strong>Homebase PT:</strong> <span id="homebasePt"></span></p>
                                <p><strong>Provinsi:</strong> <span id="provinsi"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="btnSimpanAbsensi">Simpan Absen</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>


                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const scanInput = document.getElementById('scan');
                        if (scanInput) {
                            scanInput.focus();

                            scanInput.addEventListener('keydown', function(e) {
                                if (e.key === 'Enter') {
                                    e.preventDefault();

                                    const scanValue = scanInput.value.trim();
                                    if (scanValue === "") {
                                        alert("Silakan scan QR Code terlebih dahulu.");
                                        return;
                                    }

                                    fetch("{{ route('rakernas.check_qrcode') }}", {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json",
                                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                            },
                                            body: JSON.stringify({
                                                scan: scanValue
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status === "success") {
                                                document.getElementById('idCard').innerText = data.data.id_card;
                                                document.getElementById('namaAnggota').innerText = data.data.nama_anggota;
                                                document.getElementById('homebasePt').innerText = data.data.homebase_pt;
                                                document.getElementById('provinsi').innerText = data.data.provinsi;
                                                document.getElementById('fotoAnggota').src = data.data.foto;
                                                document.getElementById('idPrk').value = data.data.id_prk; // simpan id_prk

                                                const modal = new bootstrap.Modal(document.getElementById('modalDetailPendaftar'));
                                                modal.show();
                                                scanInput.value = "";
                                                scanInput.focus();
                                            } else {
                                                alert(data.message);
                                                scanInput.select();
                                            }
                                        })
                                        .catch(error => {
                                            console.error(error);
                                            alert("Terjadi kesalahan saat memeriksa QR Code.");
                                            scanInput.select();
                                        });
                                }
                            });
                        }

                        // Handle tombol Simpan Absen
                        document.getElementById('btnSimpanAbsensi').addEventListener('click', function() {
                            const idPrk = document.getElementById('idPrk').value;
                            if (!idPrk) {
                                alert("ID Pendaftar tidak ditemukan.");
                                return;
                            }

                            fetch("{{ route('rakernas.simpan_absensi') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        id_prk: idPrk
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalDetailPendaftar'));
                                    modal.hide(); // Tutup modal otomatis baik sukses maupun gagal

                                    if (data.status === "success") {
                                        alert(data.message);
                                    } else {
                                        alert(data.message);
                                    }

                                    scanInput.focus(); // Fokus ke scan untuk scan berikutnya
                                })
                                .catch(error => {
                                    console.error(error);
                                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalDetailPendaftar'));
                                    modal.hide(); // Tetap tutup modal jika error
                                    alert("Terjadi kesalahan saat menyimpan absensi.");
                                    scanInput.focus();
                                });
                        });
                    });
                </script>




                <div class="d-flex justify-content-start mb-3 gap-2">
                    <a href="{{ url('admin/rakernas/') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scanInput = document.getElementById('scan');
            if (scanInput) {
                scanInput.focus();
            }
        });
    </script>

</div>
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
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
        confirmButtonText: 'OK'
    });
</script>
@endif
@endsection
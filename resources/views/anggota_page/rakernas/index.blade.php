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
                        <div class="d-md-flex align-items-center">
                            <div>
                                <h5 class="card-title mb-1">Daftar Kegiatan Rakernas</h5>
                            </div>
                            <form class="d-flex flex-stack flex-wrap gap-1 justify-content-md-end ms-auto">

                                <div class="position-relative topbar-search">
                                    <input name="search" type="text"
                                        class="form-control bg-light-subtle ps-4 py-1 border fs-14" placeholder="Search..."
                                        value="{{ request('search') }}">
                                    <i
                                        class="mdi mdi-magnify fs-16 position-absolute text-dark top-50 translate-middle-y ms-2"></i>
                                </div>
                                {{-- Filter --}}
                                <button type="submit"
                                    class="btn btn-outline-info btn-sm d-flex align-items-center gap-1 ms-2">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>

                                <a href="/anggota/rakernas"
                                    class="btn btn-primary btn-sm d-flex align-items-center gap-1 ms-2">
                                    <i class="mdi mdi-refresh"></i> Refresh
                                </a>

                            </form>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-traffic mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Tema</th>
                                        <th>Tempat</th>
                                        <th>Tanggal</th>
                                        <th>Batas Daftar</th>
                                        <th>Biaya</th>
                                        <th>Fasilitas</th>
                                        <th>QR Code</th>
                                        <th>Kepesertaan</th>
                                        <th>Status Acara</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rakernas as $key => $data)
                                        <tr>
                                            <td>{{ $rakernas->firstItem() + $key }}</td>
                                            <td>
                                                {{ Str::limit($data->tema, 30) }}
                                                @php
                                                    $today = \Carbon\Carbon::today();
                                                    $tanggalMulai = \Carbon\Carbon::parse($data->tanggal_mulai);
                                                    $tanggalSelesai = \Carbon\Carbon::parse($data->tanggal_selesai);
                                                @endphp
                                                @if (
                                                    $today->gt($tanggalSelesai) &&
                                                        in_array($data->id_rk, $rakernas_terdaftar_ids) &&
                                                        in_array($data->id_rk, $rakernas_status))
                                                    @php
                                                        $pendaftar = $data->pendaftar->first();
                                                    @endphp
                                                    <a href="{{ url('anggota/sertifikat_rakernas/' . $pendaftar->id_prk) }}"
                                                        class="btn btn-sm btn-primary mt-1 d-flex align-items-center gap-1"
                                                        target="_blank">
                                                        Download Sertifikat
                                                    </a>
                                                @endif

                                            </td>
                                            <td>
                                                <strong style="font-size: 16px;">{{ $data->tempat }}</strong><br>
                                                Kuota Anggota Biasa: {{ $data->kuota }}<br>
                                                Valid: {{ $data->valid_daftar }}
                                                Pending: {{ $data->unvalid_daftar }}

                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($data->tanggal_mulai)->translatedFormat('d F Y') }}
                                                s.d.
                                                {{ \Carbon\Carbon::parse($data->tanggal_selesai)->translatedFormat('d F Y') }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($data->tanggal_tutup)->subDay()->translatedFormat('d F Y') }}
                                            </td>
                                            <td><span class="text">Pengurus: Rp.
                                                    {{ number_format($data->biaya) }}</span><br>
                                                <span class="text">Non Pengurus: Rp.
                                                    {{ number_format($data->biaya_non_pengurus) }}</span>
                                            </td>
                                            <td>{{ $data->fasilitas }}</td>
                                            <td>
                                                @php
                                                    $pendaftar = $data->pendaftar->first();
                                                @endphp
                                                @if ($pendaftar && filled($pendaftar->qrcode))
                                                <!--    <button type="button" aria-label="anchor"
                                                        class="btn btn-icon btn-sm bg-info-subtle me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalValidasi{{ $data->id_prk }}"
                                                        data-bs-original-title="Validasi">
                                                        <i class="mdi mdi-eye fs-14 text-info"></i>
                                                    </button> -->
                                                    <a href="{{ asset('uploads/qrcode/' . $pendaftar->qrcode) }}"
                                                        target="_blank" class="btn btn-icon btn-sm bg-info-subtle me-1"
                                                        aria-label="anchor" title="Lihat QR Code">
                                                        <i class="mdi mdi-eye fs-14 text-info"></i>
                                                    </a>
                                                    <!-- Modal Validasi -->
                                                    <div class="modal fade" id="modalValidasi{{ $data->id_prk }}"
                                                        tabindex="-1"
                                                        aria-labelledby="modalValidasiLabel{{ $data->id_prk }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form action="" method="">
                                                                @csrf
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="modalValidasiLabel{{ $data->id_prk }}">
                                                                            QR Code Peserta
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3 w-100">
                                                                            @if ($pendaftar->qrcode)
                                                                                <div
                                                                                    class="mb-2 d-flex justify-content-center">
                                                                                    <img src="{{ asset('uploads/qrcode/' . $pendaftar->qrcode) }}"
                                                                                        alt="QR Code"
                                                                                        class="img-fluid rounded"
                                                                                        style="max-width: 100%; height: auto;">
                                                                                </div>

                                                                                <div class="text-center mb-2">
                                                                                    <a href="{{ asset('uploads/qrcode/' . $pendaftar->qrcode) }}"
                                                                                        download="QRCode_Rakernas_{{ $pendaftar->id_prk }}.png"
                                                                                        class="btn btn-success btn-sm">
                                                                                        <i class="mdi mdi-download"></i>
                                                                                        Download QR Code
                                                                                    </a>
                                                                                </div>

                                                                                <div class="text-center">
                                                                                    <a href="{{ asset('uploads/qrcode/' . $pendaftar->qrcode) }}"
                                                                                        target="_blank"
                                                                                        class="btn btn-primary btn-sm">
                                                                                        <i class="mdi mdi-eye"></i> Lihat QR
                                                                                        Code
                                                                                    </a>
                                                                                </div>
                                                                            @else
                                                                                <p class="text-muted mb-2">QR Code belum
                                                                                    tampil, masih proses validasi Admin</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    Baju: {{ $pendaftar->ukuran_baju ?? 'Belum diisi' }}
                                                @else
                                                    <i class="mdi mdi-cancel fs-14 text-danger"></i>
                                                @endif

                                            </td>
                                            <td>
                                                @if ($data->tanggal_tutup <= $today)
                                                    <span
                                                        class="badge bg-danger-subtle text-danger fw-semibold text-uppercase">
                                                        Pendaftaran Ditutup
                                                    </span>
                                                @else
                                                    @if (in_array($data->id_rk, $rakernas_terdaftar_ids))
                                                        @if (in_array($data->id_rk, $rakernas_status))
                                                            <span
                                                                class="badge bg-primary-subtle text-primary fw-semibold text-uppercase">
                                                                Terdaftar
                                                            </span>
                                                        @elseif (in_array($data->id_rk, $rakernas_terdaftar_ids))
                                                            <span
                                                                class="badge bg-warning-subtle text-warning fw-semibold text-uppercase">
                                                                Pending
                                                            </span>
                                                            <a href="{{ '/anggota_page/bayar/' . $pendaftar->snap ?? 'Belum ada' }}"
                                                                aria-label="anchor"
                                                                class="badge bg-success-subtle text-success fw-semibold text-uppercase border border-success"
                                                                style="cursor:pointer"
                                                                data-bs-original-title="Bayar Rakernas">
                                                                Klik Untuk Bayar!
                                                            </a>
                                                        @else
                                                            <span
                                                                class="badge bg-secondary-subtle text-secondary fw-semibold text-uppercase">
                                                                Belum Daftar
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span
                                                            class="badge bg-warning-subtle text-warning fw-semibold text-uppercase">
                                                            Belum Terdaftar
                                                        </span>
                                                        @if ($today->lt($tanggalMulai))
                                                            <button type="button" aria-label="anchor"
                                                                class="badge bg-success-subtle text-success fw-semibold text-uppercase border border-success"
                                                                style="cursor:pointer" data-bs-toggle="modal"
                                                                data-bs-target="#modalValidasi{{ $data->id_rk }}"
                                                                data-bs-original-title="Daftar Rakernas"
                                                                data-biaya="{{ $data->biaya ?? 0 }}"
                                                                data-biaya-non="{{ $data->biaya_non_pengurus ?? 0 }}">
                                                                Klik Untuk Daftar!
                                                            </button>


                                                            <!-- Modal Validasi -->
                                                            <div class="modal fade" id="modalValidasi{{ $data->id_rk }}"
                                                                tabindex="-1"
                                                                aria-labelledby="modalValidasiLabel{{ $data->id_rk }}"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <form
                                                                        action="{{ route('store_registrasi_rakernas') }}"
                                                                        method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="modalValidasiLabel{{ $data->id_rk }}">
                                                                                    Daftar Rakernas
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            @if ($anggota && $anggota->foto != null)
                                                                                <div class="modal-body">
                                                                                    {{-- Kepengurusan --}}
                                                                                    <div class="mb-3">
                                                                                        <label for="kepengurusan"
                                                                                            class="form-label m-0">Kepengurusan</label>
                                                                                        <select class="form-select"
                                                                                            id="kepengurusan{{ $data->id_rk }}"
                                                                                            name="kepengurusan">
                                                                                            <option value="" selected
                                                                                                disabled>--
                                                                                                Pilih
                                                                                                Kepengurusan
                                                                                                --
                                                                                            </option>
                                                                                            <option value="DPP">DPP
                                                                                            </option>
                                                                                            <option value="DPW">DPW
                                                                                            </option>
                                                                                            <option value="DPC">DPC
                                                                                            </option>
                                                                                            @if ($data->hitungSisaKuotaAll() > 0)
                                                                                                <option
                                                                                                    value="Anggota Biasa">
                                                                                                    Anggota Biasa</option>
                                                                                            @endif
                                                                                        </select>
                                                                                        @error('kepengurusan')
                                                                                            <div class="invalid-feedback">
                                                                                                {{ $message }}
                                                                                            </div>
                                                                                        @enderror
                                                                                    </div>
                                                                                    {{-- Ukuran baju --}}
                                                                                    <div class="mb-3">
                                                                                        <label for="ukuran_baju"
                                                                                            class="form-label m-0">Ukuran
                                                                                            Baju</label>
                                                                                        <select class="form-select"
                                                                                            id="ukuran_baju"
                                                                                            name="ukuran_baju">
                                                                                            <option value="" selected
                                                                                                disabled>--
                                                                                                Pilih Ukuran
                                                                                                Baju
                                                                                                --
                                                                                            </option>
                                                                                            <option value="S">S :
                                                                                                Lingkar Dada (50) & Lingkar
                                                                                                Perut (67) LENGAN PENDEK
                                                                                            </option>
                                                                                            <option value="SLP">S :
                                                                                                Lingkar Dada (50) & Lingkar
                                                                                                Perut (67) LENGAN PANJANG
                                                                                            </option>
                                                                                            <option value="M">M :
                                                                                                Lingkar Dada (52) & Lingkar
                                                                                                Perut (70) LENGAN PENDEK
                                                                                            </option>
                                                                                            <option value="MLP">M :
                                                                                                Lingkar Dada (52) & Lingkar
                                                                                                Perut (70) LENGAN PANJANG
                                                                                            </option>
                                                                                            <option value="L">L :
                                                                                                Lingkar Dada (54) & Lingkar
                                                                                                Perut (73) LENGAN PENDEK
                                                                                            </option>
                                                                                            <option value="LLP">L :
                                                                                                Lingkar Dada (54) & Lingkar
                                                                                                Perut (73) LENGAN PANJANG
                                                                                            </option>
                                                                                            <option value="XL">XL :
                                                                                                Lingkar Dada (56) & Lingkar
                                                                                                Perut (75) LENGAN PENDEK
                                                                                            </option>
                                                                                            <option value="XLLP">XL :
                                                                                                Lingkar Dada (56) & Lingkar
                                                                                                Perut (75) LENGAN PANJANG
                                                                                            </option>
                                                                                            <option value="XXL">XXL :
                                                                                                Lingkar Dada (58) & Lingkar
                                                                                                Perut (78,5) LENGAN PENDEK
                                                                                            </option>
                                                                                            <option value="XXLLP">XXL :
                                                                                                Lingkar Dada (58) & Lingkar
                                                                                                Perut (78,5) LENGAN PANJANG
                                                                                            </option>
                                                                                            <option value="3XL">3XL :
                                                                                                Lingkar Dada (60) & Lingkar
                                                                                                Perut (81,5) LENGAN PENDEK
                                                                                            </option>
                                                                                            <option value="3XLLP">3XL :
                                                                                                Lingkar Dada (60) & Lingkar
                                                                                                Perut (81,5) LENGAN PANJANG
                                                                                            </option>
                                                                                            <option value="4XL">4XL :
                                                                                                Lingkar Dada (62) & Lingkar
                                                                                                Perut (84,5) LENGAN PENDEK
                                                                                            </option>
                                                                                            <option value="4XLLP">4XL :
                                                                                                Lingkar Dada (62) & Lingkar
                                                                                                Perut (84,5) LENGAN PANJANG
                                                                                            </option>
                                                                                        </select>
                                                                                        @error('ukuran_baju')
                                                                                            <div class="invalid-feedback">
                                                                                                {{ $message }}
                                                                                            </div>
                                                                                        @enderror
                                                                                    </div>
                                                                                    {{-- Biaya --}}
                                                                                    <div class="mb-3">
                                                                                        <label for="kepesertaan"
                                                                                            class="form-label mb-1">Biaya
                                                                                            Kepesertaan</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            id="kepesertaan{{ $data->id_rk }}"
                                                                                            name="kepesertaan" readonly>

                                                                                        <input type="hidden"
                                                                                            id="kepesertaan_hidden{{ $data->id_rk }}"
                                                                                            name="kepesertaan_hidden">
                                                                                        @error('kepesertaan')
                                                                                            <div class="invalid-feedback">
                                                                                                {{ $message }}
                                                                                            </div>
                                                                                        @enderror
                                                                                    </div>

                                                                                    <input type="hidden" name="id_rk"
                                                                                        value="{{ $data->id_rk }}">
                                                                                    <input type="hidden" name="id_user"
                                                                                        value="{{ Auth::id() }}">

                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button"
                                                                                        class="btn btn-dark btn-sm"
                                                                                        data-bs-dismiss="modal">Batal</button>
                                                                                    <button type="submit"
                                                                                        class="btn btn-primary btn-sm"
                                                                                        id="btnSubmit{{ $data->id_rk }}"
                                                                                        disabled>Daftar</button>

                                                                                </div>
                                                                            @else
                                                                                <div class="modal-body">
                                                                                    {{-- Ukuran baju --}}
                                                                                    <div class="mb-3">
                                                                                        <label for="ukuran_baju"
                                                                                            class="form-label m-0">Silakan
                                                                                            Upload Poto Profil Anda
                                                                                            terlebih dahulu agar dapat
                                                                                            mengakses menu ini! <a
                                                                                                href="{{ url('anggota/profile') }}">Klik
                                                                                                Disini</a></label>

                                                                                    </div>
                                                                                </div>

                                                                                <div class="modal-footer">
                                                                                    <button type="button"
                                                                                        class="btn btn-dark btn-sm"
                                                                                        data-bs-dismiss="modal">Tutup</button>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                            </td>
                                            <td>

                                                @if ($today->lt($tanggalMulai))
                                                    <span class="badge bg-info">PENDAFTARAN</span>
                                                @elseif($today->gt($tanggalSelesai))
                                                    <span class="badge bg-secondary">SELESAI</span>
                                                @else
                                                    <span class="badge bg-success">SEDANG BERLANGSUNG</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data Rakernas yang tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer pt-3 pb-0 border-top">
                        {{ $rakernas->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.modal').forEach(function(modal) {
                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const biayaPengurus = parseInt(button.getAttribute('data-biaya')) || 0;
                    const biayaNonPengurus = parseInt(button.getAttribute('data-biaya-non')) || 0;

                    const id_rk = this.id.replace('modalValidasi', '');

                    const kepengurusanSelect = document.getElementById('kepengurusan' + id_rk);
                    const ukuranBajuSelect = document.getElementById('ukuran_baju');
                    const kepesertaanInput = document.getElementById('kepesertaan' + id_rk);
                    const kepesertaanHidden = document.getElementById('kepesertaan_hidden' + id_rk);
                    const btnSubmit = document.getElementById('btnSubmit' + id_rk);

                    // Reset form & tombol submit
                    kepengurusanSelect.value = '';
                    kepesertaanInput.value = '';
                    kepesertaanHidden.value = '';
                    ukuranBajuSelect.disabled = false;
                    ukuranBajuSelect.value = '';
                    btnSubmit.disabled = true;

                    function checkSubmitState() {
                        const kep = kepengurusanSelect.value;
                        const baju = ukuranBajuSelect.value;

                        if (!kep) {
                            btnSubmit.disabled = true;
                            return;
                        }

                        if (kep === 'Anggota Biasa') {
                            // Tidak perlu ukuran baju
                            btnSubmit.disabled = false;
                        } else {
                            // Perlu ukuran baju
                            btnSubmit.disabled = !baju;
                        }
                    }

                    // Saat modal dibuka, evaluasi awal
                    checkSubmitState();

                    // Event ketika kepengurusan berubah
                    const changeHandler = function() {
                        const selectedValue = this.value;
                        if (!selectedValue) {
                            kepesertaanInput.value = '';
                            kepesertaanHidden.value = '';
                            return;
                        }

                        const biayaDasar = (selectedValue === 'Anggota Biasa') ?
                            biayaNonPengurus : biayaPengurus;
                        const randomSuffix = Math.floor(Math.random() * 999) + 1;
                        const biayaTerpilih = biayaDasar - (biayaDasar % 1000) + randomSuffix;

                        kepesertaanInput.value = biayaTerpilih.toLocaleString('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        });
                        kepesertaanHidden.value = biayaTerpilih;

                        if (selectedValue === 'Anggota Biasa') {
                            ukuranBajuSelect.disabled = true;
                            ukuranBajuSelect.innerHTML = '';
                            const option = document.createElement('option');
                            option.text = '-- Tanpa Free Baju --';
                            option.value = '-';
                            option.disabled = true;
                            option.selected = true;
                            ukuranBajuSelect.appendChild(option);
                        } else {
                            ukuranBajuSelect.disabled = false;
                            ukuranBajuSelect.innerHTML = `
                        <option value="" selected disabled>-- Pilih Ukuran Baju --</option>
                        <option value="S">S : Lingkar Dada (50) & Lingkar Perut (67) LENGAN PENDEK</option>
                        <option value="SLP">S : Lingkar Dada (50) & Lingkar Perut (67) LENGAN PANJANG</option>
                        <option value="M">M : Lingkar Dada (52) & Lingkar Perut (70) LENGAN PENDEK</option>
                        <option value="MLP">M : Lingkar Dada (52) & Lingkar Perut (70) LENGAN PANJANG</option>
                        <option value="L">L : Lingkar Dada (54) & Lingkar Perut (73) LENGAN PENDEK</option>
                        <option value="LLP">L : Lingkar Dada (54) & Lingkar Perut (73) LENGAN PANJANG</option>
                        <option value="XL">XL : Lingkar Dada (56) & Lingkar Perut (75) LENGAN PENDEK</option>
                        <option value="XLLP">XL : Lingkar Dada (56) & Lingkar Perut (75) LENGAN PANJANG</option>
                        <option value="XXL">XXL : Lingkar Dada (58) & Lingkar Perut (78,5) LENGAN PENDEK</option>
                        <option value="XXLLP">XXL : Lingkar Dada (58) & Lingkar Perut (78,5) LENGAN PANJANG</option>
                        <option value="3XL">3XL : Lingkar Dada (60) & Lingkar Perut (81,5) LENGAN PENDEK</option>
                        <option value="3XLLP">3XL : Lingkar Dada (60) & Lingkar Perut (81,5) LENGAN PANJANG</option>
                        <option value="4XL">4XL : Lingkar Dada (62) & Lingkar Perut (84,5) LENGAN PENDEK</option>
                        <option value="4XLLP">4XL : Lingkar Dada (62) & Lingkar Perut (84,5) LENGAN PANJANG</option>
                    `;
                        }

                        checkSubmitState();
                    };

                    // Tambah event listener
                    kepengurusanSelect.addEventListener('change', changeHandler);
                    ukuranBajuSelect.addEventListener('change', checkSubmitState);
                });
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

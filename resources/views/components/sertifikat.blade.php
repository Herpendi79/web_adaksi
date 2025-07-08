@php
use Carbon\Carbon;
Carbon::setLocale('id');
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Sertifikat Kepesertaan</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;600&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            font-family: 'Poppins', sans-serif;
        }

        .card-page {
            width: 297mm;
            height: 210mm;
            position: relative;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            object-fit: cover;
            z-index: 1;
        }

        .no_sertifikat {
            position: absolute;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 10pt;
            font-weight: 600;
            color: #333;
            text-align: center;
            z-index: 2;
            white-space: nowrap;
        }

        .nama {
            position: absolute;
            top: 41%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 28pt;
            font-weight: 600;
            color: #333;
            text-align: center;
            z-index: 2;
            white-space: nowrap;
        }

        .judul {
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18pt;
            color: #555;
            text-align: center;
            z-index: 2;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            width: 80%;
            /* atau gunakan 100% jika ingin penuh */
            max-width: none;
            /* hilangkan batas */
            padding: 0 10px;
            /* opsional, memberi ruang sedikit di pinggir */
            box-sizing: border-box;
            /* agar padding tidak menambah lebar */
        }

        .tanggal_selesai {
            position: absolute;
            top: 68%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14pt;
            font-weight: 600;
            color: #333;
            text-align: center;
            z-index: 2;
            white-space: nowrap;
        }




        .page-break {
            page-break-after: always;
        }
    </style>

</head>

<body>
    {{-- Halaman Depan --}}
    <div class="card-page">
        <img src="{{ asset('uploads/webinar/' . $webinar->sertifikat_depan) }}" class="background">
        <div class="no_sertifikat">Nomor: {{ $pendaftar->no_sertifikat }}</div>
        <div class="nama">{{ $pendaftar->nama }}</div>
        <div class="judul">{{ $webinar->judul }}</div>
        <div class="tanggal_selesai">{{ \Carbon\Carbon::parse($webinar->tanggal_selesai)->translatedFormat('d F Y') }}</div>
    </div>

    <div class="page-break"></div>

    {{-- Halaman Belakang --}}
    <div class="card-page">
        <img src="{{ asset('uploads/webinar/' . $webinar->sertifikat_belakang) }}" class="background">
    </div>
</body>

<script>
    window.print();
</script>

</html>
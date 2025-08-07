<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kartu Anggota</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;600&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: white;
            width: 210mm;
            height: 297mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .card-page {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            padding: 20mm 0;
        }

        .container {
            position: relative;
            width: 52mm;
            height: 84mm;
            margin: 10mm;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            border: 1px solid #9b9b9b;
        }

        .photo {
            position: absolute;
            top: 21%;
            left: 50%;
            transform: translateX(-50%);
            width: 35%;
            object-fit: cover;
            border-radius: 10px;
            z-index: 2;
            border: 3px solid #b1b1b1;
        }

        .nama {
            position: absolute;
            top: 57%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 7pt;
            font-weight: bold;
            color: #6c6c6c;
            text-align: center;
            z-index: 3;
            white-space: nowrap;
        }

        .id_anggota {
            font-family: 'Fira Sans', sans-serif;
            position: absolute;
            top: 62%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 6.5pt;
            color: #6c6c6c;
            text-align: center;
            z-index: 3;
        }

        .tanggal_registrasi {
            position: absolute;
            bottom: 6.7%;
            left: 38%;
            font-size: 4pt;
            color: #ffffff;
            z-index: 3;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-weight: bold;
        }

        .tanggal_berlaku {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-weight: bold;
            position: absolute;
            bottom: 4%;
            left: 12.6%;
            font-size: 5pt;
            color: #ffffff;
            z-index: 3;
            font-size: 4pt;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="card-page">
        {{-- Halaman Depan --}}
        <div class="container">
            <img src="/assets/images/depan.png" class="background">
            <img src="/uploads/foto_anggota/{{ $data['foto'] }}" class="photo">
            <div class="nama">{{ $data['nama_anggota'] }}</div>
            <div class="id_anggota">{{ $data['id_card'] }}</div>
        </div>

        {{-- Halaman Belakang --}}
        <div class="container">
            <img src="/assets/images/belakang.png" class="background">
            <div class="tanggal_registrasi">{{ $data['created_at'] }}</div>
            <div class="tanggal_berlaku">Berlaku hingga : {{ $data['masa_berlaku_sampai'] }}</div>
        </div>
    </div>
</body>

<script>
    window.print();
</script>

</html>

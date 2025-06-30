<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kartu Anggota</title>
    {{-- Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    {{-- Fira Sans --}}
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .photo {
            position: absolute;
            top: 220px;
            left: 50%;
            transform: translateX(-50%);
            width: 220px;
            height: 230px;
            object-fit: cover;
            border-radius: 10px;
            z-index: -1;
            border: 3px solid #fff;
        }

        .nama {
            position: absolute;
            top: 500px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 20pt;
            font-weight: bold;
            color: #6c6c6c;
            text-align: center;
            z-index: 2;
        }

        .id_anggota {
            font-family: 'Fira Sans', sans-serif;
            position: absolute;
            top: 550px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 18pt;
            color: #6c6c6c;
            text-align: center;
            z-index: 2;
        }

        .tanggal_registrasi {
            position: absolute;
            bottom: 50px;
            left: 87px;
            font-size: 12pt;
            color: #ffffff;
            text-align: center;
            z-index: 2;
        }
    </style>
</head>

<body>
    {{-- Halaman Depan --}}
    <div style="position:relative; width:100%; height:100vh; page-break-after: always;">
        <img src="{{ public_path('assets/images/depan.png') }}" class="background"
            style="width:100%; position:absolute; top:0; left:0; z-index:0;">
        <img src="{{ public_path('uploads/foto_anggota/' . $foto) }}" class="photo">
        <div class="nama">{{$nama_anggota ?? "Nama Anggota"}}</div>
        <div class="id_anggota">{{$nip_nipppk ?? "NIP/NIPPPK"}}</div>
    </div>

    {{-- Halaman Belakang --}}
    <div style="position:relative; width:100%; height:100vh;">
        <img src="{{ public_path('assets/images/belakang.png') }}" class="background"
            style="width:100%; position:absolute; top:0; left:0; z-index:0;">
        {{-- Tambahkan konten halaman belakang di sini jika diperlukan --}}
        {{-- Tanggal Registrasi --}}
        <div class="tanggal_registrasi">{{ $created_at ?? "" }}</div>
    </div>
</body>

</html>

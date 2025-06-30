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

        .container {
            position: relative;
            width: 2.051in;
            height: 3.303in;
            margin-bottom: 0.25in;
            /* page-break-after: always; */
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
            left: 49%;
            transform: translateX(-50%);
            width: 35%;
            /* height: 80px; */
            object-fit: cover;
            border-radius: 10px;
            z-index: -1;
            border: 3px solid #b1b1b1;
        }

        .nama {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 7pt;
            font-weight: bold;
            color: #6c6c6c;
            text-align: center;
            z-index: 2;
            white-space: nowrap;
        }

        .id_anggota {
            font-family: 'Fira Sans', sans-serif;
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 6.5pt;
            color: #6c6c6c;
            text-align: center;
            z-index: 2;
        }

        .tanggal_registrasi {
            position: absolute;
            bottom: 4%;
            left: 12%;
            font-size: 5pt;
            color: #ffffff;
            text-align: center;
            z-index: 2;
        }
    </style>
</head>

<body>
    {{-- Halaman Depan --}}
    <div class="container">
        {{-- Gambar Depan --}}
        <img src="{{ public_path('assets/images/depan.png') }}" class="background"
            style="width:100%; position:absolute; top:0; left:0; z-index:0;">
        <img src="{{ public_path('uploads/foto_anggota/' . $foto) }}" class="photo">
        <div class="nama">{{$nama_anggota ?? "Nama Anggota"}}</div>
        <div class="id_anggota">{{$id_card ?? "ID Card"}}</div>
    </div>

    {{-- Halaman Belakang --}}
    <div class="container">
        <img src="{{ public_path('assets/images/belakang.png') }}" class="background"
            style="width:100%; position:absolute; top:0; left:0; z-index:0;">
        {{-- Tambahkan konten halaman belakang di sini jika diperlukan --}}
        {{-- Tanggal Registrasi --}}
        <div class="tanggal_registrasi">{{ $created_at ?? "" }}</div>
    </div>
</body>

</html>
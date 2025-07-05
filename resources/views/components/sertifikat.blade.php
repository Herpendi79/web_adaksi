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

    html, body {
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

    .nama {
        position: absolute;
        top: 55%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 24pt;
        font-weight: 600;
        color: #333;
        text-align: center;
        z-index: 2;
        white-space: nowrap;
    }

    .id_anggota {
        position: absolute;
        top: 65%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 18pt;
        color: #555;
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
        <div class="nama">{{ $webinar->nama }}</div>
        <div class="id_anggota">{{ $webinar->judul }}</div>
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="/assets/images/favicon.ico">

    <title>Beranda</title>

    <!-- Begin Jekyll SEO tag v2.8.0 -->
    <title>Beranda | ADAKSI</title>
    <meta name="generator" content="Jekyll v4.4.1" />
    <meta property="og:title" content="Beranda" />
    <meta name="author" content="adaksi" />
    <meta property="og:locale" content="en_us" />
    <meta property="og:site_name" content="ADAKSI" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary" />
    <meta property="twitter:title" content="Beranda" />
    <!-- Laravel SEO & JSON-LD -->
    <style>
        .pagination {
            flex-direction: row !important;
            display: flex !important;
            justify-content: center;
            gap: 0.5rem;
        }
    </style>
    <script type="application/ld+json">
        {
            !!json_encode([
                "@context" => "https://schema.org",
                "@type" => "WebSite",
                "author" => [
                    "@type" => "Person",
                    "name" => "adaksi"
                ],
                "headline" => "Beranda",
                "name" => "ADAKSI",
                "publisher" => [
                    "@type" => "Organization",
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => asset('assets/images/logo.png')
                    ],
                    "name" => "adaksi"
                ],
                "url" => url('/')
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!
        }
    </script>
    <!-- End Laravel SEO & JSON-LD -->


    <link href="/assets/css/theme.css" rel="stylesheet">

    <!-- custom CSS - Remove this if you don't use it or choose to customize the stylesheet with sass-->
    <link href="/assets/css/custom.css" rel="stylesheet">
    <!-- custom CSS end-->
    <style>
        .video-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: auto;
            padding-top: 56.25%;
            /* 16:9 aspect ratio */
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>


<body class="layout-default">


    <!-- Menu Navigation
================================================== -->
    @include('partials.navigation')

    <!-- Container
================================================== -->
    <main class="container">
        <!-- Home Intro
================================================== -->

        <div class="rounded mb-5 hero">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6 col-sm-12 text-center text-md-left" style="margin-bottom:30px">
                    <h1 class="font-weight-bold mb-4 serif-font">ADAKSI</h1>
                    <p class="lead mb-4">Aliansi Dosen Akademik dan Kevokasian Seluruh Indonesia</p>
                    <div class="d-flex justify-content-center justify-content-md-start">
                        <a href="/about" class="btn btn-white px-5 btn-lg">Tentang Kami</a>
                    </div>

                </div>
                <div class="col-md-6 col-sm-12 text-center text-md-right pl-0 pl-lg-6">
                    <img class="intro" height="250" src="/assets/images/adaksidash.png">
                </div>
            </div>
        </div>

        <!-- Posts List with Sidebar (except featured)
================================================== -->
        <section class="row">
            <div class="col-sm-8">
                <div class="row">
                    @foreach ($webinars as $webinar)
                    <div class="col-md-6 mb-5">
                        <div class="card">
                            <a href="{{ url('/agenda/'. $webinar->id_wb) }}" target="_blank">
                                <div style="position: relative; width: 100%; aspect-ratio: 4 / 5; overflow: hidden;" class="rounded mb-4">
                                    <img
                                        src="{{ asset('uploads/webinar/' . $webinar->flyer) }}"
                                        alt="{{ $webinar->judul }}"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </a>

                            <div class="card-block">
                                <h2 class="card-title h4 serif-font">
                                    <a href="{{ url('/agenda/'. $webinar->id_wb) }}" target="_blank"> {{ $webinar->judul }}</a>
                                </h2>
                                <p class="card-text text-muted">
                                    {!! Str::limit(strip_tags($webinar->deskripsi), 150) !!}
                                </p>
                                <p class="card-text text-muted">
                                    📅 {{ \Carbon\Carbon::parse($webinar->tanggal_mulai)->translatedFormat('l, d F Y') }} <br>
                                    🕘 Pukul {{ $webinar->pukul }} <br>
                                    💻 Via Zoom
                                </p>
                                <div class="metafooter">
                                    <div class="wrapfooter small d-flex align-items-center">
                                        <span class="author-meta">
                                            By <span class="post-name">ADAKSI,</span>
                                            on <span class="post-date">{{ \Carbon\Carbon::parse($webinar->created_at)->format('d M Y') }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div> <!-- End row -->

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <nav>
                        <ul class="pagination">
                            {{ $webinars->links() }}
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="sidebar sticky-top" style="margin-top:-50px">
                    <div class="sidebar-section">
                        <h5><span>Jumlah Anggota</span></h5>
                        <h2 class="font-weight-bold mb-4 text-center">{{ number_format($noUrutTerakhir, 0, ',', '.') }}</h2>
                    </div>
                    <div class="sidebar-section">
                        <h5><span>Agenda</span></h5>
                        <ul class="event-list">
                            @foreach($latestWebinars as $webinar)
                            <li>
                                <span class="event-date">
                                    {{ \Carbon\Carbon::parse($webinar->tanggal_mulai)->translatedFormat('d F Y') }}
                                </span>
                                <span class="event-name">
                                    {{ Str::limit($webinar->judul, 50) }}
                                </span>
                            </li>
                            @endforeach
                        </ul>

                    </div>
                    <div class="sidebar-section">
                        <h5><span>Usulan Kebijakan</span></h5>
                        <ol>
                            <li><a href="#">Kategori Tunjangan Kinerja ASN</a></li>
                        </ol>

                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer
================================================== -->
    @include('partials.footer')

    <!-- JavaScript
================================================== -->
    <script src="/assets-template/libs/jquery/jquery.min.js"></script>
    <script src="/assets-template/libs/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>
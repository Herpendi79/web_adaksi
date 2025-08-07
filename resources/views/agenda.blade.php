<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="/assets/images/favicon.ico">

    <title>{!! Str::limit(strip_tags($agenda->judul), 50) !!}</title>

    <!-- Begin Jekyll SEO tag v2.8.0 -->
    <title>{!! Str::limit(strip_tags($agenda->judul), 50) !!}</title>
    <meta name="generator" content="Jekyll v4.4.1" />
    <meta property="og:title" content="Webinar Pola Pembelajaran dan Riset S3" />
    <meta name="author" content="adaksi" />
    <meta property="og:locale" content="en_us" />
    <meta name="description" content="Webinar Pola Pembelajaran dan Riset S3" />
    <meta property="og:description" content="Webinar Pola Pembelajaran dan Riset S3" />
    <meta property="og:site_name" content="ADAKSI" />
    <meta property="og:image" content="/assets/images/pola-riset.jpeg" />
    <meta property="og:type" content="article" />
    <meta property="article:published_time" content="2025-04-24T00:00:00+00:00" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta property="twitter:image" content="/assets/images/pola-riset.jpeg" />
    <meta property="twitter:title" content="Webinar Pola Pembelajaran dan Riset S3" />
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BlogPosting",
            "author": {
                "@type": "Person",
                "name": "adaksi"
            },
            "dateModified": "2025-04-24T00:00:00+00:00",
            "datePublished": "2025-04-24T00:00:00+00:00",
            "description": "Materi dapat diunduh pada tautan berikut:",
            "headline": "Webinar Pola Pembelajaran dan Riset S3",
            "image": "/assets/images/obe.jpeg",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "/seminar-pembahasan-tukin/"
            },
            "publisher": {
                "@type": "Organization",
                "logo": {
                    "@type": "ImageObject",
                    "url": "/assets/images/logo.png"
                },
                "name": "adaksi"
            },
            "url": "/seminar-pembahasan-tukin/"
        }
    </script>
    <!-- End Jekyll SEO tag -->


    <link href="/assets/css/theme.css" rel="stylesheet">

    <!-- custom CSS - Remove this if you don't use it or choose to customize the stylesheet with sass-->
    <link href="/assets/css/custom.css" rel="stylesheet">
    <!-- custom CSS end-->

</head>

<body class="layout-post">

    <!-- Menu Navigation
================================================== -->
    @include('partials.navigation')

    <!-- Container
================================================== -->
    <main class="container">
        <!-- Begin Article
================================================== -->

        <div class="row">
            <!-- Post -->
            <div class="col-sm-8">
                <!-- Post Featured Image -->
                <img class="featured-image img-fluid rounded" src="{{ asset('uploads/webinar/' . $agenda->flyer) }}" alt="flyer">
                <!-- End Featured Image -->
                <div class="mainheading">
                    <!-- Post Title -->
                    <h1 class="posttitle">{{ $agenda->judul }}</h1>
                </div>
                <!-- Post Content -->
                <div class="article-post serif-font">
                    @if($agenda->hari!='0' && $agenda->pukul!='0')
                    <p>🗓
                        {{ \Carbon\Carbon::parse($agenda->tanggal_mulai)->translatedFormat('l, d F Y') }}

                        @if ($agenda->tanggal_selesai && $agenda->tanggal_selesai > $agenda->tanggal_mulai)
                        - {{ \Carbon\Carbon::parse($agenda->tanggal_selesai)->translatedFormat('l, d F Y') }}
                        @endif
                        🕘 Pukul {{ $agenda->pukul }}
                        📍 Via Zoom Meeting
                    </p>
                    @endif

                    <p>{!! $agenda->deskripsi !!}</p>
                    
                    @if($agenda->hari!='0' && $agenda->pukul!='0')
                    @if ($agenda->fasilitas->count())
                    <p>🎁 Fasilitas Peserta:</p>
                    <ul>
                        @foreach ($agenda->fasilitas as $fasilitas)
                        <li>✅ {{ $fasilitas->nama }}</li>
                        @endforeach
                    </ul>
                    @endif
                    @endif

                    <hr>
                    @if ($agenda->status == 'publish')
                    @if($agenda->hari!='0' && $agenda->pukul!='0')
                    <p>🔗 <strong>Link Registrasi untuk Anggota ADAKSI</strong> :
                        <a href="/login" target="_blank">Klik Disini!</a>
                    </p>
                    <p>🔗 <strong>Link Registrasi untuk NON ADAKSI</strong> :
                        <a href="{{ url('/registrasi/' . $agenda->id_wb) }}" target="_blank">Klik Disini!</a>
                    </p>
                    @endif
                    @else
                    @if($agenda->hari!='0' && $agenda->pukul!='0')
                    <p>🔗 <strong>Link Download Sertifikat untuk NON ADAKSI</strong> :
                        <a href="{{ url('/download/') }}" target="_blank">Klik Disini!</a>
                    </p>
                    @endif
                    @endif
                    <hr>
                    <p>💬 *Yuk gabung di channel WA ADAKSI untuk info lengkap dan update acara!
                        👉 Klik untuk Bergabung ke channel WhatsApp ADAKSI:
                        <a href="https://whatsapp.com/channel/0029VbAU8q47DAX5TaAZhJ30">Klik Disini!</a>
                    </p>


                    <div class="clearfix"></div>
                </div>

                <!-- Post Date -->
                <p>
                    <small>
                        <span class="post-date"><time class="post-date" datetime="2025-04-24"> {{ \Carbon\Carbon::parse($agenda->created_at)->translatedFormat('l, d F Y') }}</time></span>

                    </small>
                </p>
                <!-- Prev/Next -->
                <div class="row PageNavigation mt-4 prevnextlinks d-flex justify-content-between">
                    <div class="col-md-6 text-right pr-0">
                        <a class="thepostlink" href="/">Agenda Lainnya &raquo;</a>
                    </div>
                </div>
                <!-- End Prev/Next -->


                <!-- Author Box -->

                <div class="row post-top-meta">
                    <div class="col-md-2">
                        <img class="author-thumb" src="/assets/images/sal.png" alt="ADAKSI">
                    </div>
                    <div class="col-md-10">
                        <a target="_blank" class="link-dark" href="https://adaksi.org/">ADAKSI</a><a target="_blank" href="https://x.com/tukin_dosenASN?t=eM4uPbjtWvxJDx7FlsIT0A&s=09" class="btn follow">Follow</a>
                        <span class="author-description">-</span>
                    </div>
                </div>
            </div>
            <!-- End Post -->
            <!-- Sidebar -->
            <div class="col-sm-4">
                <div class="sidebar sticky-top" style="margin-top:-50px">
                    <div class="sidebar-section">
                        <h5><span>Jumlah Anggota</span></h5>
                        <h2 class="font-weight-bold mb-4 text-center"> {{ number_format($noUrutTerakhir, 0, ',', '.') }}</h2>
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
        </div>
        <!-- End Article
================================================== -->
    </main>
    <!-- Footer
================================================== -->
    @include('partials.footer')
    <!-- JavaScript
================================================== -->
    <script src="/assets-template/libs/jquery/jquery.min.js"></script>
    <script src="/assets-template/libs/bootstrap/js/bootstrap.min.js"></script>
    <!-- <script src="/assets/js/theme.js"></script>-->
</body>

</html>
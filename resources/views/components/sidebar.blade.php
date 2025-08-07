<ul id="side-menu">
    <li class="menu-title mt-2">Main</li>

    <li>
        <a href="{{ url('admin/dashboard') }}" class="tp-link">
            <i data-feather="home"></i>
            <span> Dashboard </span>
        </a>
    </li>

    @if (Auth::user()->email === 'absen@gmail.com')
        {{-- Menu untuk admin dengan email absen@gmail.com --}}
        <li>
            <a href="{{ url('admin/rakernas') }}" class="tp-link">
                <i data-feather="book-open"></i>
                <span> Rakernas</span>
            </a>
        </li>
    @elseif (Auth::user()->role === 'hukum')
        {{-- Menu untuk role hukum --}}
        <li>
            <a href="{{ url('admin/aduan') }}" class="tp-link">
                <i data-feather="briefcase"></i>
                <span> Aduan</span>
            </a>
        </li>
    @else
        {{-- Menu untuk admin lainnya (bukan hukum dan bukan absen@gmail.com) --}}
        <li>
            <a href="#sidebarDashboards" data-bs-toggle="collapse">
                <i data-feather="users"></i>
                <span> Pengguna </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarDashboards">
                <ul class="nav-second-level">
                    <li>
                        <a href="{{ url('admin/calonanggota') }}" class="tp-link">Calon Anggota</a>
                    </li>
                    <li>
                        <a href="{{ url('admin/anggota') }}" class="tp-link">Anggota</a>
                    </li>
                    <li>
                        <a href="{{ url('admin/tabulasi') }}" class="tp-link">Tabulasi Anggota</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <a href="#sidebarKegiatan" data-bs-toggle="collapse">
                <i data-feather="activity"></i>
                <span> Kegiatan </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarKegiatan">
                <ul class="nav-second-level">
                    <li>
                        <a href="{{ url('admin/webinar') }}" class="tp-link">Webinar</a>
                    </li>
                    <li>
                        <a href="{{ url('admin/rakernas') }}" class="tp-link">Rakernas</a>
                    </li>
                    <li>
                        <a href="#" class="tp-link">Munas</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <a href="#sidebarMaster" data-bs-toggle="collapse">
                <i data-feather="airplay"></i>
                <span> Master </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarMaster">
                <ul class="nav-second-level">
                    <li>
                        <a href="{{ url('admin/rekening') }}" class="tp-link">Rekening</a>
                    </li>
                </ul>
                <ul class="nav-second-level">
                    <li>
                        <a href="{{ url('admin/biaya-anggota') }}" class="tp-link">Biaya Anggota</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <a href="{{ url('admin/setting') }}" class="tp-link">
                <i data-feather="settings"></i>
                <span> Pengaturan </span>
            </a>
        </li>
        <li>
            <a href="{{ url('admin/rekap') }}" class="tp-link">
                <i data-feather="book"></i>
                <span> Rekap </span>
            </a>
        </li>
        <li>
            <a href="{{ url('admin/aduan') }}" class="tp-link">
                <i data-feather="briefcase"></i>
                <span> Aduan</span>
            </a>
        </li>
    @endif
</ul>

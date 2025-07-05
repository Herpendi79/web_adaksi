 <ul id="side-menu">
     <li class="menu-title mt-2">Main</li>

     <li>
         <a href="{{ url('anggota/dashboard') }}" class="tp-link">
             <i data-feather="home"></i>
             <span> Dashboard </span>
         </a>
     </li>
     
     <li class="{{ Request::is('anggota/profile*') ? 'menuitem-active' : '' }}">
         <a href="{{ url('anggota/profile') }}" class="tp-link {{ Request::is('anggota/profile*') ? 'active' : '' }}">
             <i data-feather="user"></i>
             <span> Profil </span>
         </a>
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
                     <a href="{{ url('anggota/webinar') }}" class="tp-link">Webinar</a>
                 </li>
                 <li>
                     <a href="#" class="tp-link">Rakernas</a>
                 </li>
                 <li>
                     <a href="#" class="tp-link">Munas</a>
                 </li>
             </ul>
         </div>
     </li>
 </ul>
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
 </ul>

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="index.html"> <img alt="image" src="/assets/images/logo/logo-elearning.png" class="header-logo" /> <span
            class="logo-name">E-Learning</span>
        </a>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Menu</li>
        <li class="dropdown{{ request()->routeIs('dashboard') ? ' active' : '' }}">
          <a href="{{ route('dashboard') }}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
        </li>
        {{-- <li class="dropdown">
            <a href="index.html" class="nav-link"><i data-feather="user"></i><span>Profile</span></a>
        </li> --}}

        @can('jadwal kuliah')
          <li class="dropdown{{ request()->routeIs('jadwalKuliah') || request()->routeIs('jadwalPengganti') ? ' active' : '' }}">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="calendar"></i><span>Jadwal</span></a>
              <ul class="dropdown-menu">
                  <li><a class="nav-link" href="{{ route('jadwalKuliah') }}">Jadwal Kuliah</a></li>
                <li><a class="nav-link" href="{{ route('jadwalPengganti') }}">Jadwal Pengganti</a></li>
              </ul>
          </li>
        @endcan

          @can('management nilai')          
              <li class="dropdown">
                <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="clipboard"></i><span>Management Nilai</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="icon-material.html">Dosen</a></li>
                  <li><a class="nav-link" href="icon-font-awesome.html">Mahasiswa</a></li>
                </ul>
              </li>
          @endcan

          @can('management roles and permissions')            
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="clipboard"></i><span>Role & Permission</span></a>
              <ul class="dropdown-menu">
                  <li><a class="nav-link" href="icon-material.html">Role</a></li>
                <li><a class="nav-link" href="icon-font-awesome.html">Permission</a></li>
              </ul>
            </li>
          @endcan

          @can('management users')
            <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="users"></i><span>Management Users</span></a>
              <ul class="dropdown-menu">
                  <li><a class="nav-link" href="{{ route('dosen') }}">Dosen</a></li>
                <li><a class="nav-link" href="{{ route('mahasiswa') }}">Mahasiswa</a></li>
              </ul>
            </li>
          @endcan

        {{-- <li class="dropdown">
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <a href="{{ route('logout') }}" class="nav-link"><i data-feather="log-out"></i><span>Logout</span></a>
            </form>
        </li> --}}
        <li class="dropdown">
          <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                             <i data-feather="log-out"></i>
            <span>Logout</span>
          </a>
  
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>

      </ul>
    </aside>
  </div>
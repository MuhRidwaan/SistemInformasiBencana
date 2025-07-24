<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <!-- Pastikan path gambar ini benar di aplikasi Laravel Anda -->
        <img src="{{ asset('dist/img/disasterLogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Disaster Info System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <!-- Pastikan path gambar ini benar di aplikasi Laravel Anda -->
                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <!-- Menampilkan nama pengguna yang sedang login -->
                <a href="#" class="d-block">
                    @if(Auth::check())
                        {{ Auth::user()->nama_lengkap }}
                    @else
                        Guest
                    @endif
                </a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Dashboard -->
                {{-- Dashboard biasanya bisa diakses oleh semua user yang login --}}
                @if (Auth::check())
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @endif

                <!-- Manajemen Pengguna & Hak Akses -->
                {{-- Tampilkan menu utama jika user memiliki setidaknya satu izin di kategori ini --}}
                @if (Auth::check() && (
                    Auth::user()->hasPermissionTo('view-users') ||
                    Auth::user()->hasPermissionTo('create-users') ||
                    Auth::user()->hasPermissionTo('edit-users') ||
                    Auth::user()->hasPermissionTo('delete-users') ||
                    Auth::user()->hasPermissionTo('view-roles') ||
                    Auth::user()->hasPermissionTo('create-roles') ||
                    Auth::user()->hasPermissionTo('edit-roles') ||
                    Auth::user()->hasPermissionTo('delete-roles') ||
                    Auth::user()->hasPermissionTo('view-permissions') ||
                    Auth::user()->hasPermissionTo('create-permissions') ||
                    Auth::user()->hasPermissionTo('edit-permissions') ||
                    Auth::user()->hasPermissionTo('delete-permissions') ||
                    Auth::user()->hasPermissionTo('view-permission-modules') || {{-- Tambahkan izin untuk modul izin --}}
                    Auth::user()->hasPermissionTo('create-permission-modules') ||
                    Auth::user()->hasPermissionTo('edit-permission-modules') ||
                    Auth::user()->hasPermissionTo('delete-permission-modules')
                ))
                <li class="nav-item has-treeview {{ Request::routeIs(['users.*', 'roles.*', 'permissions.*', 'permission-modules.*']) ? 'menu-open' : '' }}"> {{-- Tambahkan 'permission-modules.*' --}}
                    <a href="#" class="nav-link {{ Request::routeIs(['users.*', 'roles.*', 'permissions.*', 'permission-modules.*']) ? 'active' : '' }}"> {{-- Tambahkan 'permission-modules.*' --}}
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            Manajemen Pengguna
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- Manajemen Pengguna --}}
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-users'))
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ Request::routeIs('users.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Pengguna</p>
                            </a>
                        </li>
                        @endif
                        {{-- Manajemen Peran --}}
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-roles'))
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ Request::routeIs('roles.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manajemen Peran</p>
                            </a>
                        </li>
                        @endif
                        {{-- Manajemen Izin --}}
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-permissions'))
                        <li class="nav-item">
                            <a href="{{ route('permissions.index') }}" class="nav-link {{ Request::routeIs('permissions.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manajemen Izin</p>
                            </a>
                        </li>
                        @endif
                        {{-- Manajemen Modul Izin --}}
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-permission-modules'))
                            <li class="nav-item">
                                <a href="{{ route('permission-modules.index') }}" class="nav-link {{ Request::routeIs('permission-modules.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i> {{-- Menggunakan far fa-circle untuk konsistensi sub-menu --}}
                                    <p>Manajemen Modul Izin</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Manajemen Bencana -->
                {{-- Asumsi izin: view-disasters, create-disasters, view-disaster-types, etc. --}}
                @if (Auth::check() && (
                    Auth::user()->hasPermissionTo('view-disasters') ||
                    Auth::user()->hasPermissionTo('view-disaster-types')
                    // ... tambahkan izin lain yang relevan
                ))
                <li class="nav-item has-treeview {{ Request::routeIs(['jenis-bencana.*','bencana.*']) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs(['jenis-bencana.*','bencana.*']) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                        <p>
                            Manajemen Bencana
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-disasters'))
                        <li class="nav-item">
                           <a href="{{ route('bencana.index') }}" class="nav-link {{ Request::routeIs('bencana.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Bencana</p>
                            </a>
                        </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-disaster-types'))
                       <li class="nav-item">
                        <a href="{{ route('jenis-bencana.index') }}" class="nav-link {{ Request::routeIs('jenis-bencana.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Jenis Bencana</p>
                        </a>
                    </li>

                        @endif
                    </ul>
                </li>
                @endif

                <!-- Logistik & Sumber Daya -->
                {{-- Asumsi izin: view-logistics, view-posts, view-volunteers, etc. --}}
                @if (Auth::check() && (
                    Auth::user()->hasPermissionTo('view-logistics') ||
                    Auth::user()->hasPermissionTo('view-posts') ||
                    Auth::user()->hasPermissionTo('view-volunteers')
                    // ... tambahkan izin lain yang relevan
                ))
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>
                            Logistik & Sumber Daya
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-logistics'))
                        <li class="nav-item">
                            <a href="{{ route('kebutuhan-logistik.index') }}" class="nav-link {{ Request::routeIs('logistik.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kebutuhan Logistik</p>
                            </a>
                        </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-posts'))
                        <li class="nav-item">
                            <a href="{{ route('lokasi-posko.index') }}" class="nav-link {{ Request::routeIs('lokasi-posko.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lokasi Posko</p>
                            </a>
                        </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-volunteers'))
                        <li class="nav-item">
                            <a href="{{ route('relawan.index') }}" class="nav-link {{ Request::routeIs('relawan.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Relawan</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Data Referensi Wilayah -->
                {{-- Asumsi izin: view-provinces, view-cities, view-districts, view-villages, etc. --}}
                @if (Auth::check() && (
                    Auth::user()->hasPermissionTo('view-provinces') ||
                    Auth::user()->hasPermissionTo('view-cities') ||
                    Auth::user()->hasPermissionTo('view-districts') ||
                    Auth::user()->hasPermissionTo('view-villages')
                    // ... tambahkan izin lain yang relevan
                ))
                <li class="nav-item has-treeview {{ Request::routeIs(['provinsi.*', 'kota.*', 'kecamatan.*', 'kelurahan.*']) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs(['provinsi.*', 'kota.*', 'kecamatan.*', 'kelurahan.*']) ? 'active' : '' }}">
                        
                        <i class="nav-icon fas fa-globe-asia"></i>
                        <p>
                            Data Referensi Wilayah
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-provinces'))
                        <li class="nav-item">
                        <a href="{{ route('provinsi.index') }}" class="nav-link {{ Request::routeIs('provinsi.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Provinsi</p>
                            </a>
                        </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-cities'))
                        <li class="nav-item">
                        <a href="{{ route('kota.index') }}" class="nav-link {{ Request::routeIs('kota.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Kota/Kabupaten</p>
                            </a>
                        </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-districts'))
                        <li class="nav-item">
                            <a href="{{ route('kecamatan.index') }}" class="nav-link {{ Request::routeIs('kecamatan.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Kecamatan</p>
                            </a>
                        </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermissionTo('view-villages'))
                        <li class="nav-item">
                          <a href="{{ route('kelurahan.index') }}" class="nav-link {{ Request::routeIs('kelurahan.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Kelurahan</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Laporan & Penanganan -->
                {{-- Asumsi izin: view-community-reports, view-damage-data, view-victim-data, view-handling-efforts, etc. --}}
                @if (Auth::check() && (
                    Auth::user()->hasPermissionTo('view-community-reports') ||
                    Auth::user()->hasPermissionTo('view-damage-data') ||
                    Auth::user()->hasPermissionTo('view-victim-data') ||
                    Auth::user()->hasPermissionTo('view-handling-efforts')
                    // ... tambahkan izin lain yang relevan
                ))
                <li class="nav-item has-treeview 
    {{ Request::routeIs([
        'upaya-penanganan.*', 
        'laporan-masyarakat.*', 
        'kerusakan.*', 
        'korban.*'
    ]) ? 'menu-open' : '' }}">
    
    <a href="#" class="nav-link 
        {{ Request::routeIs([
            'upaya-penanganan.*', 
            'laporan-masyarakat.*', 
            'kerusakan.*', 
            'korban.*'
        ]) ? 'active' : '' }}">
        <i class="nav-icon fas fa-clipboard-list"></i>
        <p>
            Laporan & Penanganan
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">
        @if (Auth::check() && Auth::user()->hasPermissionTo('view-community-reports'))
        <li class="nav-item">
            <a href="{{ route('laporan-masyarakat.index') }}" 
               class="nav-link {{ Request::routeIs('laporan-masyarakat.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Laporan Masyarakat</p>
            </a>
        </li>
        @endif

        @if (Auth::check() && Auth::user()->hasPermissionTo('view-damage-data'))
        <li class="nav-item">
            <a href="{{ route('kerusakan.index') }}" 
               class="nav-link {{ Request::routeIs('kerusakan.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Data Kerusakan</p>
            </a>
        </li>
        @endif

        @if (Auth::check() && Auth::user()->hasPermissionTo('view-victim-data'))
        <li class="nav-item">
            <a href="{{ route('korban.index') }}" 
               class="nav-link {{ Request::routeIs('korban.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Data Korban</p>
            </a>
        </li>
        @endif

        @if (Auth::check() && Auth::user()->hasPermissionTo('view-handling-efforts'))
        <li class="nav-item">
            <a href="{{ route('upaya-penanganan.index') }}" 
               class="nav-link {{ Request::routeIs('upaya-penanganan.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Upaya Penanganan</p>
            </a>
        </li>
        @endif
    </ul>
</li>

                @endif

@if (Auth::check())
<li class="nav-item">
    <a href="#" class="nav-link" onclick="confirmLogout(event)">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>

<script>
    function confirmLogout(event) {
        event.preventDefault();
        if (confirm('Yakin ingin logout?')) {
            document.getElementById('logout-form').submit();
        }
    }
</script>
@endif


            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

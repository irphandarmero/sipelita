<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SI-PELITA RS</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0d6efd;
            --sidebar-bg: #1e293b;
            --sidebar-color: #cbd5e1;
            --sidebar-active: #3b82f6;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Topbar Navbar */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            height: 65px;
            z-index: 1030;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-color);
            transition: all 0.3s ease-in-out;
            z-index: 1040;
            overflow-y: auto;
        }

        #sidebar.collapsed {
            left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-brand {
            height: 65px;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            background-color: #0f172a;
            border-bottom: 1px solid #334155;
        }

        .sidebar-brand-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .sidebar-section {
            padding: 0.75rem 1.25rem 0.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.8px;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.7rem 1.25rem;
            color: var(--sidebar-color);
            text-decoration: none;
            font-size: 0.925rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .nav-link-custom:hover {
            background-color: #334155;
            color: #ffffff;
        }

        .nav-link-custom.active {
            background-color: #334155;
            color: #ffffff;
            border-left-color: var(--sidebar-active);
        }

        .nav-link-custom i {
            font-size: 1.15rem;
            margin-right: 0.8rem;
            width: 22px;
            text-align: center;
        }

        /* Main Content Wrapper */
        #content-wrapper {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #content-wrapper.expanded {
            margin-left: 0;
        }

        .main-content {
            padding: 1.75rem;
            flex: 1;
        }

        .badge-role {
            font-size: 0.725rem;
            padding: 0.35em 0.65em;
            text-transform: uppercase;
        }

        .footer-custom {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.75rem;
            font-size: 0.85rem;
            color: #64748b;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show-mobile {
                left: 0;
            }
            #content-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <aside id="sidebar">
        <!-- Brand Header -->
        <div class="sidebar-brand">
            <i class="bi bi-hospital fs-3 text-primary me-2"></i>
            <div class="sidebar-brand-text">
                SI-PELITA
                <small class="d-block text-muted fw-normal" style="font-size: 0.65rem;">Sistem Pelaporan RS</small>
            </div>
        </div>

        <!-- User Info Brief -->
        <div class="p-3 border-bottom border-secondary border-opacity-25 bg-dark bg-opacity-25">
            <div class="d-flex align-items-center">
                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; font-weight: 600;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-white text-truncate fw-semibold" style="font-size: 0.875rem;">
                        {{ Auth::user()->name ?? 'Pengguna RS' }}
                    </div>
                    <small class="text-info d-block text-truncate" style="font-size: 0.75rem;">
                        <i class="bi bi-building me-1"></i>{{ Auth::user()->unitKerja->nama_unit ?? 'Unit Kerja' }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="mt-2 mb-4">
            <div class="sidebar-section">Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill text-primary"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-section">Pelaporan</div>
            <a href="{{ route('laporan.create') }}" class="nav-link-custom {{ request()->routeIs('laporan.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill text-success"></i>
                <span>Buat Laporan Baru</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="nav-link-custom {{ request()->routeIs('laporan.index') && !request('view_type') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill text-info"></i>
                <span>Laporan Saya</span>
            </a>

            <!-- Laporan Masuk khusus Unit Penanggung Jawab (Petugas/Admin) -->
            @if(in_array(Auth::user()->role ?? '', ['admin', 'petugas', 'kepala_unit']))
            <a href="{{ route('laporan.index', ['view_type' => 'incoming']) }}" class="nav-link-custom {{ request('view_type') == 'incoming' ? 'active' : '' }}">
                <i class="bi bi-inbox-fill text-warning"></i>
                <span>Laporan Masuk Unit</span>
                @if(isset($unhandledCount) && $unhandledCount > 0)
                    <span class="badge bg-danger rounded-pill ms-auto" style="font-size: 0.7rem;">{{ $unhandledCount }}</span>
                @endif
            </a>
            @endif

            <a href="{{ route('laporan.index', ['jenis' => 'Rutin']) }}" class="nav-link-custom {{ request('jenis') == 'Rutin' ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line-fill text-purple"></i>
                <span>Rekap Routine / Sensus</span>
            </a>

            <!-- Menu Master Data khusus Admin -->
            @if((Auth::user()->role ?? '') === 'admin')
                <div class="sidebar-section">Administrator</div>
                <a href="#masterSubmenu" data-bs-toggle="collapse" class="nav-link-custom d-flex justify-content-between align-items-center {{ request()->is('master*') ? 'active' : '' }}">
                    <div>
                        <i class="bi bi-gear-wide-connected text-secondary"></i>
                        <span>Master Data</span>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
                </a>
                <div class="collapse {{ request()->is('master*') ? 'show' : '' }}" id="masterSubmenu" style="background-color: #0f172a;">
                    <a href="{{ route('master.unit-kerja.index') }}" class="nav-link-custom ps-4 fs-7">
                        <i class="bi bi-diagram-3 me-2"></i>Unit Kerja RS
                    </a>
                    <a href="{{ route('master.master-laporan.index') }}" class="nav-link-custom ps-4 fs-7">
                        <i class="bi bi-journal-bookmark me-2"></i>Kategori Laporan
                    </a>
                    <a href="{{ route('master.users.index') }}" class="nav-link-custom ps-4 fs-7">
                        <i class="bi bi-people me-2"></i>Manajemen Users
                    </a>
                </div>
            @endif
        </nav>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div id="content-wrapper">

        <!-- TOPBAR NAVBAR -->
        <header class="navbar navbar-expand navbar-custom sticky-top px-3">
            <button class="btn btn-light border-0 me-3" id="sidebarToggle" type="button">
                <i class="bi bi-list fs-4"></i>
            </button>

            <!-- Current Location Badge -->
            <div class="d-none d-md-flex align-items-center me-auto">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                    <i class="bi bi-hospital me-1"></i>{{ Auth::user()->unitKerja->nama_unit ?? 'RS SIMRS' }}
                </span>
            </div>

            <!-- Right Topbar Items -->
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark fw-medium d-flex align-items-center py-0" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end me-2 d-none d-sm-block">
                            <div class="fw-bold" style="font-size: 0.875rem;">{{ Auth::user()->name ?? 'User' }}</div>
                            <span class="badge bg-secondary badge-role">
                                {{ strtoupper(Auth::user()->role ?? 'USER') }}
                            </span>
                        </div>
                        <i class="bi bi-person-circle fs-3 text-secondary"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userDropdown">
                        <li class="px-3 py-2 border-bottom d-sm-none">
                            <div class="fw-bold">{{ Auth::user()->name ?? 'User' }}</div>
                            <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="#">
                                <i class="bi bi-person me-2 text-primary"></i>Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger fw-semibold">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="main-content">
            <!-- Flash Message Alert -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="footer-custom d-flex flex-column flex-sm-row justify-content-between align-items-center">
            <div>
                <strong>SI-PELITA</strong> &copy; {{ date('Y') }} - Sistem Pelaporan & Informasi Terpadu Antar-Unit RS.
            </div>
            <div class="mt-2 mt-sm-0 text-muted">
                Versi 1.0.0
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle Sidebar Navigasi
        const sidebar = document.getElementById('sidebar');
        const contentWrapper = document.getElementById('content-wrapper');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show-mobile');
            } else {
                sidebar.classList.toggle('collapsed');
                contentWrapper.classList.toggle('expanded');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
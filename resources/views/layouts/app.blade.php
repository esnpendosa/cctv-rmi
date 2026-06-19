<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CCTV RMI') }} - Dashboard</title>

    <!-- Google Fonts: Lexend Deca -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Hope UI Theme CSS -->
    <link href="{{ asset('css/hope-ui.css') }}" rel="stylesheet">

    <!-- Leaflet JS -->
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @livewireStyles
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebarMenu">
            <div class="sidebar-brand">
                <div style="width:42px;height:42px;background:var(--color-brand);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-camera-video-fill" style="font-size:1.25rem;color:#fff;"></i>
                </div>
                <span class="sidebar-brand-name">CCTV RMI</span>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ Request::routeIs('monitor.wall') ? 'active' : '' }}">
                    <a href="{{ route('monitor.wall') }}" target="_blank">
                        <i class="bi bi-display"></i>
                        <span>Monitor</span>
                    </a>
                </li>
                
                @can('camera.view')
                <li class="sidebar-menu-item {{ Request::routeIs('cameras*') ? 'active' : '' }}">
                    <a href="{{ route('cameras.index') }}">
                        <i class="bi bi-camera-video"></i>
                        <span>Kamera CCTV</span>
                    </a>
                </li>
                @endcan

                <li class="sidebar-menu-item {{ Request::routeIs('clients*') ? 'active' : '' }}">
                    <a href="{{ route('clients.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Klien & Lokasi</span>
                    </a>
                </li>

                @can('inventory.view')
                <li class="sidebar-menu-item {{ Request::routeIs('inventories*') ? 'active' : '' }}">
                    <a href="{{ route('inventories.index') }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Inventaris</span>
                    </a>
                </li>
                @endcan

                @can('invoice.view')
                <li class="sidebar-menu-item {{ Request::routeIs('quotations*') ? 'active' : '' }}">
                    <a href="{{ route('quotations.index') }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Penawaran</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Request::routeIs('invoices*') ? 'active' : '' }}">
                    <a href="{{ route('invoices.index') }}">
                        <i class="bi bi-receipt"></i>
                        <span>Invoice</span>
                    </a>
                </li>
                @endcan

                <li class="sidebar-menu-item {{ Request::routeIs('reports*') ? 'active' : '' }}">
                    <a href="{{ route('reports.index') }}">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Laporan</span>
                    </a>
                </li>

                @can('settings.view')
                <li class="sidebar-menu-item {{ Request::routeIs('settings*') ? 'active' : '' }}">
                    <a href="{{ route('settings.index') }}">
                        <i class="bi bi-gear"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
                @endcan
            </ul>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-content flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar-custom">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link p-0 d-lg-none" type="button" id="sidebarToggle" aria-label="Toggle Sidebar" style="color:var(--color-text-dark);">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div>
                        <div style="font-size:var(--font-size-xs);color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">CCTV RMI</div>
                        <div style="font-size:var(--font-size-md);font-weight:700;color:var(--color-text-dark);line-height:1.2;">Sistem Manajemen CCTV & Keuangan</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications Dropdown -->
                    @livewire('notification-dropdown')

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle d-flex align-items-center gap-2 p-0 text-decoration-none" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color:var(--color-text-dark);">
                            <div class="d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;border-radius:50%;background:var(--color-brand);font-size:13px;flex-shrink:0;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="d-none d-sm-flex flex-column text-start" style="line-height:1.2;">
                                <span style="font-size:var(--font-size-sm);font-weight:700;color:var(--color-text-dark);">{{ Auth::user()->name ?? 'User' }}</span>
                                <span style="font-size:10px;color:var(--color-text-secondary);">{{ Auth::user()->roles->first()->name ?? 'User' }}</span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-2 border-0" aria-labelledby="profileDropdown" style="border-radius: var(--radius-md);">
                            <li><span class="dropdown-header">Peran: {{ Auth::user()->roles->pluck('name')->implode(', ') ?? 'N/A' }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content Body -->
            <main class="content-body">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: var(--radius-sm);">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: var(--radius-sm);">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebarMenu')?.classList.toggle('show');
        });
    </script>
    @livewireScripts
</body>
</html>

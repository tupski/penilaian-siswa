<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Penilaian Siswa - SMART Method</title>

    <!-- Bootstrap 4 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            font-size: 14px;
            background: #f5f7fa;
        }

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            min-height: 100vh;
        }

        /* Sidebar Styles - Fix tinggi dan posisi logout */
        .sidebar {
            width: 250px;
            min-width: 250px;
            background: linear-gradient(135deg, #2d3e50 0%, #1a2a3a 100%);
            color: #e8edf2;
            transition: all 0.3s ease-in-out;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
        }

        /* Desktop: sidebar selalu terbuka */
        @media (min-width: 769px) {
            .sidebar {
                margin-left: 0 !important;
            }
            .sidebar.active {
                margin-left: -250px !important;
            }
        }

        /* Mobile: sidebar tertutup default */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
        }

        .sidebar .sidebar-header {
            padding: 20px 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }

        .sidebar .sidebar-header h5 {
            margin: 0;
            color: #e8edf2;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .sidebar .sidebar-header small {
            color: #9aaebf;
            font-size: 11px;
        }

        /* Nav menu - scrollable area */
        .sidebar-nav {
            flex: 1;
            padding: 10px 15px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom scrollbar untuk nav menu */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 12px 15px;
            margin: 4px 0;
            border-radius: 10px;
            transition: all 0.3s;
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #ffffff;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            border-left: 3px solid #7ab2d6;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            font-size: 0.9rem;
        }

        /* Logout button - FIXED di bagian bawah sidebar */
        .sidebar-footer {
            flex-shrink: 0;
            padding: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
        }

        .logout-btn {
            width: 100%;
        }

        .logout-btn button {
            background: rgba(248, 113, 113, 0.15);
            border: 1px solid rgba(248, 113, 113, 0.3);
            cursor: pointer;
            width: 100%;
            text-align: left;
            padding: 12px 15px;
            border-radius: 10px;
            color: #f87171 !important;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .logout-btn button:hover {
            background: rgba(248, 113, 113, 0.3);
            border-color: #f87171;
            transform: translateX(5px);
        }

        .logout-btn button i {
            margin-right: 12px;
            width: 20px;
            color: #f87171;
        }

        .content {
            flex: 1;
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            margin-left: 250px;
        }

        /* Desktop */
        @media (min-width: 769px) {
            .content {
                margin-left: 250px;
            }
            .content.active {
                margin-left: 0;
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .content {
                margin-left: 0 !important;
                width: 100% !important;
            }
            .content.active {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        .main-content {
            background: #f0f4f8;
            min-height: calc(100vh - 60px);
            padding: 20px !important;
        }

        .navbar-top {
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 1001;
            border-bottom: 1px solid #e9ecef;
        }

        .navbar-top h5 {
            font-size: 0.95rem;
            margin-bottom: 0;
            color: #2c3e50;
            font-weight: 500;
        }

        #sidebarCollapse {
            background: #eef2f7;
            border: 1px solid #e2e8f0;
            color: #4a6fa5;
            border-radius: 10px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        #sidebarCollapse:hover {
            background: #e2e8f0;
            transform: scale(1.02);
            color: #2c3e50;
        }

        .card-stats {
            border-radius: 16px;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            background: #ffffff;
        }

        .card-stats:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .card-stats .card-body {
            padding: 1.25rem !important;
        }

        .card-stats h6 {
            font-size: 0.7rem;
            margin-bottom: 0;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .card-stats h2 {
            font-size: 1.8rem;
            margin-bottom: 0;
            font-weight: 700;
        }

        .card-stats i {
            font-size: 2rem;
            opacity: 0.8;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
            cursor: pointer;
        }

        .overlay.active {
            display: block;
        }

        @media (min-width: 769px) {
            .overlay {
                display: none !important;
            }
        }

        .btn-primary {
            background: #4a6fa5;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #3a5a8c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74,111,165,0.3);
        }

        .btn-warning {
            background: #f5b042;
            border: none;
            border-radius: 10px;
            padding: 6px 16px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
        }

        .btn-warning:hover {
            background: #e5a032;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #94a3b8;
            border: none;
            border-radius: 10px;
            padding: 6px 16px;
            font-size: 0.8rem;
        }

        .alert {
            border-radius: 12px;
            animation: slideDown 0.4s ease-out;
            padding: 0.85rem 1.25rem;
            font-size: 0.85rem;
            border: none;
        }

        .alert-success {
            background: #e6f7ec;
            color: #1e6f3f;
        }

        .alert-danger {
            background: #ffe8e8;
            color: #a03a3a;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control {
            font-size: 0.85rem;
            padding: 0.6rem 0.9rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #4a6fa5;
            box-shadow: 0 0 0 3px rgba(74,111,165,0.15);
            outline: none;
        }

        .form-group label {
            font-size: 0.8rem;
            margin-bottom: 0.35rem;
            font-weight: 500;
            color: #334155;
        }

        .modal-content {
            border-radius: 16px;
            border: none;
        }

        .modal-header {
            padding: 1rem 1.25rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .badge-primary {
            background: #e8f0fe;
            color: #1e3a5f;
            padding: 0.35rem 0.75rem;
            font-size: 0.7rem;
            font-weight: 500;
            border-radius: 20px;
        }

        .badge-success {
            background: #e6f7ec;
            color: #1e6f3f;
        }

        .text-success {
            color: #2d6a4f !important;
        }

        .text-warning {
            color: #b45309 !important;
        }

        .text-danger {
            color: #c0392b !important;
        }

        .table-responsive-custom {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px !important;
            }

            .table-responsive {
                font-size: 12px;
            }

            .btn-sm {
                padding: 4px 10px;
                font-size: 10px;
            }

            .card-body {
                padding: 15px;
            }

            .navbar-top h5 {
                font-size: 12px;
            }

            .card-stats h2 {
                font-size: 1.3rem;
            }

            .card-stats i {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 1200px) {
            .main-content {
                padding: 25px !important;
            }

            .card-stats h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="overlay"></div>

        <!-- Sidebar dengan struktur flex column -->
        <div class="sidebar">
            <!-- Header - tetap di atas -->
            <div class="sidebar-header">
                <h5><i class="fas fa-trophy"></i> SPK SMART</h5>
                <small>MIN 3 Tangerang</small>
            </div>

  <div class="sidebar-nav">
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('alternatif.*') ? 'active' : '' }}" href="{{ route('alternatif.index') }}">
            <i class="fas fa-users"></i> Data Siswa
        </a>

        {{-- MENU ABSENSI - SEMUA USER BISA AKSES --}}
        <a class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}" href="{{ route('absensi.index') }}">
            <i class="fas fa-calendar-check"></i> Absensi
        </a>

        {{-- MENU KRITERIA - SEMUA USER BISA LIHAT --}}
        <a class="nav-link {{ request()->routeIs('kriteria.*') ? 'active' : '' }}" href="{{ route('kriteria.index') }}">
            <i class="fas fa-list"></i> Kriteria
        </a>

        <a class="nav-link {{ request()->routeIs('penilaian.*') ? 'active' : '' }}" href="{{ route('penilaian.index') }}">
            <i class="fas fa-star"></i> Penilaian
        </a>
        <a class="nav-link {{ request()->routeIs('rangking.*') ? 'active' : '' }}" href="{{ route('rangking.index') }}">
            <i class="fas fa-trophy"></i> Rangking
        </a>
        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
            <i class="fas fa-file-pdf"></i> Laporan
        </a>
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
            <i class="fas fa-user-cog"></i> Pengaturan
        </a>
    </nav>
</div>

            <!-- Footer dengan Logout - SELALU TERLIHAT DI BAWAH -->
            <div class="sidebar-footer">
                <div class="logout-btn">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content">
            <nav class="navbar-top d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 ml-3">Selamat Datang, {{ auth()->user()->name ?? 'Guest' }}</h5>
                </div>
                <div class="d-flex align-items-center">
                <span class="badge badge-primary mr-3">
                    {{ auth()->user() && auth()->user()->role == 'admin' ? 'Administrator' : 'Guru' }}
                </span>
                <i class="fas fa-user-circle fa-2x text-secondary" style="color: #94a3b8 !important;"></i>
            </div>
            </nav>

            <div class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            function isMobile() {
                return $(window).width() <= 768;
            }

            function updateOverlay() {
                var isSidebarOpen = $('.sidebar').hasClass('active');
                var mobile = isMobile();

                if (mobile && isSidebarOpen) {
                    $('.overlay').addClass('active');
                } else {
                    $('.overlay').removeClass('active');
                }
            }

            $('#sidebarCollapse').on('click', function() {
                $('.sidebar').toggleClass('active');
                $('.content').toggleClass('active');

                if ($('.sidebar').hasClass('active')) {
                    localStorage.setItem('sidebarState', 'closed');
                } else {
                    localStorage.setItem('sidebarState', 'open');
                }

                updateOverlay();
            });

            function loadSavedState() {
                var savedState = localStorage.getItem('sidebarState');
                var isDesktop = !isMobile();

                if (isDesktop) {
                    if (savedState === 'closed') {
                        $('.sidebar').addClass('active');
                        $('.content').addClass('active');
                    } else {
                        $('.sidebar').removeClass('active');
                        $('.content').removeClass('active');
                    }
                } else {
                    if (savedState === 'closed') {
                        $('.sidebar').addClass('active');
                        $('.content').addClass('active');
                    } else {
                        $('.sidebar').removeClass('active');
                        $('.content').removeClass('active');
                    }
                }

                updateOverlay();
            }

            $('.overlay').on('click', function() {
                if (isMobile()) {
                    $('.sidebar').removeClass('active');
                    $('.content').removeClass('active');
                    localStorage.setItem('sidebarState', 'closed');
                    updateOverlay();
                }
            });

            function handleResize() {
                var wasMobile = isMobile();
                var nowMobile = $(window).width() <= 768;

                if (wasMobile !== nowMobile) {
                    if (nowMobile) {
                        $('.sidebar').addClass('active');
                        $('.content').addClass('active');
                        localStorage.setItem('sidebarState', 'closed');
                    } else {
                        $('.sidebar').removeClass('active');
                        $('.content').removeClass('active');
                        localStorage.setItem('sidebarState', 'open');
                    }
                }

                updateOverlay();
            }

            $(window).on('resize', function() {
                handleResize();
            });

            $(document).on('keydown', function(e) {
                if (e.ctrlKey && (e.key === 'b' || e.key === 'B')) {
                    e.preventDefault();
                    $('#sidebarCollapse').click();
                }
            });

            loadSavedState();

            if ($('.datatable').length) {
                $('.datatable').each(function() {
                    if (!$.fn.DataTable.isDataTable($(this))) {
                        $(this).DataTable({
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                            },
                            "pageLength": 10,
                            "responsive": true,
                            "autoWidth": false
                        });
                    }
                });
            }

            setTimeout(function() {
                $(".alert").fadeOut("slow", function() {
                    $(this).remove();
                });
            }, 4000);

            if ($('[data-toggle="tooltip"]').length) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'CashFlow Tracker')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('assets/img/icon.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Lato:300,400,700,900"] },
            custom: {
                families: [
                    "Flaticon",
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/atlantis.min.css') }}" />

    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        <div class="main-header">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="blue">
                <a href="{{ route('dashboard') }}" class="logo">
                    <span class="navbar-brand text-white font-weight-bold">CashFlow Tracker</span>
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <i class="icon-menu"></i>
                    </span>
                </button>
                <button class="topbar-toggler more">
                    <i class="icon-options-vertical"></i>
                </button>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="icon-menu"></i>
                    </button>
                </div>
            </div>
            <!-- End Logo Header -->

            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
                <div class="container-fluid">
                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <img src="{{ asset('assets/img/profile.jpg') }}" alt="..." class="avatar-img rounded-circle" />
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <div class="dropdown-user-scroll scrollbar-outer">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg">
                                                <img src="{{ asset('assets/img/profile.jpg') }}" alt="image profile" class="avatar-img rounded" />
                                            </div>
                                            <div class="u-text">
                                                <h4>{{ auth()->user()->name }}</h4>
                                                <p class="text-muted">{{ auth()->user()->email }}</p>
                                                <p class="text-muted"><span class="badge badge-success">{{ auth()->user()->getRoleNames()->first() }}</span></p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </div>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-style-2">
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-primary">
                        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Menu</h4>
                        </li>

                        <li class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                            <a href="{{ route('transactions.index') }}">
                                <i class="fas fa-exchange-alt"></i>
                                <p>Transaksi</p>
                            </a>
                        </li>

                        @role('admin|manager')
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Master Data</h4>
                        </li>

                        <li class="nav-item {{ request()->routeIs('transaction-groups.*') ? 'active' : '' }}">
                            <a href="{{ route('transaction-groups.index') }}">
                                <i class="fas fa-layer-group"></i>
                                <p>Grup Transaksi</p>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <a href="{{ route('categories.index') }}">
                                <i class="fas fa-tags"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('wallet-groups.*') ? 'active' : '' }}">
                            <a href="{{ route('wallet-groups.index') }}">
                                <i class="fas fa-wallet"></i>
                                <p>Grup Dompet</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('wallets.*') ? 'active' : '' }}">
                            <a href="{{ route('wallets.index') }}">
                                <i class="fas fa-wallet"></i>
                                <p>Dompet</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('wallet-transfers.*') ? 'active' : '' }}">
                            <a href="{{ route('wallet-transfers.index') }}">
                                <i class="fas fa-random"></i>
                                <p>Transfer Dompet</p>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('members.*') ? 'active' : '' }}">
                            <a href="{{ route('members.index') }}">
                                <i class="fas fa-user-friends"></i>
                                <p>Anggota</p>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('trash.*') ? 'active' : '' }}">
                            <a href="{{ route('trash.index') }}">
                                <i class="fas fa-trash"></i>
                                <p>Tempat Sampah</p>
                            </a>
                        </li>
                        @endrole

                        @role('admin')
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Administrasi</h4>
                        </li>

                        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i>
                                <p>Manajemen User</p>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}">
                                <i class="fas fa-user-tag"></i>
                                <p>Kelola Role</p>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                            <a href="{{ route('permissions.index') }}">
                                <i class="fas fa-shield-alt"></i>
                                <p>Kelola Permission</p>
                            </a>
                        </li>
                        @endrole
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <!-- Main Panel -->
        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright ml-auto">
                        {{ date('Y') }}, CashFlow Tracker
                    </div>
                </div>
            </footer>
        </div>
        <!-- End Main Panel -->
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery UI -->
    <script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Atlantis JS -->
    <script src="{{ asset('assets/js/atlantis.min.js') }}"></script>

    <!-- Custom JS - Format Rupiah -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @stack('scripts')
</body>
</html>

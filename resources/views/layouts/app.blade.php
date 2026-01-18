<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - Inventory Warehouse</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Figtree', sans-serif;
                background-color: #f8f9fa;
            }

            .main-content {
                padding: 2rem;
            }

            .sidebar {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 1rem 0;
            }

            .sidebar .nav-link {
                color: rgba(255,255,255,0.8);
                padding: 0.75rem 1.5rem;
                margin: 0.25rem 0;
                transition: all 0.3s ease;
            }

            .sidebar .nav-link:hover {
                color: white;
                background-color: rgba(255,255,255,0.1);
            }

            .sidebar .nav-link.active {
                color: white;
                background-color: rgba(255,255,255,0.2);
                border-left: 3px solid white;
            }

            .sidebar .nav-link i {
                margin-right: 0.5rem;
                width: 1.25rem;
            }

            .navbar-brand {
                font-weight: 700;
                font-size: 1.25rem;
                color: white;
            }

            .topbar {
                background: white;
                padding: 1rem 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 2rem;
            }

            .stat-card {
                border: none;
                border-left: 4px solid #0d6efd;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }

            .stat-card h2 {
                color: #0d6efd;
                font-size: 2rem;
                font-weight: bold;
            }

            .stat-card.suppliers {
                border-left-color: #198754;
            }

            .stat-card.suppliers h2 {
                color: #198754;
            }

            .stat-card.locations {
                border-left-color: #fd7e14;
            }

            .stat-card.locations h2 {
                color: #fd7e14;
            }

            .stat-card.value {
                border-left-color: #6f42c1;
            }

            .stat-card.value h2 {
                color: #6f42c1;
            }

            .card {
                border: none;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 1.5rem;
            }

            .card-header {
                background-color: white;
                border-bottom: 1px solid #e9ecef;
                padding: 1.25rem;
            }

            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row g-0">
                <!-- Sidebar -->
                <nav class="col-md-2 sidebar d-none d-md-block">
                    <div class="navbar-brand p-3 text-white mb-3">
                        <i class="bi bi-box-seam"></i> Inventory
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="bi bi-house"></i> Dashboard
                            </a>
                        </li>

                        <!-- Master Data Section -->
                        <li class="nav-item mt-3">
                            <span class="nav-link text-white" style="cursor: default; opacity: 0.6;">
                                <small>MASTER DATA</small>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" 
                               href="{{ route('suppliers.index') }}">
                                <i class="bi bi-shop"></i> Supplier
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}" 
                               href="{{ route('items.index') }}">
                                <i class="bi bi-box"></i> Item
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" 
                               href="{{ route('locations.index') }}">
                                <i class="bi bi-geo-alt"></i> Lokasi
                            </a>
                        </li>

                        <!-- Transaction Section -->
                        <li class="nav-item mt-3">
                            <span class="nav-link text-white" style="cursor: default; opacity: 0.6;">
                                <small>TRANSAKSI</small>
                            </span>
                        </li>

                        @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}" 
                               href="{{ route('purchase-orders.index') }}">
                                <i class="bi bi-arrow-down-circle"></i> Barang Masuk (PO)
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}" 
                               href="{{ route('sales-orders.index') }}">
                                <i class="bi bi-arrow-up-circle"></i> Barang Keluar (SO)
                            </a>
                        </li>

                        <!-- Admin Section -->
                        @if(Auth::user()->isAdmin())
                        <li class="nav-item mt-3">
                            <span class="nav-link text-white" style="cursor: default; opacity: 0.6;">
                                <small>ADMIN</small>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                               href="{{ route('users.index') }}">
                                <i class="bi bi-people"></i> User Management
                            </a>
                        </li>
                        @endif
                    </ul>

                    <!-- User Profile at Bottom -->
                    <div class="position-absolute bottom-0 w-100 p-3" style="border-top: 1px solid rgba(255,255,255,0.1);">
                        <div class="text-white text-center">
                            <p class="mb-2" style="font-size: 0.875rem;">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </p>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-10 ms-md-auto">
                    <!-- Top Navigation -->
                    <nav class="navbar navbar-expand-lg navbar-light topbar">
                        <div class="container-fluid">
                            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#navbarNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <div class="ms-auto">
                                    <span class="me-3">{{ Auth::user()->name }}</span>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Page Content -->
                    <div class="main-content">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

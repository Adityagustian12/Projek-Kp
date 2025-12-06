<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kos-Kosan H.Kastim System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            transition: transform 0.3s ease;
        }
        .status-badge {
            font-size: 0.8rem;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
        }
        .main-content {
            min-height: calc(100vh - 56px);
        }
        /* Pastikan teks user di navbar terlihat jelas di background biru */
        .navbar.bg-primary .nav-link,
        .navbar.bg-primary .navbar-brand {
            color: #fff !important;
        }
        .navbar.bg-primary .nav-link:hover,
        .navbar.bg-primary .nav-link:focus {
            color: #e6e6e6 !important;
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 56px;
                left: -100%;
                width: 250px;
                height: calc(100vh - 56px);
                z-index: 1000;
                transition: left 0.3s ease;
                overflow-y: auto;
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                width: 100%;
                padding: 1rem !important;
            }
            .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            .card-body {
                padding: 1rem !important;
            }
            h2 {
                font-size: 1.5rem !important;
            }
            .badge.fs-6 {
                font-size: 0.75rem !important;
            }
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 0.5rem;
            }
            .d-flex.justify-content-between.align-items-center {
                align-items: flex-start !important;
            }
            /* Overlay untuk mobile sidebar */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 56px;
                left: 0;
                width: 100%;
                height: calc(100vh - 56px);
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
        }
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: inline-block;
                margin-right: 1rem;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            @if(request()->routeIs('public.home'))
                {{-- Hide mobile menu button on home page --}}
                <span class="navbar-brand">
                    <i class="fas fa-home me-2"></i>Kos-Kosan H.Kastim
                </span>
            @else
                {{-- Show mobile menu button on other pages --}}
                <button class="btn btn-link text-white mobile-menu-btn" type="button" id="mobileMenuBtn" onclick="toggleSidebar()">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <span class="navbar-brand">
                    <i class="fas fa-home me-2"></i>Kos-Kosan H.Kastim
                </span>
            @endif
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-warning" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Masuk
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile (only show on pages with sidebar) -->
    @if(!request()->routeIs('public.home'))
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- CSRF Token Setup -->
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Mobile sidebar toggle (only on pages with sidebar)
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const menuBtn = document.getElementById('mobileMenuBtn');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Only handle sidebar on pages that have it (not home page)
            if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('show') && menuBtn) {
                if (!sidebar.contains(event.target) && !menuBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                    if (overlay) overlay.classList.remove('show');
                }
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
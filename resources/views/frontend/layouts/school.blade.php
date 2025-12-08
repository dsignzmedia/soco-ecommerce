<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Dashboard | Soco Uniforms')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #490d59;
            --primary-light: #f7f2fb;
            --accent: #f97316;
            --bg: #f6f4ef;
            --sidebar: #ffffff;
            --card: #ffffff;
            --border: #e5e7eb;
            --text: #475467;
            --heading: #111827;
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }
        a { color: inherit; text-decoration: none; }

        /* Layout Grid */
        .layout { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar { 
            background: var(--sidebar); 
            padding: 32px 24px; 
            border-right: 1px solid rgba(15, 23, 42, 0.05); 
            height: 100vh; 
            position: sticky; 
            top: 0; 
            display: flex; 
            flex-direction: column; 
            z-index: 10;
        }
        
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .brand h2 { font-size: 20px; font-weight: 700; color: var(--primary); margin: 0; }
        
        .school-nav { display: flex; flex-direction: column; gap: 8px; }
        .school-nav__item { 
            padding: 12px 16px; 
            border-radius: 12px; 
            font-weight: 500; 
            color: var(--text); 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            transition: all 0.2s ease; 
            font-size: 14px; 
        }
        .school-nav__item:hover, .school-nav__item.active { 
            background: var(--primary); 
            color: white; 
            text-decoration: none;
        }
        .school-nav__item i { width: 20px; text-align: center; }

        /* Main Content Styles */
        .main-content { padding: 0; display: flex; flex-direction: column; min-width: 0; }
        .header-wrapper { background: var(--bg); position: sticky; top: 0; z-index: 9; border-bottom: 1px solid var(--border); }
        .content-area { padding: 32px 40px; flex: 1; overflow-y: auto; }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { display: none; } 
            .content-area { padding: 20px; }
        }
        
        /* Bootstrap Overrides / Utilities */
        .h-100 { height: 100% !important; }
        .rounded-4 { border-radius: 1rem !important; }
        .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; }
        .border-0 { border: 0 !important; }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                @if(Auth::user()->school && Auth::user()->school->logo)
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('storage/' . Auth::user()->school->logo) }}" alt="{{ Auth::user()->school->name }}" style="max-height: 40px; max-width: 40px; object-fit: contain;">
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--primary); margin: 0;">{{ Auth::user()->school->name }}</h2>
                    </div>
                @else
                    <i class="fas fa-school fa-lg" style="color: var(--primary);"></i>
                    <h2>School Portal</h2>
                @endif
            </div>

            <nav class="school-nav">
                <a href="{{ route('frontend.school.dashboard') }}" class="school-nav__item {{ Request::routeIs('frontend.school.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('frontend.school.orders') }}" class="school-nav__item {{ Request::routeIs('frontend.school.orders') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i> Orders Management
                </a>
                <a href="{{ route('frontend.school.students') }}" class="school-nav__item {{ Request::routeIs('frontend.school.students') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Student Management
                </a>
                <a href="{{ route('frontend.school.products') }}" class="school-nav__item {{ Request::routeIs('frontend.school.products') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Product Catalog
                </a>
                <a href="{{ route('frontend.school.reports') }}" class="school-nav__item {{ Request::routeIs('frontend.school.reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Reports & Analytics
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header-wrapper">
                @include('frontend.partials.school-header')
            </div>
            
            <div class="content-area">
                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    @stack('scripts')
</body>
</html>

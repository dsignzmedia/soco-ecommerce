<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Master Admin Portal | The Skool Store')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
        }

        a {
            color: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        button,
        input[type="submit"],
        input[type="reset"],
        input[type="button"] {
            cursor: pointer;
        }

        .layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: var(--sidebar);
            padding: 32px 24px;
            border-right: 1px solid rgba(15, 23, 42, 0.05);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 36px;
        }

        .brand img {
            width: 140px;
        }

        .brand small {
            color: var(--text);
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav__item {
            padding: 12px 14px;
            border-radius: 12px;
            font-weight: 500;
            color: #0f172a;
            display: flex;
            align-items: center;
            font-size: 14px !important;
            gap: 10px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav__item.active {
            background: var(--primary-light);
            color: var(--primary);
            font-size: 14px !important;
        }

        .content {
            padding: 32px 40px;
        }

        label {
            display: flex;
            flex-direction: column;
            font-size: 13px;
            color: var(--text);
            gap: 6px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 14px;
            color: var(--heading);
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .profile-chip {
            position: relative;
            padding: 10px 14px;
            border-radius: 999px;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            box-shadow: 0 5px 20px rgba(15, 23, 42, 0.08);
            cursor: pointer;
            user-select: none;
        }

        .profile-chip:hover {
            box-shadow: 0 5px 25px rgba(15, 23, 42, 0.12);
        }

        .profile-chip span {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 600;
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.15);
            min-width: 180px;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
            z-index: 1000;
        }

        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .profile-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: var(--text);
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .profile-dropdown a:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .profile-dropdown a:first-child {
            border-bottom: 1px solid var(--border);
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(15, 23, 42, 0.05);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        }

        @media (max-width: 960px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: none;
                border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            }

            .content {
                padding: 24px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }

        /* Pagination Fixes */
        nav[role="navigation"] {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        nav[role="navigation"] svg {
            width: 16px;
            height: 16px;
        }
        nav[role="navigation"] > div:first-child {
            display: none; /* Hide the 'Showing X to Y' text on mobile if needed, or just style it */
        }
        @media (min-width: 640px) {
            nav[role="navigation"] > div:first-child {
                display: block;
            }
        }
        nav[role="navigation"] a, 
        nav[role="navigation"] span[aria-current="page"] {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            margin-left: -1px;
            color: #475467;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        nav[role="navigation"] span[aria-current="page"] {
            background-color: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        nav[role="navigation"] span[aria-disabled="true"] {
            color: #98a2b3;
            cursor: not-allowed;
        }

        /* Custom Global Button Style - Outline Purple Pill */
        .btn-vs-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #490d59 !important; /* Purple Text */
            background-color: transparent; /* Transparent BG */
            border: 1.5px solid #490d59; /* Purple Border */
            border-radius: 9999px;
            transition: all 0.3s ease;
            text-decoration: none;
            line-height: normal;
            white-space: nowrap;
            margin-bottom: 8px;
        }
        .btn-vs-sm:hover {
            background-color: #490d59;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(73, 13, 89, 0.2);
        }
        .btn-vs-sm i { font-size: 12px; }

        /* Custom Back Button Style - Outline with Dash effect preference or plain stroke */
        .btn-back-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border: 1.5px solid #d0d5dd;
            border-radius: 8px;
            background: #fff;
            color: #344054;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-back-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('assets/img/logo.svg') }}" alt="The Skool Store logo">
                <small>Master Admin Portal</small>
            </div>
            <nav class="nav">
                @php($navItems = [
                    ['label' => 'Dashboard', 'route' => 'master.admin.dashboard', 'icon' => 'fas fa-th-large'],
                    ['label' => 'School Management', 'route' => 'master.admin.schools.index', 'icon' => 'fas fa-school'],
                    ['label' => 'Orders', 'route' => 'master.admin.orders.index', 'icon' => 'fas fa-shopping-bag'],
                    ['label' => 'Products & Catalog', 'route' => 'master.admin.catalog.index', 'icon' => 'fas fa-box-open'],
                    ['label' => 'Inventory', 'route' => 'master.admin.inventory.dashboard', 'icon' => 'fas fa-warehouse'],
                    ['label' => 'Returns & Exchanges', 'route' => 'master.admin.returns-exchange.index', 'icon' => 'fas fa-exchange-alt'],
                    ['label' => 'Shipping', 'route' => 'master.admin.shipping.edit', 'icon' => 'fas fa-shipping-fast'],
                    ['label' => 'Reports', 'route' => 'master.admin.reports.index', 'icon' => 'fas fa-chart-bar'],
                    ['label' => 'System Settings', 'route' => 'master.admin.settings.index', 'icon' => 'fas fa-cog'],
                    ['label' => 'Audit Logs', 'route' => 'master.admin.settings.audit-logs', 'icon' => 'fas fa-clipboard-list'],
                ])
                @foreach($navItems as $item)
                    <a class="nav__item {{ ($item['route'] === 'master.admin.dashboard' ? request()->routeIs($item['route']) : (request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*'))) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                        <i class="{{ $item['icon'] }}" style="width: 18px; text-align: center;"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>
        <main class="content">
            <div class="topbar">
                <div class="topbar__title">
                    <h2 style="margin:0;color:var(--heading);font-size:24px;">
                        @yield('page_heading', 'Master Admin Portal')
                    </h2>
                    <p style="margin:4px 0 0;color:var(--text);">
                        @yield('page_subheading', 'Full access • Manage. Monitor. Master.')
                    </p>
                </div>
                <div class="profile-chip" id="profileChip">
                    <span>A</span>
                    Admin
                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="{{ route('master.admin.profile') }}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 8C10.2091 8 12 6.20914 12 4C12 1.79086 10.2091 0 8 0C5.79086 0 4 1.79086 4 4C4 6.20914 5.79086 8 8 8Z" fill="currentColor"/>
                                <path d="M8 10C4.68629 10 2 12.6863 2 16H14C14 12.6863 11.3137 10 8 10Z" fill="currentColor"/>
                            </svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('master.admin.logout') }}" style="margin:0;">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 14H3C2.46957 14 1.96086 13.7893 1.58579 13.4142C1.21071 13.0391 1 12.5304 1 12V4C1 3.46957 1.21071 2.96086 1.58579 2.58579C1.96086 2.21071 2.46957 2 3 2H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M11 11L15 8L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15 8H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Logout
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            @if(session('status'))
                <div class="card" style="margin-bottom:16px;background:#ecfdf3;color:#027a48;">
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <div class="card" style="margin-bottom:16px;background:#fef3f2;color:#b42318;">
                    <ul style="margin:0;padding-left:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('scripts')
    
    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:32px; max-width:400px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div style="width:64px; height:64px; background:#fef3f2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fas fa-sign-out-alt" style="font-size:24px; color:#b42318;"></i>
            </div>
            <h3 style="margin:0 0 8px; color:#111827; font-size:20px;">Leaving Dashboard?</h3>
            <p style="margin:0 0 24px; color:#475467; font-size:14px;">Are you sure you want to logout? You will need to login again to access the admin panel.</p>
            <div style="display:flex; gap:12px; justify-content:center;">
                <button id="cancelLogout" style="padding:12px 24px; border-radius:10px; border:1px solid #d0d5dd; background:#fff; color:#344054; font-weight:600; cursor:pointer; transition:all 0.2s;">
                    Stay Here
                </button>
                <button id="confirmLogout" style="padding:12px 24px; border-radius:10px; border:none; background:#b42318; color:#fff; font-weight:600; cursor:pointer; transition:all 0.2s;">
                    Yes, Logout
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileChip = document.getElementById('profileChip');
            const profileDropdown = document.getElementById('profileDropdown');

            if (profileChip && profileDropdown) {
                // Toggle dropdown on click
                profileChip.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('active');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!profileChip.contains(e.target)) {
                        profileDropdown.classList.remove('active');
                    }
                });

                // Close dropdown on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        profileDropdown.classList.remove('active');
                    }
                });
            }

            // ========================================
            // Back Button Logout Confirmation Logic
            // ========================================
            const logoutModal = document.getElementById('logoutModal');
            const cancelLogoutBtn = document.getElementById('cancelLogout');
            const confirmLogoutBtn = document.getElementById('confirmLogout');
            
            // Push a state to history so we can detect back button
            history.pushState(null, null, location.href);
            
            // Handle back button press
            window.addEventListener('popstate', function(event) {
                // Show the logout confirmation modal
                logoutModal.style.display = 'flex';
                // Push state again to prevent immediate navigation
                history.pushState(null, null, location.href);
            });
            
            // Cancel logout - hide modal and stay on page
            if (cancelLogoutBtn) {
                cancelLogoutBtn.addEventListener('click', function() {
                    logoutModal.style.display = 'none';
                });
            }
            
            // Confirm logout - submit the logout form
            if (confirmLogoutBtn) {
                confirmLogoutBtn.addEventListener('click', function() {
                    // Find the logout form and submit it
                    const logoutForm = document.querySelector('form[action*="logout"]');
                    if (logoutForm) {
                        logoutForm.submit();
                    } else {
                        // Fallback: redirect to login
                        window.location.href = '{{ route("master.admin.login") }}';
                    }
                });
            }
            
            // Close modal when clicking outside
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    logoutModal.style.display = 'none';
                }
            });
            
            // Close modal on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && logoutModal.style.display === 'flex') {
                    logoutModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>


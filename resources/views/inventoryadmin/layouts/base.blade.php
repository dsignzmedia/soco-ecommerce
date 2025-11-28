<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventory Admin Portal | The Skool Store')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --accent: #4f46e5;
            --surface: #f5f7fb;
            --card: #ffffff;
            --border: rgba(15, 23, 42, 0.08);
            --text: #475467;
            --heading: #0f172a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--surface);
            color: var(--text);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 28px 24px;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .brand {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .brand img {
            width: 120px;
        }

        .brand small {
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav__item {
            padding: 11px 12px;
            border-radius: 12px;
            font-weight: 500;
            color: var(--heading);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav__item.active {
            background: rgba(79, 70, 229, 0.08);
            color: var(--accent);
        }

        .content {
            padding: 32px 40px;
        }

        .card {
            background: var(--card);
            border-radius: 18px;
            padding: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .profile-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border-radius: 999px;
            padding: 8px 16px;
            border: 1px solid var(--border);
            font-weight: 600;
            cursor: pointer;
            position: relative;
        }

        .profile-chip span {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #4f46e5;
            color: #fff;
            font-weight: 700;
        }

        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            padding: 8px;
            min-width: 180px;
            display: none;
            z-index: 10;
        }

        .profile-dropdown.active {
            display: block;
        }

        .profile-dropdown a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--heading);
        }

        .profile-dropdown a:hover {
            background: rgba(79, 70, 229, 0.06);
            color: #4f46e5;
        }

        @media (max-width: 960px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .content {
                padding: 24px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('assets/img/logo.svg') }}" alt="The Skool Store">
                <small>Inventory Admin</small>
            </div>
            <nav class="nav">
                <a class="nav__item {{ request()->routeIs('inventory.admin.dashboard') ? 'active' : '' }}" href="{{ route('inventory.admin.dashboard') }}">
                    Dashboard
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.orders.*') && !request()->routeIs('inventory.admin.orders.shipping') ? 'active' : '' }}" href="{{ route('inventory.admin.orders.index') }}">
                    Orders
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.orders.shipping') ? 'active' : '' }}" href="{{ route('inventory.admin.orders.shipping') }}">
                    Shipping
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.inventory.*') ? 'active' : '' }}" href="{{ route('inventory.admin.inventory.index') }}">
                    Inventory
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.returns-exchange.index') ? 'active' : '' }}" href="{{ route('inventory.admin.returns-exchange.index') }}">
                    Returns & Exchanges
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.reports.*') ? 'active' : '' }}" href="{{ route('inventory.admin.reports.index') }}">
                    Reports
                </a>
            </nav>
        </aside>
        <main class="content">
            <div class="topbar">
                <div>
                    <h1 style="margin:0;font-size:24px;color:var(--heading);">
                        @yield('page_heading', 'Inventory Admin Portal')
                    </h1>
                    <p style="margin:4px 0 0;color:#94a3b8;">
                        @yield('page_subheading', 'Focused tools for stock teams')
                    </p>
                </div>
                <div class="profile-chip" id="profileChip">
                    <span>A</span>
                    Admin
                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="{{ route('inventory.admin.profile') }}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 8C10.2091 8 12 6.20914 12 4C12 1.79086 10.2091 0 8 0C5.79086 0 4 1.79086 4 4C4 6.20914 5.79086 8 8 8Z" fill="currentColor"/>
                                <path d="M8 10C4.68629 10 2 12.6863 2 16H14C14 12.6863 11.3137 10 8 10Z" fill="currentColor"/>
                            </svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('inventory.admin.logout') }}" style="margin:0;">
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

            @yield('content')
        </main>
    </div>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileChip = document.getElementById('profileChip');
            const profileDropdown = document.getElementById('profileDropdown');

            if (profileChip && profileDropdown) {
                profileChip.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('active');
                });

                document.addEventListener('click', function(e) {
                    if (!profileChip.contains(e.target)) {
                        profileDropdown.classList.remove('active');
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        profileDropdown.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>


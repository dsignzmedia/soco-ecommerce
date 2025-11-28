<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Master Admin Portal | The Skool Store')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        }

        a {
            color: inherit;
            text-decoration: none;
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
            gap: 10px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav__item.active {
            background: var(--primary-light);
            color: var(--primary);
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
                    ['label' => 'Dashboard', 'route' => 'master.admin.dashboard'],
                    ['label' => 'Orders', 'route' => 'master.admin.orders.index'],
                    ['label' => 'School Management', 'route' => 'master.admin.schools.index'],
                    ['label' => 'Products & Catalog', 'route' => 'master.admin.catalog.index'],
                    ['label' => 'Inventory', 'route' => 'master.admin.inventory.dashboard'],
                    ['label' => 'Returns & Exchanges', 'route' => 'master.admin.returns-exchange.index'],
                    ['label' => 'Shipping', 'route' => 'master.admin.shipping.edit'],
                    ['label' => 'Reports', 'route' => 'master.admin.reports.index'],
                    ['label' => 'System Settings', 'route' => 'master.admin.settings.index'],
                    ['label' => 'Audit Logs', 'route' => 'master.admin.settings.audit-logs'],
                ])
                @foreach($navItems as $item)
                    <a class="nav__item {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}">
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
        });
    </script>
</body>
</html>


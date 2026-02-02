
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Master Admin Portal | The Skool Store')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/soco_logo/favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tom Select UI -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Custom Theme to match Admin UI */
        .ts-control {
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            padding: 10px 14px !important;
            font-family: inherit !important;
            font-size: 14px !important;
            color: var(--heading) !important;
            background-color: #fff !important;
            box-shadow: none !important;
            min-height: 46px; /* Match standard input height */
            display: flex;
            align-items: center;
        }
        .ts-control:focus, .ts-control.focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1) !important;
        }
        .ts-control .item {
            display: flex;
            align-items: center;
        }
        .ts-dropdown {
            border: 1px solid rgba(15, 23, 42, 0.05) !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
            padding: 6px !important;
            margin-top: 8px !important;
            z-index: 9999 !important;
        }
        /* Hide placeholder/empty value option from the list */
        .ts-dropdown .option[data-value=""] {
            display: none !important;
        }
        .ts-dropdown-content {
            max-height: 250px !important; /* Adjusted height */
            overflow-y: auto !important;
        }
        /* Scrollbar styles for better UI */
        .ts-dropdown-content::-webkit-scrollbar {
            width: 6px;
        }
        .ts-dropdown-content::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .ts-dropdown-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .ts-dropdown-content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .ts-dropdown .option {
            border-radius: 8px !important;
            padding: 10px 14px !important;
            margin-bottom: 2px !important;
        }
        .ts-dropdown .active {
            background-color: var(--primary-light) !important;
            color: var(--primary) !important;
            font-weight: 500 !important;
        }
        .ts-dropdown .option.selected {
            background-color: var(--primary) !important;
            color: #fff !important;
        }
        /* User request: "after selecting an option no cursor should come" */
        /* Completely hide input cursor and text when an item is selected */
        .ts-wrapper.has-items .ts-control input {
            opacity: 0 !important;
            position: absolute !important;
            z-index: -1 !important;
            width: 1px !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            caret-color: transparent !important; /* Key to hiding blinking cursor */
            color: transparent !important;
            pointer-events: none !important;
            left: -10000px !important;
        }

        /* Ensure the selected item takes full layout width */
        .ts-wrapper.single.has-items .ts-control .item {
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 20px; /* Space for the 'x' remove button if present */
        }
        
        /* If user clicks to focus, we still keep it visually hidden but functional for blind typing (standard select behavior) */
        .ts-wrapper.has-items.focus .ts-control input {
            /* Still keep it hidden visually */
            opacity: 0 !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            transition: grid-template-columns 0.3s ease;
        }

        .sidebar {
            background: var(--sidebar);
            padding: 32px 14px;
            border-right: 1px solid rgba(15, 23, 42, 0.05);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            width: 100%;
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 36px;
            transition: all 0.3s ease;
        }

        .brand img {
            width: 160px;
            transition: all 0.3s ease;
        }

        .brand small {
            color: var(--text);
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            transition: all 0.3s ease;
            opacity: 1;
            max-height: 30px;
            overflow: hidden;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav__item-wrapper {
            position: relative;
        }
        .nav__submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding-left: 24px;
            padding-top: 0;
            padding-bottom: 0;
            background: #f9fafb;
            border-left: 2px solid #e5e7eb;
            margin-left: 12px;
        }
        .nav__submenu.open {
            max-height: 500px;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .nav__subitem {
            /* padding-left: 32px !important; */
            font-size: 13px;
        }
        .nav__chevron {
            margin-left: auto;
            font-size: 11px;
            transition: transform 0.2s;
        }
        .nav__item-wrapper.open .nav__chevron {
            transform: rotate(180deg);
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
            transition: all 0.3s ease;
        }

        .nav__item.active {
            background: #490d59;
            color: #ffffff;
            font-size: 14px !important;
        }

        .content {
            padding: 0;
            display: flex;
            flex-direction: column;
            min-width: 0;
            transition: all 0.3s ease;
        }

        .content-body {
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
            background-color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
            padding-right: 40px; /* Space for arrow */
            cursor: pointer;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg);
            padding: 24px 40px;
            /* No negative margins needed */
            margin: 0; 
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
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

        /* Tablet Responsive Styles (768px - 1024px) */
        @media (min-width: 768px) and (max-width: 1024px) {
            .layout {
                grid-template-columns: 220px 1fr;
            }

            .sidebar {
                padding: 24px 12px;
            }

            .brand img {
                width: 120px;
            }

            .brand small {
                font-size: 10px;
            }

            .nav__item {
                padding: 10px 12px;
                font-size: 13px !important;
            }

            .nav__item i {
                font-size: 16px;
            }

            .content-body {
                padding: 24px 28px;
            }

            .topbar {
                padding: 20px 28px;
            }

            .topbar__title h2 {
                font-size: 20px !important;
            }

            .topbar__title p {
                font-size: 13px;
            }

            .card {
                padding: 20px;
            }
        }

        /* Mobile Responsive Styles */
        @media (max-width: 767px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: none;
                border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            }

            .content-body {
                padding: 20px 16px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                padding: 16px 20px;
                margin: 0;
                position: sticky;
                top: 0;
                z-index: 999;
                border-bottom: 1px solid rgba(15, 23, 42, 0.05);
            }

            .topbar__title h2 {
                font-size: 18px !important;
            }

            .card {
                padding: 16px;
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
            /* padding: 6px 12px; */
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

        /* Collapsed Sidebar Styles */
        .layout.collapsed {
            grid-template-columns: 80px 1fr;
        }
        
        .layout.collapsed .sidebar .brand {
            align-items: center;
            padding: 0;
            margin-bottom: 24px;
        }
        
        .layout.collapsed .sidebar .brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .layout.collapsed .sidebar .brand small {
            opacity: 0;
            max-height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .layout.collapsed .sidebar .nav__item {
            justify-content: center;
            padding: 12px;
            position: relative;
        }
        .layout.collapsed .sidebar .nav__item a {       
            justify-content: center;
            padding: 12px;
            position: relative;
        }
        .layout.collapsed .sidebar .nav__item span {
            opacity: 0;
            width: 0;
            overflow: hidden;
            transition: opacity 0.2s ease, width 0.3s ease;
        }
        
        .layout.collapsed .sidebar .nav__chevron {
            display: none !important;
        }
        
        .sidebar .nav__item span {
            transition: opacity 0.2s ease, width 0.3s ease;
            opacity: 1;
            white-space: nowrap;
        }
        
        .sidebar .nav__chevron {
            transition: opacity 0.2s ease;
            opacity: 1;
        }
        
        .layout.collapsed .sidebar .nav__item i {
            margin: 0;
            font-size: 18px;
        }
        
        .sidebar .nav__item i {
            transition: margin 0.3s ease, font-size 0.3s ease;
        }

        /* Show tooltip/text on hover for collapsed state could be added here, 
           but for now sticking to the requested "icons alone" */
        
        .sidebar-toggle {
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            color: var(--text);
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-toggle:hover {
            background: rgba(0,0,0,0.05);
            color: var(--heading);
        }
        
        .layout.collapsed .nav__submenu {
            display: none !important;
        }

    </style>
    @stack('styles')
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('assets/img/new logo/new_logo.png') }}" alt="The Skool Store logo">
                <small>Master Admin Portal</small>
            </div>
            <nav class="nav">
                @php
                    $navItems = [
                        ['label' => 'Dashboard', 'route' => 'master.admin.dashboard', 'icon' => 'fas fa-th-large'],
                        ['label' => 'School Management', 'route' => 'master.admin.schools.index', 'active' => 'master.admin.schools.*', 'icon' => 'fas fa-school'],
                        ['label' => 'Orders', 'route' => 'master.admin.orders.index', 'active' => 'master.admin.orders.*', 'icon' => 'fas fa-shopping-bag'],
                        [
                            'label' => 'Product', 
                            'route' => 'master.admin.catalog.index', 
                            'active' => 'master.admin.catalog.*', 
                            'icon' => 'fas fa-box-open',
                            'submenu' => [
                                ['label' => 'Catalog', 'route' => 'master.admin.catalog.index', 'active' => 'master.admin.catalog.*', 'icon' => 'fas fa-book'],
                                ['label' => 'Settings', 'route' => 'master.admin.product-settings.index', 'active' => ['master.admin.product-settings.*', 'master.admin.product-types.*', 'master.admin.categories.*'], 'icon' => 'fas fa-cog']
                            ]
                        ],
                        ['label' => 'Payments', 'route' => 'master.admin.payments.index', 'active' => 'master.admin.payments.*', 'icon' => 'fas fa-credit-card'],
                        ['label' => 'Inventory', 'route' => 'master.admin.inventory.dashboard', 'active' => 'master.admin.inventory.*', 'icon' => 'fas fa-warehouse'],
                        ['label' => 'Exchanges', 'route' => 'master.admin.returns-exchange.index', 'active' => 'master.admin.returns-exchange.*', 'icon' => 'fas fa-exchange-alt'],
                        ['label' => 'Shipping', 'route' => 'master.admin.shipping.edit', 'active' => 'master.admin.shipping.*', 'icon' => 'fas fa-shipping-fast'],
                        ['label' => 'Reports', 'route' => 'master.admin.reports.index', 'active' => 'master.admin.reports.*', 'icon' => 'fas fa-chart-bar'],
                        ['label' => 'System Settings', 'route' => 'master.admin.settings.index', 'active' => [
                            'master.admin.settings.index',
                            'master.admin.settings.payment-gateways*',
                            'master.admin.settings.invoice-templates*',
                            'master.admin.settings.email-templates*',
                            'master.admin.settings.sms-templates*',
                            'master.admin.settings.exchange-template*',
                            'master.admin.settings.app-branding*',
                            'master.admin.settings.backups*'
                        ], 'icon' => 'fas fa-cog'],
                        ['label' => 'Audit Logs', 'route' => 'master.admin.settings.audit-logs', 'active' => 'master.admin.settings.audit-logs', 'icon' => 'fas fa-clipboard-list'],
                    ];
                @endphp
                @foreach($navItems as $item)
                    @if(isset($item['submenu']) && !empty($item['submenu']))
                        <div class="nav__item-wrapper" style="position:relative;">
                            <div class="nav__item {{ (isset($item['active']) ? request()->routeIs($item['active']) : request()->routeIs($item['route'])) ? 'active' : '' }}" style="display:flex;align-items:center;gap:10px;">
                                <a href="{{ route($item['route']) }}" style="flex:1;display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
                                    <i class="{{ $item['icon'] }}" style="width: 18px; text-align: center;"></i>
                                    <span>{{ $item['label'] }}</span>

                                </a>
                                <i class="fas fa-chevron-down nav__chevron" onclick="toggleSubmenu(event, this.closest('.nav__item-wrapper'));" style="font-size:11px;transition:transform 0.2s;cursor:pointer;padding:4px;flex-shrink:0;"></i>
                            </div>
                            <div class="nav__submenu">
                                @foreach($item['submenu'] as $subItem)
                                    <a class="nav__item nav__subitem {{ (isset($subItem['active']) ? request()->routeIs($subItem['active']) : request()->routeIs($subItem['route'])) ? 'active' : '' }}" href="{{ route($subItem['route']) }}">
                                        <i class="{{ $subItem['icon'] ?? 'fas fa-circle' }}" style="width: 18px; text-align: center;font-size:13px;"></i>
                                        <span>{{ $subItem['label'] }}</span>

                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a class="nav__item {{ (isset($item['active']) ? request()->routeIs($item['active']) : request()->routeIs($item['route'])) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                            <i class="{{ $item['icon'] }}" style="width: 18px; text-align: center;"></i>
                            <span>{{ $item['label'] }}</span>

                        </a>
                    @endif
                @endforeach
            </nav>
        </aside>
        <main class="content">
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:16px;">
                    <div class="sidebar-toggle" onclick="toggleSidebar()" style="font-size:20px;cursor:pointer;color:var(--text);border-right:1px solid var(--border);">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div class="topbar__title">
                        <h2 style="margin:0;color:var(--heading);font-size:24px;">
                            @yield('page_heading', 'Master Admin Portal')
                        </h2>
                        <p style="margin:4px 0 0;color:var(--text);">
                            @yield('page_subheading', 'Full access • Manage. Monitor. Master.')
                        </p>
                    </div>
                </div>
                @php
                    $unreadNotifications = collect();
                    $unreadCount = 0;
                    try {
                        $unreadNotifications = \App\Models\Notification::whereNull('read_at')
                                                ->where(function($q) {
                                                    $q->where('target_role', 'master')
                                                      ->orWhereNull('target_role');
                                                })
                                                ->orderByDesc('created_at')
                                                ->take(5)
                                                ->get();
                        $unreadCount = \App\Models\Notification::whereNull('read_at')
                                        ->where(function($q) {
                                            $q->where('target_role', 'master')
                                              ->orWhereNull('target_role');
                                        })
                                        ->count();
                    } catch (\Exception $e) {
                        // DB might not be ready or other issue
                        \Illuminate\Support\Facades\Log::error('Notification fetch failed: ' . $e->getMessage());
                    }
                @endphp
                <div style="display:flex;align-items:center;margin-left:auto;gap:16px;">
                    <div class="notification-wrapper" style="position:relative;">
                        <button id="notificationBtn" style="background:none;border:none;position:relative;padding:8px;cursor:pointer;">
                            <i class="fas fa-bell" style="font-size:20px;color:var(--text);"></i>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span style="position:absolute;top:0;right:0;background:#ef4444;color:white;font-size:10px;padding:2px 5px;border-radius:99px;font-weight:700;border:2px solid var(--bg);">{{ $unreadCount }}</span>
                            @endif
                        </button>
                        <div class="profile-dropdown" id="notificationDropdown" style="width:300px;right:-50px;">
                            <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-weight:600;color:var(--heading);">
                                Notifications
                            </div>
                            <div style="max-height:300px;overflow-y:auto;">
                                @forelse($unreadNotifications ?? [] as $notif)
                                    <a href="{{ route('master.admin.notifications.read', $notif->id) }}" style="display:flex;text-decoration:none;color:inherit;padding:12px 16px;border-bottom:1px solid #f3f4f6;align-items:start;gap:12px;transition:background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                                        <div style="flex:1;">
                                            <p style="margin:0 0 4px;font-weight:600;font-size:13px;color:#374151;">{{ $notif->title }}</p>
                                            <p style="margin:0;font-size:12px;color:#6b7280;line-height:1.4;">{{ Str::limit($notif->message, 60) }}</p>
                                            <small style="margin-top:4px;display:block;font-size:11px;color:#9ca3af;">{{ $notif->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div style="color:#10b981;font-size:12px;padding-top:2px;">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </a>
                                @empty
                                    <div style="padding:24px;text-align:center;color:#6b7280;font-size:13px;">
                                        No new notifications
                                    </div>
                                @endforelse
                            </div>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <form action="{{ route('master.admin.notifications.read-all') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="display:block;width:100%;text-align:center;padding:10px;font-size:12px;color:var(--primary);font-weight:600;border:none;border-top:1px solid var(--border);background:none;cursor:pointer;">
                                        Mark all as read
                                    </button>
                                </form>
                            @endif
                        </div>
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
            </div>

            <div class="content-body">
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
            </div>
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
        // Submenu toggle functionality (available globally)
        function toggleSubmenu(event, wrapper) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            if (!wrapper) return;
            
            const submenu = wrapper.querySelector('.nav__submenu');
            if (!submenu) return;
            
            const isOpen = wrapper.classList.contains('open');
            
            // Close all other submenus
            document.querySelectorAll('.nav__item-wrapper').forEach(w => {
                if (w !== wrapper) {
                    w.classList.remove('open');
                    const sm = w.querySelector('.nav__submenu');
                    if (sm) sm.classList.remove('open');
                }
            });
            
            // Toggle current submenu
            if (isOpen) {
                wrapper.classList.remove('open');
                submenu.classList.remove('open');
            } else {
                wrapper.classList.add('open');
                submenu.classList.add('open');
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-open submenu if current route matches any submenu item or parent route
            document.querySelectorAll('.nav__item-wrapper').forEach(wrapper => {
                const submenu = wrapper.querySelector('.nav__submenu');
                if (!submenu) return;
                
                const activeSubItem = submenu.querySelector('.nav__subitem.active');
                const parentItem = wrapper.querySelector('.nav__item');
                const isParentActive = parentItem && parentItem.classList.contains('active');
                
                // Open if any submenu item is active OR if parent is active
                if (activeSubItem || isParentActive) {
                    wrapper.classList.add('open');
                    submenu.classList.add('open');
                }
            });

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

            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationBtn && notificationDropdown) {
                 notificationBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('active');
                    profileDropdown.classList.remove('active'); // Close profile if open
                });
                document.addEventListener('click', function(e) {
                    if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationDropdown.classList.remove('active');
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

            // Restore sidebar state
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed) {
                document.querySelector('.layout').classList.add('collapsed');
            }
        
            window.toggleSidebar = function() {
                const layout = document.querySelector('.layout');
                layout.classList.toggle('collapsed');
                const isCollapsed = layout.classList.contains('collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed);
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

            // ========================================
            // Tom Select Initialization
            // ========================================
            document.querySelectorAll('select:not(.no-tom)').forEach((el) => {
                // Skip sorting for order_status dropdowns to maintain workflow order
                const shouldSort = !el.name || el.name !== 'order_status';
                const ts = new TomSelect(el, {
                    plugins: [],
                    // controlInput: null, // REMOVED to enable search
                    allowEmptyOption: true,
                    create: false,
                    sortField: shouldSort ? {
                        field: "text",
                        direction: "asc"
                    } : null,
                    onItemAdd: function() {
                        this.blur(); // Fix blinky cursor on selection
                    },
                    onDropdownOpen: function() {
                        // 1. Move dropdown to body
                        if (this.dropdown.parentNode !== document.body) {
                            document.body.appendChild(this.dropdown);
                        }

                        // 2. Smart Positioning
                        const rect = this.control.getBoundingClientRect();
                        const spaceBelow = window.innerHeight - rect.bottom;
                        
                        // Basic positioning
                        this.dropdown.style.position = "absolute";
                        this.dropdown.style.width = rect.width + "px";
                        this.dropdown.style.left = (rect.left + window.scrollX) + "px";
                        this.dropdown.style.zIndex = "99999";

                        // If limited space below (< 220px) and more space above, flip UP
                        if (spaceBelow < 220 && rect.top > spaceBelow) {
                            this.dropdown.style.top = (rect.top + window.scrollY) + "px";
                            this.dropdown.style.transform = "translateY(-100%)";
                            this.dropdown.style.marginTop = "-8px"; // Add space between control and menu
                            this.dropdown.classList.add('dropdown-flipped'); // Helper for CSS if needed
                        } else {
                            this.dropdown.style.top = (rect.bottom + window.scrollY) + "px";
                            this.dropdown.style.transform = "none";
                            this.dropdown.style.marginTop = "8px"; // Default space
                            this.dropdown.classList.remove('dropdown-flipped');
                        }

                        // 3. Auto-close on scroll
                        this.scrollListener = (e) => {
                            // Close if scrolling something other than the dropdown content
                            if (!this.dropdown.contains(e.target)) {
                                this.close();
                            }
                        };
                        window.addEventListener('scroll', this.scrollListener, { capture: true, passive: true });
                    },
                    onDropdownClose: function() {
                        if (this.scrollListener) {
                            window.removeEventListener('scroll', this.scrollListener, { capture: true });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
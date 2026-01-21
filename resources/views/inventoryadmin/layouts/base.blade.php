<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/soco_logo/favicon.ico') }}" type="image/x-icon">
    <title>@yield('title', 'Inventory Admin Portal | The Skool Store')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
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
            cursor: pointer;
        }

        button,
        input[type="submit"],
        input[type="reset"],
        input[type="button"] {
            cursor: pointer;
        }

        /* Custom Global Button Style - Outline Purple Pill */
        .btn-vs-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid #d0d5dd;
            background: white;
            color: #490d59;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }
        .btn-vs-sm:hover {
            background-color: #f3e8f5;
            border-color: #490d59;
            text-decoration: none;
            color: #490d59;
            transform: none;
            box-shadow: none;
        }
        .btn-vs-sm i { font-size: 12px; }

        /* Tom Select Customization */
        .ts-control {
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
            padding: 10px 16px !important;
            height: 46px !important;
            font-size: 14px !important;
            box-shadow: none !important;
            background-color: #fff !important;
            display: flex;
            align-items: center;
        }
        .ts-control .item {
            font-size: 14px !important;
            color: #374151 !important;
            font-weight: 500;
        }
        .ts-dropdown {
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            padding: 8px !important;
            z-index: 99999 !important;
            background: #fff;
        }
        .ts-dropdown .option {
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 14px !important;
            margin-bottom: 2px;
            color: #374151;
        }
        .ts-dropdown .option.active, .ts-dropdown .option:hover {
            background-color: #f3e8f5 !important;
            color: #490d59 !important;
        }
        .ts-dropdown .option.selected {
            background-color: #490d59 !important;
            color: #fff !important;
        }
        /* Hide clear button */
        .ts-control .clear-button { display: none !important; }
        
        /* Hide input inside control */
        .ts-control input { display: none !important; }

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
        .ts-dropdown-content {
            max-height: 250px !important;
            overflow-y: auto !important;
        }

        .layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 32px 14px;
            display: flex;
            flex-direction: column;
            gap: 32px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
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
            background: rgb(73 13 89);
            color: #ffffff;
        }

        .content {
            padding: 0;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .content-body {
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
            background: var(--surface);
            padding: 24px 40px;
            margin: 0;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid var(--border);
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

            .content-body {
                padding: 24px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 20px 24px;
                margin: 0;
                position: sticky;
                top: 0;
                z-index: 999;
                border-bottom: 1px solid var(--border);
            }
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
            display: none;
        }
        
        .layout.collapsed .sidebar .nav__item {
            justify-content: center;
            padding: 12px;
        }
        
        .layout.collapsed .sidebar .nav__item span {
            display: none;
        }

        /* Hide content if flex is used, but icon persists */
        .layout.collapsed .sidebar .nav__item {
             /* font-size: 0 !important; */ 
             /* Easier to span wrap */
        }
        
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
    </style>
    @stack('head')
    @stack('styles')
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('assets/img/new logo/new_logo.png') }}" alt="The Skool Store">
                <small>Inventory Admin</small>
            </div>
            <nav class="nav">
                <a class="nav__item {{ request()->routeIs('inventory.admin.dashboard') ? 'active' : '' }}" href="{{ route('inventory.admin.dashboard') }}">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-th-large" style="width: 18px; text-align: center;"></i>
                        <span>Dashboard</span>
                    </div>
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.orders.*') && !request()->routeIs('inventory.admin.orders.shipping') ? 'active' : '' }}" href="{{ route('inventory.admin.orders.index') }}">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-shopping-bag" style="width: 18px; text-align: center;"></i>
                        <span>Orders</span>
                    </div>
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.orders.shipping') ? 'active' : '' }}" href="{{ route('inventory.admin.orders.shipping') }}">
                     <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-shipping-fast" style="width: 18px; text-align: center;"></i>
                        <span>Shipping</span>
                    </div>
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.inventory.*') ? 'active' : '' }}" href="{{ route('inventory.admin.inventory.index') }}">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-warehouse" style="width: 18px; text-align: center;"></i>
                        <span>Inventory</span>
                    </div>
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.returns-exchange.index') ? 'active' : '' }}" href="{{ route('inventory.admin.returns-exchange.index') }}">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-exchange-alt" style="width: 18px; text-align: center;"></i>
                        <span>Exchanges</span>
                    </div>
                </a>
                <a class="nav__item {{ request()->routeIs('inventory.admin.reports.*') ? 'active' : '' }}" href="{{ route('inventory.admin.reports.index') }}">
                     <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-chart-bar" style="width: 18px; text-align: center;"></i>
                        <span>Reports</span>
                    </div>
                </a>
            </nav>
        </aside>
        <main class="content">
            <div class="topbar">
                <div style="display:flex;align-items:center;gap:16px;">
                    <div class="sidebar-toggle" onclick="toggleSidebar()" style="font-size:20px;cursor:pointer;color:var(--text);border-right:1px solid var(--border);">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div>
                        <h1 style="margin:0;font-size:24px;color:var(--heading);">
                            @yield('page_heading', 'Inventory Admin Portal')
                        </h1>
                        <p style="margin:4px 0 0;color:#94a3b8;">
                            @yield('page_subheading', 'Focused tools for stock teams')
                        </p>
                    </div>
                </div>
                @php
                    $unreadNotifications = collect();
                    $unreadCount = 0;
                    try {
                        // Fetch DB notifications
                        $unreadNotifications = \App\Models\Notification::whereNull('read_at')
                                                ->where(function($q) {
                                                    $q->where('target_role', 'inventory')
                                                      ->orWhereNull('target_role');
                                                })
                                                ->orderByDesc('created_at')
                                                ->take(5)
                                                ->get();
                        $unreadCount = \App\Models\Notification::whereNull('read_at')
                                        ->where(function($q) {
                                            $q->where('target_role', 'inventory')
                                              ->orWhereNull('target_role');
                                        })
                                        ->count();
                        
                        // NEW: Dynamic Low Stock Check
                        $lowStockCount = \App\Models\Admin\Master\ProductMapping::where('inventory_stock', '<=', 5)->count();
                        if ($lowStockCount > 0) {
                            $lowStockNotif = new \stdClass();
                            $lowStockNotif->id = 'low-stock';
                            $lowStockNotif->title = 'Low Stock Alert';
                            $lowStockNotif->message = number_format($lowStockCount) . " products are near out of stock (<= 5).";
                            $lowStockNotif->created_at = now();
                            $lowStockNotif->type = 'low_stock_fake';
                            
                            // Prepend to list and increment count
                            $unreadNotifications->prepend($lowStockNotif);
                            $unreadCount++;
                        }

                    } catch (\Exception $e) {
                         \Illuminate\Support\Facades\Log::error('Notification fetch failed: ' . $e->getMessage());
                    }
                @endphp
                <div style="display:flex;align-items:center;margin-left:auto;gap:16px;">
                    <div class="notification-wrapper" style="position:relative;">
                        <button id="notificationBtn" style="background:none;border:none;position:relative;padding:8px;cursor:pointer;">
                            <i class="fas fa-bell" style="font-size:20px;color:var(--text);"></i>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span style="position:absolute;top:0;right:0;background:#ef4444;color:white;font-size:10px;padding:2px 5px;border-radius:99px;font-weight:700;border:2px solid var(--surface);">{{ $unreadCount }}</span>
                            @endif
                        </button>
                        <div class="profile-dropdown" id="notificationDropdown" style="width:300px;right:-50px;">
                            <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-weight:600;color:var(--heading);">
                                Notifications
                            </div>
                            <div style="max-height:300px;overflow-y:auto;">
                                @forelse($unreadNotifications ?? [] as $notif)
                                    @php
                                        $notifLink = (isset($notif->type) && $notif->type === 'low_stock_fake') 
                                            ? route('inventory.admin.reports.index') 
                                            : route('inventory.admin.notifications.read', $notif->id);
                                        
                                        $iconColor = (isset($notif->type) && $notif->type === 'low_stock_fake') ? '#dc2626' : '#10b981';
                                        $iconClass = (isset($notif->type) && $notif->type === 'low_stock_fake') ? 'fa-exclamation-triangle' : 'fa-chevron-right';
                                    @endphp
                                    <a href="{{ $notifLink }}" style="display:flex;text-decoration:none;color:inherit;padding:12px 16px;border-bottom:1px solid #f3f4f6;align-items:start;gap:12px;transition:background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                                        <div style="flex:1;">
                                            <p style="margin:0 0 4px;font-weight:600;font-size:13px;color:#374151;">{{ $notif->title }}</p>
                                            <p style="margin:0;font-size:12px;color:#6b7280;line-height:1.4;">{{ Str::limit($notif->message, 60) }}</p>
                                            <small style="margin-top:4px;display:block;font-size:11px;color:#9ca3af;">{{ $notif->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div style="color:{{ $iconColor }};font-size:12px;padding-top:2px;">
                                            <i class="fas {{ $iconClass }}"></i>
                                        </div>
                                    </a>
                                @empty
                                    <div style="padding:24px;text-align:center;color:#6b7280;font-size:13px;">
                                        No new notifications
                                    </div>
                                @endforelse
                            </div>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <form action="{{ route('inventory.admin.notifications.read-all') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="display:block;width:100%;text-align:center;padding:10px;font-size:12px;color:var(--accent);font-weight:600;border:none;border-top:1px solid var(--border);background:none;cursor:pointer;">
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
            </div>

            <div class="content-body">
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
        window.toggleSidebar = function() {
            const layout = document.querySelector('.layout');
            layout.classList.toggle('collapsed');
            const isCollapsed = layout.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Tom Select
            document.querySelectorAll('select').forEach((el) => {
                if (!el.classList.contains('no-tom')) {
                   new TomSelect(el, {
                        plugins: [],
                        allowEmptyOption: true,
                        create: false,
                        controlInput: null, // Disable input in control
                        sortField: {
                            field: "text",
                            direction: "asc"
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
                                this.dropdown.style.marginTop = "-8px"; 
                                this.dropdown.classList.add('dropdown-flipped'); 
                            } else {
                                this.dropdown.style.top = (rect.bottom + window.scrollY) + "px";
                                this.dropdown.style.transform = "none";
                                this.dropdown.style.marginTop = "8px"; 
                                this.dropdown.classList.remove('dropdown-flipped');
                            }

                            // 3. Auto-close on scroll
                            this.scrollListener = (e) => {
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
                }
            });

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
            
            // Confirm logout - submit the logout form
            if (confirmLogoutBtn) {
                confirmLogoutBtn.addEventListener('click', function() {
                    // Find the logout form and submit it
                    const logoutForm = document.querySelector('form[action*="logout"]');
                    if (logoutForm) {
                        logoutForm.submit();
                    } else {
                        // Fallback: redirect to login
                        window.location.href = '{{ route("inventory.admin.login") }}';
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

            // Restore sidebar state
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed) {
                document.querySelector('.layout').classList.add('collapsed');
            }
        });
    </script>
</body>
</html>


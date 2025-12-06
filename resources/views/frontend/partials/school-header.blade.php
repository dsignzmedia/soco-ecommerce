@php
    $headerRouteParams = [];
    // Try to get profile from request first, then fallback to session
    $headerProfileId = request('profile_id');
    if (!$headerProfileId) {
        $headerProfiles = session('student_profiles', []);
        if (count($headerProfiles) > 0) {
            $headerProfileId = $headerProfiles[0]['id'];
        }
    }
    if ($headerProfileId) {
        $headerRouteParams['profile_id'] = $headerProfileId;
    }
@endphp

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
    .school-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 40px;
        background: var(--bg);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .school-topbar__title h2 {
        margin: 0;
        color: var(--heading);
        font-size: 24px;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
    }
    .school-topbar__title p {
        margin: 4px 0 0;
        color: var(--text);
        font-size: 14px;
        font-family: 'Inter', sans-serif;
    }
    .profile-chip {
        position: relative;
        padding: 8px 12px 8px 8px;
        border-radius: 999px;
        background: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        box-shadow: 0 5px 20px rgba(15, 23, 42, 0.08);
        cursor: pointer;
        user-select: none;
        color: var(--heading);
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        border: 1px solid var(--border);
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
        font-size: 14px;
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
        border: 1px solid var(--border);
    }
    .profile-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .profile-dropdown a, .profile-dropdown button {
        display: block;
        width: 100%;
        padding: 12px 20px;
        text-align: left;
        background: none;
        border: none;
        color: var(--text);
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        font-family: 'Inter', sans-serif;
    }
    .profile-dropdown a:hover, .profile-dropdown button:hover {
        background: var(--primary-light);
        color: var(--primary);
    }
    .logout-btn {
        color: #ef4444 !important;
        border-top: 1px solid var(--border) !important;
    }
    .logout-btn:hover {
        background: #fef2f2 !important;
        color: #dc2626 !important;
    }
</style>

<div class="school-topbar">
    <div class="school-topbar__title">
        <h2>School Dashboard</h2>
        <p>Manage. Monitor. Master.</p>
    </div>

    <div class="header-right">
        <div class="profile-chip" id="profileChip">
            <span>{{ substr(Auth::user()->name, 0, 1) }}</span>
            {{ Auth::user()->name }}
            
            <div class="profile-dropdown" id="profileDropdown">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
        }
    });
</script>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:32px; max-width:400px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="width:64px; height:64px; background:#fef3f2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <i class="fas fa-sign-out-alt" style="font-size:24px; color:#b42318;"></i>
        </div>
        <h3 style="margin:0 0 8px; color:#111827; font-size:20px;">Leaving Dashboard?</h3>
        <p style="margin:0 0 24px; color:#475467; font-size:14px;">Are you sure you want to logout? You will need to login again to access the dashboard.</p>
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

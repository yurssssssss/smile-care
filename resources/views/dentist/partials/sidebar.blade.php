<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon" style="width:28px;height:28px;border-radius:7px;flex-shrink:0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 4.17 4.42 9.92 6.24 12.11a1 1 0 001.53 0C14.58 18.92 19 13.17 19 9c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
        </div>
        SmileCare
    </div>

    <div class="nav-section">Menu</div>

    <a href="{{ route('dentist.appointments') }}" class="nav-item {{ $active === 'appointments' ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17 2h-1V1h-2v1H8V1H6v1H5a2 2 0 00-2 2v16a2 2 0 002 2h14a2 2 0 002-2V4a2 2 0 00-2-2zM5 20V9h14v11H5zm0-13V4h14v3H5z"/></svg>
        Appointments
    </a>

    <a href="{{ route('profile') }}" class="nav-item {{ $active === 'profile' ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
             My Profile
    </a>

    <div style="border-top:1px solid #f0f0ee;margin:16px 0;"></div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nav-item" style="width:100%;background:none;border:none;text-align:left;cursor:pointer;color:#888;font-family:inherit;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
            Sign out
        </button>
    </form>
</div>
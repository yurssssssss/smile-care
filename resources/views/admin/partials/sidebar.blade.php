<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon" style="width:28px;height:28px;border-radius:7px;flex-shrink:0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 4.17 4.42 9.92 6.24 12.11a1 1 0 001.53 0C14.58 18.92 19 13.17 19 9c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
        </div>
        SmileCare
    </div>

    <div class="nav-section">Menu</div>

    <a href="{{ route('admin.dashboard') }}"    class="nav-item {{ $active === 'dashboard'    ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>
        Overview
    </a>

    <a href="{{ route('admin.appointments') }}" class="nav-item {{ $active === 'appointments' ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17 2h-1V1h-2v1H8V1H6v1H5a2 2 0 00-2 2v16a2 2 0 002 2h14a2 2 0 002-2V4a2 2 0 00-2-2zM5 20V9h14v11H5zm0-13V4h14v3H5z"/></svg>
        Appointments
    </a>

    <a href="{{ route('admin.patients') }}"     class="nav-item {{ $active === 'patients'     ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        Patients
    </a>
      <a href="{{ route('admin.dentists') }}"     class="nav-item {{ $active === 'dentists'     ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        Dentists
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

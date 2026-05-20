@extends('layouts.app')
@section('title', 'Dashboard — SmileCare Dental')

@push('styles')
<style>
body { margin:0; padding:0; background:#F5F5F3; }
.container, .container-fluid, main, #app > *:not(.sc-admin-wrap) {
    max-width:none !important; padding:0 !important; margin:0 !important;
}

/* Shell */
.sc-admin-wrap { display:flex; min-height:100vh; width:100%; }

/* Sidebar */
.sc-sidebar {
    width:220px; min-width:220px; background:#fff;
    border-right:1px solid #EBEBEA; display:flex; flex-direction:column;
    position:sticky; top:0; height:100vh; overflow-y:auto;
    z-index:100; flex-shrink:0; transition:transform .25s ease;
}
.sc-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,.45); z-index:199; cursor:pointer;
}
.sc-overlay.active { display:block; }

/* Main */
.sc-main { flex:1; min-width:0; display:flex; flex-direction:column; }

/* Topbar */
.sc-topbar {
    height:52px; background:#fff; border-bottom:1px solid #EBEBEA;
    display:flex; align-items:center; padding:0 20px; gap:10px;
    position:sticky; top:0; z-index:90; flex-shrink:0;
}
.sc-topbar-title { font-size:15px; font-weight:600; flex:1; }
.sc-hamburger {
    display:none; background:none; border:none; cursor:pointer;
    padding:5px; border-radius:6px; color:#444;
    align-items:center; justify-content:center;
}
.sc-hamburger:hover { background:#f0f0ee; }

/* Content */
.sc-content { padding:20px; flex:1; }

/* Cards */
.sc-card {
    background:#fff; border:1px solid #EBEBEA;
    border-radius:10px; overflow:hidden;
}
.sc-card-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 16px; border-bottom:1px solid #f0f0ee; flex-wrap:wrap; gap:8px;
}

/* Stat cards */
.sc-stat { background:#fff; border:1px solid #EBEBEA; border-radius:10px; padding:18px; }
.sc-stat-label { font-size:12px; color:#888; margin-bottom:6px; }
.sc-stat-value { font-size:28px; font-weight:700; color:#111; }

/* Table */
.sc-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.sc-table-wrap table { width:100%; min-width:560px; border-collapse:collapse; font-size:13px; }
.sc-table-wrap th {
    text-align:left; font-size:11px; font-weight:600; text-transform:uppercase;
    letter-spacing:.5px; color:#999; padding:10px 14px;
    border-bottom:1px solid #f0f0ee; white-space:nowrap;
}
.sc-table-wrap td {
    padding:12px 14px; border-bottom:1px solid #f9f9f8;
    vertical-align:middle; color:#333;
}
.sc-table-wrap tr:last-child td { border-bottom:none; }

/* Badges */
.sc-badge { display:inline-flex; align-items:center; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap; }
.sc-badge-green  { background:#E1F5EE; color:#0F6E56; }
.sc-badge-yellow { background:#FEF9C3; color:#854F0B; }
.sc-badge-gray   { background:#f3f4f6; color:#9ca3af; }
.sc-badge-blue   { background:#EEF2FF; color:#3730A3; }
.sc-badge-red    { background:#FEE2E2; color:#991B1B; }

/* Buttons */
.sc-btn {
    display:inline-flex; align-items:center; justify-content:center;
    padding:7px 14px; border-radius:7px; font-size:13px;
    font-weight:500; cursor:pointer; border:none; transition:opacity .15s; white-space:nowrap;
}
.sc-btn:hover { opacity:.88; }
.sc-btn-primary { background:#1C6B4A; color:#fff; }
.sc-btn-ghost   { background:transparent; color:#444; border:1px solid #DDDDD9; }
.sc-btn-sm { padding:5px 11px; font-size:12px; }

@media(max-width:768px) {
    .sc-sidebar { position:fixed; top:0; left:0; height:100%; z-index:200; transform:translateX(-100%); }
    .sc-sidebar.open { transform:translateX(0); }
    .sc-hamburger { display:inline-flex; }
    .sc-content { padding:14px; }
    .sc-topbar { padding:0 14px; }
    .sc-stat { padding:14px; }
    .sc-stat-value { font-size:22px; }
}
@media(max-width:480px) {
    .sc-topbar-username { display:none; }
    .sc-stat-value { font-size:20px; }
}
</style>
@endpush

@section('content')
<div class="sc-overlay" id="scOverlay"></div>

<div class="sc-admin-wrap">
    <aside class="sc-sidebar admin-sidebar" id="scSidebar">
        @include('admin.partials.sidebar', ['active' => 'dashboard'])
    </aside>

    <div class="sc-main">
        {{-- Topbar --}}
        <div class="sc-topbar">
            <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="sc-topbar-title">Dashboard</span>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <span class="sc-topbar-username" style="font-size:13px;color:#555;">{{ Auth::user()->full_name }}</span>
                <span class="sc-badge sc-badge-blue">Admin</span>
            </div>
        </div>

        {{-- Content --}}
        <div class="sc-content">

            {{-- Stat Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-xl-3">
                    <div class="sc-stat h-100">
                        <div class="sc-stat-label">Today's appointments</div>
                        <div class="sc-stat-value">{{ $stats['today'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="sc-stat h-100">
                        <div class="sc-stat-label">Total patients</div>
                        <div class="sc-stat-value">{{ $stats['patients'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="sc-stat h-100">
                        <div class="sc-stat-label">Pending</div>
                        <div class="sc-stat-value" style="color:#854F0B;">{{ $stats['pending'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="sc-stat h-100">
                        <div class="sc-stat-label">Completed</div>
                        <div class="sc-stat-value" style="color:#3B6D11;">{{ $stats['completed'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Recent Appointments --}}
            <div class="sc-card">
                <div class="sc-card-header">
                    <span style="font-size:15px;font-weight:600;">Recent appointments</span>
                    <a href="{{ route('admin.appointments') }}" class="sc-btn sc-btn-ghost sc-btn-sm">View all</a>
                </div>
                <div class="sc-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date & Time</th>
                                <th>Service</th>
                                <th>Dentist</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent as $appt)
                            <tr>
                                <td>
                                    <div style="font-weight:500;">{{ $appt->user->full_name }}</div>
                                    <div style="font-size:12px;color:#aaa;">{{ $appt->user->email }}</div>
                                </td>
                                <td>
                                    {{ $appt->formatted_date }}<br>
                                    <span style="font-size:12px;color:#888;">{{ $appt->formatted_time }}</span>
                                </td>
                                <td>{{ $appt->service }}</td>
                                <td>{{ $appt->dentist->name }}</td>
                                <td><span class="sc-badge {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span></td>
                                <td>
                                    @if($appt->status === 'pending')
                                    <form method="POST" action="{{ route('admin.appointments.status', $appt) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="sc-btn sc-btn-primary sc-btn-sm">Confirm</button>
                                    </form>
                                    @else
                                    <span style="font-size:12px;color:#ccc;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4" style="color:#aaa;">No appointments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function(){
    const sidebar=document.getElementById('scSidebar'),overlay=document.getElementById('scOverlay'),ham=document.getElementById('scHamburger');
    function open(){sidebar.classList.add('open');overlay.classList.add('active');}
    function close(){sidebar.classList.remove('open');overlay.classList.remove('active');}
    ham.addEventListener('click',()=>sidebar.classList.contains('open')?close():open());
    overlay.addEventListener('click',close);
    window.addEventListener('resize',()=>{if(window.innerWidth>768)close();});
})();
</script>
@endsection
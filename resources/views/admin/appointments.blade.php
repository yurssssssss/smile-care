@extends('layouts.app')
@section('title', 'Appointments — SmileCare Dental')

@push('styles')
<style>
body { margin:0; padding:0; background:#F5F5F3; }
.container, .container-fluid, main, #app > *:not(.sc-admin-wrap) {
    max-width:none !important; padding:0 !important; margin:0 !important;
}
.sc-admin-wrap { display:flex; min-height:100vh; width:100%; }
.sc-sidebar {
    width:220px; min-width:220px; background:#fff; border-right:1px solid #EBEBEA;
    display:flex; flex-direction:column; position:sticky; top:0; height:100vh;
    overflow-y:auto; z-index:100; flex-shrink:0; transition:transform .25s ease;
}
.sc-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:199; cursor:pointer; }
.sc-overlay.active { display:block; }
.sc-main { flex:1; min-width:0; display:flex; flex-direction:column; }
.sc-topbar {
    height:52px; background:#fff; border-bottom:1px solid #EBEBEA;
    display:flex; align-items:center; padding:0 20px; gap:10px;
    position:sticky; top:0; z-index:90; flex-shrink:0;
}
.sc-topbar-title { font-size:15px; font-weight:600; flex:1; }
.sc-hamburger { display:none; background:none; border:none; cursor:pointer; padding:5px; border-radius:6px; color:#444; align-items:center; justify-content:center; }
.sc-hamburger:hover { background:#f0f0ee; }
.sc-content { padding:20px; flex:1; }
.sc-card { background:#fff; border:1px solid #EBEBEA; border-radius:10px; overflow:hidden; }
.sc-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.sc-table-wrap table { width:100%; min-width:620px; border-collapse:collapse; font-size:13px; }
.sc-table-wrap th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#999; padding:10px 14px; border-bottom:1px solid #f0f0ee; white-space:nowrap; }
.sc-table-wrap td { padding:12px 14px; border-bottom:1px solid #f9f9f8; vertical-align:middle; color:#333; }
.sc-table-wrap tr:last-child td { border-bottom:none; }
.sc-badge { display:inline-flex; align-items:center; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap; }
.sc-badge-green  { background:#E1F5EE; color:#0F6E56; }
.sc-badge-yellow { background:#FEF9C3; color:#854F0B; }
.sc-badge-gray   { background:#f3f4f6; color:#9ca3af; }
.sc-badge-blue   { background:#EEF2FF; color:#3730A3; }
.sc-badge-red    { background:#FEE2E2; color:#991B1B; }
.sc-btn { display:inline-flex; align-items:center; justify-content:center; padding:7px 14px; border-radius:7px; font-size:13px; font-weight:500; cursor:pointer; border:none; transition:opacity .15s; white-space:nowrap; }
.sc-btn:hover { opacity:.88; }
.sc-btn-primary { background:#1C6B4A; color:#fff; }
.sc-btn-ghost   { background:transparent; color:#444; border:1px solid #DDDDD9; }
.sc-btn-sm { padding:5px 11px; font-size:12px; }
.sc-form-control { width:100%; padding:8px 10px; border:1px solid #DDDDD9; border-radius:7px; font-size:13px; background:#fff; box-sizing:border-box; color:#222; outline:none; }
.sc-form-control:focus { border-color:#1C6B4A; box-shadow:none; }

@media(max-width:768px) {
    .sc-sidebar { position:fixed; top:0; left:0; height:100%; z-index:200; transform:translateX(-100%); }
    .sc-sidebar.open { transform:translateX(0); }
    .sc-hamburger { display:inline-flex; }
    .sc-content { padding:14px; }
    .sc-topbar { padding:0 14px; }
}
@media(max-width:480px) {
    .sc-topbar-username { display:none; }
}
</style>
@endpush

@section('content')
<div class="sc-overlay" id="scOverlay"></div>

<div class="sc-admin-wrap">
    <aside class="sc-sidebar admin-sidebar" id="scSidebar">
        @include('admin.partials.sidebar', ['active' => 'appointments'])
    </aside>

    <div class="sc-main">
        {{-- Topbar --}}
        <div class="sc-topbar">
            <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="sc-topbar-title">Appointments</span>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <span class="sc-topbar-username" style="font-size:13px;color:#555;">{{ Auth::user()->full_name }}</span>
                <span class="sc-badge sc-badge-blue">Admin</span>
            </div>
        </div>

        <div class="sc-content">

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.appointments') }}" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <label class="form-label" style="font-size:12px;font-weight:500;color:#555;margin-bottom:4px;">Status</label>
                        <select name="status" class="sc-form-control">
                            <option value="">All statuses</option>
                            @foreach(['pending','confirmed','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <label class="form-label" style="font-size:12px;font-weight:500;color:#555;margin-bottom:4px;">Date</label>
                        <input type="date" name="date" class="sc-form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-6 col-sm-auto">
                        <button type="submit" class="sc-btn sc-btn-primary sc-btn-sm w-100" style="height:38px;">Filter</button>
                    </div>
                    <div class="col-6 col-sm-auto">
                        <a href="{{ route('admin.appointments') }}" class="sc-btn sc-btn-ghost sc-btn-sm w-100" style="height:38px;">Clear</a>
                    </div>
                </div>
            </form>

            <div class="sc-card">
                <div class="sc-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Service</th>
                                <th>Dentist</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appt)
                            <tr>
                                <td>
                                    <div style="font-weight:500;">{{ $appt->user->full_name }}</div>
                                    <div style="font-size:12px;color:#aaa;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                                </td>
                                <td>{{ $appt->service }}</td>
                                <td>{{ $appt->dentist->name }}</td>
                                <td>
                                    {{ $appt->formatted_date }}<br>
                                    <span style="font-size:12px;color:#888;">{{ $appt->formatted_time }}</span>
                                </td>
                                <td><span class="sc-badge {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span></td>
                                <td>
                                    @if($appt->status === 'completed')
                                        <span class="sc-badge sc-badge-green" style="opacity:.75;">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><path d="M20 6L9 17l-5-5"/></svg>
                                            Completed
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.appointments.status', $appt) }}"
                                            class="d-flex gap-2 align-items-center flex-wrap">
                                            @csrf @method('PATCH')
                                            <select name="status" class="sc-form-control"
                                                style="width:auto;font-size:12px;padding:5px 8px;flex:1;min-width:110px;">
                                                @foreach(['pending','confirmed','completed','cancelled'] as $s)
                                                <option value="{{ $s }}" {{ $appt->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="sc-btn sc-btn-primary sc-btn-sm">Save</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4" style="color:#aaa;">No appointments found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($appointments->hasPages())
                <div style="padding:12px 14px;border-top:1px solid #f0f0ee;">
                    {{ $appointments->withQueryString()->links() }}
                </div>
                @endif
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
@extends('layouts.app')
@section('title', 'Patients — SmileCare Dental')

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
.sc-card-header { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #f0f0ee; flex-wrap:wrap; gap:8px; }
.sc-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.sc-table-wrap table { width:100%; min-width:480px; border-collapse:collapse; font-size:13px; }
.sc-table-wrap th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#999; padding:10px 14px; border-bottom:1px solid #f0f0ee; white-space:nowrap; }
.sc-table-wrap td { padding:12px 14px; border-bottom:1px solid #f9f9f8; vertical-align:middle; color:#333; }
.sc-table-wrap tr:last-child td { border-bottom:none; }
.sc-badge { display:inline-flex; align-items:center; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap; }
.sc-badge-blue { background:#EEF2FF; color:#3730A3; }
.sc-avatar { width:32px; height:32px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; }

@media(max-width:768px) {
    .sc-sidebar { position:fixed; top:0; left:0; height:100%; z-index:200; transform:translateX(-100%); }
    .sc-sidebar.open { transform:translateX(0); }
    .sc-hamburger { display:inline-flex; }
    .sc-content { padding:14px; }
    .sc-topbar { padding:0 14px; }
    /* Hide less important columns on mobile */
    .sc-col-phone, .sc-col-registered { display:none; }
}
@media(max-width:480px) {
    .sc-topbar-username { display:none; }
    .sc-col-email { display:none; }
}
</style>
@endpush

@section('content')
<div class="sc-overlay" id="scOverlay"></div>

<div class="sc-admin-wrap">
    <aside class="sc-sidebar admin-sidebar" id="scSidebar">
        @include('admin.partials.sidebar', ['active' => 'patients'])
    </aside>

    <div class="sc-main">
        <div class="sc-topbar">
            <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="sc-topbar-title">Patients</span>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <span class="sc-topbar-username" style="font-size:13px;color:#555;">{{ Auth::user()->full_name }}</span>
                <span class="sc-badge sc-badge-blue">Admin</span>
            </div>
        </div>

        <div class="sc-content">
            <div class="sc-card">
                <div class="sc-card-header">
                    <span style="font-size:15px;font-weight:600;">All patients ({{ $patients->total() }})</span>
                </div>
                <div class="sc-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="sc-col-email">Email</th>
                                <th class="sc-col-phone">Phone</th>
                                <th>Appointments</th>
                                <th class="sc-col-registered">Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patients as $patient)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="sc-avatar" style="background:#E1F5EE;color:#0F6E56;">
                                            {{ strtoupper(substr($patient->first_name,0,1).substr($patient->last_name,0,1)) }}
                                        </div>
                                        <span style="font-weight:500;">{{ $patient->full_name }}</span>
                                    </div>
                                </td>
                                <td class="sc-col-email" style="color:#555;">{{ $patient->email }}</td>
                                <td class="sc-col-phone" style="color:#555;">{{ $patient->phone ?? '—' }}</td>
                                <td><span class="sc-badge sc-badge-blue">{{ $patient->appointments_count }}</span></td>
                                <td class="sc-col-registered" style="color:#888;font-size:13px;">{{ $patient->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4" style="color:#aaa;">No patients yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($patients->hasPages())
                <div style="padding:12px 14px;border-top:1px solid #f0f0ee;">
                    {{ $patients->links() }}
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
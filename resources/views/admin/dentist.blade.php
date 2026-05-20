@extends('layouts.app')
@section('title', 'Dentists — SmileCare Dental')

@push('styles')
<style>
/* ── Force full-viewport layout, overriding any parent container ── */
body { margin: 0; padding: 0; }

/* Neutralize any wrapping container from layouts/app.blade.php */
.container, .container-fluid, main, #app > *:not(.sc-admin-wrap) {
    max-width: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* ── Root admin shell ─────────────────────────────────────── */
.sc-admin-wrap {
    display: flex;
    min-height: 100vh;
    width: 100%;
    background: #F5F5F3;
    position: relative;
}

/* ── Sidebar ──────────────────────────────────────────────── */
.sc-sidebar {
    width: 220px;
    min-width: 220px;
    background: #fff;
    border-right: 1px solid #EBEBEA;
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 100;
    flex-shrink: 0;
    transition: transform .25s ease;
}

/* ── Sidebar overlay (mobile) ─────────────────────────────── */
.sc-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 199;
    cursor: pointer;
}
.sc-overlay.active { display: block; }

/* ── Main content area ────────────────────────────────────── */
.sc-main {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

/* ── Topbar ───────────────────────────────────────────────── */
.sc-topbar {
    height: 52px;
    background: #fff;
    border-bottom: 1px solid #EBEBEA;
    display: flex;
    align-items: center;
    padding: 0 20px;
    gap: 10px;
    position: sticky;
    top: 0;
    z-index: 90;
    flex-shrink: 0;
}
.sc-topbar-title { font-size: 15px; font-weight: 600; flex: 1; }
.sc-topbar-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }

/* Hamburger */
.sc-hamburger {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    border-radius: 6px;
    color: #444;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
}
.sc-hamburger:hover { background: #f0f0ee; }

/* ── Inner content padding ────────────────────────────────── */
.sc-content {
    padding: 20px;
    flex: 1;
}

/* ── Cards ────────────────────────────────────────────────── */
.sc-card {
    background: #fff;
    border: 1px solid #EBEBEA;
    border-radius: 10px;
    overflow: hidden;
}
.sc-card-pad { padding: 20px; }

.sc-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0ee;
    flex-wrap: wrap;
    gap: 8px;
}

/* ── Two-column dentist layout ────────────────────────────── */
.sc-dentist-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 18px;
    align-items: start;
}

/* ── Table ────────────────────────────────────────────────── */
.sc-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.sc-table-wrap table {
    width: 100%;
    min-width: 580px;
    border-collapse: collapse;
    font-size: 13px;
}
.sc-table-wrap th {
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #999;
    padding: 10px 14px;
    border-bottom: 1px solid #f0f0ee;
    white-space: nowrap;
}
.sc-table-wrap td {
    padding: 12px 14px;
    border-bottom: 1px solid #f9f9f8;
    vertical-align: middle;
    color: #333;
}
.sc-table-wrap tr:last-child td { border-bottom: none; }

/* ── Badges ───────────────────────────────────────────────── */
.sc-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.sc-badge-green  { background: #E1F5EE; color: #0F6E56; }
.sc-badge-yellow { background: #FEF9C3; color: #854F0B; }
.sc-badge-gray   { background: #f3f4f6; color: #9ca3af; }
.sc-badge-blue   { background: #EEF2FF; color: #3730A3; }

/* ── Buttons ──────────────────────────────────────────────── */
.sc-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 7px 14px; border-radius: 7px; font-size: 13px;
    font-weight: 500; cursor: pointer; border: none; transition: opacity .15s;
    white-space: nowrap;
}
.sc-btn:hover { opacity: .88; }
.sc-btn-primary { background: #1C6B4A; color: #fff; }
.sc-btn-ghost   { background: transparent; color: #444; border: 1px solid #DDDDD9; }
.sc-btn-sm      { padding: 5px 11px; font-size: 12px; }

/* ── Avatar ───────────────────────────────────────────────── */
.sc-avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
}

/* ── Form ─────────────────────────────────────────────────── */
.sc-form-group { margin-bottom: 14px; }
.sc-form-group label { display: block; font-size: 12px; font-weight: 500; color: #555; margin-bottom: 5px; }
.sc-form-control {
    width: 100%; padding: 9px 11px; border: 1px solid #DDDDD9;
    border-radius: 7px; font-size: 13px; background: #fff;
    box-sizing: border-box; color: #222; outline: none; transition: border-color .15s;
}
.sc-form-control:focus { border-color: #1C6B4A; }

/* ── Alerts ───────────────────────────────────────────────── */
.sc-alert { padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
.sc-alert-success { background: #E1F5EE; border: 1px solid #A3D9C4; color: #0F6E56; }
.sc-alert-error   { background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; }

/* ── Modal ────────────────────────────────────────────────── */
.sc-modal-bg {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.4); z-index: 999;
    align-items: center; justify-content: center; padding: 16px;
}
.sc-modal-bg.open { display: flex; }
.sc-modal {
    background: #fff; border-radius: 12px; padding: 24px;
    width: 400px; max-width: 100%; box-shadow: 0 8px 40px rgba(0,0,0,.18);
}

/* ── Tablet (≤1024px) ─────────────────────────────────────── */
@media (max-width: 1024px) {
    .sc-dentist-grid { grid-template-columns: 1fr; }
}

/* ── Mobile (≤768px) ──────────────────────────────────────── */
@media (max-width: 768px) {
    .sc-sidebar {
        position: fixed;
        top: 0; left: 0;
        height: 100%;
        z-index: 200;
        transform: translateX(-100%);
    }
    .sc-sidebar.open { transform: translateX(0); }

    .sc-hamburger { display: inline-flex; }

    .sc-content { padding: 14px; }
    .sc-topbar { padding: 0 14px; }
    .sc-dentist-grid { grid-template-columns: 1fr; gap: 14px; }
}

@media (max-width: 480px) {
    .sc-topbar-username { display: none; }
    .sc-content { padding: 10px; }
    .sc-card-pad { padding: 14px; }
}
</style>
@endpush

@section('content')
{{-- Mobile overlay --}}
<div class="sc-overlay" id="scOverlay"></div>

<div class="sc-admin-wrap">

    {{-- ── Sidebar ─────────────────────────────────────────── --}}
    <aside class="sc-sidebar admin-sidebar" id="scSidebar">
        @include('admin.partials.sidebar', ['active' => 'dentists'])
    </aside>

    {{-- ── Main ───────────────────────────────────────────── --}}
    <div class="sc-main">

        {{-- Topbar --}}
        <div class="sc-topbar">
            <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <line x1="3" y1="6"  x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="sc-topbar-title">Dentists</span>
            <div class="sc-topbar-actions">
                <span class="sc-topbar-username" style="font-size:13px;color:#555;">{{ Auth::user()->full_name }}</span>
                <span class="sc-badge sc-badge-blue">Admin</span>
            </div>
        </div>

        {{-- Content --}}
        <div class="sc-content">

            @if(session('success'))
            <div class="sc-alert sc-alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="sc-alert sc-alert-error">✕ {{ session('error') }}</div>
            @endif

            <div class="sc-dentist-grid">

                {{-- ── Dentists table ───────────────────────── --}}
                <div class="sc-card">
                    <div class="sc-card-header">
                        <span style="font-size:15px;font-weight:600;">All dentists ({{ $dentists->count() }})</span>
                    </div>
                    <div class="sc-table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Dentist</th>
                                    <th>Specialization</th>
                                    <th>Login account</th>
                                    <th>Appointments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dentists as $dentist)
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div class="sc-avatar" style="background:#E8E4FB;color:#533AB7;">
                                                {{ strtoupper(substr($dentist->name,0,2)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight:500;">{{ $dentist->name }}</div>
                                                @if($dentist->user)
                                                <div style="font-size:12px;color:#aaa;">{{ $dentist->user->email }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#555;">{{ $dentist->specialization ?? 'General Dentistry' }}</td>
                                    <td>
                                        @if($dentist->user)
                                            <span class="sc-badge sc-badge-green">Account linked</span>
                                        @else
                                            <span class="sc-badge sc-badge-yellow">No account yet</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="sc-badge sc-badge-blue">{{ $dentist->appointments_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($dentist->is_active)
                                            <span class="sc-badge sc-badge-green">Active</span>
                                        @else
                                            <span class="sc-badge sc-badge-gray">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                            @if(!$dentist->user)
                                                <button type="button" class="sc-btn sc-btn-primary sc-btn-sm"
                                                    onclick="openCreateModal('{{ $dentist->id }}', '{{ addslashes($dentist->name) }}')">
                                                    Create account
                                                </button>
                                            @else
                                                <button type="button" class="sc-btn sc-btn-ghost sc-btn-sm"
                                                    onclick="openResetModal('{{ $dentist->user->id }}', '{{ addslashes($dentist->name) }}')">
                                                    Reset password
                                                </button>
                                            @endif
                                            <form method="POST" action="{{ route('admin.dentists.toggle', $dentist) }}">
                                                @csrf @method('PATCH')
                                                @php $action = $dentist->is_active ? 'Deactivate' : 'Activate'; @endphp
                                                <button type="submit" class="sc-btn sc-btn-ghost sc-btn-sm"
                                                    onclick="return confirm('{{ $action }} this dentist?')">
                                                    {{ $action }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;color:#aaa;padding:32px;">
                                        No dentists found. Add one using the form.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ── Add dentist form ──────────────────────── --}}
                <div class="sc-card sc-card-pad">
                    <h3 style="font-size:15px;font-weight:600;margin:0 0 4px;">Add new dentist</h3>
                    <p style="font-size:13px;color:#888;margin:0 0 18px;">Adds the dentist to the system. You can create their login account afterward.</p>

                    <form method="POST" action="{{ route('admin.dentists.store') }}">
                        @csrf
                        <div class="sc-form-group">
                            <label>Full name <span style="color:#aaa;font-weight:400;">(with Dr. if needed)</span></label>
                            <input name="name" type="text"
                                class="sc-form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Dr. Maria Santos" required>
                            @error('name') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div class="sc-form-group">
                            <label>Specialization</label>
                            <input name="specialization" type="text"
                                class="sc-form-control @error('specialization') is-invalid @enderror"
                                value="{{ old('specialization') }}" placeholder="e.g. Orthodontics">
                            @error('specialization') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="sc-btn sc-btn-primary" style="width:100%;">
                            Add dentist
                        </button>
                    </form>
                </div>

            </div>{{-- /dentist-grid --}}
        </div>{{-- /sc-content --}}
    </div>{{-- /sc-main --}}
</div>{{-- /sc-admin-wrap --}}

{{-- ── Create Account Modal ─────────────────────────────── --}}
<div class="sc-modal-bg" id="createModal">
    <div class="sc-modal">
        <h3 style="font-size:16px;font-weight:600;margin:0 0 4px;">Create login account</h3>
        <p style="font-size:13px;color:#888;margin:0 0 18px;">For: <strong id="createModalName"></strong></p>
        <form method="POST" id="createModalForm">
            @csrf
            <div class="sc-form-group">
                <label>Email address</label>
                <input name="email" type="email" class="sc-form-control" placeholder="dentist@smilecare.com" required>
            </div>
            <div class="sc-form-group">
                <label>Temporary password</label>
                <input name="password" type="password" class="sc-form-control" placeholder="Minimum 8 characters" required minlength="8">
            </div>
            <div class="sc-form-group">
                <label>Confirm password</label>
                <input name="password_confirmation" type="password" class="sc-form-control" placeholder="Re-enter password" required>
            </div>
            <p style="font-size:12px;color:#aaa;margin-bottom:16px;">The dentist will use these credentials to log in.</p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button type="submit" class="sc-btn sc-btn-primary" style="flex:1;min-width:120px;">Create account</button>
                <button type="button" class="sc-btn sc-btn-ghost" onclick="closeModal('createModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Reset Password Modal ──────────────────────────────── --}}
<div class="sc-modal-bg" id="resetModal">
    <div class="sc-modal" style="width:380px;">
        <h3 style="font-size:16px;font-weight:600;margin:0 0 4px;">Reset password</h3>
        <p style="font-size:13px;color:#888;margin:0 0 18px;">For: <strong id="resetModalName"></strong></p>
        <form method="POST" id="resetModalForm">
            @csrf @method('PATCH')
            <div class="sc-form-group">
                <label>New password</label>
                <input name="password" type="password" class="sc-form-control" placeholder="Minimum 8 characters" required minlength="8">
            </div>
            <div class="sc-form-group">
                <label>Confirm new password</label>
                <input name="password_confirmation" type="password" class="sc-form-control" placeholder="Re-enter password" required>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button type="submit" class="sc-btn sc-btn-primary" style="flex:1;min-width:120px;">Reset password</button>
                <button type="button" class="sc-btn sc-btn-ghost" onclick="closeModal('resetModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const sidebar  = document.getElementById('scSidebar');
    const overlay  = document.getElementById('scOverlay');
    const hamburger = document.getElementById('scHamburger');

    function openSidebar()  { sidebar.classList.add('open');  overlay.classList.add('active'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('active'); }

    hamburger.addEventListener('click', () => sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar on resize to desktop
    window.addEventListener('resize', () => { if (window.innerWidth > 768) closeSidebar(); });
})();

function openCreateModal(id, name) {
    document.getElementById('createModalName').textContent = name;
    document.getElementById('createModalForm').action = `/admin/dentists/${id}/account`;
    document.getElementById('createModal').classList.add('open');
}
function openResetModal(id, name) {
    document.getElementById('resetModalName').textContent = name;
    document.getElementById('resetModalForm').action = `/admin/users/${id}/password`;
    document.getElementById('resetModal').classList.add('open');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}
// Backdrop click closes modals
document.querySelectorAll('.sc-modal-bg').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});
</script>
@endsection
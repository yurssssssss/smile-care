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
<div class="d-flex" style="min-height:100vh;">

    @include('dentist.partials.sidebar', ['active' => 'appointments'])

    <div class="flex-grow-1 d-flex flex-column overflow-auto">

        {{-- Topbar --}}
           <div class="sc-topbar">
            <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="sc-topbar-title">Appointments</span>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <span class="sc-topbar-username" style="font-size:13px;color:#555;">Dr. {{ Auth::user()->full_name }}</span>
                <span class="sc-badge sc-badge-blue">Dentist</span>
            </div>
        </div>
        

        <div class="p-3 p-md-4">

            @if(session('success'))
            <div class="alert alert-success small py-2 mb-3">✓ {{ session('success') }}</div>
            @endif

            {{-- Awaiting Response --}}
            @if($pendingAppointments->isNotEmpty())
            <div class="card border border-warning border-start border-start-4 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">⏳ Awaiting your response ({{ $pendingAppointments->count() }})</span>
                    </div>

                    {{-- Desktop table --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th>
                                    <th>Service</th>
                                    <th>Date &amp; Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingAppointments as $appt)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $appt->user->full_name }}</div>
                                        <div class="text-muted" style="font-size:12px;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                                    </td>
                                    <td>{{ $appt->service }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}<br>
                                        <span class="text-muted" style="font-size:12px;">{{ \Carbon\Carbon::createFromFormat('H:i:s', $appt->appointment_time)->format('g:i A') }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="confirmed">
                                                <button class="btn btn-success btn-sm">Accept</button>
                                            </form>
                                            <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="cancelled">
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Decline this appointment?')">Decline</button>
                                            </form>
                                            <button class="btn btn-outline-secondary btn-sm"
                                                onclick="openReschedModal('{{ $appt->id }}', '{{ $appt->appointment_date }}', '{{ substr($appt->appointment_time,0,5) }}')">
                                                Reschedule
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="d-md-none d-flex flex-column gap-2">
                        @foreach($pendingAppointments as $appt)
                        <div class="border rounded-2 p-3 small">
                            <div class="fw-medium mb-1">{{ $appt->user->full_name }}</div>
                            <div class="text-muted mb-1">{{ $appt->user->phone ?? $appt->user->email }}</div>
                            <div class="mb-1"><strong>Service:</strong> {{ $appt->service }}</div>
                            <div class="mb-2">
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}
                                · {{ \Carbon\Carbon::createFromFormat('H:i:s', $appt->appointment_time)->format('g:i A') }}
                            </div>
                            <div class="d-flex gap-1 flex-wrap">
                                <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="confirmed">
                                    <button class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="cancelled">
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Decline this appointment?')">Decline</button>
                                </form>
                                <button class="btn btn-outline-secondary btn-sm"
                                    onclick="openReschedModal('{{ $appt->id }}', '{{ $appt->appointment_date }}', '{{ substr($appt->appointment_time,0,5) }}')">
                                    Reschedule
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Today's Schedule --}}
            <div class="card border mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Today's schedule</span>
                        <span class="text-muted small">{{ now()->format('F d, Y') }}</span>
                    </div>

                    {{-- Desktop --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th><th>Patient</th><th>Service</th>
                                    <th>Status</th><th>Notes</th><th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayAppointments as $appt)
                                @php
                                    $badge = ['pending'=>'bg-warning text-dark','confirmed'=>'bg-success','completed'=>'bg-primary','cancelled'=>'bg-danger'];
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-nowrap">
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $appt->appointment_time)->format('g:i A') }}
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $appt->user->full_name }}</div>
                                        <div class="text-muted" style="font-size:12px;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                                    </td>
                                    <td>{{ $appt->service }}</td>
                                    <td><span class="badge {{ $badge[$appt->status] ?? 'bg-secondary' }}">{{ ucfirst($appt->status) }}</span></td>
                                    <td class="text-muted">{{ $appt->notes ?? '—' }}</td>
                                    <td>
                                        @if($appt->status === 'confirmed')
                                        <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="action" value="completed">
                                            <button class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Mark as completed?')">Complete</button>
                                        </form>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No appointments scheduled for today.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile --}}
                    <div class="d-md-none d-flex flex-column gap-2">
                        @forelse($todayAppointments as $appt)
                        @php $badge = ['pending'=>'bg-warning text-dark','confirmed'=>'bg-success','completed'=>'bg-primary','cancelled'=>'bg-danger']; @endphp
                        <div class="border rounded-2 p-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold">{{ \Carbon\Carbon::createFromFormat('H:i:s', $appt->appointment_time)->format('g:i A') }}</span>
                                <span class="badge {{ $badge[$appt->status] ?? 'bg-secondary' }}">{{ ucfirst($appt->status) }}</span>
                            </div>
                            <div class="fw-medium">{{ $appt->user->full_name }}</div>
                            <div class="text-muted mb-1" style="font-size:12px;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                            <div class="mb-1"><strong>Service:</strong> {{ $appt->service }}</div>
                            @if($appt->notes)<div class="text-muted mb-2">{{ $appt->notes }}</div>@endif
                            @if($appt->status === 'confirmed')
                            <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="completed">
                                <button class="btn btn-sm btn-outline-success w-100"
                                    onclick="return confirm('Mark as completed?')">Complete</button>
                            </form>
                            @endif
                        </div>
                        @empty
                        <p class="text-muted text-center py-3 mb-0">No appointments scheduled for today.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- All Appointments with Filter --}}
            <div class="card border">
                <div class="card-body p-3">
                    <div class="mb-3">
                        <span class="fw-semibold">All appointments</span>
                    </div>

                    <form method="GET" action="{{ route('dentist.appointments') }}"
                          class="d-flex gap-2 mb-3 flex-wrap align-items-end">
                        <div class="flex-fill" style="min-width:140px;">
                            <label class="form-label small mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All statuses</option>
                                @foreach(['pending','confirmed','completed','cancelled'] as $s)
                                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-fill" style="min-width:140px;">
                            <label class="form-label small mb-1">Date</label>
                            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Filter</button>
                        <a href="{{ route('dentist.appointments') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    </form>

                    {{-- Desktop --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th><th>Service</th><th>Date &amp; Time</th>
                                    <th>Status</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appt)
                                @php $badge = ['pending'=>'bg-warning text-dark','confirmed'=>'bg-success','completed'=>'bg-primary','cancelled'=>'bg-danger']; @endphp
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $appt->user->full_name }}</div>
                                        <div class="text-muted" style="font-size:12px;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                                    </td>
                                    <td>{{ $appt->service }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}<br>
                                        <span class="text-muted" style="font-size:12px;">{{ \Carbon\Carbon::createFromFormat('H:i:s', $appt->appointment_time)->format('g:i A') }}</span>
                                        @if($appt->rescheduled_from)
                                        <div class="text-warning" style="font-size:11px;">↻ Rescheduled</div>
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $badge[$appt->status] ?? 'bg-secondary' }}">{{ ucfirst($appt->status) }}</span></td>
                                    <td>
                                        @if(in_array($appt->status, ['pending','confirmed']))
                                        <div class="d-flex gap-1 flex-wrap">
                                            @if($appt->status === 'pending')
                                            <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="confirmed">
                                                <button class="btn btn-success btn-sm">Accept</button>
                                            </form>
                                            @endif
                                            @if($appt->status === 'confirmed')
                                            <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="completed">
                                                <button class="btn btn-outline-success btn-sm"
                                                    onclick="return confirm('Mark as completed?')">Complete</button>
                                            </form>
                                            @endif
                                            <button class="btn btn-outline-secondary btn-sm"
                                                onclick="openReschedModal('{{ $appt->id }}', '{{ $appt->appointment_date }}', '{{ substr($appt->appointment_time,0,5) }}')">
                                                Reschedule
                                            </button>
                                            <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="cancelled">
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Decline this appointment?')">Decline</button>
                                            </form>
                                        </div>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No appointments found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile --}}
                    <div class="d-md-none d-flex flex-column gap-2">
                        @forelse($appointments as $appt)
                        @php $badge = ['pending'=>'bg-warning text-dark','confirmed'=>'bg-success','completed'=>'bg-primary','cancelled'=>'bg-danger']; @endphp
                        <div class="border rounded-2 p-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ $appt->user->full_name }}</span>
                                <span class="badge {{ $badge[$appt->status] ?? 'bg-secondary' }}">{{ ucfirst($appt->status) }}</span>
                            </div>
                            <div class="text-muted mb-1" style="font-size:12px;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                            <div class="mb-1"><strong>Service:</strong> {{ $appt->service }}</div>
                            <div class="mb-2">
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}
                                · {{ \Carbon\Carbon::createFromFormat('H:i:s', $appt->appointment_time)->format('g:i A') }}
                                @if($appt->rescheduled_from)<span class="text-warning ms-1" style="font-size:11px;">↻ Rescheduled</span>@endif
                            </div>
                            @if(in_array($appt->status, ['pending','confirmed']))
                            <div class="d-flex gap-1 flex-wrap">
                                @if($appt->status === 'pending')
                                <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="confirmed">
                                    <button class="btn btn-success btn-sm">Accept</button>
                                </form>
                                @endif
                                @if($appt->status === 'confirmed')
                                <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="completed">
                                    <button class="btn btn-outline-success btn-sm"
                                        onclick="return confirm('Mark as completed?')">Complete</button>
                                </form>
                                @endif
                                <button class="btn btn-outline-secondary btn-sm"
                                    onclick="openReschedModal('{{ $appt->id }}', '{{ $appt->appointment_date }}', '{{ substr($appt->appointment_time,0,5) }}')">
                                    Reschedule
                                </button>
                                <form method="POST" action="{{ route('dentist.appointments.action', $appt) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="cancelled">
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Decline?')">Decline</button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @empty
                        <p class="text-muted text-center py-3 mb-0">No appointments found.</p>
                        @endforelse
                    </div>

                    @if($appointments->hasPages())
                    <div class="pt-3 border-top mt-2">
                        {{ $appointments->withQueryString()->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Reschedule Modal --}}
<div class="modal fade" id="reschedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Reschedule appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <p class="text-muted small px-3 mb-0">Pick a new date and time for this patient.</p>
            <div class="modal-body">
                <form method="POST" id="reschedForm">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="rescheduled">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">New date</label>
                        <input type="date" name="new_date" id="reschedDate" class="form-control" required
                               min="{{ now()->addDay()->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">New time</label>
                        <select name="new_time" id="reschedTime" class="form-select" required>
                            @foreach(['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00'] as $t)
                            <option value="{{ $t }}">{{ \Carbon\Carbon::createFromFormat('H:i', $t)->format('g:i A') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Reason <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="reschedule_reason" class="form-control" rows="2"
                                  placeholder="Let the patient know why..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-fill">Confirm reschedule</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="sc-overlay" id="scOverlay"></div>

<script>
function openReschedModal(id, date, time) {
    document.getElementById('reschedForm').action = `/dentist/appointments/${id}/action`;
    document.getElementById('reschedDate').value = date;
    document.getElementById('reschedTime').value = time;
    new bootstrap.Modal(document.getElementById('reschedModal')).show();
}

(function () {
    const hamburger = document.getElementById('scHamburger');
    const overlay   = document.getElementById('scOverlay');
    const sidebar   = document.querySelector('.sc-sidebar, [class*="sidebar"]');

    function openSidebar()  { sidebar?.classList.add('open');    overlay?.classList.add('active'); }
    function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('active'); }

    hamburger?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);
})();
</script>
@endsection 
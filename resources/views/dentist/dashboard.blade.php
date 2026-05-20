@extends('layouts.app')
@section('title', 'My Dashboard — SmileCare Dental')

@push('styles')
<style>
/* Mobile sidebar burger */
.sc-sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 199;
    cursor: pointer;
}
.sc-sidebar-overlay.active { display: block; }

.sc-hamburger {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    border-radius: 6px;
    color: #444;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.sc-hamburger:hover { background: #f0f0ee; }

@media (max-width: 768px) {
    /* Push sidebar off-screen and make it fixed */
    .d-flex > [class*="sidebar"],
    .d-flex > div[style*="width:220px"],
    .d-flex > div[style*="width: 220px"] {
        position: fixed !important;
        top: 0;
        left: 0;
        height: 100% !important;
        z-index: 200;
        transform: translateX(-100%);
        transition: transform .25s ease;
    }
    .d-flex > [class*="sidebar"].sidebar-open,
    .d-flex > div[style*="width:220px"].sidebar-open,
    .d-flex > div[style*="width: 220px"].sidebar-open {
        transform: translateX(0);
    }
    .sc-hamburger { display: inline-flex !important; }
}
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height:100vh;">

    @include('dentist.partials.sidebar', ['active' => 'dashboard'])

    <div class="flex-grow-1 d-flex flex-column overflow-auto">

        {{-- Topbar --}}
        <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 bg-white border-bottom sticky-top">
            <div class="d-flex align-items-center gap-2">
                <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="fw-semibold fs-6">My Dashboard</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-sm-inline">Dr. {{ Auth::user()->full_name }}</span>
                <span class="badge bg-primary">Dentist</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Sign out</button>
                </form>
            </div>
        </div>

        <div class="p-3 p-md-4">

            {{-- Stats --}}
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-3">
                    <div class="card border h-100 p-3 text-center">
                        <div class="text-muted small mb-1">Today's appointments</div>
                        <div class="fs-3 fw-bold">{{ $stats['today'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border h-100 p-3 text-center">
                        <div class="text-muted small mb-1">My patients</div>
                        <div class="fs-3 fw-bold">{{ $stats['patients'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border h-100 p-3 text-center">
                        <div class="text-muted small mb-1">Pending</div>
                        <div class="fs-3 fw-bold text-warning">{{ $stats['pending'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border h-100 p-3 text-center">
                        <div class="text-muted small mb-1">Completed</div>
                        <div class="fs-3 fw-bold text-success">{{ $stats['completed'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Pending Appointments --}}
            @if($pendingAppointments->isNotEmpty())
            <div class="card border border-warning border-start border-start-4 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">⏳ Awaiting your response</span>
                        <a href="{{ route('dentist.appointments') }}?status=pending"
                           class="btn btn-outline-secondary btn-sm">View all</a>
                    </div>

                    {{-- Desktop --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th><th>Service</th>
                                    <th>Date &amp; Time</th><th>Actions</th>
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
                                        {{ $appt->formatted_date }}<br>
                                        <span class="text-muted" style="font-size:12px;">{{ $appt->formatted_time }}</span>
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
                                                onclick="openReschedModal('{{ $appt->id }}', '{{ $appt->appointment_date }}', '{{ $appt->appointment_time }}')">
                                                Reschedule
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile --}}
                    <div class="d-md-none d-flex flex-column gap-2">
                        @foreach($pendingAppointments as $appt)
                        <div class="border rounded-2 p-3 small">
                            <div class="fw-medium mb-1">{{ $appt->user->full_name }}</div>
                            <div class="text-muted mb-1" style="font-size:12px;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                            <div class="mb-1"><strong>Service:</strong> {{ $appt->service }}</div>
                            <div class="mb-2"><strong>Date:</strong> {{ $appt->formatted_date }} · {{ $appt->formatted_time }}</div>
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
                                        onclick="return confirm('Decline?')">Decline</button>
                                </form>
                                <button class="btn btn-outline-secondary btn-sm"
                                    onclick="openReschedModal('{{ $appt->id }}', '{{ $appt->appointment_date }}', '{{ $appt->appointment_time }}')">
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
            <div class="card border">
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
                                    <th>Status</th><th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayAppointments as $appt)
                                <tr>
                                    <td class="fw-semibold text-nowrap">{{ $appt->formatted_time }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $appt->user->full_name }}</div>
                                        <div class="text-muted" style="font-size:12px;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                                    </td>
                                    <td>{{ $appt->service }}</td>
                                    <td><span class="badge {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span></td>
                                    <td class="text-muted" style="max-width:160px;">{{ $appt->notes ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No appointments scheduled for today.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile --}}
                    <div class="d-md-none d-flex flex-column gap-2">
                        @forelse($todayAppointments as $appt)
                        <div class="border rounded-2 p-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold">{{ $appt->formatted_time }}</span>
                                <span class="badge {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                            </div>
                            <div class="fw-medium">{{ $appt->user->full_name }}</div>
                            <div class="text-muted mb-1" style="font-size:12px;">{{ $appt->user->phone ?? $appt->user->email }}</div>
                            <div><strong>Service:</strong> {{ $appt->service }}</div>
                            @if($appt->notes)<div class="text-muted mt-1">{{ $appt->notes }}</div>@endif
                        </div>
                        @empty
                        <p class="text-muted text-center py-3 mb-0">No appointments scheduled for today.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="sc-sidebar-overlay" id="scOverlay"></div>

{{-- Reschedule Modal --}}
<div class="modal fade" id="reschedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Reschedule appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
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
    // Target the sidebar — first child div of the outer flex wrapper
    const sidebar   = document.querySelector('.d-flex[style*="min-height"] > div:first-child');

    function openSidebar()  { sidebar?.classList.add('sidebar-open');    overlay?.classList.add('active'); }
    function closeSidebar() { sidebar?.classList.remove('sidebar-open'); overlay?.classList.remove('active'); }

    hamburger?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);
})();
</script>
@endsection
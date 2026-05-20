@extends('layouts.app')
@section('title', 'My Patients — SmileCare Dental')

@push('styles')
<style>
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
    .d-flex > div:first-child[style*="width"] {
        position: fixed !important;
        top: 0;
        left: 0;
        height: 100% !important;
        z-index: 200;
        transform: translateX(-100%);
        transition: transform .25s ease;
    }
    .d-flex > div:first-child[style*="width"].sidebar-open {
        transform: translateX(0);
    }
    .sc-hamburger { display: inline-flex !important; }
}
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height:100vh;">

    @include('dentist.partials.sidebar', ['active' => 'patients'])

    <div class="flex-grow-1 d-flex flex-column overflow-auto">

        {{-- Topbar --}}
        <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 bg-white border-bottom sticky-top">
            <div class="d-flex align-items-center gap-2">
                <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="fw-semibold fs-6">My Patients</span>
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
            <div class="card border">
                <div class="card-body p-3">
                    <div class="mb-3">
                        <span class="fw-semibold">Patients assigned to me ({{ $patients->total() }})</span>
                    </div>

                    {{-- Desktop table --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Services received</th>
                                    <th>Last visit</th>
                                    <th>Upcoming</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patients as $patient)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                                 style="width:34px;height:34px;background:#E1F5EE;font-size:12px;color:#0F6E56;">
                                                {{ strtoupper(substr($patient->first_name,0,1).substr($patient->last_name,0,1)) }}
                                            </div>
                                            <span class="fw-medium">{{ $patient->full_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted">{{ $patient->email }}</div>
                                        <div class="text-muted" style="font-size:12px;">{{ $patient->phone ?? '—' }}</div>
                                    </td>
                                    <td>
                                        @foreach($patient->myAppointments->pluck('service')->unique() as $svc)
                                        <span class="badge bg-primary me-1 mb-1">{{ $svc }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-muted">
                                        @if($patient->myAppointments->where('status','completed')->isNotEmpty())
                                            {{ $patient->myAppointments->where('status','completed')->sortByDesc('appointment_date')->first()->formatted_date }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $upcoming = $patient->myAppointments->whereIn('status',['pending','confirmed'])->sortBy('appointment_date')->first(); @endphp
                                        @if($upcoming)
                                        <span class="text-success fw-medium">{{ $upcoming->formatted_date }}</span><br>
                                        <span class="text-muted" style="font-size:12px;">{{ $upcoming->formatted_time }}</span>
                                        @else
                                        <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No patients yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="d-md-none d-flex flex-column gap-2">
                        @forelse($patients as $patient)
                        @php $upcoming = $patient->myAppointments->whereIn('status',['pending','confirmed'])->sortBy('appointment_date')->first(); @endphp
                        <div class="border rounded-2 p-3 small">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                     style="width:34px;height:34px;background:#E1F5EE;font-size:12px;color:#0F6E56;">
                                    {{ strtoupper(substr($patient->first_name,0,1).substr($patient->last_name,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $patient->full_name }}</div>
                                    <div class="text-muted" style="font-size:12px;">{{ $patient->email }}</div>
                                    @if($patient->phone)<div class="text-muted" style="font-size:12px;">{{ $patient->phone }}</div>@endif
                                </div>
                            </div>
                            <div class="mb-2">
                                @foreach($patient->myAppointments->pluck('service')->unique() as $svc)
                                <span class="badge bg-primary me-1 mb-1">{{ $svc }}</span>
                                @endforeach
                            </div>
                            <div class="d-flex gap-3">
                                <div>
                                    <div class="text-muted mb-0" style="font-size:11px;">LAST VISIT</div>
                                    <div>
                                        @if($patient->myAppointments->where('status','completed')->isNotEmpty())
                                            {{ $patient->myAppointments->where('status','completed')->sortByDesc('appointment_date')->first()->formatted_date }}
                                        @else —
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="text-muted mb-0" style="font-size:11px;">UPCOMING</div>
                                    <div>
                                        @if($upcoming)
                                        <span class="text-success fw-medium">{{ $upcoming->formatted_date }}</span>
                                        <span class="text-muted"> · {{ $upcoming->formatted_time }}</span>
                                        @else <span class="text-muted">None</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3 mb-0">No patients yet.</p>
                        @endforelse
                    </div>

                    @if($patients->hasPages())
                    <div class="pt-3 border-top mt-2">
                        {{ $patients->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<div class="sc-sidebar-overlay" id="scOverlay"></div>

@push('scripts')
<script>
(function () {
    const hamburger = document.getElementById('scHamburger');
    const overlay   = document.getElementById('scOverlay');
    const sidebar   = document.querySelector('.d-flex[style*="min-height"] > div:first-child');

    function openSidebar()  { sidebar?.classList.add('sidebar-open');    overlay?.classList.add('active'); }
    function closeSidebar() { sidebar?.classList.remove('sidebar-open'); overlay?.classList.remove('active'); }

    hamburger?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);
})();
</script>
@endpush
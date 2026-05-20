@extends('layouts.app')
@section('title', 'My Appointments — SmileCare Dental')

@section('content')

<nav class="navbar navbar-expand-lg px-3 px-md-4 py-2 shadow-sm bg-white border-bottom">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-success" href="#">
        <span class="d-flex align-items-center justify-content-center rounded-circle bg-success"
              style="width:32px;height:32px;flex-shrink:0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 4.17 4.42 9.92 6.24 12.11a1 1 0 001.53 0C14.58 18.92 19 13.17 19 9c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/>
            </svg>
        </span>
        SmileCare Dental
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTop">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarTop">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2 mt-2 mt-lg-0">
            <span class="text-muted small">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
            <span class="badge bg-primary">Patient</span>
            <a href="{{ route('profile') }}" class="btn btn-outline-secondary btn-sm">My Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">Sign out</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">

    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 small py-2" role="alert">
        ✓ {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2 small py-2" role="alert">
        ✕ {{ session('error') }}
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="h5 fw-semibold mb-0">My Appointments</h2>
        <a href="{{ route('appointments.create') }}" class="btn btn-success btn-sm">+ Book appointment</a>
    </div>

    {{-- Desktop table --}}
    <div class="card border d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small text-muted fw-semibold">Service</th>
                        <th class="small text-muted fw-semibold">Dentist</th>
                        <th class="small text-muted fw-semibold">Date</th>
                        <th class="small text-muted fw-semibold">Time</th>
                        <th class="small text-muted fw-semibold">Status</th>
                        <th class="small text-muted fw-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td class="small">{{ $appt->service }}</td>
                        <td class="small">{{ $appt->dentist->name ?? '—' }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</td>
                        <td class="small">{{ \Carbon\Carbon::createFromFormat('H:i:s', $appt->appointment_time)->format('g:i A') }}</td>
                        <td>
                            @php
                                $badgeMap = [
                                    'pending'   => 'bg-warning text-dark',
                                    'confirmed' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    'declined'  => 'bg-danger',
                                    'completed' => 'bg-primary',
                                ];
                            @endphp
                            <span class="badge {{ $badgeMap[$appt->status] ?? 'bg-secondary' }}">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </td>
                        <td>
                            @if($appt->status === 'pending')
                            <form method="POST" action="{{ route('appointments.cancel', $appt) }}" class="m-0">
                                @csrf @method('PATCH')
                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Cancel this appointment?')">Cancel</button>
                            </form>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No appointments yet. <a href="{{ route('appointments.create') }}">Book one now</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile cards --}}
    <div class="d-md-none d-flex flex-column gap-3">
        @forelse($appointments as $appt)
        @php
            $badgeMap = [
                'pending'   => 'bg-warning text-dark',
                'confirmed' => 'bg-success',
                'cancelled' => 'bg-danger',
                'declined'  => 'bg-danger',
                'completed' => 'bg-primary',
            ];
        @endphp
        <div class="card border p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="fw-semibold small">{{ $appt->service }}</div>
                <span class="badge {{ $badgeMap[$appt->status] ?? 'bg-secondary' }}">
                    {{ ucfirst($appt->status) }}
                </span>
            </div>
            <div class="text-muted small mb-1">
                <strong>Dentist:</strong> {{ $appt->dentist->name ?? '—' }}
            </div>
            <div class="text-muted small mb-1">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}
            </div>
            <div class="text-muted small mb-2">
                <strong>Time:</strong> {{ \Carbon\Carbon::createFromFormat('H:i:s', $appt->appointment_time)->format('g:i A') }}
            </div>
            @if($appt->status === 'pending')
            <form method="POST" action="{{ route('appointments.cancel', $appt) }}" class="m-0">
                @csrf @method('PATCH')
                <button class="btn btn-danger btn-sm w-100"
                        onclick="return confirm('Cancel this appointment?')">Cancel</button>
            </form>
            @endif
        </div>
        @empty
        <div class="text-center text-muted py-4">
            No appointments yet. <a href="{{ route('appointments.create') }}">Book one now</a>.
        </div>
        @endforelse
    </div>

</div>
@endsection
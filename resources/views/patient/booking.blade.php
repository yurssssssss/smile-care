@extends('layouts.app')
@section('title', 'Book Appointment — SmileCare Dental')

@section('content')

{{-- Topbar --}}
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
            <a href="{{ route('patient.home') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width:640px;">

    {{-- Step Indicator --}}
    <div class="d-flex rounded overflow-hidden border mb-4">
        @php $steps = ['1. Service', '2. Schedule', '3. Confirm']; @endphp
        @foreach($steps as $i => $label)
            <div class="flex-fill text-center py-2 small fw-medium
                {{ $step === $i+1 ? 'bg-success text-white' : ($step > $i+1 ? 'text-success' : 'text-muted bg-white') }}"
                 style="{{ $step > $i+1 ? 'background:#E1F5EE;' : '' }}">
                {{ $label }}
            </div>
        @endforeach
    </div>

    {{-- ── STEP 1: Choose service ── --}}
    @if($step === 1)
    <h2 class="h5 fw-semibold mb-3">Select a service</h2>
    <form method="POST" action="{{ route('appointments.step', 1) }}">
        @csrf
        <div class="d-flex flex-column gap-2 mb-3">
            @foreach([
                ['General Checkup',  'Oral exam, cleaning, X-ray if needed'],
                ['Tooth Extraction', 'Simple or surgical extraction'],
                ['Teeth Whitening',  'Professional in-clinic treatment'],
                ['Dental Filling',   'Composite or amalgam filling'],
            ] as [$svc, $desc])
            <label class="svc-label d-flex align-items-center gap-3 border rounded-3 p-3"
                   style="cursor:pointer;transition:.15s;border-color:{{ old('service') === $svc ? '#0F6E56' : '#dee2e6' }} !important;">
                <input type="radio" name="service" value="{{ $svc }}"
                       class="form-check-input mt-0 flex-shrink-0"
                       style="accent-color:#0F6E56;"
                       {{ old('service') === $svc ? 'checked' : '' }} required>
                <div>
                    <div class="fw-semibold small">{{ $svc }}</div>
                    <div class="text-muted" style="font-size:13px;">{{ $desc }}</div>
                </div>
            </label>
            @endforeach
        </div>

        @error('service')
            <p class="text-danger small mb-2">{{ $message }}</p>
        @enderror

        <div class="mb-3">
            <label class="form-label">Describe your concern <span class="text-muted fw-normal">(optional)</span></label>
            <textarea name="notes" class="form-control" rows="3"
                      placeholder="e.g. I have a toothache on my upper right molar...">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">
            Next: Choose schedule →
        </button>
    </form>
    @endif

    {{-- ── STEP 2: Choose date, dentist, time ── --}}
    @if($step === 2)
    <h2 class="h5 fw-semibold mb-3">Choose date &amp; time</h2>
    <form method="POST" action="{{ route('appointments.step', 2) }}" id="scheduleForm">
        @csrf
        <input type="hidden" name="service" value="{{ session('booking.service') }}">
        <input type="hidden" name="notes"   value="{{ session('booking.notes') }}">

        <div class="mb-3">
            <label class="form-label">Preferred date</label>
            <input type="date" name="appointment_date" id="dateInput" class="form-control"
                   value="{{ old('appointment_date', date('Y-m-d', strtotime('+1 day'))) }}"
                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
            @error('appointment_date')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Select dentist</label>
            <select name="dentist_id" id="dentistSelect" class="form-select" required
                    onchange="loadAvailability()">
                <option value="">— Choose a dentist —</option>
                @foreach($dentists as $dentist)
                <option value="{{ $dentist->id }}"
                        data-specialization="{{ $dentist->specialization }}"
                        {{ old('dentist_id') == $dentist->id ? 'selected' : '' }}>
                    {{ $dentist->name }}{{ $dentist->specialization ? ' — '.$dentist->specialization : '' }}
                </option>
                @endforeach
            </select>
            @error('dentist_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Dentist busy-date notice --}}
        <div id="unavailableNotice" class="alert alert-warning d-none p-2 small" role="alert">
            <strong>ℹ️ Heads up:</strong> This dentist already has patients on
            <span id="unavailableDates"></span>. You can still book on those days but slots may be limited.
        </div>

        <div class="mb-3">
            <label class="form-label">Available time slots</label>
            <div class="row g-2" id="timeSlots">
                @php
                    $times = ['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00'];
                    $takenTimes = old('appointment_date') && old('dentist_id')
                        ? \App\Models\Appointment::where('dentist_id', old('dentist_id'))
                            ->where('appointment_date', old('appointment_date'))
                            ->whereIn('status',['pending','confirmed'])
                            ->pluck('appointment_time')
                            ->map(fn($t) => substr($t,0,5))
                            ->toArray()
                        : [];
                @endphp
                @foreach($times as $t)
                @php $taken = in_array($t, $takenTimes); @endphp
                <div class="col-6 col-sm-3">
                    <label class="time-slot d-flex align-items-center justify-content-center w-100 rounded-2 border py-2 small fw-medium
                        {{ $taken ? 'time-slot-taken text-muted bg-light' : '' }}"
                        style="cursor:{{ $taken ? 'not-allowed' : 'pointer' }};
                               border-color:{{ !$taken && old('appointment_time')===$t ? '#0F6E56' : '#dee2e6' }} !important;
                               background:{{ old('appointment_time')===$t && !$taken ? '#0F6E56' : '' }} !important;
                               color:{{ old('appointment_time')===$t && !$taken ? 'white' : '' }};">
                        <input type="radio" name="appointment_time" value="{{ $t }}"
                               style="display:none;" {{ $taken ? 'disabled' : '' }}
                               {{ old('appointment_time') === $t ? 'checked' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('H:i', $t)->format('g:i A') }}
                    </label>
                </div>
                @endforeach
            </div>
            <p class="text-muted mt-1" style="font-size:12px;">Grayed slots are already taken.</p>
            @error('appointment_time')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('appointments.create') }}" class="btn btn-outline-secondary">← Back</a>
            <button type="submit" class="btn btn-success flex-fill">Next: Review →</button>
        </div>
    </form>
    @endif

    {{-- ── STEP 3: Confirm ── --}}
    @if($step === 3)
    <h2 class="h5 fw-semibold mb-3">Review &amp; confirm</h2>

    <div class="card border mb-3 p-3">
        <dl class="row mb-0 small">
            <dt class="col-4 text-muted fw-normal">Service</dt>
            <dd class="col-8 fw-semibold">{{ session('booking.service') }}</dd>

            <dt class="col-4 text-muted fw-normal">Dentist</dt>
            <dd class="col-8 fw-semibold">{{ $dentist->name ?? '—' }}</dd>

            <dt class="col-4 text-muted fw-normal">Date</dt>
            <dd class="col-8 fw-semibold">
                {{ \Carbon\Carbon::parse(session('booking.appointment_date'))->format('F d, Y') }}
            </dd>

            <dt class="col-4 text-muted fw-normal">Time</dt>
            <dd class="col-8 fw-semibold">
                {{ \Carbon\Carbon::createFromFormat('H:i', session('booking.appointment_time'))->format('g:i A') }}
            </dd>

            @if(session('booking.notes'))
            <dt class="col-4 text-muted fw-normal">Notes</dt>
            <dd class="col-8">{{ session('booking.notes') }}</dd>
            @endif
        </dl>
    </div>

    <form method="POST" action="{{ route('appointments.store') }}">
        @csrf
        <input type="hidden" name="service"          value="{{ session('booking.service') }}">
        <input type="hidden" name="dentist_id"        value="{{ session('booking.dentist_id') }}">
        <input type="hidden" name="appointment_date"  value="{{ session('booking.appointment_date') }}">
        <input type="hidden" name="appointment_time"  value="{{ session('booking.appointment_time') }}">
        <input type="hidden" name="concern"           value="{{ session('booking.notes') }}">
        <button type="submit" class="btn btn-success w-100 py-3">
            ✓ Confirm booking
        </button>
    </form>

    <form method="POST" action="{{ route('appointments.step', 2) }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-outline-secondary w-100">
            ← Change schedule
        </button>
    </form>
    @endif

</div>

<script>
const dentistBusyDates = @json($dentistBusyDates ?? []);

document.getElementById('dateInput')?.addEventListener('change', loadAvailability);

function loadAvailability() {
    const dentistId = document.getElementById('dentistSelect')?.value;
    const date      = document.getElementById('dateInput')?.value;
    if (!dentistId || !date) return;

    const busyDates = dentistBusyDates[dentistId] || [];
    const notice    = document.getElementById('unavailableNotice');
    const datesSpan = document.getElementById('unavailableDates');

    if (busyDates.length > 0) {
        const formatted = busyDates.map(d => {
            const [y,m,day] = d.split('-');
            return new Date(y, m-1, day).toLocaleDateString('en-PH', {month:'short',day:'numeric'});
        }).join(', ');
        datesSpan.textContent = formatted;
        notice.classList.remove('d-none');
    } else {
        notice.classList.add('d-none');
    }

    fetch(`/appointments/slots?dentist_id=${dentistId}&date=${date}`)
        .then(r => r.json())
        .then(data => {
            const takenSlots = data.taken || [];
            document.querySelectorAll('.time-slot').forEach(label => {
                const input  = label.querySelector('input');
                const isTaken = takenSlots.includes(input.value);
                label.style.cursor      = isTaken ? 'not-allowed' : 'pointer';
                label.style.color       = isTaken ? '#9ca3af' : '#333';
                label.style.background  = isTaken ? '#f9fafb' : 'white';
                label.style.borderColor = isTaken ? '#e5e7eb' : '#dee2e6';
                input.disabled          = isTaken;
                if (isTaken && input.checked) input.checked = false;
            });
        })
        .catch(() => {});
}

document.querySelectorAll('.time-slot').forEach(label => {
    label.addEventListener('click', function () {
        if (this.querySelector('input').disabled) return;
        document.querySelectorAll('.time-slot:not(.time-slot-taken)').forEach(l => {
            l.style.background  = 'white';
            l.style.color       = '#333';
            l.style.borderColor = '#dee2e6';
        });
        this.style.background  = '#0F6E56';
        this.style.color       = 'white';
        this.style.borderColor = '#0F6E56';
    });
});

// Service card highlight
document.querySelectorAll('.svc-label').forEach(label => {
    label.addEventListener('click', function () {
        document.querySelectorAll('.svc-label').forEach(l => l.style.borderColor = '#dee2e6');
        this.style.borderColor = '#0F6E56';
    });
});

if (document.getElementById('dentistSelect')?.value) loadAvailability();
</script>
@endsection
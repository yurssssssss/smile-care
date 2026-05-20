@extends('layouts.app')
@section('title', 'Book Appointment — SmileCare Dental')

@section('content')

<nav class="topbar">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ route('patient.home') }}" style="font-size:13px;color:#888;">← Back</a>
        <span style="font-size:15px;font-weight:600;">Book a consultation</span>
    </div>
    <span style="font-size:13px;color:#888;">SmileCare Dental</span>
</nav>

<div class="container" style="max-width:580px;">

    <form method="POST" action="{{ route('appointments.store') }}" id="appt-form">
        @csrf

        {{-- Step tabs --}}
        <div class="steps">
            <div class="step active" id="tab-1">1. Service</div>
            <div class="step"        id="tab-2">2. Schedule</div>
            <div class="step"        id="tab-3">3. Confirm</div>
        </div>

        {{-- ── STEP 1: Service ── --}}
        <div id="step-1">
            <p style="font-size:15px;font-weight:600;margin-bottom:14px;">Select a service</p>

            @foreach($services as $service)
            <label style="display:flex;align-items:center;gap:12px;cursor:pointer;margin-bottom:10px;">
                <input type="radio" name="service" value="{{ $service }}" style="width:auto;flex-shrink:0;"
                    {{ old('service') === $service ? 'checked' : '' }} required>
                <div class="card" style="flex:1;margin:0;padding:13px 16px;cursor:pointer;">
                    <span style="font-size:14px;font-weight:600;">{{ $service }}</span>
                    <span style="font-size:12px;color:#888;display:block;margin-top:2px;">
                        {{ match($service) {
                            'General Checkup'  => 'Oral exam, cleaning, X-ray if needed',
                            'Tooth Extraction' => 'Simple or surgical extraction',
                            'Teeth Whitening'  => 'Professional in-clinic treatment',
                            'Dental Filling'   => 'Composite or amalgam filling',
                            default            => ''
                        } }}
                    </span>
                </div>
            </label>
            @endforeach

            <div class="form-group" style="margin-top:18px;">
                <label for="concern">Describe your concern <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                <textarea name="concern" id="concern" class="form-control"
                    placeholder="e.g. I have a toothache on my upper right molar...">{{ old('concern') }}</textarea>
            </div>

            <button type="button" class="btn btn-primary" onclick="nextStep(2)"
                style="width:100%;justify-content:center;">Next: Choose schedule →</button>
        </div>

        {{-- ── STEP 2: Schedule ── --}}
        <div id="step-2" style="display:none;">
            <p style="font-size:15px;font-weight:600;margin-bottom:14px;">Choose date &amp; time</p>

            <div class="form-group">
                <label>Preferred date</label>
                <input type="date" name="appointment_date" id="appointment_date" class="form-control"
                    min="{{ date('Y-m-d') }}" value="{{ old('appointment_date', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label>Select dentist</label>
                <select name="dentist_id" id="dentist_id" class="form-control" required>
                    <option value="">— Choose a dentist —</option>
                    @foreach($dentists as $d)
                    <option value="{{ $d->id }}" {{ old('dentist_id') == $d->id ? 'selected' : '' }}>
                        {{ $d->name }}{{ $d->specialization ? ' — '.$d->specialization : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Available time slots</label>
                <div class="time-grid">
                    @foreach(['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00'] as $t)
                    <div class="time-slot" data-time="{{ $t }}" onclick="selectTime('{{ $t }}')">
                        {{ date('g:i A', strtotime($t)) }}
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="appointment_time" id="appointment_time"
                    value="{{ old('appointment_time') }}" required>
                <p style="font-size:12px;color:#aaa;margin-top:8px;">Grayed slots are already taken.</p>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="button" class="btn btn-ghost" onclick="nextStep(1)" style="flex:.4;justify-content:center;">← Back</button>
                <button type="button" class="btn btn-primary" onclick="nextStep(3)" style="flex:1;justify-content:center;">Next: Review →</button>
            </div>
        </div>

        {{-- ── STEP 3: Confirm ── --}}
        <div id="step-3" style="display:none;">
            <p style="font-size:15px;font-weight:600;margin-bottom:14px;">Review &amp; confirm</p>

            <div class="card" style="background:#f5f5f3;border:none;margin-bottom:16px;">
                <table style="width:100%;font-size:14px;">
                    <tr><td style="color:#888;padding:7px 0;width:90px;">Service</td>  <td style="font-weight:600;" id="r-service">—</td></tr>
                    <tr><td style="color:#888;padding:7px 0;">Dentist</td>  <td id="r-dentist">—</td></tr>
                    <tr><td style="color:#888;padding:7px 0;">Date</td>     <td id="r-date">—</td></tr>
                    <tr><td style="color:#888;padding:7px 0;">Time</td>     <td id="r-time">—</td></tr>
                </table>
            </div>

            <div class="card" style="display:flex;gap:12px;align-items:flex-start;margin-bottom:20px;">
                <div style="width:34px;height:34px;background:#E1F5EE;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#0F6E56"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
                <p style="font-size:13px;color:#666;line-height:1.6;">Please arrive 10 minutes before your appointment. Bring a valid ID. You may cancel up to 24 hours in advance.</p>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="button" class="btn btn-ghost" onclick="nextStep(2)" style="flex:.4;justify-content:center;">← Back</button>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Confirm booking ✓</button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;

function nextStep(n) {
    if (n === 2) {
        const service = document.querySelector('input[name="service"]:checked');
        if (!service) { Toast.warning('Select a service', 'Please choose a service before continuing.'); return; }
    }
    if (n === 3) {
        const dentist = document.getElementById('dentist_id').value;
        const time    = document.getElementById('appointment_time').value;
        if (!dentist) { Toast.warning('Select a dentist', 'Please choose a dentist before continuing.'); return; }
        if (!time)    { Toast.warning('Select a time', 'Please pick an available time slot.'); return; }
        fillReview();
    }
    document.getElementById('step-' + currentStep).style.display = 'none';
    document.getElementById('step-' + n).style.display = 'block';
    ['1','2','3'].forEach(i => {
        const tab = document.getElementById('tab-' + i);
        tab.className = 'step' + (i == n ? ' active' : (i < n ? ' done' : ''));
    });
    currentStep = n;
    window.scrollTo(0, 0);
}

function selectTime(t) {
    document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
    const el = document.querySelector(`.time-slot[data-time="${t}"]`);
    if (el && !el.classList.contains('taken')) {
        el.classList.add('selected');
        document.getElementById('appointment_time').value = t;
    }
}

function fillReview() {
    const serviceEl = document.querySelector('input[name="service"]:checked');
    const dentistEl = document.getElementById('dentist_id');
    const dateEl    = document.getElementById('appointment_date');
    const timeVal   = document.getElementById('appointment_time').value;
    document.getElementById('r-service').textContent = serviceEl ? serviceEl.value : '—';
    document.getElementById('r-dentist').textContent = dentistEl.options[dentistEl.selectedIndex]?.text || '—';
    document.getElementById('r-date').textContent    = dateEl.value || '—';
    document.getElementById('r-time').textContent    = timeVal
        ? new Date('1970-01-01T' + timeVal).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })
        : '—';
}

async function loadTakenSlots() {
    const dentistId = document.getElementById('dentist_id').value;
    const date      = document.getElementById('appointment_date').value;
    if (!dentistId || !date) return;

    try {
        const res  = await fetch(`/api/taken-slots?dentist_id=${dentistId}&date=${date}`);
        const data = await res.json();
        document.querySelectorAll('.time-slot').forEach(el => {
            el.classList.remove('taken', 'selected');
            if (data.includes(el.dataset.time)) el.classList.add('taken');
        });
        document.getElementById('appointment_time').value = '';
    } catch (e) {
        Toast.error('Error', 'Could not load time slots. Please try again.');
    }
}

document.getElementById('dentist_id').addEventListener('change', loadTakenSlots);
document.getElementById('appointment_date').addEventListener('change', loadTakenSlots);
</script>
@endpush

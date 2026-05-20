<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dentist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /* ──────────────────────────────────────────
     | PATIENT: list appointments (dashboard)
     ────────────────────────────────────────── */
    public function index()
    {
        $appointments = Auth::user()->appointments()
            ->with('dentist')
            ->orderByDesc('appointment_date')
            ->get();

        return view('patient.dashboard', compact('appointments'));
    }

    /* ──────────────────────────────────────────
     | PATIENT: booking form (step 1)
     ────────────────────────────────────────── */
    public function create()
    {
        $dentists = Dentist::where('is_active', true)->get();

        $dentistBusyDates = $dentists->mapWithKeys(function ($dentist) {
            $dates = Appointment::where('dentist_id', $dentist->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('appointment_date', '>=', today())
                ->pluck('appointment_date')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->unique()
                ->values();
            return [$dentist->id => $dates];
        });

        return view('patient.booking', [
            'step'             => 1,
            'dentists'         => $dentists,
            'dentistBusyDates' => $dentistBusyDates,
        ]);
    }

    /* ──────────────────────────────────────────
     | PATIENT: booking wizard steps 2 & 3
     ────────────────────────────────────────── */
    public function step(Request $request, int $step)
    {
        if ($step === 1) {
            $request->validate(['service' => 'required|string']);
            session([
                'booking.service' => $request->service,
                'booking.notes'   => $request->notes,
            ]);
            $step = 2;
        } elseif ($step === 2) {
            $request->validate([
                'dentist_id'       => 'required|exists:dentists,id',
                'appointment_date' => 'required|date|after:today',
                'appointment_time' => 'required',
            ]);
            session([
                'booking.dentist_id'       => $request->dentist_id,
                'booking.appointment_date' => $request->appointment_date,
                'booking.appointment_time' => $request->appointment_time,
            ]);
            $step = 3;
        }

        $dentists = Dentist::where('is_active', true)->get();
        $dentist  = session('booking.dentist_id')
            ? Dentist::find(session('booking.dentist_id'))
            : null;

        $dentistBusyDates = $dentists->mapWithKeys(function ($d) {
            $dates = Appointment::where('dentist_id', $d->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('appointment_date', '>=', today())
                ->pluck('appointment_date')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->unique()
                ->values();
            return [$d->id => $dates];
        });

        return view('patient.booking', compact('step', 'dentists', 'dentist', 'dentistBusyDates'));
    }

    /* ──────────────────────────────────────────
     | PATIENT: save new appointment
     ────────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'service'          => 'required|in:General Checkup,Tooth Extraction,Teeth Whitening,Dental Filling',
            'dentist_id'       => 'required|exists:dentists,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'concern'          => 'nullable|string|max:1000',
        ]);

        // Double-check the slot isn't already taken
        $conflict = Appointment::where('dentist_id', $request->dentist_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflict) {
            return back()
                ->withErrors(['appointment_time' => 'This time slot is already taken. Please choose another.'])
                ->withInput();
        }

        Appointment::create([
            'user_id'          => Auth::id(),
            'dentist_id'       => $request->dentist_id,
            'service'          => $request->service,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'concern'          => $request->concern,
            'status'           => 'pending',
        ]);

        // Clear booking session
        session()->forget(['booking.service', 'booking.notes', 'booking.dentist_id',
                           'booking.appointment_date', 'booking.appointment_time']);

        return redirect()->route('patient.home')
            ->with('success', 'Appointment booked! We will confirm it shortly.');
    }

    /* ──────────────────────────────────────────
     | PATIENT: cancel own appointment
     ────────────────────────────────────────── */
    public function cancel(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment has been cancelled.');
    }

    /* ──────────────────────────────────────────
     | AJAX: return taken time slots
     | GET /appointments/slots?dentist_id=X&date=Y
     ────────────────────────────────────────── */
    public function takenSlots(Request $request)
    {
        $request->validate([
            'dentist_id' => 'required|exists:dentists,id',
            'date'       => 'required|date',
        ]);

        $slots = Appointment::where('dentist_id', $request->dentist_id)
            ->where('appointment_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->map(fn($t) => substr($t, 0, 5)); // "08:00:00" → "08:00"

        return response()->json(['taken' => $slots->values()]); // ← FIXED
    }
}
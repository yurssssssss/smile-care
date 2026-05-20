<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DentistController extends Controller
{
    private function myDentist()
    {
        return Auth::user()->dentist;
    }

    /* ──────────────────────────────────────────
     | DASHBOARD — redirect to appointments
     ────────────────────────────────────────── */
    public function dashboard()
    {
        return redirect()->route('dentist.appointments');
    }

    /* ──────────────────────────────────────────
     | APPOINTMENTS — main page for dentist
     ────────────────────────────────────────── */
    public function appointments(Request $request)
    {
        $dentist = $this->myDentist();

        // Pending appointments waiting for dentist action
        $pendingAppointments = Appointment::where('dentist_id', $dentist->id)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Today's confirmed/pending appointments
        $todayAppointments = Appointment::where('dentist_id', $dentist->id)
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('user')
            ->orderBy('appointment_time')
            ->get();

        // All appointments with filters
        $query = Appointment::where('dentist_id', $dentist->id)
            ->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query
            ->orderByDesc('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(15);

        return view('dentist.appointments', compact(
            'appointments',
            'pendingAppointments',
            'todayAppointments'
        ));
    }

    /* ──────────────────────────────────────────
     | ACCEPT / DECLINE / COMPLETE / RESCHEDULE
     ────────────────────────────────────────── */
    public function appointmentAction(Request $request, Appointment $appointment)
    {
        abort_if($appointment->dentist_id !== $this->myDentist()->id, 403);

        $action = $request->input('action');

        if ($action === 'rescheduled') {
            $request->validate([
                'new_date' => 'required|date|after:today',
                'new_time' => 'required',
            ]);

            $appointment->update([
                'appointment_date'  => $request->new_date,
                'appointment_time'  => $request->new_time,
                'status'            => 'confirmed',
                'reschedule_reason' => $request->reschedule_reason,
                'rescheduled_from'  => $appointment->appointment_date,
            ]);

            return back()->with('success', 'Appointment rescheduled successfully.');
        }

        $allowed = ['confirmed', 'cancelled', 'completed'];
        abort_if(!in_array($action, $allowed), 422);

        $appointment->update(['status' => $action]);

        $message = match ($action) {
            'confirmed' => 'Appointment accepted.',
            'cancelled' => 'Appointment declined.',
            'completed' => 'Appointment marked as completed.',
        };

        return back()->with('success', $message);
    }

    /* ──────────────────────────────────────────
     | MY PATIENTS
     ────────────────────────────────────────── */
    public function patients()
    {
        $dentist = $this->myDentist();

        $patientIds = Appointment::where('dentist_id', $dentist->id)
            ->distinct('user_id')
            ->pluck('user_id');

        $patients = User::whereIn('id', $patientIds)
            ->with(['appointments' => function ($query) use ($dentist) {
                $query->where('dentist_id', $dentist->id);
            }])
            ->paginate(20);

        return view('dentist.patients', compact('patients'));
    }
}
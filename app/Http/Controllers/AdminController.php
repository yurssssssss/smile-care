<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /* ──────────────────────────────────────────
     | DASHBOARD
     ────────────────────────────────────────── */
    public function dashboard()
    {
        $stats = [
            'today'     => Appointment::whereDate('appointment_date', today())->count(),
            'pending'   => Appointment::where('status', 'pending')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'patients'  => User::where('role', 'patient')->count(),
        ];

        $recent = Appointment::with(['user', 'dentist'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent'));
    }

    /* ──────────────────────────────────────────
     | APPOINTMENTS
     ────────────────────────────────────────── */
    public function appointments(Request $request)
    {
        $query = Appointment::with(['user', 'dentist'])->orderByDesc('appointment_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->paginate(15);

        return view('admin.appointments', compact('appointments'));
    }

 public function updateStatus(Request $request, Appointment $appointment)
{
    if ($appointment->status === 'completed') {
        return back()->with('error', 'Completed appointments cannot be changed.');
    }

    $request->validate([
        'status'      => 'required|in:pending,confirmed,completed,cancelled',
        'admin_notes' => 'nullable|string|max:500',
    ]);

    $appointment->update(['status' => $request->status]);
    return back()->with('success', 'Status updated to ' . ucfirst($request->status) . '.');
}

    /* ──────────────────────────────────────────
     | PATIENTS
     ────────────────────────────────────────── */
    public function patients()
    {
        $patients = User::where('role', 'patient')
            ->withCount('appointments')
            ->orderBy('last_name')
            ->paginate(20);

        return view('admin.patients', compact('patients'));
    }

    /* ──────────────────────────────────────────
     | DENTISTS  (new)
     ────────────────────────────────────────── */

    /** Show all dentists + create dentist form */
    public function dentists()
    {
        $dentists = Dentist::withCount('appointments')->with('user')->get();

        return view('admin.dentist', compact('dentists'));
    }

    /** Add a new dentist record to the dentists table */
    public function storeDentist(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'specialization' => 'nullable|string|max:100',
        ]);

        Dentist::create([
            'name'           => $request->name,
            'specialization' => $request->specialization,
            'is_active'      => true,
        ]);

        return back()->with('success', "Dentist '{$request->name}' added successfully.");
    }

    /** Create a login account (User) for an existing dentist record */
    public function createDentistAccount(Request $request, Dentist $dentist)
    {
        $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($dentist->user_id) {
            return back()->with('error', 'This dentist already has a login account.');
        }

        $user = User::create([
            'first_name' => $dentist->name,
            'last_name'  => '',
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'dentist',
        ]);

        // Link the User to the Dentist row
        $dentist->update(['user_id' => $user->id]);

        return back()->with('success', "Login account created for {$dentist->name}.");
    }

    /** Activate or deactivate a dentist */
    public function toggleDentist(Dentist $dentist)
    {
        $dentist->update(['is_active' => !$dentist->is_active]);

        $status = $dentist->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$dentist->name} has been {$status}.");
    }

    /** Reset a dentist's login password */
    public function resetDentistPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password reset successfully.');
    }
}
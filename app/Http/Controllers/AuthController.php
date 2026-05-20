<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* ──────────────────────────────────────────
     | LOGIN
     ────────────────────────────────────────── */
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on role
            return match (Auth::user()->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'dentist' => redirect()->route('dentist.dashboard'),
                default   => redirect()->route('patient.home'),
            };
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->onlyInput('email');
    }

    /* ──────────────────────────────────────────
     | PATIENT REGISTER
     ────────────────────────────────────────── */
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'phone'      => 'nullable|string|max:20',
            'password'   => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'role'       => 'patient',
        ]);

        Auth::login($user);

        return redirect()->route('patient.home')
            ->with('success', 'Welcome to SmileCare Dental, ' . $user->first_name . '!');
    }

    /* ──────────────────────────────────────────
     | ADMIN REGISTER
     ────────────────────────────────────────── */
    public function showAdminRegister()
    {
        if (Auth::check() && !Auth::user()->isAdmin()) {
            return redirect()->route('patient.home');
        }

        return view('auth.admin-register');
    }

    public function adminRegister(Request $request)
    {
        $adminExists = User::where('role', 'admin')->exists();

        if ($adminExists && (!Auth::check() || !Auth::user()->isAdmin())) {
            abort(403, 'Admin account already exists. Contact your system administrator.');
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'phone'      => 'nullable|string|max:20',
            'password'   => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'role'       => 'admin',
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Admin account created successfully. Welcome, ' . $user->first_name . '!');
    }

    /* ──────────────────────────────────────────
     | LOGOUT
     ────────────────────────────────────────── */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'You have been signed out.');
    }
public function showProfile()
{
    return match(Auth::user()->role) {
        'admin'   => view('admin.profile'),
        'dentist' => view('dentist.profile'),
        default   => view('patient.profile'),
    };
}

public function updateProfile(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'first_name'    => 'required|string|max:100',
        'last_name'     => 'required|string|max:100',
        'email'         => 'required|email|unique:users,email,' . $user->id,
        'phone'         => 'nullable|string|max:20',
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('profile_photo')) {
        // Delete old photo if exists
        if ($user->profile_photo) {
            \Storage::disk('public')->delete($user->profile_photo);
        }
        $path = $request->file('profile_photo')->store('profiles', 'public');
        $user->profile_photo = $path;
    }

    $user->first_name = $request->first_name;
    $user->last_name  = $request->last_name;
    $user->email      = $request->email;
    $user->phone      = $request->phone;
    $user->save();

    return back()->with('success', 'Profile updated successfully.');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'password'         => 'required|min:8|confirmed',
    ]);

    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    Auth::user()->update([
        'password' => Hash::make($request->password),
    ]);

    return back()->with('success', 'Password changed successfully.');
}
}
@extends('layouts.app')
@section('title', 'Admin Setup — SmileCare Dental')

@section('content')
<div class="auth-wrapper" style="min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(160deg,#f0e8fe 0%,#f9f9f7 60%);padding:2rem 1rem;">

    <div class="auth-logo-block" style="text-align:center;margin-bottom:22px;">
        <div style="width:52px;height:52px;background:#533AB7;border-radius:14px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="white"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4a3 3 0 110 6 3 3 0 010-6zm0 14c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08s5.97 1.09 6 3.08C16.71 17.72 14.5 19 12 19z"/></svg>
        </div>
        <h1 style="font-size:22px;font-weight:700;color:#111;margin:0;">Admin Setup</h1>
        <p style="font-size:13px;color:#777;margin-top:3px;">Create the administrator account for SmileCare Dental</p>
    </div>

    <div class="card" style="width:min(420px, 100%);">

        @if(\App\Models\User::where('role','admin')->exists())
        <div style="background:#FAEEDA;border:1px solid #FAC775;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:#854F0B;">
            <strong>Note:</strong> An admin account already exists. You must be logged in as an admin to create additional admin accounts.
        </div>
        @endif

        <h2 style="font-size:17px;font-weight:600;margin-bottom:6px;">Admin registration</h2>
        <p style="font-size:13px;color:#888;margin-bottom:20px;">You need the admin registration code to proceed. Keep this page private.</p>

        <form method="POST" action="{{ route('admin.register') }}">
            @csrf
            <div class="grid-2">
                <div class="form-group">
                    <label>First name</label>
                    <input name="first_name" type="text"
                        class="form-control @error('first_name') is-invalid @enderror"
                        value="{{ old('first_name') }}" placeholder="Juan" required>
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Last name</label>
                    <input name="last_name" type="text"
                        class="form-control @error('last_name') is-invalid @enderror"
                        value="{{ old('last_name') }}" placeholder="dela Cruz" required>
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Email address</label>
                <input name="email" type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="admin@smilecare.com" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Phone number <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                <input name="phone" type="text" class="form-control"
                    value="{{ old('phone') }}" placeholder="09xx-xxx-xxxx">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input name="password" type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Minimum 8 characters" required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Confirm password</label>
                <input name="password_confirmation" type="password"
                    class="form-control" placeholder="Re-enter password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-size:15px;border-radius:9px;background:#533AB7 !important;border-color:#533AB7 !important;">
                Create admin account
            </button>
        </form>

        <p style="font-size:13px;color:#888;text-align:center;margin-top:16px;margin-bottom:0;">
            <a href="{{ route('login') }}">← Back to login</a>
        </p>
    </div>
</div>
@endsection
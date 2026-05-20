@extends('layouts.app')
@section('title', 'Sign In — SmileCare Dental')

@section('content')
<div class="auth-wrapper" style="min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(160deg,#E1F5EE 0%,#f9f9f7 60%);padding:2rem 1rem;">

    <div class="auth-logo-block" style="text-align:center;margin-bottom:28px;">
        <div class="brand-icon" style="width:52px;height:52px;border-radius:14px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 4.17 4.42 9.92 6.24 12.11a1 1 0 001.53 0C14.58 18.92 19 13.17 19 9c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
        </div>
        <h1 style="font-size:22px;font-weight:700;color:#111;margin:0;">SmileCare Dental</h1>
        <p style="font-size:13px;color:#777;margin-top:3px;">Consultation Appointment System</p>
    </div>

    <div class="card" style="width:min(360px, 100%);">
        <h2 style="font-size:17px;font-weight:600;margin-bottom:20px;">Sign in</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password"
                    class="form-control" placeholder="••••••••" required>
            </div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                <input type="checkbox" name="remember" id="remember" style="width:auto;accent-color:#0F6E56;">
                <label for="remember" style="font-size:13px;color:#555;margin:0;cursor:pointer;">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-size:15px;border-radius:9px;">Sign in</button>
        </form>

        <p style="font-size:13px;color:#888;text-align:center;margin-top:16px;margin-bottom:0;">
            No account yet? <a href="{{ route('register') }}">Register as patient</a>
        </p>
    </div>

    <p style="font-size:12px;color:#aaa;margin-top:16px;">
        Admin? <a href="{{ route('admin.register') }}" style="color:#aaa;">Set up admin account →</a>
    </p>
</div>
@endsection
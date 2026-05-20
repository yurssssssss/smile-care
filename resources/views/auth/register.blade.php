@extends('layouts.app')
@section('title', 'Register — SmileCare Dental')

@section('content')
<div class="auth-wrapper" style="min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(160deg,#E1F5EE 0%,#f9f9f7 60%);padding:2rem 1rem;">

    <div class="auth-logo-block" style="text-align:center;margin-bottom:22px;">
        <div class="brand-icon" style="width:52px;height:52px;border-radius:14px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 4.17 4.42 9.92 6.24 12.11a1 1 0 001.53 0C14.58 18.92 19 13.17 19 9c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
        </div>
        <h1 style="font-size:20px;font-weight:700;color:#111;margin:0;">SmileCare Dental</h1>
        <p style="font-size:13px;color:#777;margin-top:3px;">Create your patient account</p>
    </div>

    <div class="card" style="width:min(400px, 100%);">
        <h2 style="font-size:17px;font-weight:600;margin-bottom:20px;">Patient registration</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="grid-2">
                <div class="form-group">
                    <label>First name</label>
                    <input name="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror"
                        value="{{ old('first_name') }}" placeholder="Juan" required>
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Last name</label>
                    <input name="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
                        value="{{ old('last_name') }}" placeholder="dela Cruz" required>
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label>Email address</label>
                <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="you@example.com" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Phone number <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                <input name="phone" type="text" class="form-control" value="{{ old('phone') }}" placeholder="09xx-xxx-xxxx">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Minimum 8 characters" required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Confirm password</label>
                <input name="password_confirmation" type="password" class="form-control" placeholder="Re-enter password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-size:15px;border-radius:9px;">Create account</button>
        </form>

        <p style="font-size:13px;color:#888;text-align:center;margin-top:16px;margin-bottom:0;">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</div>
@endsection
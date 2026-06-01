@extends('layouts.app')
@section('title', 'My Profile — SmileCare Dental')

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
            <a href="{{ route('patient.home') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">Sign out</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width:760px;">

    @if(session('success'))
    <div class="alert alert-success small py-2" role="alert">
        ✓ {{ session('success') }}
    </div>
    @endif

    {{-- Profile Info --}}
    <div class="card border p-3 p-md-4 mb-3">
        <h3 class="h6 fw-semibold mb-3">Profile Information</h3>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- Avatar --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:72px;height:72px;background:#E1F5EE;" id="avatarWrap">
                    @if(Auth::user()->profile_photo)
                        <img src="{{ Storage::url(Auth::user()->profile_photo) }}"
                             style="width:100%;height:100%;object-fit:cover;" id="avatarImg">
                    @else
                        <span class="fw-bold text-success" style="font-size:24px;">
                            {{ strtoupper(substr(Auth::user()->first_name,0,1)) }}
                        </span>
                    @endif
                </div>
                <div>
                    <label class="btn btn-outline-secondary btn-sm mb-1" style="cursor:pointer;">
                        Upload photo
                        <input type="file" name="profile_photo" accept="image/*"
                               style="display:none;" onchange="previewPhoto(this)">
                    </label>
                    <p class="text-muted mb-0" style="font-size:12px;">JPG, PNG up to 2MB</p>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label small fw-medium">First name</label>
                    <input name="first_name" type="text"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', Auth::user()->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label small fw-medium">Last name</label>
                    <input name="last_name" type="text"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', Auth::user()->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-medium">Email address</label>
                <input name="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', Auth::user()->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-medium">
                    Phone number <span class="text-muted fw-normal">(optional)</span>
                </label>
                <input name="phone" type="text" class="form-control"
                       value="{{ old('phone', Auth::user()->phone) }}"
                       placeholder="09xx-xxx-xxxx">
            </div>

            <button type="submit" class="btn btn-success">Save changes</button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="card border p-3 p-md-4">
        <h3 class="h6 fw-semibold mb-3">Change Password</h3>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-medium">Current password</label>
                <input name="current_password" type="password"
                       class="form-control @error('current_password') is-invalid @enderror"
                       placeholder="Enter current password" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-medium">New password</label>
                <input name="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Minimum 8 characters" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-medium">Confirm new password</label>
                <input name="password_confirmation" type="password" class="form-control"
                       placeholder="Re-enter new password" required>
            </div>

            <button type="submit" class="btn btn-success">Update password</button>
        </form>
    </div>

</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        // Validate file type and size
        if (!file.type.match('image.*')) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Image must be under 2MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrap = document.getElementById('avatarWrap') 
                      ?? document.getElementById('avatar-wrap');
            if (!wrap) return;
            // Clear and insert a fresh img
            wrap.innerHTML = '';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
            wrap.appendChild(img);
        };
        reader.onerror = function() {
            alert('Could not read file. Please try again.');
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
@extends('layouts.app')
@section('title', 'My Profile — SmileCare Dental')

@section('content')
<div class="admin-layout">

    @include('dentist.partials.sidebar', ['active' => 'profile'])

    <div class="admin-main">

        {{-- Topbar --}}
        <div class="topbar d-flex align-items-center justify-content-between px-3 px-md-4 py-2 border-bottom bg-white">
            <div class="d-flex align-items-center gap-2">
                <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="fw-semibold" style="font-size:16px;">My Profile</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="d-none d-sm-inline" style="font-size:13px;">Dr. {{ Auth::user()->full_name }}</span>
                <span class="badge badge-blue">Dentist</span>
            </div>
        </div>

        <div class="admin-content py-3 py-md-4 px-3 px-md-4">
            <div class="row justify-content-center g-0">
                <div class="col-12 col-lg-9 col-xl-7">

                    @if(session('success'))
                    <div class="d-flex align-items-center gap-2 rounded-3 px-3 py-2 mb-3"
                        style="background:#E1F5EE;border:1px solid #A3D9C4;font-size:13px;color:#0F6E56;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#0F6E56"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        {{ session('success') }}
                    </div>
                    @endif

                    {{-- Profile Info Card --}}
                    <div class="card mb-3">
                        <h3 class="fw-semibold mb-3" style="font-size:15px;">Profile Information</h3>

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf

                            {{-- Avatar row --}}
                            <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                                <div id="avatar-wrap"
                                    style="width:72px;height:72px;border-radius:50%;overflow:hidden;background:#E8E4FB;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    @if(Auth::user()->profile_photo)
                                        <img src="{{ Storage::url(Auth::user()->profile_photo) }}"
                                            style="width:100%;height:100%;object-fit:cover;" alt="Avatar">
                                    @else
                                        <span style="font-size:24px;font-weight:700;color:#533AB7;">
                                            {{(substr(Auth::user()->first_name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <label class="btn btn-ghost btn-sm mb-1" style="cursor:pointer;">
                                        Upload photo
                                        <input type="file" name="profile_photo" accept="image/*"
                                            style="display:none;" onchange="previewPhoto(this)">
                                    </label>
                                    <p class="mb-0" style="font-size:12px;color:#aaa;">JPG, PNG up to 2 MB</p>
                                </div>
                            </div>

                            {{-- Name row --}}
                            <div class="row g-3 mb-1">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label>First name</label>
                                        <input name="first_name" type="text"
                                            class="form-control @error('first_name') is-invalid @enderror"
                                            value="{{ old('first_name', Auth::user()->first_name) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label>Last name</label>
                                        <input name="last_name" type="text"
                                            class="form-control @error('last_name') is-invalid @enderror"
                                            value="{{ old('last_name', Auth::user()->last_name) }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email address</label>
                                <input name="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', Auth::user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Phone number
                                    <span style="color:#aaa;font-weight:400;">(optional)</span>
                                </label>
                                <input name="phone" type="text" class="form-control"
                                    value="{{ old('phone', Auth::user()->phone) }}"
                                    placeholder="09xx-xxx-xxxx">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                                Save changes
                            </button>
                        </form>
                    </div>

                    {{-- Change Password Card --}}
                    <div class="card">
                        <h3 class="fw-semibold mb-3" style="font-size:15px;">Change Password</h3>

                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf

                            <div class="form-group">
                                <label>Current password</label>
                                <input name="current_password" type="password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    placeholder="Enter current password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-1">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label>New password</label>
                                        <input name="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Min. 8 characters" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label>Confirm new password</label>
                                        <input name="password_confirmation" type="password"
                                            class="form-control"
                                            placeholder="Re-enter new password" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 w-sm-auto mt-1">
                                Update password
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Hamburger button — hidden on desktop */
    .sc-hamburger {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        border-radius: 6px;
        color: #444;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .sc-hamburger:hover { background: #f0f0ee; }

    /* Sidebar overlay */
    .sc-sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 199;
        cursor: pointer;
    }
    .sc-sidebar-overlay.active { display: block; }
    @media (min-width: 576px) {
        .w-sm-auto { width: auto !important; }
    }

    /* On small screens, sidebar slides in from left */
    @media (max-width: 767px) {
        .admin-layout {
            flex-direction: column;
        }
        .sidebar {
            position: fixed !important;
            top: 0;
            left: 0;
            height: 100% !important;
            z-index: 200;
            transform: translateX(-100%);
            transition: transform .25s ease;
            width: 220px !important;
            min-height: unset !important;
            border-right: 1px solid #e8e8e5 !important;
            padding: 16px 12px !important;
            flex-direction: column !important;
            flex-wrap: nowrap !important;
            gap: 0 !important;
        }
        .sidebar.sidebar-open {
            transform: translateX(0);
        }
        .sidebar-brand { margin-bottom: 0 !important; }
        .nav-item { padding: 6px 10px; font-size: 12px; }
        .sc-hamburger { display: inline-flex !important; }
    }
</style>
@endpush

<div class="sc-sidebar-overlay" id="scOverlay"></div>

@push('scripts')
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
}s

(function () {
    const hamburger = document.getElementById('scHamburger');
    const overlay   = document.getElementById('scOverlay');
    const sidebar   = document.querySelector('.sidebar');

    function openSidebar()  { sidebar?.classList.add('sidebar-open');    overlay?.classList.add('active'); }
    function closeSidebar() { sidebar?.classList.remove('sidebar-open'); overlay?.classList.remove('active'); }

    hamburger?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);
})();
</script>
@endpush
@endsection
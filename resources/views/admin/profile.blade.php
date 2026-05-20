@extends('layouts.app')
@section('title', 'My Profile — SmileCare Dental')

@push('styles')
<style>
body { margin:0; padding:0; background:#F5F5F3; }
.container, .container-fluid, main, #app > *:not(.sc-admin-wrap) {
    max-width:none !important; padding:0 !important; margin:0 !important;
}
.sc-admin-wrap { display:flex; min-height:100vh; width:100%; }
.sc-sidebar {
    width:220px; min-width:220px; background:#fff; border-right:1px solid #EBEBEA;
    display:flex; flex-direction:column; position:sticky; top:0; height:100vh;
    overflow-y:auto; z-index:100; flex-shrink:0; transition:transform .25s ease;
}
.sc-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:199; cursor:pointer; }
.sc-overlay.active { display:block; }
.sc-main { flex:1; min-width:0; display:flex; flex-direction:column; }
.sc-topbar {
    height:52px; background:#fff; border-bottom:1px solid #EBEBEA;
    display:flex; align-items:center; padding:0 20px; gap:10px;
    position:sticky; top:0; z-index:90; flex-shrink:0;
}
.sc-topbar-title { font-size:15px; font-weight:600; flex:1; }
.sc-hamburger { display:none; background:none; border:none; cursor:pointer; padding:5px; border-radius:6px; color:#444; align-items:center; justify-content:center; }
.sc-hamburger:hover { background:#f0f0ee; }

/* Profile-specific content area: centered vertically + horizontally */
.sc-profile-content {
    flex:1;
    display:flex;
    flex-direction:column;
    align-items:center;
    padding:32px 20px;
}
.sc-profile-wrap { width:100%; max-width:680px; }

/* Cards */
.sc-card {
    background:#fff; border:1px solid #EBEBEA;
    border-radius:10px; padding:24px; margin-bottom:18px;
}
.sc-badge { display:inline-flex; align-items:center; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap; }
.sc-badge-blue { background:#EEF2FF; color:#3730A3; }
.sc-btn { display:inline-flex; align-items:center; justify-content:center; padding:7px 14px; border-radius:7px; font-size:13px; font-weight:500; cursor:pointer; border:none; transition:opacity .15s; white-space:nowrap; }
.sc-btn:hover { opacity:.88; }
.sc-btn-primary { background:#1C6B4A; color:#fff; }
.sc-btn-ghost   { background:transparent; color:#444; border:1px solid #DDDDD9; }
.sc-btn-sm { padding:5px 11px; font-size:12px; }

/* Form */
.sc-form-group { margin-bottom:16px; }
.sc-form-group label { display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:5px; }
.sc-form-control {
    width:100%; padding:9px 11px; border:1px solid #DDDDD9; border-radius:7px;
    font-size:13px; background:#fff; box-sizing:border-box; color:#222; outline:none; transition:border-color .15s;
}
.sc-form-control:focus { border-color:#1C6B4A; box-shadow:none; }

/* Alert */
.sc-alert-success {
    background:#E1F5EE; border:1px solid #A3D9C4;
    border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:13px; color:#0F6E56;
}

/* Photo row */
.sc-photo-row { display:flex; align-items:center; gap:20px; margin-bottom:24px; flex-wrap:wrap; }

@media(max-width:768px) {
    .sc-sidebar { position:fixed; top:0; left:0; height:100%; z-index:200; transform:translateX(-100%); }
    .sc-sidebar.open { transform:translateX(0); }
    .sc-hamburger { display:inline-flex; }
    .sc-profile-content { padding:20px 14px; }
    .sc-topbar { padding:0 14px; }
    .sc-card { padding:18px; }
}
@media(max-width:480px) {
    .sc-topbar-username { display:none; }
    .sc-card { padding:14px; }
    .sc-photo-row { gap:12px; }
}
</style>
@endpush

@section('content')
<div class="sc-overlay" id="scOverlay"></div>

<div class="sc-admin-wrap">
    <aside class="sc-sidebar admin-sidebar" id="scSidebar">
        @include('admin.partials.sidebar', ['active' => 'profile'])
    </aside>

    <div class="sc-main">
        {{-- Topbar --}}
        <div class="sc-topbar">
            <button class="sc-hamburger" id="scHamburger" aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="sc-topbar-title">My Profile</span>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <span class="sc-topbar-username" style="font-size:13px;color:#555;">{{ Auth::user()->full_name }}</span>
                <span class="sc-badge sc-badge-blue">Admin</span>
            </div>
        </div>

        {{-- Centered profile content --}}
        <div class="sc-profile-content">
            <div class="sc-profile-wrap">

                @if(session('success'))
                <div class="sc-alert-success">✓ {{ session('success') }}</div>
                @endif

                {{-- Profile Information --}}
                <div class="sc-card">
                    <h3 style="font-size:15px;font-weight:600;margin:0 0 18px;">Profile Information</h3>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Avatar --}}
                        <div class="sc-photo-row">
                            <div id="avatarWrap" style="width:72px;height:72px;border-radius:50%;overflow:hidden;background:#E8E4FB;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ Storage::url(Auth::user()->profile_photo) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <span style="font-size:24px;font-weight:700;color:#533AB7;">
                                        {{ strtoupper(substr(Auth::user()->first_name,0,1)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <label class="sc-btn sc-btn-ghost sc-btn-sm" style="cursor:pointer;">
                                    Upload photo
                                    <input type="file" name="profile_photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                                </label>
                                <p style="font-size:12px;color:#aaa;margin-top:4px;margin-bottom:0;">JPG, PNG up to 2MB</p>
                            </div>
                        </div>

                        {{-- Name row --}}
                        <div class="row g-3 mb-1">
                            <div class="col-12 col-sm-6">
                                <div class="sc-form-group mb-0">
                                    <label>First name</label>
                                    <input name="first_name" type="text"
                                        class="sc-form-control @error('first_name') is-invalid @enderror"
                                        value="{{ old('first_name', Auth::user()->first_name) }}" required>
                                    @error('first_name')
                                    <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="sc-form-group mb-0">
                                    <label>Last name</label>
                                    <input name="last_name" type="text"
                                        class="sc-form-control @error('last_name') is-invalid @enderror"
                                        value="{{ old('last_name', Auth::user()->last_name) }}" required>
                                    @error('last_name')
                                    <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="sc-form-group mt-3">
                            <label>Email address</label>
                            <input name="email" type="email"
                                class="sc-form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                            <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sc-form-group">
                            <label>Phone number <span style="color:#aaa;font-weight:400;">(optional)</span></label>
                            <input name="phone" type="text" class="sc-form-control"
                                value="{{ old('phone', Auth::user()->phone) }}" placeholder="09xx-xxx-xxxx">
                        </div>

                        <button type="submit" class="sc-btn sc-btn-primary">Save changes</button>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="sc-card">
                    <h3 style="font-size:15px;font-weight:600;margin:0 0 18px;">Change Password</h3>

                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf

                        <div class="sc-form-group">
                            <label>Current password</label>
                            <input name="current_password" type="password"
                                class="sc-form-control @error('current_password') is-invalid @enderror"
                                placeholder="Enter current password" required>
                            @error('current_password')
                            <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sc-form-group">
                            <label>New password</label>
                            <input name="password" type="password"
                                class="sc-form-control @error('password') is-invalid @enderror"
                                placeholder="Minimum 8 characters" required>
                            @error('password')
                            <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sc-form-group">
                            <label>Confirm new password</label>
                            <input name="password_confirmation" type="password" class="sc-form-control"
                                placeholder="Re-enter new password" required>
                        </div>

                        <button type="submit" class="sc-btn sc-btn-primary">Update password</button>
                    </form>
                </div>

            </div>{{-- /sc-profile-wrap --}}
        </div>{{-- /sc-profile-content --}}
    </div>{{-- /sc-main --}}
</div>{{-- /sc-admin-wrap --}}

<script>
(function(){
    const sidebar=document.getElementById('scSidebar'),overlay=document.getElementById('scOverlay'),ham=document.getElementById('scHamburger');
    function open(){sidebar.classList.add('open');overlay.classList.add('active');}
    function close(){sidebar.classList.remove('open');overlay.classList.remove('active');}
    ham.addEventListener('click',()=>sidebar.classList.contains('open')?close():open());
    overlay.addEventListener('click',close);
    window.addEventListener('resize',()=>{if(window.innerWidth>768)close();});
})();
function previewPhoto(input){
    if(input.files&&input.files[0]){
        const r=new FileReader();
        r.onload=e=>{document.getElementById('avatarWrap').innerHTML=`<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`};
        r.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
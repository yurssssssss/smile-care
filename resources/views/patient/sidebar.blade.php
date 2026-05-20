{{--
    Reusable topbar actions partial.
    Include inside a Bootstrap navbar's collapse div, e.g.:
        <div class="collapse navbar-collapse justify-content-end" id="navbarTop">
            @include('partials.sidebar')
        </div>
--}}
<div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2 mt-2 mt-lg-0">
    <span class="text-muted small">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
    <span class="badge bg-primary">Patient</span>
    <a href="{{ route('profile') }}" class="btn btn-outline-secondary btn-sm">My Profile</a>
    <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm">Sign out</button>
    </form>
</div>
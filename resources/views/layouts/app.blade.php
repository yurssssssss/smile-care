<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>@yield('title', 'SmileCare Dental')</title>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f5f5f3; color: #1a1a18; font-size: 15px; line-height: 1.6; }
        a { color: #0F6E56; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Brand / Topbar */
        .brand-icon { width: 32px; height: 32px; background: #0F6E56; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

        /* Buttons */
        .btn-primary  { background: #0F6E56 !important; border-color: #0F6E56 !important; color: #fff !important; }
        .btn-primary:hover  { background: #085041 !important; border-color: #085041 !important; }
        .btn-outline-primary { color: #0F6E56 !important; border-color: #0F6E56 !important; }
        .btn-outline-primary:hover { background: #E1F5EE !important; color: #0F6E56 !important; }
        .btn-success  { background: #0F6E56 !important; border-color: #0F6E56 !important; }
        .btn-success:hover  { background: #085041 !important; border-color: #085041 !important; }
        .btn-outline-success { color: #0F6E56 !important; border-color: #0F6E56 !important; }
        .btn-outline-success:hover { background: #E1F5EE !important; }
        .btn-ghost { background: transparent; color: #666; border: 1px solid #ddd; border-radius: 8px; padding: 5px 12px; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; }
        .btn-ghost:hover { background: #f5f5f5; text-decoration: none; }

        /* Badges */
        .badge-green  { background: #EAF3DE; color: #3B6D11; }
        .badge-amber  { background: #FAEEDA; color: #854F0B; }
        .badge-red    { background: #FCEBEB; color: #A32D2D; }
        .badge-blue   { background: #E6F1FB; color: #185FA5; }

        /* ── Card ── */
        .card {
            background: #fff;
            border: 1px solid #e8e8e5;
            border-radius: 14px;
            padding: 28px 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
        }

        /* ── Form groups ── */
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 5px; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #ddd; font-size: 14px; padding: 9px 12px; width: 100%; transition: border-color .15s, box-shadow .15s; }
        .form-control:focus, .form-select:focus { border-color: #0F6E56; box-shadow: 0 0 0 3px rgba(15,110,86,.12); outline: none; }
        .form-control.is-invalid { border-color: #e24b4a; }
        .invalid-feedback { font-size: 12px; color: #e24b4a; margin-top: 4px; }

        /* ── Two-column grid for name fields ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* Form controls (supplement Bootstrap) */
        .form-control:focus, .form-select:focus { border-color: #0F6E56; box-shadow: 0 0 0 3px rgba(15,110,86,.1); }

        /* Tables */
        .table th { font-size: 11px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: .5px; }

        /* Sidebar */
        .sidebar { width: 200px; min-height: 100vh; background: #fff; border-right: 1px solid #e8e8e5; padding: 16px; flex-shrink: 0; }
        .sidebar-brand { display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px; margin-bottom: 24px; }
        .nav-section { font-size: 10px; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin: 16px 0 6px 12px; }
        .nav-item { display: flex; align-items: center; gap: 9px; padding: 9px 12px; border-radius: 8px; font-size: 13px; color: #555; text-decoration: none; }
        .nav-item:hover { background: #E1F5EE; color: #0F6E56; text-decoration: none; }
        .nav-item.active { background: #E1F5EE; color: #0F6E56; font-weight: 600; }

        /* Stat cards */
        .stat-card { background: #f5f5f3; border-radius: 10px; padding: 16px; }
        .stat-label { font-size: 12px; color: #888; margin-bottom: 4px; }
        .stat-value { font-size: 26px; font-weight: 600; color: #1a1a18; }

        /* Admin layout */
        .admin-layout { display: flex; }
        .admin-main { flex: 1; min-width: 0; }
        .admin-content { padding: 24px 28px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }

        /* Time slots (booking) */
        .time-slot { padding: 9px; border: 1px solid #ddd; border-radius: 8px; text-align: center; font-size: 13px; cursor: pointer; transition: all .15s; }
        .time-slot:hover { border-color: #0F6E56; color: #0F6E56; }
        .time-slot.selected { background: #0F6E56; color: #fff; border-color: #0F6E56; }
        .time-slot.taken { background: #f5f5f5; color: #bbb; cursor: not-allowed; pointer-events: none; }

        /* ── Notify ── */
        #sc-notify-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .sc-notify { display: flex; align-items: flex-start; gap: 12px; min-width: 290px; max-width: 380px; background: #fff; border: 1px solid #e8e8e5; border-radius: 12px; padding: 14px 16px; box-shadow: 0 8px 24px rgba(0,0,0,.10); pointer-events: all; animation: sc-notify-in .3s cubic-bezier(.34,1.56,.64,1) both; position: relative; overflow: hidden; }
        .sc-notify.hiding { animation: sc-notify-out .25s ease forwards; }
        .sc-notify::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 12px 0 0 12px; }
        .sc-notify-success::before { background: #0F6E56; }
        .sc-notify-error::before   { background: #e24b4a; }
        .sc-notify-warning::before { background: #EF9F27; }
        .sc-notify-info::before    { background: #378ADD; }
        .sc-notify-progress { position: absolute; bottom: 0; left: 0; height: 3px; border-radius: 0 0 0 12px; }
        .sc-notify-success .sc-notify-progress { background: #0F6E56; }
        .sc-notify-error   .sc-notify-progress { background: #e24b4a; }
        .sc-notify-warning .sc-notify-progress { background: #EF9F27; }
        .sc-notify-info    .sc-notify-progress { background: #378ADD; }
        .sc-notify-icon { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .sc-notify-success .sc-notify-icon { background: #EAF3DE; }
        .sc-notify-error   .sc-notify-icon { background: #FCEBEB; }
        .sc-notify-warning .sc-notify-icon { background: #FAEEDA; }
        .sc-notify-info    .sc-notify-icon { background: #E6F1FB; }
        .sc-notify-body  { flex: 1; min-width: 0; }
        .sc-notify-title { font-size: 13px; font-weight: 700; color: #111; margin-bottom: 2px; }
        .sc-notify-msg   { font-size: 13px; color: #666; line-height: 1.4; }
        .sc-notify-close { background: none; border: none; cursor: pointer; font-size: 18px; color: #bbb; padding: 0; line-height: 1; flex-shrink: 0; }
        .sc-notify-close:hover { color: #555; }
        @keyframes sc-notify-in  { from { opacity:0; transform: translateX(40px) scale(.95); } to { opacity:1; transform: translateX(0) scale(1); } }
        @keyframes sc-notify-out { from { opacity:1; transform: translateX(0); max-height:120px; } to { opacity:0; transform: translateX(50px); max-height:0; padding-top:0; padding-bottom:0; margin:0; } }
        @keyframes sc-shrink { from { width:100%; } to { width:0%; } }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .card { padding: 20px 16px; border-radius: 12px; width: 100% !important; }
            .grid-2 { grid-template-columns: 1fr; gap: 0; }
            .auth-wrapper { padding: 1.5rem 1rem !important; }
            .auth-logo-block { margin-bottom: 16px !important; }
            #sc-notify-container { left: 12px; right: 12px; bottom: 12px; }
            .sc-notify { min-width: unset; max-width: 100%; }
        }
        @media (max-width: 768px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .admin-content { padding: 16px; }
            .sidebar { width: 160px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div id="sc-notify-container" role="alert" aria-live="polite"></div>

@yield('content')

<script>
const Toast = (() => {
    const container = document.getElementById('sc-notify-container');
    const DURATION  = 4500;
    const cfg = {
        success: { label:'Success', icon:`<svg width="16" height="16" viewBox="0 0 24 24" fill="#3B6D11"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>` },
        error:   { label:'Error',   icon:`<svg width="16" height="16" viewBox="0 0 24 24" fill="#A32D2D"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>` },
        warning: { label:'Warning', icon:`<svg width="16" height="16" viewBox="0 0 24 24" fill="#854F0B"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>` },
        info:    { label:'Info',    icon:`<svg width="16" height="16" viewBox="0 0 24 24" fill="#185FA5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>` },
    };
    function show(type, title, message, duration) {
        duration = duration || DURATION;
        const el  = document.createElement('div');
        el.className = `sc-notify sc-notify-${type}`;
        const bar = document.createElement('div');
        bar.className = 'sc-notify-progress';
        bar.style.cssText = `width:100%;animation:sc-shrink ${duration}ms linear forwards;`;
        el.innerHTML = `<div class="sc-notify-icon">${cfg[type].icon}</div><div class="sc-notify-body"><div class="sc-notify-title">${title || cfg[type].label}</div>${message ? `<div class="sc-notify-msg">${message}</div>` : ''}</div><button class="sc-notify-close" aria-label="Dismiss">&times;</button>`;
        el.appendChild(bar);
        function dismiss() { el.classList.add('hiding'); el.addEventListener('animationend', () => el.remove(), {once:true}); }
        el.querySelector('.sc-notify-close').addEventListener('click', dismiss);
        let timer = setTimeout(dismiss, duration);
        el.addEventListener('mouseenter', () => { clearTimeout(timer); bar.style.animationPlayState = 'paused'; });
        el.addEventListener('mouseleave', () => { bar.style.animationPlayState = 'running'; timer = setTimeout(dismiss, 1500); });
        container.appendChild(el);
    }
    return {
        success: (t,m,d) => show('success',t,m,d),
        error:   (t,m,d) => show('error',t,m,d),
        warning: (t,m,d) => show('warning',t,m,d),
        info:    (t,m,d) => show('info',t,m,d),
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    @if(session('success')) Toast.success('Success', @json(session('success'))); @endif
    @if(session('error'))   Toast.error('Error',     @json(session('error')));   @endif
    @if(session('warning')) Toast.warning('Warning', @json(session('warning'))); @endif
    @if(session('info'))    Toast.info('Info',        @json(session('info')));   @endif
    @if($errors->any())     Toast.error('Validation failed', @json($errors->first())); @endif
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html> 
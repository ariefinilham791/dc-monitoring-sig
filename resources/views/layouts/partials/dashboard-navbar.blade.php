<header class="dh">
    <nav class="dh-nav navbar navbar-expand-lg navbar-light {{ $classList ?? '' }} {{ $sticky === true ? 'sticky' : '' }}">
        <div class="dh-container container {{ $fixedWidth !== true ? '-fluid' : '' }}">
            <a class="dh-brand navbar-brand" href="{{ route('dashboard') }}">
                <img src="/images/logo.png" height="30" alt=""/>
            </a>
            <button class="dh-toggler navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dhMenu"
                aria-controls="dhMenu" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="dh-menu collapse navbar-collapse" id="dhMenu">
                <ul class="dh-list navbar-nav">
                    <li class="dh-item">
                        <a class="dh-link" href="{{ route('dashboard') }}">
                            <span class="dh-icon"><svg viewBox="0 0 24 24" fill="currentColor" opacity="0.9"><path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"/></svg></span>
                            <span class="dh-label">Home</span>
                        </a>
                    </li>
                    <li class="dh-item">
                        <a class="dh-link" href="{{ route('checklist.index') }}">
                            <span class="dh-icon"><svg viewBox="0 0 24 24" fill="currentColor" opacity="0.9"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg></span>
                            <span class="dh-label">Checklist</span>
                        </a>
                    </li>
                    <li class="dh-item">
                        <a class="dh-link" href="{{ route('component-type.index') }}">
                            <span class="dh-icon"><svg viewBox="0 0 24 24" fill="currentColor" opacity="0.9"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></span>
                            <span class="dh-label">Tipe Component</span>
                        </a>
                    </li>
                    <li class="dh-item">
                        <a class="dh-link" href="{{ route('server.index') }}">
                            <span class="dh-icon"><svg viewBox="0 0 24 24" fill="currentColor" opacity="0.9"><path d="M2 4h20v6H2V4zm0 10h20v6H2v-6z"/></svg></span>
                            <span class="dh-label">Server</span>
                        </a>
                    </li>
                    <li class="dh-item">
                        <form method="POST" action="{{ route('logout') }}" class="dh-form">
                            @csrf
                            <button type="submit" class="dh-link dh-btn">
                                <span class="dh-icon"><svg viewBox="0 0 24 24" fill="currentColor" opacity="0.9"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg></span>
                                <span class="dh-label">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<style>
/* ========== DASHBOARD HEADER — presisi, sejajar ========== */
.dh { --dh-h: 40px; --dh-gap: 8px; --dh-px: 12px; --dh-color: #495057; --dh-active: #335EEA; }
.dh-nav { min-height: 60px; }
.dh-container { flex-wrap: wrap; }
.dh-brand { margin-right: 0; }
.dh-toggler { border: none; padding: 8px; }

/* Desktop: baris horizontal, menu kanan */
.dh-menu.navbar-collapse { flex-grow: 0; }
@media (min-width: 992px) {
    .dh-list { display: flex; flex-direction: row; align-items: center; gap: 4px; margin: 0 0 0 auto; padding: 0; list-style: none; }
    .dh-item { display: flex; align-items: center; margin: 0; padding: 0; }
    .dh-form { display: flex; align-items: center; margin: 0; padding: 0; }
}

/* Satu gaya untuk link & tombol — tinggi tetap, flex center */
.dh-link {
    display: inline-flex;
    align-items: center;
    height: var(--dh-h);
    padding: 0 var(--dh-px);
    gap: var(--dh-gap);
    color: var(--dh-color);
    font-size: 15px;
    font-weight: 500;
    font-family: inherit;
    line-height: 1;
    text-decoration: none;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 6px;
    transition: color .15s, background .15s;
    box-sizing: border-box;
    -webkit-appearance: none;
    appearance: none;
}
.dh-link:hover { color: var(--dh-active); background: rgba(51, 94, 234, 0.08); }
.dh-link:focus { outline: none; box-shadow: none; }

/* Icon: 20x20, isi di tengah */
.dh-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}
.dh-icon svg { width: 20px; height: 20px; display: block; color: var(--dh-active); }

/* Teks: tinggi 20px = sejajar icon */
.dh-label { line-height: 20px; display: inline-block; }

/* Mobile: list vertikal, rapi */
@media (max-width: 991.98px) {
    .dh-toggler { margin-left: auto; }
    .dh-menu { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(0,0,0,.08); width: 100%; }
    .dh-list { flex-direction: column; align-items: stretch; width: 100%; margin: 0; padding: 0; list-style: none; gap: 0; }
    .dh-item { border-bottom: 1px solid rgba(0,0,0,.06); }
    .dh-item:last-child { border-bottom: none; }
    .dh-link { width: 100%; height: auto; padding: 12px 0; justify-content: flex-start; }
    .dh-form { width: 100%; }
    .dh-form .dh-link { width: 100%; justify-content: flex-start; }
}
</style>

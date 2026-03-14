<!DOCTYPE html>
<html lang="en" class="app-body">

<head>
    @include('layouts.partials/head', ['title' => $title])
    @yield('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite('node_modules/aos/dist/aos.css')
    @vite('node_modules/swiper/swiper-bundle.min.css')
    @vite('node_modules/magnific-popup/dist/magnific-popup.css')
    @vite('node_modules/bootstrap-select/dist/css/bootstrap-select.min.css')
    @vite('node_modules/leaflet/dist/leaflet.css')
    @vite('node_modules/jarallax/dist/jarallax.css')
    @vite('resources/scss/theme.scss')
</head>

<body>
    {{-- Global page loader --}}
    <div class="page-loader" id="pageLoader" aria-hidden="true">
        <div class="page-loader-spinner"></div>
        <span class="page-loader-text">Memuat...</span>
    </div>

    <div class="page-content-wrap">
@yield('content')

    </div>
<!-- back to top -->
<a class="btn btn-soft-primary shadow-none btn-icon btn-back-to-top" href='#'><i class="icon-xxs"
                                                                                 data-feather="arrow-up"></i></a>

@yield('script')
<script>
(function() {
    function hideLoader() {
        document.body.classList.add('page-loaded');
        var el = document.getElementById('pageLoader');
        if (el) el.setAttribute('aria-hidden', 'true');
    }
    function showLoader() {
        document.body.classList.remove('page-loaded');
        var el = document.getElementById('pageLoader');
        if (el) el.setAttribute('aria-hidden', 'false');
    }
    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader);
        document.addEventListener('DOMContentLoaded', function() { setTimeout(hideLoader, 150); });
    }
    // Saat user pakai tombol Back browser, halaman bisa di-restore dari bfcache
    // tanpa trigger load/DOMContentLoaded lagi → loader tetap muter. Pastikan sembunyikan.
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) hideLoader();
    });
    document.addEventListener('click', function(e) {
        var a = e.target.closest('a[href]');
        if (!a || a.target === '_blank' || a.hasAttribute('download')) return;
        var href = (a.getAttribute('href') || '').trim();
        if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
        try {
            var u = new URL(href, window.location.origin);
            if (u.origin === window.location.origin) showLoader();
        } catch (err) {}
    }, true);
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@vite(['resources/js/theme.js','resources/js/swiper.js'])
@yield('script-bottom')
<style>
/* Base responsive: no horizontal scroll, touch-friendly */
.app-body { background: #e8eef5 !important; min-height: 100vh; overflow-x: hidden; }
.app-body .bg-gradient2 { background: #e8eef5 !important; }
.app-body .card { background: #fff; }
.app-body .navbar { background: #fff !important; }
/* Touch targets on mobile: comfortable tap size */
@media (max-width: 991.98px) {
    .app-body .btn:not(.btn-sm):not(.btn-link) { min-height: 44px; padding-top: 0.5rem; padding-bottom: 0.5rem; }
    .app-body .form-control:not(.form-control-sm), .app-body .form-select:not(.form-select-sm) { min-height: 44px; }
}

/* Status pills — pilihan status yang rapi */
.status-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.status-pill-input { position: absolute; opacity: 0; pointer-events: none; }
.status-pill {
    display: inline-block;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 9999px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
    background: #e9ecef;
    color: #495057;
}
.status-pill:hover { background: #dee2e6; color: #212529; }
.status-pill-success { background: #e8f5e9; color: #2e7d32; }
.status-pill-success:hover { background: #c8e6c9; color: #1b5e20; }
.status-pill-input:checked + .status-pill-success { background: #2e7d32; color: #fff; border-color: #2e7d32; }
.status-pill-secondary { background: #f5f5f5; color: #616161; }
.status-pill-secondary:hover { background: #eeeeee; color: #424242; }
.status-pill-input:checked + .status-pill-secondary { background: #616161; color: #fff; border-color: #616161; }
.status-pill-warning { background: #fff8e1; color: #f57c00; }
.status-pill-warning:hover { background: #ffecb3; color: #e65100; }
.status-pill-input:checked + .status-pill-warning { background: #f57c00; color: #fff; border-color: #f57c00; }
.status-pill-info { background: #e3f2fd; color: #1565c0; }
.status-pill-info:hover { background: #bbdefb; color: #0d47a1; }
.status-pill-input:checked + .status-pill-info { background: #1565c0; color: #fff; border-color: #1565c0; }

/* Page loader */
.page-loader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.9);
    transition: opacity 0.25s ease, visibility 0.25s ease;
}
body.page-loaded .page-loader {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}
.page-loader-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e9ecef;
    border-top-color: #0d6efd;
    border-radius: 50%;
    animation: page-loader-spin 0.8s linear infinite;
}
.page-loader-text {
    margin-top: 12px;
    font-size: 0.875rem;
    color: #6c757d;
}
@keyframes page-loader-spin {
    to { transform: rotate(360deg); }
}

/* Skeleton placeholder */
.skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-shine 1.2s ease-in-out infinite; border-radius: 4px; }
.skeleton-text { height: 1em; }
.skeleton-title { height: 1.5em; width: 60%; }
.skeleton-card { height: 120px; }
.skeleton-row { height: 48px; }
@keyframes skeleton-shine {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
</body>

</html>

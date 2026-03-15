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
    {{-- Full page loader: hanya saat first load --}}
    <div class="page-loader" id="pageLoader" aria-hidden="true">
        <div class="page-loader-spinner"></div>
    </div>

    {{-- Content loader: hanya area di bawah header saat pindah menu (tanpa teks) --}}
    <div class="content-loader" id="contentLoader" aria-hidden="true">
        <div class="content-loader-spinner"></div>
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
    var contentLoader = document.getElementById('contentLoader');
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
    function hideContentLoader() {
        if (contentLoader) {
            contentLoader.classList.remove('content-loader--active');
            contentLoader.setAttribute('aria-hidden', 'true');
        }
    }
    function showContentLoader() {
        if (!contentLoader) return;
        var header = document.querySelector('.page-content-wrap .dh') || document.querySelector('.page-content-wrap .dh-nav') || document.querySelector('.page-content-wrap header');
        var top = 60;
        if (header && header.offsetHeight) top = Math.max(top, header.offsetHeight);
        contentLoader.style.top = top + 'px';
        contentLoader.classList.add('content-loader--active');
        contentLoader.setAttribute('aria-hidden', 'false');
    }
    if (document.readyState === 'complete') {
        hideLoader();
        hideContentLoader();
    } else {
        window.addEventListener('load', function() { hideLoader(); hideContentLoader(); });
        document.addEventListener('DOMContentLoaded', function() { setTimeout(function() { hideLoader(); hideContentLoader(); }, 150); });
    }
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) { hideLoader(); hideContentLoader(); }
    });
    document.addEventListener('click', function(e) {
        var a = e.target.closest('a[href]');
        if (!a || a.target === '_blank' || a.hasAttribute('download') || a.getAttribute('data-skip-loader')) return;
        var href = (a.getAttribute('href') || '').trim();
        if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
        try {
            var u = new URL(href, window.location.origin);
            if (u.origin === window.location.origin) showContentLoader();
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

/* Page loader (full screen, first load) */
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

/* Content loader: hanya area di bawah header, tanpa teks */
.content-loader {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    top: 60px;
    z-index: 9990;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(232, 238, 245, 0.85);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    transition: opacity 0.2s ease, visibility 0.2s ease;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}
.content-loader.content-loader--active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}
.content-loader-spinner {
    width: 36px;
    height: 36px;
    border: 3px solid rgba(13, 110, 253, 0.2);
    border-top-color: #0d6efd;
    border-radius: 50%;
    animation: page-loader-spin 0.6s linear infinite;
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

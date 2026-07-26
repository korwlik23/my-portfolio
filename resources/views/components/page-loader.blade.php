<div id="page-loading-overlay" class="page-loading-overlay" aria-live="polite" aria-hidden="true">
    <div class="page-loading-panel" role="status">
        <div class="page-loading-brand" aria-hidden="true">
            @if(!empty($globalSystemSettings['logo_path']))
                <img src="{{ route('portfolio.media', ['path' => $globalSystemSettings['logo_path']]) }}" alt="" class="page-loading-logo">
            @else
                <span>{{ \Illuminate\Support\Str::substr($globalSystemSettings['brand_name'] ?? config('app.name', 'Laravel Starter'), 0, 1) }}</span>
            @endif
        </div>
        <div class="page-loading-orbit" aria-hidden="true">
            <div class="page-loading-spinner"></div>
            <div class="page-loading-spinner page-loading-spinner-soft"></div>
        </div>
        <div class="page-loading-copy">
            <p class="page-loading-title">{{ __('messages.page_loading') }}</p>
            <p class="page-loading-desc">{{ __('messages.please_wait') }}</p>
            <div class="page-loading-bar" aria-hidden="true">
                <div class="page-loading-bar-fill"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-loading-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        padding: 24px;
        background:
            linear-gradient(rgba(250, 250, 250, 0.82), rgba(244, 244, 245, 0.86));
        opacity: 0;
        pointer-events: none;
        transition: opacity 180ms ease;
        backdrop-filter: blur(10px) saturate(1.05);
    }

    .dark .page-loading-overlay {
        background:
            linear-gradient(rgba(9, 9, 11, 0.84), rgba(24, 24, 27, 0.88));
    }

    .page-loading-overlay.is-visible {
        opacity: 1;
        pointer-events: auto;
    }

    .page-loading-panel {
        display: flex;
        align-items: center;
        gap: 16px;
        width: min(100%, 380px);
        padding: 18px;
        border: 1px solid rgba(212, 212, 216, 0.9);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.96);
        color: #18181b;
        box-shadow: 0 20px 60px rgba(24, 24, 27, 0.18);
        transform: translateY(8px) scale(0.98);
        transition: transform 180ms ease;
    }

    .page-loading-overlay.is-visible .page-loading-panel {
        transform: translateY(0) scale(1);
    }

    .dark .page-loading-panel {
        border-color: rgba(63, 63, 70, 0.9);
        background: rgba(24, 24, 27, 0.96);
        color: #fafafa;
        box-shadow: 0 18px 48px rgba(0, 0, 0, 0.35);
    }

    .page-loading-brand {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: #18181b;
        color: #ffffff;
        font-size: 1.05rem;
        font-weight: 800;
        overflow: hidden;
    }

    .dark .page-loading-brand {
        background: #fafafa;
        color: #18181b;
    }

    .page-loading-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 8px;
    }

    .page-loading-orbit {
        position: relative;
        flex: 0 0 auto;
        width: 46px;
        height: 46px;
    }

    .page-loading-spinner {
        position: absolute;
        inset: 0;
        border: 3px solid rgba(14, 165, 233, 0.18);
        border-top-color: #0ea5e9;
        border-radius: 999px;
        animation: page-loading-spin 760ms linear infinite;
    }

    .page-loading-spinner-soft {
        inset: 8px;
        border-width: 2px;
        border-color: rgba(16, 185, 129, 0.16);
        border-bottom-color: #10b981;
        animation-duration: 1100ms;
        animation-direction: reverse;
    }

    .page-loading-copy {
        min-width: 0;
        flex: 1;
    }

    .page-loading-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
    }

    .page-loading-desc {
        margin: 2px 0 0;
        font-size: 0.8rem;
        color: #71717a;
    }

    .dark .page-loading-desc {
        color: #a1a1aa;
    }

    .page-loading-bar {
        position: relative;
        height: 4px;
        margin-top: 12px;
        overflow: hidden;
        border-radius: 999px;
        background: #e4e4e7;
    }

    .dark .page-loading-bar {
        background: #3f3f46;
    }

    .page-loading-bar-fill {
        position: absolute;
        inset-block: 0;
        left: -45%;
        width: 45%;
        border-radius: inherit;
        background: #0ea5e9;
        animation: page-loading-bar 1100ms ease-in-out infinite;
    }

    @keyframes page-loading-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes page-loading-bar {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(320%);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .page-loading-overlay {
            transition: none;
        }

        .page-loading-spinner {
            animation-duration: 1400ms;
        }

        .page-loading-bar-fill {
            animation-duration: 2200ms;
        }
    }
</style>

<script>
    (function () {
        var overlay = document.getElementById('page-loading-overlay');

        if (!overlay) {
            return;
        }

        var suppressUnloadLoaderUntil = 0;

        function showLoader() {
            overlay.classList.add('is-visible');
            overlay.setAttribute('aria-hidden', 'false');
        }

        function hideLoader() {
            overlay.classList.remove('is-visible');
            overlay.setAttribute('aria-hidden', 'true');
        }

        function suppressUnloadLoader() {
            suppressUnloadLoaderUntil = Date.now() + 2500;
            hideLoader();
        }

        function showLoaderForUnload() {
            if (Date.now() < suppressUnloadLoaderUntil) {
                return;
            }

            showLoader();
        }

        function shouldSkipLink(anchor, event) {
            if (!anchor) {
                return true;
            }

            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return true;
            }

            if (anchor.dataset.noLoader !== undefined || anchor.hasAttribute('download')) {
                suppressUnloadLoader();

                return true;
            }

            if (anchor.target && anchor.target !== '_self') {
                suppressUnloadLoader();

                return true;
            }

            var href = anchor.getAttribute('href');

            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return true;
            }

            if (href.startsWith('mailto:') || href.startsWith('tel:')) {
                suppressUnloadLoader();

                return true;
            }

            var targetUrl = new URL(anchor.href, window.location.href);
            var currentUrl = new URL(window.location.href);

            if (targetUrl.origin !== currentUrl.origin) {
                suppressUnloadLoader();

                return true;
            }

            return (targetUrl.pathname === currentUrl.pathname
                    && targetUrl.search === currentUrl.search
                    && targetUrl.hash !== currentUrl.hash);
        }

        document.addEventListener('click', function (event) {
            var anchor = event.target.closest ? event.target.closest('a[href]') : null;

            if (!shouldSkipLink(anchor, event)) {
                showLoader();
            }
        }, true);

        document.addEventListener('submit', function (event) {
            var form = event.target;

            if (!form || form.dataset.noLoader !== undefined || (form.target && form.target !== '_self')) {
                return;
            }

            showLoader();
        }, true);

        window.addEventListener('pageshow', hideLoader);
        window.addEventListener('popstate', hideLoader);
        window.addEventListener('beforeunload', showLoaderForUnload);
        window.addEventListener('pagehide', showLoaderForUnload);
    })();
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@stack('title', config('global.SITE_NAME'))</title>
    <meta property="og:title" content="{{ config('global.SITE_NAME') }}" />
    <link rel="shortcut icon" href="{{ asset('frontend/assets/images/logo-fa.svg') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}" />
    {{-- Global Plugin Styles (ai-admin structure) --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />
    {{-- Theme: base black (structure) + purple overrides (match frontend) --}}
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/themes/black.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/themes/purple.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/custom.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.css') }}?v={{ time() }}" rel="stylesheet" />
    <style>
        /* Admin header: logo white on dark background */
        #kt_header .admin-header-logo { filter: brightness(0) invert(1); }
        /* Side drawer forms: left/right padding */
        #kt_activities .drawer-body,
        #kt_drawer_chat .drawer-body { padding: 1.25rem 1.5rem; }
        /* ACL dropdown: ensure it opens and is visible */
        #kt_header,
        #kt_header .header-top,
        #kt_header .header-tabs,
        #kt_header #kt_brand_tabs { overflow: visible; }
        #kt_header .dropdown-menu { z-index: 1060; }
        #kt_header .dropdown.show .dropdown-menu { display: block; }
    </style>
    <script src="{{ asset('assets/js/custom.js') }}?v={{ time() }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/vendor/sweetalert2.min.css') }}" />
    <script src="{{ asset('assets/js/vendor/sweetalert2.all.min.js') }}"></script>
    @stack('css')
    <script>
        var admin_url = '{{ url("cmcontrol") }}';
        var defaultThemeMode = "light";
        var themeMode = localStorage.getItem("data-bs-theme") || document.documentElement.getAttribute("data-bs-theme-mode") || defaultThemeMode;
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    </script>
</head>
<body id="kt_body" class="header-extended header-fixed header-tablet-and-mobile-fixed">
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                @yield('header')
                @yield('toolbar')
                @yield('content')
                @yield('models')
                @yield('footer')
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script>window.visitorAnalyticsSource = 'admin'; window.visitorAnalyticsBaseUrl = '{{ url('') }}';</script>
    <script src="{{ asset('assets/js/visitor-analytics.js') }}"></script>
    <script>
    window.showAdminAlert = function(type, msg) {
        var t = msg || (type === 'success' ? 'Done.' : 'Something went wrong.');
        if (type === 'success') {
            Swal.fire({ icon: 'success', title: 'Success', text: t }).then(function() { location.reload(); });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: t });
        }
    };
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('a[data-confirm-delete]').forEach(function(a) {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                var url = this.getAttribute('href');
                var msg = this.getAttribute('data-confirm-delete') || 'Are you sure?';
                Swal.fire({
                    title: 'Confirm',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete'
                }).then(function(result) {
                    if (result.isConfirmed && url) window.location.href = url;
                });
            });
        });
    });
    </script>
    @if (Session::has('flash_message_success'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon: 'success', title: 'Success', text: {{Session::get('flash_message_success')}} });
    });
    </script>
    @endif
    @if (Session::has('flash_message_error'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon: 'error', title: 'Error', text: {{Session::get('flash_message_error') }} });
    });
    </script>
    @endif
    <script>
    (function() {
        function initAclDropdowns() {
            var toggles = document.querySelectorAll('#kt_header [data-bs-toggle="dropdown"]');
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                toggles.forEach(function(el) {
                    try {
                        if (!bootstrap.Dropdown.getInstance(el)) {
                            new bootstrap.Dropdown(el, { boundary: 'clippingParents', popperConfig: { strategy: 'fixed' } });
                        }
                    } catch (err) { console.warn('Dropdown init:', err); }
                });
            }
        }
        document.addEventListener('DOMContentLoaded', initAclDropdowns);
        if (document.readyState !== 'loading') initAclDropdowns();
    })();
    </script>
    @stack('scripts')
    @yield('script')
</body>
</html>

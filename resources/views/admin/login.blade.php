<!DOCTYPE html>
<html lang="en">
<head>
    <base href="{{ url('/') }}/" />
    <title>Login - {{ config('global.SITE_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ asset('frontend/assets/images/logo-fa.svg') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}" />
    {{-- ai-admin structure + purple theme (match frontend) --}}
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/themes/black.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/themes/purple.css') }}?v={{ time() }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/custom.css') }}?v={{ time() }}" rel="stylesheet" />
    <style>
        /* CM portal logo on dark panel: keep visible (ai-admin uses logo on dark bg) */
        .login-bg .login-panel-logo { filter: brightness(0) invert(1); }
    </style>
    <script>
        var defaultThemeMode = "light";
        var themeMode = localStorage.getItem("data-bs-theme") || document.documentElement.getAttribute("data-bs-theme-mode") || defaultThemeMode;
        if (themeMode === "system") themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    </script>
</head>
<body id="kt_body" class="auth-bg">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="w-lg-500px p-10">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                        @if (Session::has('flash_message_error'))
                            <div class="alert alert-danger bg-danger text-white">{{ Session::get('flash_message_error') }}</div>
                        @endif
                        @if (Session::has('flash_message_success'))
                            <div class="alert alert-success bg-success text-white">{{ Session::get('flash_message_success') }}</div>
                        @endif
                        <form class="form w-100" method="POST" action="{{ route('admin.login.do') }}">
                            @csrf
                            <div class="text-center mb-11">
                                <h1 class="text-gray-900 fw-bolder mb-3">Sign in to continue</h1>
                            </div>
                            <div class="fv-row mb-8">
                                <input type="text" placeholder="Enter Username" name="username" autocomplete="off" required class="form-control bg-transparent" value="{{ old('username') }}" />
                            </div>
                            <div class="fv-row mb-3">
                                <input type="password" placeholder="Enter Password" name="password" autocomplete="off" class="form-control bg-transparent" required />
                            </div>
                            <div class="d-grid mb-10">
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Sign In</span>
                                    <span class="indicator-progress d-none">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Right panel: ai-admin style (black bg), CM portal logo --}}
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2 login-bg">
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20 login-panel-logo" src="{{ asset('frontend/assets/images/logo-1.svg') }}" alt="{{ config('global.SITE_NAME') }}" />
                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">Admin Panel</h1>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
  <meta name="description" content="Cyber Majlis Members' Portal">
  <title>@yield('title', 'Portal') - Cyber Majlis</title>
  <link rel="shortcut icon" href="{{ asset('frontend/assets/images/logo-fa.svg') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('frontend/assets/material-design/css/material-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/web/assets/mobirise-icons2/mobirise2.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/animatecss/animate.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/dropdown/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/socicon/css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/theme/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/mobirise/css/mbr-additional.css') }}">
  <link href="{{ asset('frontend/assets/fonts/wix-madefor-display/wix-madefor-display.css') }}" rel="stylesheet">

  @stack('styles')
</head>
<body>
  <div class="portal-top-fill" aria-hidden="true"></div>
  <section data-bs-version="5.1" class="ascentm5 menu menu1 cid-v9NfC3uoRa" once="menu" id="menu1-mb">
    <nav class="navbar navbar-dropdown navbar-fixed-top navbar-expand-lg">
      <div class="container">
        <div class="navbar-brand">
          <span class="navbar-logo">
            <a href="{{ session()->get('portal_authenticated') ? route('portal') : route('home') }}"><img src="{{ asset('frontend/assets/images/logo-fa.svg') }}" alt="Cyber Majlis" style="height: 3.1rem;"></a>
          </span>
          <span class="navbar-caption-wrap"><a class="navbar-caption text-primary display-5" href="{{ session()->get('portal_authenticated') ? route('portal') : route('home') }}">Cyber Majlis</a></span>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <div class="hamburger"><span></span><span></span><span></span><span></span></div>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav nav-dropdown" data-app-modern-menu="true">
            <li class="nav-item"><a class="nav-link link text-primary display-4" href="{{ route('portal.interviews') }}">Expert Interviews</a></li>
            <li class="nav-item"><a class="nav-link link text-primary display-4" href="{{ route('portal.materials') }}">Event materials</a></li>
            <li class="nav-item"><a class="nav-link link text-primary display-4" href="{{ route('portal.documentation') }}">Documentation</a></li>
            <li class="nav-item"><a class="nav-link link text-primary display-4" href="{{ route('portal.training') }}">Training</a></li>
            <li class="nav-item"><a class="nav-link link text-primary display-4" href="{{ route('portal.reports') }}">Reports</a></li>
            <li class="nav-item"><a class="nav-link link text-primary display-4" href="{{ route('portal.agenda') }}">Agenda</a></li>
          </ul>
          <div class="navbar-buttons mbr-section-btn"><a class="btn btn-primary display-4" href="{{ route('portal.logout') }}">Sign Out</a></div>
        </div>
      </div>
    </nav>
  </section>

  <div class="portal-content-wrap">
  @yield('content')
  </div>

  <section data-bs-version="5.1" class="footer2 cid-v9MK0I1eV7" once="footers" id="footer02-iz">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-6 center mt-2 mb-3">
          <p class="mbr-fonts-style copyright mb-0 display-4">© Copyright Cyber Majlis - All Rights Reserved</p>
        </div>
      </div>
    </div>
  </section>

  <div id="scrollToTop" class="scrollToTop mbr-arrow-up"><a style="text-align: center;"><i class="mbr-arrow-up-icon mbr-arrow-up-icon-cm cm-icon cm-icon-smallarrow-up"></i></a></div>
  <input name="animation" type="hidden">

  <script src="{{ asset('frontend/assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('frontend/assets/smoothscroll/smooth-scroll.js') }}"></script>
  <script src="{{ asset('frontend/assets/ytplayer/index.js') }}"></script>
  <script src="{{ asset('frontend/assets/dropdown/js/navbar-dropdown.js') }}"></script>
  <script src="{{ asset('frontend/assets/theme/js/script.js') }}"></script>
  @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Exploring the Future of Technology and Innovations - Initiative of ICD">
  <title>@yield('title', 'Cyber Majlis')</title>
  <link rel="shortcut icon" href="{{ asset('frontend/assets/images/logo-fa.svg') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('frontend/assets/web/assets/mobirise-icons2/mobirise2.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/parallax/jarallax.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/animatecss/animate.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/dropdown/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/theme/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/mobirise/css/mbr-additional.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/assets/css/frontend-original-ui.css') }}">
  <link href="{{ asset('frontend/assets/fonts/wix-madefor-display/wix-madefor-display.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body>
  <section data-bs-version="5.1" class="menu menu6 cid-uCLy4WgLcA" once="menu" id="menu06-7x">
    <nav class="navbar navbar-dropdown opacityScrollOff navbar-fixed-top navbar-expand-lg">
      <div class="container">
        <div class="navbar-brand">
          <span class="navbar-logo">
            <a href="{{ route('home') }}"><img src="{{ asset('frontend/assets/images/logo-1.svg') }}" alt="CyberM" style="height: 3.5rem;"></a>
          </span>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <div class="hamburger"><span></span><span></span><span></span><span></span></div>
        </button>
        <div class="collapse navbar-collapse opacityScrollOff" id="navbarSupportedContent">
          <div class="navbar-buttons mbr-section-btn">
            <a class="btn btn-primary display-4" href="{{ session()->get('portal_authenticated') ? route('portal') : route('members-portal') }}">Members' Portal</a>
          </div>
        </div>
      </div>
    </nav>
  </section>

  @yield('content')

  <section data-bs-version="5.1" class="footer2 cid-usROXTxOVN" once="footers" id="footer02-y">
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
  <script src="{{ asset('frontend/assets/parallax/jarallax.js') }}"></script>
  <script src="{{ asset('frontend/assets/smoothscroll/smooth-scroll.js') }}"></script>
  <script src="{{ asset('frontend/assets/ytplayer/index.js') }}"></script>
  <script src="{{ asset('frontend/assets/dropdown/js/navbar-dropdown.js') }}"></script>
  <script src="{{ asset('frontend/assets/scrollgallery/scroll-gallery.js') }}"></script>
  <script src="{{ asset('frontend/assets/theme/js/script.js') }}"></script>
  <script>window.visitorAnalyticsSource = 'frontend'; window.visitorAnalyticsBaseUrl = '{{ url('') }}';</script>
  <script src="{{ asset('assets/js/visitor-analytics.js') }}"></script>
  <script>
    (function() {
      function triggerAnimationScroll() {
        window.dispatchEvent(new CustomEvent('scroll'));
      }
      if (document.readyState === 'complete') {
        setTimeout(triggerAnimationScroll, 100);
      } else {
        window.addEventListener('load', function() { setTimeout(triggerAnimationScroll, 100); });
      }
      requestAnimationFrame(function() { requestAnimationFrame(triggerAnimationScroll); });
    })();
  </script>
  @stack('scripts')
</body>
</html>

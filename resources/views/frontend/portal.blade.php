@extends('layouts.portal')

@section('title', 'Members\' Portal')

@section('content')
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11000;">
  <div id="portalToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body mbr-fonts-style display-7" style="font-family: 'Wix Madefor Display', sans-serif;"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
@push('scripts')
<script>
(function() {
  var msg = @json(session('success') ?: session('error'));
  var isError = @json(session()->has('error'));
  if (!msg) return;
  var el = document.getElementById('portalToast');
  if (!el) return;
  el.querySelector('.toast-body').textContent = msg;
  if (isError) {
    el.classList.remove('text-bg-success');
    el.classList.add('text-bg-danger');
  }
  var toast = new bootstrap.Toast(el, { autohide: true, delay: 5000 });
  toast.show();
})();
</script>
@endpush

<section data-bs-version="5.1" class="article11 cid-v9ML4lL5vu" id="article11-j6">
  <div class="container">
    <div class="row justify-content-center">
      <div class="title col-md-12 col-lg-7">
        <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-2"><strong>Welcome to the Cyber Majlis</strong><div><strong>Members’ Portal</strong></div></h3>
        <h4 class="mbr-section-subtitle align-center mbr-fonts-style mt-4 display-5">The Cyber Majlis Members’ Portal is a private knowledge space created for senior cybersecurity professionals, decision-makers, and trusted partners.</h4>
      </div>
    </div>
  </div>
</section>

@if(isset($hero) && $hero)
<section data-bs-version="5.1" class="article06 operationm5 cid-v98Vk3H94e" id="article06-hw">
  <div class="container">
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="content-wrapper flex-row-reverse">
          @if($hero->image)<img src="{{ asset($hero->image) }}" alt="{{ $hero->title }}">@else<div class="hero-placeholder" style="position:absolute;inset:0;background:#f0f0f0;border-radius:2rem;"></div>@endif
          <div class="card-wrap">
            <h2 class="mbr-section-title mbr-fonts-style display-5"><strong>{{ $hero->title }}</strong></h2>
            @if($hero->description)<p class="mbr-text mbr-fonts-style display-7">{{ $hero->description }}</p>@endif
            @if($hero->video)
            @php
              $vext = strtolower(pathinfo($hero->video, PATHINFO_EXTENSION));
              $vtype = $vext === 'webm' ? 'video/webm' : ($vext === 'mov' ? 'video/quicktime' : 'video/mp4');
              $heroStreamPath = Str::replaceFirst('storage/', '', $hero->video);
              $heroStreamUrl = route('stream.video', ['path' => $heroStreamPath]);
            @endphp
            <div class="mbr-section-btn align-left mb-3">
              <button type="button" class="btn btn-primary display-4 hero-watch-video-btn" data-bs-toggle="modal" data-bs-target="#heroVideoModal" data-video-src="{{ $heroStreamUrl }}" data-video-type="{{ $vtype }}" aria-label="Watch video">
                <span class="material material-play-arrow mbr-iconfont mbr-iconfont-btn" style="font-size: 28px;"></span> Watch the full interview
              </button>
            </div>
            <div class="modal fade" id="heroVideoModal" tabindex="-1" aria-labelledby="heroVideoModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
              <div class="modal-dialog modal-dialog-centered modal-lg hero-video-modal-dialog">
                <div class="modal-content hero-video-modal-content">
                  <div class="modal-header hero-video-modal-header" style="background-color:#483183 !important; color:#fff !important; padding:1rem 1.25rem !important; border-bottom:none !important; display:flex !important; align-items:center !important; justify-content:space-between !important;">
                    <h5 class="modal-title" id="heroVideoModalLabel" style="color:#fff !important; margin:0 !important; font-weight:600 !important;">Watch the full interview</h5>
                    <button type="button" class="btn hero-video-modal-close" data-bs-dismiss="modal" aria-label="Close" style="color:#fff !important; background:rgba(255,255,255,0.2) !important; border:1px solid rgba(255,255,255,0.5) !important; border-radius:0.5rem !important; padding:0.4rem 0.9rem !important; font-size:0.95rem !important; cursor:pointer !important;">
                      <span aria-hidden="true" style="margin-right:0.35rem;">×</span> Close
                    </button>
                  </div>
                  <div class="modal-body p-0 hero-video-modal-body" style="background:#1a1a1a !important;">
                    <div class="ratio ratio-16x9">
                      <video class="w-100" controls preload="auto" playsinline id="hero-video-player" crossorigin="anonymous"></video>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @elseif($hero->video_link)
            <div class="mbr-section-btn align-left"><a class="btn btn-primary display-4" href="{{ $hero->video_link }}" target="_blank" rel="noopener noreferrer"><span class="material material-play-arrow mbr-iconfont mbr-iconfont-btn" style="font-size: 28px;"></span>Watch the full interview</a></div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endif

<section data-bs-version="5.1" class="features5 cid-v9MxqUuWJU" id="features05-ip">
  <div class="container">
    <div class="row mb-5 justify-content-center">
      <div class="col-12 content-head">
        <h3 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2"><strong>Main sections</strong></h3>
        <h5 class="mbr-section-subtitle mbr-fonts-style align-center mb-0 mt-4 display-7">All content available in the portal is developed to support informed decision-making, practical implementation, and strategic dialogue at the highest professional level.</h5>
      </div>
    </div>
    <div class="row">
      <div class="item features-without-image col-12 col-md-6 col-lg-4 item-mb">
        <div class="item-wrapper">
          <div class="card-box align-left">
            <div class="iconfont-wrapper"><span class="mbr-iconfont material material-person-pin"></span></div>
            <h5 class="card-title mbr-fonts-style display-5"><strong>Expert Interviews</strong></h5>
            <p class="card-text mbr-fonts-style display-4">Gathering and exchanging opinions of experts on cybersecurity, digital risks and new technologies.</p>
            <div class="mbr-section-btn item-footer"><a href="{{ route('portal.interviews') }}" class="btn item-btn btn-primary display-4">Learn More</a></div>
          </div>
        </div>
      </div>
      <div class="item features-without-image col-12 col-md-6 col-lg-4 item-mb">
        <div class="item-wrapper">
          <div class="card-box align-left">
            <div class="iconfont-wrapper"><span class="mbr-iconfont material material-blur-on"></span></div>
            <h5 class="card-title mbr-fonts-style display-5"><strong>Event Materials</strong></h5>
            <p class="card-text mbr-fonts-style display-4">Exclusive access to the materials of closed meetings of the Cyber Majlis, round tables and official events.</p>
            <div class="mbr-section-btn item-footer"><a href="{{ route('portal.materials') }}" class="btn item-btn btn-primary display-4">Learn More</a></div>
          </div>
        </div>
      </div>
      <div class="item features-without-image col-12 col-md-6 col-lg-4 item-mb">
        <div class="item-wrapper">
          <div class="card-box align-left">
            <div class="iconfont-wrapper"><span class="mbr-iconfont material material-assessment"></span></div>
            <h5 class="card-title mbr-fonts-style display-5"><strong>Reports</strong></h5>
            <p class="card-text mbr-fonts-style display-4">A section presents original analytical publications developed within the Cyber Majlis ecosystem, focusing on cybersecurity trends, AI, threat landscapes, and strategic priorities in the UAE and global context.</p>
            <div class="mbr-section-btn item-footer"><a href="{{ route('portal.reports') }}" class="btn item-btn btn-primary display-4">Learn More</a></div>
          </div>
        </div>
      </div>
      <div class="item features-without-image col-12 col-md-6 col-lg-4 item-mb">
        <div class="item-wrapper">
          <div class="card-box align-left">
            <div class="iconfont-wrapper">
              <span class="mbr-iconfont material material-chrome-reader-mode"></span>
            </div>
            <h5 class="card-title mbr-fonts-style display-5"><strong>Documentation</strong></h5>
            <p class="card-text mbr-fonts-style display-4">A section of reference materials, frameworks, and internal guidelines shared across ICD subsidiaries.</p>
            <div class="mbr-section-btn item-footer"><a href="{{ route('portal.documentation') }}" class="btn item-btn btn-primary display-4">Learn More</a></div>
          </div>
        </div>
      </div>
      <div class="item features-without-image col-12 col-md-6 col-lg-4 item-mb">
        <div class="item-wrapper">
          <div class="card-box align-left">
            <div class="iconfont-wrapper">
              <span class="mbr-iconfont material material-local-library"></span>
            </div>
            <h5 class="card-title mbr-fonts-style display-5"><strong>Training</strong></h5>
            <p class="card-text mbr-fonts-style display-4">A curated collection of learning materials and educational resources focused on technology, cybersecurity, and digital transformation.</p>
            <div class="mbr-section-btn item-footer"><a href="{{ route('portal.training') }}" class="btn item-btn btn-primary display-4">Learn More</a></div>
          </div>
        </div>
      </div>
      <div class="item2 features-without-image col-12 col-md-6 col-lg-4 item-mb">
        <div class="item-wrapper2">
          <div class="card-box align-left">
            <h5 class="card-title2 mbr-fonts-style display-5"><strong>Agenda</strong></h5>
            <p class="card-text2 mbr-fonts-style display-7">Q4 2025 will take place on November 13, featuring key discussions on cybersecurity resilience,</p>
            <div class="mbr-section-btn item-footer"><a href="{{ route('portal.agenda') }}" class="btn item-btn2 btn-white display-4">Learn More</a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@push('styles')
<style>
/* Agenda card (item2): dark purple #483183, white text, light purple button - match design */
#features05-ip .item2 .item-wrapper2 {
  background: transparent !important;
  padding: 0 !important;
  height: 100%;
}
#features05-ip .item2 .item-wrapper2 .card-box {
  background: #483183 !important;
  color: #fff !important;
  border-radius: 2rem !important;
  padding: 2.25rem !important;
  height: 100%;
  display: flex !important;
  flex-direction: column !important;
}
#features05-ip .item2 .item-wrapper2 .card-box .item-footer {
  margin-top: auto !important;
}
#features05-ip .item2 .card-title2,
#features05-ip .item2 .card-text2 {
  color: #fff !important;
}
#features05-ip .item2 .card-title2 {
  margin-bottom: 0.5rem;
}
#features05-ip .item2 .card-text2 {
  margin-bottom: 1rem;
}
#features05-ip .item2 .item-btn2.btn-white {
  background: #fff !important;
  border: 2px solid rgba(255,255,255,0.8) !important;
  color: #483183 !important;
  border-radius: 2rem;
}
#features05-ip .item2 .item-btn2.btn-white:hover {
  background: #f5f0ff !important;
  border-color: #fff !important;
  color: #483183 !important;
}
</style>
@endpush

<section data-bs-version="5.1" class="testimonails1 carousel slide testimonials-slider cid-vabycdbLIJ" data-interval="false" data-bs-interval="false" id="testimonials1-o7">
  <div class="text-center container">
    <div class="carousel slide" role="listbox" data-pause="true" data-keyboard="false" data-ride="carousel" data-bs-ride="carousel" data-interval="7000" data-bs-interval="7000">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="user col-md-6 mx-auto">
            <div class="user_image">
              <img src="{{ asset('frontend/assets/images/khalifa-al-daboos-200x200.jpg') }}" alt="Khalifa Al Daboos">
            </div>
            <div class="user_text mb-4">
              <p class="mbr-fonts-style display-7">"As we navigate the rapidly evolving landscape of our digital age, collaboration and communication stand as the cornerstones of innovation and success. At Cyber Majlis, we believe that leveraging technology is not just about tools, but about fostering human connections. It empowers us to exchange ideas, stay informed, and interact effectively, ensuring that we are not just a team, but a vibrant community. Together, let's harness these connections to drive forward and shape a future where every voice is heard and every idea matters."</p>
            </div>
            <div class="user_name mbr-fonts-style mb-2 display-7"><strong>Mr. Khalifa Al Daboos</strong></div>
            <div class="user_desk mbr-fonts-style display-4">Deputy Chief Executive<br><span style="font-size: 1.1rem;">Officer, ICD</span></div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="user col-md-6 mx-auto">
            <div class="user_image">
              <img src="{{ asset('frontend/assets/images/douraid-zaghouani-200x200.jpg') }}" alt="Douraid Zaghouani">
            </div>
            <div class="user_text mb-4">
              <p class="mbr-fonts-style display-7">"The future lies in IT and technological advancement, as these fields are the driving forces behind innovation and progress in every aspect of our lives. As we embrace this digital era, it becomes clear that the path forward is paved with the limitless possibilities offered by IT and technological advancements.<br><br>So as we look to the future, we know that Cyber Majlis will continue to be this driving force in cultivating opportunities for growth and collaboration among the best technological minds."</p>
            </div>
            <div class="user_name mbr-fonts-style mb-2 display-7"><strong>Mr. Douraid Zaghouani</strong></div>
            <div class="user_desk mbr-fonts-style display-4">President of Cyber Majlis, FVP Head of IT, ICD</div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="user col-md-6 mx-auto">
            <div class="user_image">
              <img src="{{ asset('frontend/assets/images/ahmed-al-zarouni-2-200x200.jpg') }}" alt="Ahmed Al Zarouni">
            </div>
            <div class="user_text mb-4">
              <p class="mbr-fonts-style display-7">"As we step into this new era of connectivity and collaboration, Cyber Majlis invites you to be a part of a transformative gathering where visionary thinkers share ideas, foster insights, and define tomorrow's digital ecosystem.<br>Together, we'll explore the uncharted territories of innovation, challenge the status quo, and inspire one another to shape what lies ahead.<br>Welcome to Cyber Majlis to redefine the digital landscape of the future."</p>
            </div>
            <div class="user_name mbr-fonts-style mb-2 display-7"><strong>Ahmed Al Zarouni</strong></div>
            <div class="user_desk mbr-fonts-style display-4">President of Cyber Majlis, FVP Head of IT, ICD</div>
          </div>
        </div>
      </div>
      <div class="carousel-controls">
        <a class="carousel-control-prev" role="button" data-slide="prev" data-bs-slide="prev">
          <span aria-hidden="true" class="mobi-mbri mobi-mbri-arrow-prev mbr-iconfont"></span>
          <span class="sr-only visually-hidden">Previous</span>
        </a>
        <a class="carousel-control-next" role="button" data-slide="next" data-bs-slide="next">
          <span aria-hidden="true" class="mobi-mbri mobi-mbri-arrow-next mbr-iconfont"></span>
          <span class="sr-only visually-hidden">Next</span>
        </a>
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="article11 cid-uuQm6tCt5U" id="article11-4r">
  <div class="container">
    <div class="row justify-content-center">
      <div class="title col-md-12 col-lg-8">
        <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-5"><strong>Highlights</strong></h3>
        <h4 class="mbr-section-subtitle align-center mbr-fonts-style mt-4 display-5">Since its launch in November 2024, Cyber Majlis has been active as a professional platform supporting dialogue and innovation in technology, artificial intelligence, and information security.<div>Through its events, the initiative brings together diverse expertise to discuss key aspects of digital transformation in the UAE.</div></h4>
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="article11 cid-vafuMR4pbs" id="article11-pq">
  <div class="container">
    <div class="row justify-content-center">
      <div class="title col-md-12 col-lg-10">
        <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-2"><strong>Indicators</strong></h3>
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="features07 stockm5 cid-vafuz0oSFG" id="features07-pp">
  <div class="container">
    <div class="row flex-row-reverse">
      <div class="col-12">
        <div class="content-wrap flex-row-reverse">
          <div class="content-wrapper">
            <h2 class="mbr-section-title mbr-fonts-style display-5">The portal is continuously updated with original research, expert perspectives, and materials from Cyber Majlis closed-door sessions and events.</h2>
            <div class="items-wrapper">
              <div class="item features-without-image active">
                <div class="item-wrapper">
                  <div class="card-box">
                    <h4 class="item-title mbr-fonts-style display-5"><strong>10+ industries including:</strong></h4>
                    <p class="item-text mbr-fonts-style display-7">Finance &amp; Banking, Technology &amp; IT, Energy &amp; Infrastructure etc</p>
                  </div>
                </div>
              </div>
              <div class="item features-without-image">
                <div class="item-wrapper">
                  <div class="card-box">
                    <h4 class="item-title mbr-fonts-style display-5"><strong>4 quarterly events in 2025</strong></h4>
                    <p class="item-text mbr-fonts-style display-7">Closed-door discussions with industry leaders</p>
                  </div>
                </div>
              </div>
              <div class="item features-without-image">
                <div class="item-wrapper">
                  <div class="card-box">
                    <h4 class="item-title mbr-fonts-style display-5"><strong>30+ participating companies</strong></h4>
                    <p class="item-text mbr-fonts-style display-7">Across key industries and sectors</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="image-wrapper">
            <img src="{{ asset('frontend/assets/images/freepik-img7-img6-img6-img6-img7-46195-1248x697.png') }}" alt="">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@if(isset($hero) && $hero && $hero->video)
@push('styles')
<style>
/* Ensure modal and backdrop sit above theme elements (e.g. scroll-to-top z-index: 5000) */
#heroVideoModal {
  z-index: 10050 !important;
}
#heroVideoModal .modal-dialog {
  max-width: 900px;
  height: auto !important;
  z-index: 10051 !important;
  pointer-events: auto !important;
}
#heroVideoModal .modal-content {
  height: auto !important;
  border: 2px solid #483183 !important;
  border-radius: 1rem !important;
  overflow: hidden !important;
  box-shadow: 0 1rem 3rem rgba(72, 49, 131, 0.35) !important;
  pointer-events: auto !important;
}
#heroVideoModal .modal-header,
#heroVideoModal .modal-body,
#heroVideoModal .hero-video-modal-close,
#heroVideoModal .ratio,
#heroVideoModal .ratio video {
  pointer-events: auto !important;
}
/* Video above ratio ::before so seek bar / controls receive clicks and drags */
#heroVideoModal .ratio video {
  z-index: 2 !important;
  touch-action: manipulation !important;
}
#heroVideoModal .hero-video-modal-header {
  font-family: 'Wix Madefor Display', sans-serif;
}
#heroVideoModal .hero-video-modal-close:hover {
  background: rgba(255,255,255,0.35) !important;
  border-color: rgba(255,255,255,0.8) !important;
}
body .modal-backdrop {
  z-index: 10049 !important;
}
</style>
@endpush
@push('scripts')
<script>
(function() {
  var modal = document.getElementById('heroVideoModal');
  var player = document.getElementById('hero-video-player');
  if (!modal || !player) return;

  // Move modal to body so it is not inside any transform/overflow parent (fixes unclickable modal)
  if (modal.parentNode && modal.parentNode !== document.body) {
    document.body.appendChild(modal);
  }

  modal.addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget || document.querySelector('.hero-watch-video-btn');
    var src = btn && btn.getAttribute('data-video-src');
    var type = btn && btn.getAttribute('data-video-type');
    if (src && type) {
      player.innerHTML = '<source src="' + src + '" type="' + type + '">';
      player.load();
      player.play().catch(function() {});
    }
  });
  modal.addEventListener('hidden.bs.modal', function() {
    player.pause();
    player.removeAttribute('src');
    player.innerHTML = '';
  });

  // Fallback: ensure Close button closes the modal even if Bootstrap event is blocked
  var closeBtn = modal.querySelector('.hero-video-modal-close');
  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      var bsModal = typeof bootstrap !== 'undefined' && bootstrap.Modal && bootstrap.Modal.getInstance(modal);
      if (bsModal) {
        bsModal.hide();
      } else {
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        var backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
      }
    });
  }
})();
</script>
@endpush
@endif
@endsection

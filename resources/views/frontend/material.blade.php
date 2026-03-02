@extends('layouts.portal')

@section('title', $event->title)

@push('styles')
<style>
/* Event participant video: full rounded border – frame matches section bg so corners never look cut */
.portal-video-wrap {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  border-radius: 0.5rem;
  background: #000;
  border: 3px solid #fff;
  box-sizing: border-box;
  left:15px
}
.portal-video-wrap video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  pointer-events: auto !important;
  display: block;
  border-radius: 0.35rem;
}
.portal-video-player {
  touch-action: manipulation;
}
</style>
@endpush

@section('content')
<section data-bs-version="5.1" class="header13 cid-v9NimGWMM9" id="header13-kq">
  <div class="align-center container">
    <div class="row justify-content-center">
      <div class="col-md-12 mb-5 content-head">
        <h1 class="mbr-section-title mbr-fonts-style display-4"><a href="{{ route('portal') }}" class="text-primary">Portal</a>&nbsp; |&nbsp; <a href="{{ route('portal.materials') }}" class="text-primary">Event materials</a>&nbsp; |&nbsp; {{ $event->title }}</h1>
        <p class="mbr-text mbr-fonts-style mt-4 display-2"><strong>{{ $event->title }}</strong></p>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-12 col-md-12">
        @if($event->image)
        <img class="w-100" src="{{ asset($event->image) }}" alt="{{ $event->title }}" title="">
        @else
        <img class="w-100" src="{{ asset('frontend/assets/images/a7301638-1501x1001.jpeg') }}" alt="{{ $event->title }}" title="">
        @endif
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="article11 cid-v9Nk5Qj69A" id="article11-kr">
    <div class="container">
        <div class="row justify-content-center">
            <div class="title col-md-12 col-lg-10">
                @php
                    $intro = '';
                    if ($event->event_date) {
                        $intro = 'On ' . $event->event_date->format('F j, Y') . ', ';
                    }
                    $body = $intro . ($event->description ?? '');
                @endphp
                @if($body !== '')
                <h4 class="mbr-section-subtitle align-center mbr-fonts-style mt-4 display-5">{{ $body }}</h4>
                @endif
            </div>
        </div>
    </div>
</section>

@if($event->highlights)
<section data-bs-version="5.1" class="header15 cid-v9NkEU7ayS" id="header15-l6">
	<div class="container">
		<div class="row justify-content-center">
			<div class="card col-12 col-lg-12">
				<div class="card-wrapper wrap">
					<div class="card-box align-center">
						<h1 class="card-title mbr-fonts-style mb-4 display-2"><strong>Highlights from Our Recent Event</strong></h1>
						<div class="mbr-text mbr-fonts-style mb-4 display-7">{{nl2br(e($event->highlights))}}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endif

@if($event->eventParticipants && $event->eventParticipants->count() > 0)
@foreach($event->eventParticipants as $ep)
@php $p = $ep->participant; @endphp
<section data-bs-version="5.1" class="people1 cid-v9NnMztYIw" id="people01-{{ $ep->id }}">
    <div class="container">
        <div class="wrapper">
            <div class="row justify-content-center">
                <div class="col-12 col-md-12 col-lg-7 image-wrapper">
                    @if($ep->video)
                    @php
                        $vext = strtolower(pathinfo($ep->video, PATHINFO_EXTENSION));
                        $vtype = $vext === 'webm' ? 'video/webm' : ($vext === 'mov' ? 'video/quicktime' : 'video/mp4');
                        $streamPath = \Illuminate\Support\Str::replaceFirst('storage/', '', $ep->video);
                        $streamUrl = route('stream.video', ['path' => $streamPath]);
                    @endphp
                    <div class="video-wrapper portal-video-wrap">
                        <video class="w-100 portal-video-player" controls preload="auto" playsinline crossorigin="anonymous">
                            <source src="{{ $streamUrl }}" type="{{ $vtype }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    @elseif($ep->image)
                    <img class="w-100" src="{{ asset($ep->image) }}" alt="{{ $ep->topic ?: $p->name }}" style="border-radius: 0.5rem;">
                    @elseif($event->image)
                    <img class="w-100" src="{{ asset($event->image) }}" alt="{{ $event->title }}" style="border-radius: 0.5rem;">
                    @else
                    <img class="w-100" src="{{ asset('frontend/assets/images/tg-image-2485592577-1600x1067.png') }}" alt="" style="border-radius: 0.5rem;">
                    @endif
                </div>
                <div class="col-12 col-md-12 m-auto col-lg-5">
                    <div class="text-wrapper align-left">
                        <div class="wrapper-inner">
                            <h2 class="mbr-section-title mbr-fonts-style display-5"><br><strong>{{ $ep->topic ?: 'Address by ' . $p->name }}</strong></h2>
                            @if($ep->description)
                            <h2 class="mbr-section-title2 mbr-fonts-style display-7">{{ nl2br(e($ep->description))}}</h2>
                            @endif
                            <div class="wrapper-inner">
                                <div class="d-flex mt-4">
                                    @if($p->image)
                                    <img class="team" src="{{ asset($p->image) }}" alt="{{ $p->name }}">
                                    @else
                                    <div class="team rounded bg-light d-flex align-items-center justify-content-center" style="width:200px;height:200px;"><span class="text-muted">No photo</span></div>
                                    @endif
                                </div><br>
                                <p class="mbr-text align-left mbr-fonts-style display-4"><strong>{{ $p->name }}<br></strong>
                                @if($p->position){{ $p->position }}@endif
                                @if($p->company)@if($p->position), @endif{{ $p->company->name }}@endif
                                <br></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@if($ep->eventParticipantDocuments && $ep->eventParticipantDocuments->count() > 0)
<section data-bs-version="5.1" class="features06 merchm5 cid-v9Nq2QkJam" id="features06-{{ $ep->id }}">
    <div class="container">
        <div class="row">
            @foreach($ep->eventParticipantDocuments as $doc)
            @php $docSize = $doc->getFileSizeBytes(); @endphp
            <div class="col-12 col-lg-4 item features-without-image item-mb">
                <div class="item-wrapper">
                    <a href="{{ asset($doc->file_path) }}" target="_blank" rel="noopener noreferrer">
                        <div class="card-box">
                            <div class="title-wrap">
                                <h4 class="item-title mbr-fonts-style display-7"><strong>{{ $doc->title }}</strong><br>PDF
                                @if($docSize)
                                , {{ number_format($docSize / 1048576, 1) }} Mb
                                @endif
                                </h4>
                                <span class="mbr-iconfont material material-note" style="color: rgb(72, 49, 131); fill: rgb(72, 49, 131);"></span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endforeach
@endif

@if($event->eventImages && $event->eventImages->count() > 0)
@push('styles')
<style>
#gallery07-kx .gallery-wrapper { overflow: visible; }
#gallery07-kx .event-gallery-scroll-wrap { overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; padding-bottom: 0.5rem; }
#gallery07-kx .event-gallery-thumb { cursor: pointer; -webkit-tap-highlight-color: transparent; touch-action: manipulation; }
#gallery07-kx .event-gallery-thumb img { display: block; border-radius: 0.5rem; }
#event-gallery-lightbox { display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.95); align-items: center; justify-content: center; overflow: hidden; flex-direction: row; }
#event-gallery-lightbox.show { display: flex !important; }
#event-gallery-lightbox .event-gallery-lightbox-content { position: relative; z-index: 10; display: flex; align-items: center; justify-content: center; max-width: 90%; max-height: 90vh; }
#event-gallery-lightbox .event-gallery-lightbox-content img { display: block; max-width: 100%; max-height: 90vh; width: auto; height: auto; object-fit: contain; }
.event-gallery-lightbox-backdrop { cursor: default; }
</style>
@endpush
<section data-bs-version="5.1" class="gallery07 cid-v9NkiVpDbd" id="gallery07-kx">
  <div class="container-fluid gallery-wrapper">
    <div class="row justify-content-center">
      <div class="col-12 content-head"></div>
    </div>
    <div class="event-gallery-scroll-wrap">
    <div class="grid-container">
      <div class="grid-container-3 moving-left">
        @foreach($event->eventImages as $idx => $img)
        <div class="grid-item event-gallery-thumb" data-src="{{ asset($img->image) }}" data-index="{{ $idx }}" role="button" tabindex="0" aria-label="View image {{ $idx + 1 }}">
          <img src="{{ asset($img->image) }}" alt="{{ $event->title }}">
        </div>
        @endforeach
      </div>
    </div>
    </div>
  </div>
</section>

{{-- Lightbox modal --}}
<div id="event-gallery-lightbox" class="event-gallery-lightbox" role="dialog" aria-modal="true" aria-label="Image gallery">
  <div class="event-gallery-lightbox-backdrop" style="position: absolute; inset: 0; z-index: 0;"></div>
  <div class="event-gallery-lightbox-content">
    <img id="event-gallery-lightbox-img" src="" alt="{{ $event->title }}">
  </div>
  <button type="button" class="event-gallery-close" aria-label="Close" style="position: absolute; top: 1rem; right: 1rem; z-index: 11; background: rgba(255,255,255,0.2); border: none; color: #fff; width: 48px; height: 48px; border-radius: 50%; font-size: 1.5rem; cursor: pointer;">&times;</button>
  <button type="button" class="event-gallery-prev" aria-label="Previous" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); z-index: 11; background: rgba(255,255,255,0.2); border: none; color: #fff; width: 48px; height: 48px; border-radius: 50%; font-size: 1.5rem; cursor: pointer;">&#10094;</button>
  <button type="button" class="event-gallery-next" aria-label="Next" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); z-index: 11; background: rgba(255,255,255,0.2); border: none; color: #fff; width: 48px; height: 48px; border-radius: 50%; font-size: 1.5rem; cursor: pointer;">&#10095;</button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var lightbox = document.getElementById('event-gallery-lightbox');
    var lightboxImg = document.getElementById('event-gallery-lightbox-img');
    if (!lightbox || !lightboxImg) return;

    var thumbs = document.querySelectorAll('#gallery07-kx .event-gallery-thumb');
    var urls = [];
    for (var i = 0; i < thumbs.length; i++) {
        var src = thumbs[i].getAttribute('data-src');
        if (!src && thumbs[i].querySelector('img')) src = thumbs[i].querySelector('img').getAttribute('src');
        urls.push(src || '');
    }
    if (urls.length === 0 || !urls[0]) return;

    var currentIndex = 0;

    function showImage(index) {
        if (index < 0) index = urls.length - 1;
        if (index >= urls.length) index = 0;
        currentIndex = index;
        var url = urls[currentIndex];
        if (url) {
            lightboxImg.src = url;
            lightboxImg.style.visibility = 'visible';
        }
    }

    function openLightbox(index) {
        var idx = typeof index === 'number' ? index : 0;
        currentIndex = idx;
        showImage(currentIndex);
        lightbox.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('show');
        document.body.style.overflow = '';
    }

    document.addEventListener('click', function(e) {
        var thumb = e.target.closest('#gallery07-kx .event-gallery-thumb');
        if (thumb) {
            e.preventDefault();
            e.stopPropagation();
            var i = parseInt(thumb.getAttribute('data-index'), 10);
            openLightbox(isNaN(i) ? 0 : i);
            return;
        }
        if (e.target.closest('#event-gallery-lightbox')) {
            if (e.target.classList.contains('event-gallery-lightbox-backdrop')) closeLightbox();
        }
    });

    var closeBtn = lightbox.querySelector('.event-gallery-close');
    var prevBtn = lightbox.querySelector('.event-gallery-prev');
    var nextBtn = lightbox.querySelector('.event-gallery-next');
    if (closeBtn) closeBtn.addEventListener('click', function(e) { e.stopPropagation(); closeLightbox(); });
    if (prevBtn) prevBtn.addEventListener('click', function(e) { e.stopPropagation(); showImage(currentIndex - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function(e) { e.stopPropagation(); showImage(currentIndex + 1); });

    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('show')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') showImage(currentIndex - 1);
        if (e.key === 'ArrowRight') showImage(currentIndex + 1);
    });
});
</script>
@endpush
@endif

<section data-bs-version="5.1" class="content11 cid-v9NhQKNsvE" id="content11-back">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mbr-section-btn align-center"><a class="btn btn-primary display-4" href="{{ route('portal.materials') }}"><span class="material material-perm-media mbr-iconfont mbr-iconfont-btn" style="font-size: 28px;"></span>Back to Event materials</a></div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.portal')

@section('title', $training->title ?? 'Training video')

@section('content')
<section data-bs-version="5.1" class="video2 cid-v9NaQi6ijF" id="video2-k9">
  <div class="container">
    <div class="mbr-section-head">
      <h4 class="mbr-section-title mbr-fonts-style mb-0 display-7"><a href="{{ route('portal') }}" class="text-primary">Portal</a>&nbsp; |&nbsp; <a href="{{ route('portal.training') }}" class="text-primary">Training</a>&nbsp; |&nbsp; {{ $training->title }}</h4>
      <h5 class="mbr-section-subtitle mbr-fonts-style mb-0 mt-2 display-2"><strong>{{ $training->title }}</strong></h5>
    </div>
    <div class="row justify-content-center mt-4">
      <div class="col-12 col-md-12 video-block">
        @if($training->video)
        @php
          $vext = strtolower(pathinfo($training->video, PATHINFO_EXTENSION));
          $vtype = $vext === 'webm' ? 'video/webm' : ($vext === 'mov' ? 'video/quicktime' : 'video/mp4');
          $streamPath = \Illuminate\Support\Str::replaceFirst('storage/', '', $training->video);
          $streamUrl = route('stream.video', ['path' => $streamPath]);
        @endphp
        <div class="video-wrapper portal-video-wrap">
          <video class="w-100 portal-video-player" controls preload="auto" playsinline crossorigin="anonymous">
            <source src="{{ $streamUrl }}" type="{{ $vtype }}">
            Your browser does not support the video tag.
          </video>
        </div>
        @elseif($training->training_image)
        <div class="video-wrapper portal-video-wrap">
          <img src="{{ asset($training->training_image) }}" alt="{{ $training->title }}" class="w-100">
        </div>
        @else
        <div class="alert alert-secondary">No video or image available for this training.</div>
        @endif
      </div>
    </div>
    <div class="row justify-content-center mt-4">
      <div class="col-12 col-md-12 video-block">
        <div class="card card-wrapper text-start">
          <div class="card-box align-left text-start">
            <h4 class="card-title mbr-semibold pb-3 mbr-black mbr-fonts-style display-5 text-start"><strong>Description</strong></h4>
            <p class="mbr-text pb-3 mbr-regular mbr-black mbr-fonts-style display-7 text-start">
              @if($training->description)
                {{nl2br(e($training->description))}}
              @else
                {{ $training->title }}. Watch the content above for learning materials and insights.
              @endif
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="content11 cid-v9NaeVCpJ7" id="content11-q0">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="mbr-section-btn align-center"><a class="btn btn-primary display-4" href="{{ route('portal.training') }}">Training</a></div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('styles')
<style>
/* Training video/image: full rounded border so corners are not cut (same as interviews) */
#video2-k9 .portal-video-wrap {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  max-height: 720px;
  overflow: hidden;
  border-radius: 0.5rem;
  background: #000;
  border: 3px solid #fff;
  box-sizing: border-box;
}
#video2-k9 .portal-video-wrap video {
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
#video2-k9 .portal-video-wrap img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
  display: block;
  border-radius: 0.35rem;
}
#video2-k9 .portal-video-player {
  z-index: 2;
  touch-action: manipulation;
}
#video2-k9 .video-wrapper {
  position: relative;
}
#video2-k9 .card-box,
#video2-k9 .card-title,
#video2-k9 .mbr-text {
  text-align: start !important;
}
</style>
@endpush

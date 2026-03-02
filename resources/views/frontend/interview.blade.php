@extends('layouts.portal')

@section('title', $interview->title)

@section('content')
<section data-bs-version="5.1" class="video2 cid-v9NaQi6ijF" id="video2-k9">
    <div class="container">
        <div class="mbr-section-head">
            <h4 class="mbr-section-title mbr-fonts-style mb-0 display-7"><a href="{{ route('portal') }}" class="text-primary">Portal</a>&nbsp; |&nbsp; <a href="{{ route('portal.interviews') }}" class="text-primary">Expert Interviews</a>&nbsp; |&nbsp; {{ $interview->title }}</h4>
            <h5 class="mbr-section-subtitle mbr-fonts-style mb-0 mt-2 display-2"><strong>{{ $interview->title }}</strong></h5>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-md-12 video-block">
                @if($interview->video)
                @php
                    $vext = strtolower(pathinfo($interview->video, PATHINFO_EXTENSION));
                    $vtype = $vext === 'webm' ? 'video/webm' : ($vext === 'mov' ? 'video/quicktime' : 'video/mp4');
                    $streamPath = Str::replaceFirst('storage/', '', $interview->video);
                    $streamUrl = route('stream.video', ['path' => $streamPath]);
                @endphp
                <div class="video-wrapper portal-video-wrap">
                    <video class="w-100 portal-video-player" controls preload="auto" playsinline crossorigin="anonymous">
                        <source src="{{ $streamUrl }}" type="{{ $vtype }}">
                        Your browser does not support the video tag.
                    </video>
                </div>
                @elseif($interview->video_embed_url)
                <div class="video-wrapper">
                    <iframe class="mbr-embedded-video" src="{{ $interview->video_embed_url }}" width="1280" height="720" frameborder="0" allowfullscreen></iframe>
                </div>
                @elseif($interview->interview_image)
                <div class="video-wrapper portal-video-wrap">
                    <img src="{{ asset($interview->interview_image) }}" alt="{{ $interview->title }}" class="w-100">
                </div>
                @else
                <div class="alert alert-secondary">No video or interview image available.</div>
                @endif
            </div>
        </div>
    </div>
</section>

<section data-bs-version="5.1" class="testimonials01 cid-v9NdB8ThPx" id="testimonials01-kd">
    <div class="container">
        <div class="row justify-content-center">
            <div class="card col-12 col-md-12 col-lg-8">
                <div class="card-wrapper">
                    <div class="card-box align-left">
                        <h4 class="card-title align-left mbr-semibold pb-3 mbr-black mbr-fonts-style display-5"><strong>Description of the interview</strong></h4>
                        <p class="mbr-text pb-3 mbr-regular align-left mbr-black mbr-fonts-style display-7">
                            @if($interview->description)
                                {{nl2br(e($interview->description))}}
                            @else
                                {{ $interview->title }}@if($interview->duration) — {{ $interview->duration }}@endif. Watch the full interview above for expert perspective on cybersecurity, digital risk, and emerging technologies.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="card col-12 col-md-12 col-lg-4">
                <div class="name-wrapper mbr-flex">
                    @php $participant = $interview->participant; @endphp
                    @if($participant && $participant->image)
                    <img src="{{ asset($participant->image) }}" alt="{{ $participant->name }}">
                    @elseif($interview->image)
                    <img src="{{ asset($interview->image) }}" alt="{{ $participant ? $participant->name : $interview->title }}">
                    @else
                    <img src="{{ asset('frontend/assets/images/ahmed-al-zarouni-2-200x200.jpg') }}" alt="{{ $participant ? $participant->name : $interview->title }}">
                    @endif
                </div>
                <p class="mbr-text pb-3 align-left mbr-regular mbr-black mbr-fonts-style display-4">
                    <strong>{{ $participant ? $participant->name : $interview->title }}</strong>
                    @if($participant)
                    @php
                        $subline = $participant->position ?? '';
                        if ($participant->company) { $subline = $subline ? $subline . ', ' . $participant->company->name : $participant->company->name; }
                        if (!$subline) { $subline = 'Expert Interview'; }
                    @endphp
                    <br>{{ $subline }}
                    @else
                    <br>Expert Interview
                    @endif
                </p>
            </div>
        </div>
    </div>
</section>
@push('styles')
<style>
/* Interview video/image: full rounded border so corners are not cut */
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
#video2-k9 .video-wrapper::before {
  pointer-events: none !important;
}
</style>
@endpush
@endsection

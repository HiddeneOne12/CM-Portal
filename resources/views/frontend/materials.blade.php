@extends('layouts.portal')

@section('title', 'Event materials')

@section('content')
<section data-bs-version="5.1" class="article11 cid-v9NfC3HoBT" id="article11-kf">
    <div class="container">
        <div class="row justify-content-center">
            <div class="title col-md-12 col-lg-9">
                <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-2"><strong>Event materials</strong></h3>
                <h4 class="mbr-section-subtitle align-center mbr-fonts-style mt-4 display-5">The Event Materials section provides members with exclusive access to content from Cyber Majlis closed-door sessions, roundtables, and official events.</h4>
            </div>
        </div>
    </div>
</section>

<section data-bs-version="5.1" class="article13 cid-v9NfC3O2R6" id="article13-kg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="card col-md-12 col-lg-7">
                <div class="card-wrapper">
                    <div class="card-box align-left">
                        <p class="mbr-text mbr-fonts-style mt-4 display-7">
                        This section brings together presentation materials, video recordings, case studies, and supporting documents, capturing key discussions and insights shared during Cyber Majlis engagements and enabling members to revisit and apply them in their professional context.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section data-bs-version="5.1" class="news06 cid-v9Nge6Xoj7" id="news06-kk">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 mb-5 content-head">
        <h3 class="mbr-fonts-style align-center mb-0 mbr-section-maintitle display-2"><strong>Events</strong></h3>
      </div>
    </div>
    @forelse($events ?? [] as $event)
    <div class="row justify-content-center align-items-center item features-image">
      <div class="col-12 ">
        <div class="item-wrapper">
          <div class="row">
            <div class="col-12 col-md-12 col-lg-6 image-wrapper">
              @if($event->image)
              <img class="w-100" src="{{ asset($event->image) }}" alt="{{ $event->title }}">
              @else
              <img class="w-100" src="{{ asset('frontend/assets/images/img-6362.jpg-1551x1034.jpg') }}" alt="{{ $event->title }}">
              @endif
            </div>
            <div class="col-12 col-lg col-md-12">
              <div class="text-wrapper align-left">
                <h5 class="mbr-section-title mbr-fonts-style mb-3 display-5"><strong>{{ $event->title }}</strong></h5>
                <p class="price mbr-fonts-style mb-3 display-4">{{ $event->event_date ? $event->event_date->format('F j, Y') : '—' }}</p>
                <p class="mbr-text mbr-fonts-style mb-3 display-7">{{ $event->description ? Str::limit($event->description, 300) : '—' }}</p>
                <div class="mbr-section-btn mt-3"><a class="btn btn-lg btn-primary display-4" href="{{ route('portal.material', encryptIdForUrl($event->id)) }}">More</a></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="row justify-content-center">
      <div class="col-12 text-center text-muted py-5">No events yet.</div>
    </div>
    @endforelse
  </div>
</section>

@if(isset($events) && $events->total() > 4 && $events->hasMorePages())
<section data-bs-version="5.1" class="content11 cid-v9NfC48eWH" id="content11-ki">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mbr-section-btn align-center"><a class="btn btn-primary display-4" href="{{ $events->nextPageUrl() }}">Show more</a></div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection

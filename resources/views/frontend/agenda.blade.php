@extends('layouts.portal')

@section('title', 'Agenda')

@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/assets/parallax/jarallax.css') }}">
<style>
  /* Agenda: purple to very top so no white strip; header and top bar match */
  #header13-9s.cid-uD9p4ekiSj { background-color: #483183 !important; }
  .portal-top-fill { background: #483183 !important; }
</style>
@endpush

@section('content')
<section data-bs-version="5.1" class="header13 cid-uD9p4ekiSj mbr-parallax-background" id="header13-9s">
  <div class="align-center container">
    <div class="row justify-content-center">
      <div class="col-md-12 mb-5 content-head">
        <h1 class="mbr-section-title mbr-fonts-style display-1"><strong>{{ $upcomingEvent ? $upcomingEvent->title : 'Agenda' }}</strong></h1>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-12 col-md-12">
        @if($upcomingEvent && $upcomingEvent->image)
        <img src="{{ asset($upcomingEvent->image) }}" alt="{{ $upcomingEvent->title }}" class="w-100" title="">
        @else
        <img src="{{ asset('frontend/assets/images/w2-1876x1005.png') }}" alt="Agenda" class="w-100" title="">
        @endif
      </div>
    </div>
  </div>
</section>

@if($upcomingEvent)
<section data-bs-version="5.1" class="contacts1 cid-v9XtAJS2dv" id="contacts1-mi">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="card col-12 col-lg-4">
                <div class="card-wrapper">
                    <div class="card-box align-center">
                        <div class="image-wrapper">
                            <span class="mbr-iconfont material material-access-time" style="font-size: 40px; color: rgb(216, 199, 233); fill: rgb(216, 199, 233);"></span>
                        </div>
                        <h4 class="card-title mbr-fonts-style mb-2 display-7"><strong>Date &amp; Time</strong></h4>
                        <p class="mbr-text mbr-fonts-style mb-2 display-5">{{ $upcomingEvent->event_date ? $upcomingEvent->event_date->format('F j, Y') : '—' }}<br>@if($upcomingEvent->start_time || $upcomingEvent->end_time){{ $upcomingEvent->start_time ? substr($upcomingEvent->start_time, 0, 5) : '—' }} – {{ $upcomingEvent->end_time ? substr($upcomingEvent->end_time, 0, 5) : '—' }}@else—@endif</p>
                    </div>
                </div>
            </div>
            <div class="card col-12 col-lg-4">
                <div class="card-wrapper">
                    <div class="card-box align-center">
                        <div class="image-wrapper">
                            <span class="mbr-iconfont material material-domain" style="font-size: 40px; color: rgb(216, 199, 233); fill: rgb(216, 199, 233);"></span>
                        </div>
                        <h4 class="card-title mbr-fonts-style align-center mb-2 display-7"><strong>Venue</strong></h4>
                        <p class="mbr-text mbr-fonts-style mb-2 display-5">{{ $upcomingEvent->location ?: '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($upcomingEvent->description)
<section data-bs-version="5.1" class="article11 cid-uD9fAUSs8p" id="article11-9r">
    <div class="container">
        <div class="row justify-content-center">
            <div class="title col-md-12 col-lg-10">
                <p class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-5">{{ $upcomingEvent->description }}</p>
            </div>
        </div>
    </div>
</section>
@endif
@else
<section data-bs-version="5.1" class="article11 cid-uD9fAUSs8p" id="article11-9r">
    <div class="container">
        <div class="row justify-content-center">
            <div class="title col-md-12 col-lg-10">
                <p class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-5">No upcoming event is currently scheduled. Check back later for the next Cyber Majlis agenda.</p>
            </div>
        </div>
    </div>
</section>
@endif

<section data-bs-version="5.1" class="article11 cid-v9Y0dMblem" id="article11-ny">
    <div class="container">
        <div class="row justify-content-center">
            <div class="title col-md-12 col-lg-10">
                <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-2"><strong>Schedule</strong></h3>
            </div>
        </div>
    </div>
</section>

@if($upcomingEvent && $upcomingEvent->eventParticipants && $upcomingEvent->eventParticipants->count() > 0)
@foreach($upcomingEvent->eventParticipants as $ep)
@php $p = $ep->participant; @endphp
<section data-bs-version="5.1" class="features9 cid-v9XPsPHo80" id="schedule-{{ $ep->id }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row item-border">
                    <div class="card col-12 col-md-2 col-lg-3">
                        <div class="card-wrapper">
                            <div class="card-box">
                                <h4 class="card-subtitle align-left mbr-fonts-style mb-2 display-5">@if($ep->start_time || $ep->end_time)<strong>{{ $ep->start_time ? substr($ep->start_time, 0, 5) : '—' }} – {{ $ep->end_time ? substr($ep->end_time, 0, 5) : '—' }}</strong>@endif</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card icon col-12 col-md-2 col-lg-2">
                        <div class="card-wrapper">
                            <div class="img-wrapper">
                                @if($p->image)
                                <img src="{{ asset($p->image) }}" alt="{{ $p->name }}">
                                @else
                                <img src="{{ asset('frontend/assets/images/img-3788-artguru-200x200.jpg') }}" alt="">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card col-12 col-md-8 col-lg">
                        <div class="card-wrapper">
                            <div class="card-box">
                                <h5 class="card-title align-left mbr-fonts-style display-5"><strong>{{ $ep->role ?: $ep->topic ?: 'Speaker' }}</strong></h5>
                                <p class="card-text align-left mbr-fonts-style display-4"><strong>{{ $p->name }}<br></strong>
                                @if($p->position){{ $p->position }}@endif
                                @if($p->company)@if($p->position)<br>@endif{{ $p->company->name }}@endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endforeach
@endif

<section data-bs-version="5.1" class="features6 start cid-v9Y0PHmBFB" id="features06-nz">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 content-head">
                <div class="mbr-section-head mb-5">
                    <h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2"><span style="font-size: 44.8px;"><strong>Cyber Majlis research</strong></span></h4>
                </div>
            </div>
        </div>
        <div class="row">
            @forelse($reports ?? [] as $report)
            <div class="item features-image col-12 col-md-6 col-lg-4">
                <div class="item-wrapper">
                    <div class="item-img">
                        @if($report->image)
                        <img src="{{ asset($report->image) }}" alt="{{ $report->title }}">
                        @else
                        <img src="{{ asset('frontend/assets/images/cm-banners-25-660x660.png') }}" alt="{{ $report->title }}">
                        @endif
                    </div>
                    <div class="item-content">
                        <h5 class="item-title mbr-fonts-style display-5"><strong>{{ $report->title }}</strong></h5>
                        @if($report->description)
                        <p class="mbr-text mbr-fonts-style display-7 mb-2">{{ Str::limit($report->description, 120) }}</p>
                        @endif
                        <p class="mbr-text mbr-fonts-style display-4">{{ $report->published_in_date ? $report->published_in_date->format('Y') : '' }}</p>
                       
                        <div class="mbr-section-btn item-footer">
                            @if($report->report_pdf)
                            <a href="{{ asset($report->report_pdf) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary item-btn display-4"><span class="material material-file-download mbr-iconfont mbr-iconfont-btn" style="font-size: 25px;"></span>View Report</a>
                            @else
                            <span class="btn btn-secondary item-btn display-4">No PDF</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">No reports available yet.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection

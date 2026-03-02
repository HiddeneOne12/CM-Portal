@foreach($interviews as $item)
@php $interviewUrl = route('portal.interview', encryptIdForUrl($item->id)); @endphp
<div class="item features-image col-12 col-md-6 col-lg-4">
    <div class="item-wrapper interview-card-wrapper">
        <a href="{{ $interviewUrl }}" class="interview-card-link" aria-label="View {{ $item->title }}"></a>
        <div class="item-img">
            @if($item->image)
            <img src="{{ asset($item->image) }}" alt="{{ $item->title }}">
            @else
            <div class="bg-light rounded" style="height: 200px; display: flex; align-items: center; justify-content: center;"><span class="text-muted">No image</span></div>
            @endif
        </div>
        <div class="item-content">
            <h5 class="item-title mbr-fonts-style display-5"><strong>{{ $item->title }}</strong></h5>
            @if($item->duration)
            <p class="mbr-text mbr-fonts-style display-7">{{ $item->duration }}</p>
            @endif
            <div class="mbr-section-btn item-footer">
                @if($item->video || $item->video_link || $item->interview_image)
                <span class="btn btn-primary item-btn display-4"><span class="material material-play-arrow mbr-iconfont mbr-iconfont-btn" style="font-size: 25px;"></span>Watch</span>
                @else
                <span class="btn btn-secondary item-btn display-4">View</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@foreach($trainings as $item)
<div class="item features-image col-12 col-md-6 col-lg-4">
  <div class="item-wrapper interview-card-wrapper">
    <a href="{{ route('portal.training-video', encryptIdForUrl($item->id)) }}" class="interview-card-link" aria-label="Watch {{ $item->title }}"></a>
    <div class="item-img">
      @if($item->image)
      <img src="{{ asset($item->image) }}" alt="{{ $item->title }}">
      @else
      <img src="{{ asset('frontend/assets/images/-whatsapp-2024-11-01-15.29.06-10db1a35-1256x940.jpeg') }}" alt="{{ $item->title }}">
      @endif
    </div>
    <div class="item-content">
      <h5 class="item-title mbr-fonts-style display-5"><strong>{{ $item->title }}</strong></h5>
      <div class="mbr-section-btn item-footer">
        <span class="btn btn-primary item-btn display-4"><span class="material material-play-arrow mbr-iconfont mbr-iconfont-btn" style="font-size: 25px;"></span>Watch</span>
      </div>
    </div>
  </div>
</div>
@endforeach

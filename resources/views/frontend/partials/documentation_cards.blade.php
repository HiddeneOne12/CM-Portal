@foreach($documentations as $doc)
<div class="item features-image col-12 col-md-6 col-lg-4">
  <div class="item-wrapper documentation-card-wrapper">
    @if($doc->report_pdf)
    <a href="{{ asset($doc->report_pdf) }}" target="_blank" rel="noopener noreferrer" class="documentation-card-link" aria-label="View report: {{ $doc->title }}"></a>
    @endif
    <div class="item-img">
      @if($doc->image)
      <img src="{{ asset($doc->image) }}" alt="{{ $doc->title }}">
      @else
      <img src="{{ asset('frontend/assets/images/cm-banners-25-660x660.png') }}" alt="{{ $doc->title }}">
      @endif
    </div>
    <div class="item-content">
      <h5 class="item-title mbr-fonts-style display-5"><strong>{{ $doc->title }}</strong></h5>
      <p class="mbr-text mbr-fonts-style display-7">{{ Str::limit($doc->description, 80) ?: 'A section of reference materials, frameworks, and internal guidelines.' }}</p>
      <p class="mbr-text mbr-fonts-style display-4">{{ $doc->published_in_date ? $doc->published_in_date->format('Y') : '' }}</p>
      <div class="mbr-section-btn item-footer">
        @if($doc->report_pdf)
        <span class="btn btn-primary item-btn display-4"><span class="material material-file-download mbr-iconfont mbr-iconfont-btn" style="font-size: 25px;"></span>View Report</span>
        @else
        <span class="btn btn-secondary item-btn display-4">No PDF</span>
        @endif
      </div>
    </div>
  </div>
</div>
@endforeach

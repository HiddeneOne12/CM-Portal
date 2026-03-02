@foreach($reports as $report)
<div class="item features-image col-12 col-md-6 col-lg-4">
    <div class="item-wrapper report-card-wrapper">
        @if($report->report_pdf)
        <a href="{{ asset($report->report_pdf) }}" target="_blank" rel="noopener noreferrer" class="report-card-link" aria-label="View report: {{ $report->title }}"></a>
        @endif
        <div class="item-img">
            @if($report->image)
            <img src="{{ asset($report->image) }}" alt="{{ $report->title }}">
            @else
            <div class="bg-light rounded" style="height: 200px; display: flex; align-items: center; justify-content: center;"><span class="text-muted">No image</span></div>
            @endif
        </div>
        <div class="item-content">
            <h5 class="item-title mbr-fonts-style display-5"><strong>{{ $report->title }}</strong></h5>
            @if($report->description)
            <p class="mbr-text mbr-fonts-style display-7 mt-2 mb-2">{{ Str::limit($report->description, 120) }}</p>
            @endif
            <p class="mbr-text mbr-fonts-style display-4">{{ $report->published_in_date ? $report->published_in_date->format('Y') : '' }}</p>
            <div class="mbr-section-btn item-footer">
                @if($report->report_pdf)
                <span class="btn btn-primary item-btn display-4"><span class="material material-file-download mbr-iconfont mbr-iconfont-btn" style="font-size: 25px;"></span>View Report</span>
                @else
                <span class="btn btn-secondary item-btn display-4 disabled">No PDF</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

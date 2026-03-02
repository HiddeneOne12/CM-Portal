@extends('layouts.portal')

@section('title', 'Reports')

@push('styles')
<style>
/* Whole report card clickable via overlay link; keeps theme flex layout so View Report stays inside card */
#features06-lz .report-card-wrapper { position: relative; }
#features06-lz .report-card-link {
  position: absolute;
  inset: 0;
  z-index: 1;
  cursor: pointer;
}
#features06-lz .report-card-link:hover { opacity: 0; }
#features06-lz .item-img,
#features06-lz .item-content { position: relative; z-index: 0; }
</style>
@endpush
@section('content')
<section data-bs-version="5.1" class="article11 cid-v9Nrm0kr3Y" id="article11-lu">
    <div class="container">
        <div class="row justify-content-center">
            <div class="title col-md-12 col-lg-9">
                <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-2"><strong>Reports</strong></h3>
                <h4 class="mbr-section-subtitle align-center mbr-fonts-style mt-4 display-5">The Research Reports section presents original analytical publications developed within the Cyber Majlis ecosystem, focusing on cybersecurity trends, AI,  threat landscapes, and strategic priorities in the UAE and global context.</h4>
            </div>
        </div>
    </div>
</section>

<section data-bs-version="5.1" class="article13 cid-v9Nrm0yNcz" id="article13-lv">
    <div class="container">
        <div class="row justify-content-center">
            <div class="card col-md-12 col-lg-7">
                <div class="card-wrapper">
                    <div class="card-box align-left">
                        <p class="mbr-text mbr-fonts-style mt-4 display-7">
                        These reports are designed to support informed decision-making by providing structured analysis, benchmarks, and practical recommendations for leaders and senior professionals navigating an evolving digital risk environment.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section data-bs-version="5.1" class="features6 start cid-v9NrMfHxZr" id="features06-lz">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 content-head">
                <div class="mbr-section-head mb-5">
                    <h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
                        <strong>Reports</strong>
                    </h4>
                </div>
            </div>
        </div>
        <div class="row" id="reports-grid">
            @if(isset($reports) && $reports->count() > 0)
            @include('frontend.partials.report_cards', ['reports' => $reports])
            @else
            <div class="col-12">
                <p class="mbr-text mbr-fonts-style display-7 text-center text-muted">No reports available yet.</p>
            </div>
            @endif
        </div>
    </div>
</section>

@if(isset($reports) && $reports->hasMorePages())
<section data-bs-version="5.1" class="content11 cid-v9NaeVCpJ7" id="load-more-reports-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mbr-section-btn align-center">
                    <button type="button" class="btn btn-primary display-4" id="load-more-reports" data-next-page="{{ $reports->currentPage() + 1 }}">Load more</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var loadMoreBtn = document.getElementById('load-more-reports');
    var grid = document.getElementById('reports-grid');
    var section = document.getElementById('load-more-reports-section');
    if (!loadMoreBtn || !grid) return;

    loadMoreBtn.addEventListener('click', function() {
        var page = loadMoreBtn.getAttribute('data-next-page');
        if (!page || page === '0') return;
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Loading...';

        var url = '{{ route("portal.reports.load-more") }}?page=' + page;
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success && data.html) {
                    var wrap = document.createElement('div');
                    wrap.innerHTML = data.html.trim();
                    while (wrap.firstChild) grid.appendChild(wrap.firstChild);
                }
                if (!data.has_more || !section) {
                    if (section) section.remove();
                } else {
                    loadMoreBtn.setAttribute('data-next-page', data.next_page);
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.textContent = 'Load more';
                }
            })
            .catch(function() {
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load more';
            });
    });
});
</script>
@endpush

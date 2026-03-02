@extends('layouts.portal')

@section('title', 'Expert Interviews')

@push('styles')
<style>
/* Whole interview card clickable via overlay link; keeps theme flex layout so Watch stays inside card */
#features06-jv .interview-card-wrapper { position: relative; }
#features06-jv .interview-card-link {
  position: absolute;
  inset: 0;
  z-index: 1;
  cursor: pointer;
}
#features06-jv .interview-card-link:hover { opacity: 0; }
#features06-jv .item-img,
#features06-jv .item-content { position: relative; z-index: 0; }
</style>
@endpush
@section('content')
<section data-bs-version="5.1" class="article11 cid-v9MNHr05oO" id="article11-j7">
    <div class="container">
        <div class="row justify-content-center">
            <div class="title col-md-12 col-lg-9">
                <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-2"><strong>Expert Interviews</strong></h3>
                <h4 class="mbr-section-subtitle align-center mbr-fonts-style mt-4 display-5">The Expert Interviews section is a core component of the Cyber Majlis Members' Portal, created to capture and share expert perspectives on cybersecurity, digital risk, and emerging technologies.</h4>
            </div>
        </div>
    </div>
</section>

<section data-bs-version="5.1" class="article13 cid-v9N1VjN8V3" id="article13-jm">
    <div class="container">
        <div class="row justify-content-center">
            <div class="card col-md-12 col-lg-7">
                <div class="card-wrapper">
                    <div class="card-box align-left">
                        <p class="mbr-text mbr-fonts-style mt-4 display-7">
                        Through structured conversations with CISOs, cybersecurity leaders, and industry experts, this section provides access to real-world experience, strategic insights, and applied approaches to addressing complex cyber challenges in the UAE and global context.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section data-bs-version="5.1" class="features6 start cid-v9N7UCs9w6" id="features06-jv">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 content-head">
                <div class="mbr-section-head mb-5">
                    <h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
                        <strong>Interviews</strong>
                    </h4>
                </div>
            </div>
        </div>
        <div class="row" id="interviews-grid">
            @if(isset($interviews) && $interviews->count() > 0)
            @include('frontend.partials.interview_cards', ['interviews' => $interviews])
            @else
            <div class="col-12">
                <p class="mbr-text mbr-fonts-style display-7 text-center text-muted">No interviews available yet.</p>
            </div>
            @endif
        </div>
    </div>
</section>

@if(isset($interviews) && $interviews->hasMorePages())
<section data-bs-version="5.1" class="content11 cid-v9NaeVCpJ7" id="load-more-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="mbr-section-btn align-center">
                    <button type="button" class="btn btn-primary display-4" id="load-more-interviews" data-next-page="{{ $interviews->currentPage() + 1 }}">Load more</button>
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
    var loadMoreBtn = document.getElementById('load-more-interviews');
    var grid = document.getElementById('interviews-grid');
    var section = document.getElementById('load-more-section');
    if (!loadMoreBtn || !grid) return;

    loadMoreBtn.addEventListener('click', function() {
        var page = loadMoreBtn.getAttribute('data-next-page');
        if (!page || page === '0') return;
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Loading...';

        var url = '{{ route("portal.interviews.load-more") }}?page=' + page;
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

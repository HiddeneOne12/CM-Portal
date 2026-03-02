@extends('layouts.portal')

@section('title', 'Training')

@push('styles')
<style>
/* Whole training card clickable via overlay link; same as interviews */
#features06-training .interview-card-wrapper { position: relative; }
#features06-training .interview-card-link {
  position: absolute;
  inset: 0;
  z-index: 1;
  cursor: pointer;
}
#features06-training .interview-card-link:hover { opacity: 0; }
#features06-training .item-img,
#features06-training .item-content { position: relative; z-index: 0; }
/* Title close under image */
#features06-training .item-content { padding-top: 0.75rem; margin-top: 0; }
#features06-training .item-title { margin-top: 0; margin-bottom: 0.5rem; }
</style>
@endpush

@section('content')
<section data-bs-version="5.1" class="article11 cid-v9MNHr05oO" id="article11-ps">
  <div class="container">
    <div class="row justify-content-center">
      <div class="title col-md-12 col-lg-9">
        <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-2"><strong>Training</strong></h3>
        <h4 class="mbr-section-subtitle align-center mbr-fonts-style mt-4 display-5">A curated collection of learning materials and educational resources focused on technology, cybersecurity, and digital transformation.</h4>
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="article13 cid-v9N1VjN8V3" id="article13-pt">
  <div class="container">
    <div class="row justify-content-center">
      <div class="card col-md-12 col-lg-7">
        <div class="card-wrapper">
          <div class="card-box align-left">
            <p class="mbr-text mbr-fonts-style mt-4 display-7">
              The Training section supports continuous professional development through practical insights, expert-led content, and applied knowledge relevant to ICD subsidiaries.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="features6 start cid-v9N7UCs9w6" id="features06-training">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 content-head">
        <div class="mbr-section-head mb-5">
          <h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2">
            <strong>Training videos</strong>
          </h4>
        </div>
      </div>
    </div>
    <div class="row" id="training-grid">
      @if(isset($trainings) && $trainings->count() > 0)
      @include('frontend.partials.training_cards', ['trainings' => $trainings])
      @else
      <div class="col-12">
        <p class="mbr-text mbr-fonts-style display-7 text-center text-muted">No training videos yet.</p>
      </div>
      @endif
    </div>
  </div>
</section>

@if(isset($trainings) && $trainings->hasMorePages())
<section data-bs-version="5.1" class="content11 cid-v9NaeVCpJ7" id="load-more-training-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="mbr-section-btn align-center">
          <button type="button" class="btn btn-primary display-4" id="load-more-training" data-next-page="{{ $trainings->currentPage() + 1 }}">Load more</button>
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
  var loadMoreBtn = document.getElementById('load-more-training');
  var grid = document.getElementById('training-grid');
  var section = document.getElementById('load-more-training-section');
  if (!loadMoreBtn || !grid) return;

  loadMoreBtn.addEventListener('click', function() {
    var page = loadMoreBtn.getAttribute('data-next-page');
    if (!page || page === '0') return;
    loadMoreBtn.disabled = true;
    loadMoreBtn.textContent = 'Loading...';

    var url = '{{ route("portal.training.load-more") }}?page=' + page;
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

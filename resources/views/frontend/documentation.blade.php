@extends('layouts.portal')

@section('title', 'Documentation')

@push('styles')
<style>
/* Whole documentation card clickable via overlay link (opens PDF); same as reports */
#features06-me .item.features-image { display: flex; flex-direction: column; }
#features06-me .documentation-card-wrapper {
  position: relative;
  flex: 1;
  display: flex;
  flex-direction: column;
}
#features06-me .documentation-card-link {
  position: absolute;
  inset: 0;
  z-index: 1;
  cursor: pointer;
  display: block;
}
#features06-me .documentation-card-link:hover { opacity: 0; }
#features06-me .item-img,
#features06-me .item-content { position: relative; z-index: 0; }
</style>
@endpush

@section('content')
<section data-bs-version="5.1" class="article11 cid-v9XruhWtjV" id="article11-mc">
  <div class="container">
    <div class="row justify-content-center">
      <div class="title col-md-12 col-lg-8">
        <h3 class="mbr-section-title mbr-fonts-style align-center mt-0 mb-0 display-2"><strong>Documentation</strong></h3>
        <h4 class="mbr-section-subtitle align-center mbr-fonts-style mt-4 display-5">A section of reference materials, frameworks, and internal guidelines shared across ICD subsidiaries.</h4>
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="article13 cid-v9Xrui2UF5" id="article13-md">
  <div class="container">
    <div class="row justify-content-center">
      <div class="card col-md-12 col-lg-7">
        <div class="card-wrapper">
          <div class="card-box align-left">
            <p class="mbr-text mbr-fonts-style mt-4 display-7">
              This section provides structured documentation to support alignment, knowledge continuity, and informed decision-making across technology and digital initiatives.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section data-bs-version="5.1" class="features6 start cid-v9Xrui7OAz" id="features06-me">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 content-head"></div>
    </div>
    <div class="row" id="documentation-grid">
      @if(isset($documentations) && $documentations->count() > 0)
      @include('frontend.partials.documentation_cards', ['documentations' => $documentations])
      @else
      <div class="col-12">
        <p class="mbr-text mbr-fonts-style display-7 text-center text-muted">No documentation available yet.</p>
      </div>
      @endif
    </div>
  </div>
</section>

@if(isset($documentations) && $documentations->hasMorePages())
<section data-bs-version="5.1" class="content11 cid-v9NaeVCpJ7" id="load-more-documentation-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="mbr-section-btn align-center">
          <button type="button" class="btn btn-primary display-4" id="load-more-documentation" data-next-page="{{ $documentations->currentPage() + 1 }}">Load more</button>
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
  var loadMoreBtn = document.getElementById('load-more-documentation');
  var grid = document.getElementById('documentation-grid');
  var section = document.getElementById('load-more-documentation-section');
  if (!loadMoreBtn || !grid) return;

  loadMoreBtn.addEventListener('click', function() {
    var page = loadMoreBtn.getAttribute('data-next-page');
    if (!page || page === '0') return;
    loadMoreBtn.disabled = true;
    loadMoreBtn.textContent = 'Loading...';

    var url = '{{ route("portal.documentation.load-more") }}?page=' + page;
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

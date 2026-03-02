@extends('layouts.frontend')

@section('title', 'OTP - Cyber Majlis')

@push('styles')
  <link rel="stylesheet" href="{{ asset('frontend/assets/recaptcha.css') }}">
  <style>
    .alert.alert-danger.portal-error, .alert.alert-success.portal-alert { font-family: 'Wix Madefor Display', sans-serif; }
    .alert.alert-danger.portal-error { background-color: #dc3545; color: #fff; border-color: #dc3545; }
  </style>
@endpush

@section('content')
<section data-bs-version="5.1" class="form01 agentm5 cid-vabY1nnLsk mbr-fullscreen" id="form01-pl">
  <div class="container-fluid">
    <div class="row row-main">
      <div class="col-12 col-lg-6 card mx-auto mbr-form" data-form-type="formoid">
        <form action="{{ route('members-portal.verify-otp') }}" method="POST" class="mbr-form form-with-styler form-wrap">
          @csrf
          <div class="row">
            @if(session('success'))
              <div class="alert alert-success portal-alert col-12" role="alert">{{ session('success') }}</div>
            @endif
            @if(session('error'))
              <div class="alert alert-danger portal-error col-12" role="alert">{{ session('error') }}</div>
            @endif
            @if($errors->any())
              <div class="alert alert-danger portal-error col-12" role="alert">{{ $errors->first() }}</div>
            @endif
          </div>
          <div class="dragArea row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <h2 class="mbr-fonts-style display-5">Please enter the OTP sent to your email</h2>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
              <hr>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 form-group" data-for="otp">
              <input type="text" name="otp" placeholder="OTP" class="form-control display-7" value="{{ old('otp') }}" id="otp-input" autocomplete="one-time-code" maxlength="8" pattern="[0-9]{8}" title="Enter 8 digits">
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 mbr-section-btn">
              <button type="submit" class="w-100 btn btn-white display-7">Enter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>


@endsection

@push('scripts')
  <script src="{{ asset('frontend/assets/formoid.min.js') }}"></script>
@endpush

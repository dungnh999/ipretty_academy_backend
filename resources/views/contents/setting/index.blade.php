@extends('layouts/contentNavbarLayout')
@section('title', 'Thiết lập')
@section('content')
  <div class="row g-4 mb-4">
    <div class="col-xl-4">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Logo</h5>
        </div>
        <div class="card-body">
          <img id="logo-preview-setting-ipretty" src="https://www.shutterstock.com/image-vector/image-icon-trendy-flat-style-600nw-643080895.jpg" class="w-100 object-fit-cover">
          <label for="logo-setting-ipretty" class="btn btn-primary w-100 mt-2">Chọn Logo</label>
          <input type="file"  id="logo-setting-ipretty" accept="image/*" hidden>
        </div>
      </div>
    </div>
  </div>
  @include('contents.department.create')
  @include('contents.department.update')
@endsection
@push('pricing-script')
  <script src="{{asset('assets/js/setting/index.js')}}"></script>
@endpush

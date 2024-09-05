@extends('layouts/blankLayout')

@section('title', 'Hệ thống bảo trì')

@section('page-style')
  <!-- Page -->
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection


@section('content')
  <!-- Error -->
  <div class="container-xxl py-3">
    <div class="misc-wrapper">
      <h2 class="mb-2 mx-2">Hệ thống đang bảo trì</h2>
      <p class="mb-4 mx-2">Lỗi hệ thống cơ sở dữ liệu</p>
      <form onsubmit="return false">
        <div class="d-flex gap-2">
          <input type="email" class="form-control" placeholder="email" autofocus="">
          <button type="submit" class="btn btn-primary">Notify</button>
        </div>
      </form>
      <div class="mt-5">
        <img src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/illustrations/page-misc-error-light.png" alt="boy-with-rocket-light" width="500" class="img-fluid" data-app-dark-img="illustrations/boy-with-rocket-dark.png" data-app-light-img="illustrations/boy-with-rocket-light.png">
      </div>
    </div>
  </div>
  <!-- /Error -->
@endsection

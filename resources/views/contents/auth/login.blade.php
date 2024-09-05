@extends('layouts/blankLayout')

@section('title', 'Đăng nhập')
@section('page-style')
  <!-- Page -->
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection
@section('content')
  <div class="authentication-wrapper authentication-cover">
    <div class="authentication-inner row m-0">
      <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
        <div class="w-100 d-flex justify-content-center">
          <img
            src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/illustrations/boy-with-rocket-light.png"
            class="img-fluid" alt="Login image" width="700" data-app-dark-img="illustrations/boy-with-rocket-dark.png"
            data-app-light-img="illustrations/boy-with-rocket-light.png">
        </div>
      </div>
      <!-- /Left Text -->
      <!-- Login -->
      <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
        <div class="w-px-400 mx-auto">
          <!-- Logo -->
          <div class="app-brand mb-5 justify-content-center">
            <a href="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1" class="app-brand-link gap-2">
              <img src="https://upload-dungnh-dev.s3.ap-southeast-1.amazonaws.com/public/image/logo/Logo-header.png" alt="header__logo-image" width="200">
            </a>
          </div>
          <!-- /Logo -->
          <!-- ERROR -->
          <p class="text-center text-danger" id='text-error-login'></p>
          <!-- END ERROR -->
          <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
                action="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1"
                method="GET" novalidate="novalidate">
            <div class="mb-3 fv-plugins-icon-container">
              <label for="email" class="form-label">Tên đăng nhập</label>
              <input type="text" class="form-control" id="email" name="email-username"
                     placeholder="Nhập địa chỉ email" autofocus="" tabindex="1">
              <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
            </div>
            <div class="mb-3 form-password-toggle fv-plugins-icon-container">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Mật khẩu</label>
                <a
                  href="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1/auth/forgot-password-cover">
                  <small>Quên mật khẩu?</small>
                </a>
              </div>
              <div class="input-group input-group-merge has-validation">
                <input type="password" id="password" class="form-control" name="password" placeholder="············"
                       aria-describedby="password" tabindex="2">
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
              <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me"  tabindex="3">
                <label class="form-check-label" for="remember-me">
                  Ghi nhớ đăng nhập
                </label>
              </div>
            </div>
            <button type="button" class="btn btn-primary d-grid w-100" id="btn-login" tabindex="4">Đăng nhập</button>
          </form>
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
@endsection
@push('pricing-script')
  <script src="{{asset('assets/js/auth/index.js')}}"></script>
@endpush

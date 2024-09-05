<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
  href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
  rel="stylesheet">

<!-- Core CSS -->
<link rel="stylesheet" href="{{asset(mix('assets/css/demo.css')) }}"/>
<link rel="stylesheet" href="{{asset('style.css') }}"/>

<!-- Custom CSS -->

<link rel="stylesheet" href="{{asset('assets/css/custom.css') }}"/>

<!-- Vendors CSS -->
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/dataTable/css/dataTables.bootstrap5.css')) }}"/>
<link rel="stylesheet" href="{{asset(mix('assets/vendor/fonts/boxicons.css')) }}"/>
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')) }}"/>
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/dataTable/buttons/css/buttons.bootstrap5.css')) }}"/>
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/dataTable/responsive/css/responsive.bootstrap5.css')) }}"/>
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/sweetalert2/css/sweetalert2.css')) }}"/>
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/cropper/css/cropper.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/nouislider/css/nouislider.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/bs-stepper/css/bs-stepper.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/quill/css/quill.core.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/quill/css/quill.bubble.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/quill/css/quill.snow.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/select2/css/select2.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/jkanban/css/jkanban.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/libs/plyr/css/plyr.css'))}}">
<link rel="stylesheet" href="{{asset(mix('assets/vendor/css/core.css')) }}"/>
<link rel="stylesheet" href="{{asset(mix('assets/vendor/css/theme-default.css')) }}"/>

<!-- Vendor Styles -->
@yield('vendor-style')


<!-- Page Styles -->
@yield('page-style')

@extends('layouts/contentNavbarLayout')
@section('title', 'Danh mục khoá học')
@section('content')
  <div class="row g-4 mb-4">
    <div class="nav-align-top mb-4">
      <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                  data-bs-target="#navs-active-category-course" aria-controls="navs-pills-justified-home" aria-selected="true">
            <i class="tf-icons bx bx-home me-1"></i>
            <span class="d-none d-sm-block"> Đang hoạt động
                  <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                        id="totalActiveCourseCategory">0</span>
              </span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-unactive-category-course"
                  aria-controls="navs-pills-justified-profile" aria-selected="false" tabindex="-1">
            <i class="tf-icons bx bx-trash me-1"></i>
            <span class="d-none d-sm-block"> Tạm ngưng
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                      id="totalUnActiveCourseCategory">0</span>
            </span>
          </button>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="navs-active-category-course" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-category-course-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-center">Tên doanh mục</th>
                <th class="text-center">Mã doanh mục</th>
                <th class="text-center">Người tạo</th>
                <th class="text-center">Ngày tạo</th>
                <th></th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="navs-unactive-category-course" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-unactive-category-course-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-center">Tên Bộ Phận</th>
                <th class="text-center">Mã doanh mục</th>
                <th class="text-center">Người tạo</th>
                <th class="text-center">Ngày tạo</th>
                <th></th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('contents.category-course.create')
  @include('contents.category-course.update')
@endsection
@push('pricing-script')
  <script src="{{asset('assets/js/category-course/index.js')}}"></script>
@endpush

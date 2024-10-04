@extends('layouts/contentNavbarLayout')

@section('title', 'Quản lý tài khoản')

@section('content')
  <div class="row g-4 mb-4">
    <div class="nav-align-top mb-4">
      <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                  data-bs-target="#navs-manager-users" aria-controls="navs-pills-justified-home" aria-selected="true">
            <i class="tf-icons bx bx-home me-1"></i>
            <span class="d-none d-sm-block"> Quản Lý
                  <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                        id="totalUserManage">0</span>
              </span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-teacher-users"
                  aria-controls="navs-pills-justified-profile" aria-selected="false" tabindex="-1">
            <i class="tf-icons bx bx-user me-1"></i>
            <span class="d-none d-sm-block"> Giáo viên
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                      id="totalUserTeacher">0</span>
            </span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-employee-users"
                  aria-controls="navs-pills-justified-messages" aria-selected="false" tabindex="-1">
            <i class="tf-icons bx bxs-graduation me-1"></i>
            <span class="d-none d-sm-block"> Nhân viên
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                      id="totalUserEmployee">0</span>
            </span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-student-users"
                  aria-controls="navs-pills-justified-messages" aria-selected="false" tabindex="-1">
            <i class="tf-icons bx bxs-graduation me-1"></i>
            <span class="d-none d-sm-block"> Học viên
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                      id="totalUserStudent">0</span>
            </span>
          </button>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="navs-manager-users" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-manage-ipretty" class="datatables-users table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-right">Tên</th>
                <th class="text-right">Mã</th>
                <th class="text-center">Giới tính</th>
                <th class="text-center">Xác Thực</th>
                <th class="text-center">Trạng thái</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center"></th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="navs-teacher-users" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-teacher-ipretty" class="datatables-users table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-right">Tên</th>
                <th class="text-right">Mã</th>
                <th class="text-center">Giới tính</th>
                <th class="text-center">Xác Thực</th>
                <th class="text-center">Trạng thái</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center"></th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="navs-employee-users" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-employee-ipretty" class="datatables-users table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-right">Tên</th>
                <th class="text-center">Mã</th>
                <th class="text-center">Giới tính</th>
                <th class="text-center">Xác Thực</th>
                <th class="text-center">Trạng thái</th>
                <th class="text-center">Ngày tạo</th>
                <th class="text-center"></th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="navs-student-users" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-student-ipretty" class="datatables-users table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-right">Tên</th>
                <th class="text-center">Giới tính</th>
                <th class="text-center">Xác Thực</th>
                <th class="text-center">Trạng thái</th>
                <th class="text-center"></th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @include('contents.users.create')
  @include('contents.users.update')
  @include('contents.users.info')
@endsection
@push('pricing-script')
  <script src="{{asset('assets/js/users/index.js')}}"></script>
@endpush

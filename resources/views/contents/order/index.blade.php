@extends('layouts/contentNavbarLayout')
@section('title', 'Đơn hàng')
@section('content')
  <div class="row g-4 mb-4">
    <div class="nav-align-top mb-4">
      <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                  data-bs-target="#navs-order-checkout" aria-controls="navs-pills-justified-home" aria-selected="true">
            <i class="tf-icons bx bx-money me-1"></i>
            <span class="d-none d-sm-block"> Chưa thanh toán
                  <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                        id="total-data-order-checkout">0</span>
              </span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-order-paid"
                  aria-controls="navs-pills-justified-profile" aria-selected="false" tabindex="-1">
            <i class='tf-icons bx bx-wallet-alt me-1'></i>
            <span class="d-none d-sm-block"> Chờ xác nhận
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                      id="total-data-order-paid">0</span>
            </span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-order-ordered"
                  aria-controls="navs-pills-justified-profile" aria-selected="false" tabindex="-1">
            <i class="tf-icons bx bx-dollar-circle me-1"></i>
            <span class="d-none d-sm-block"> Đã thanh toán
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                      id="total-data-order-oredered">0</span>
            </span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-order-canceled"
                  aria-controls="navs-pills-justified-profile" aria-selected="false" tabindex="-1">
            <i class="tf-icons bx bx-trash me-1"></i>
            <span class="d-none d-sm-block"> Đơn huỷ
                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1"
                      id="total-data-order-canceled">0</span>
            </span>
          </button>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="navs-order-checkout" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-order-checkedout-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
              <thead>
                <tr>
                  <th class="text-center">STT</th>
                  <th class="text-center">Mã đơn hàng</th>
                  <th class="text-center">Học viên</th>
                  <th class="text-center">Tổng tiền</th>
                  <th class="text-center">Giảm giá</th>
                  <th class="text-center">Thanh toán</th>
                  <th class="text-center">Ngày tạo</th>
                  <th class="text-center"></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="navs-order-paid" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-paid-order-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-center">Mã đơn hàng</th>
                <th class="text-center">Học viên</th>
                <th class="text-center">Tổng tiền</th>
                <th class="text-center">Giảm giá</th>
                <th class="text-center">Thanh toán</th>
                <th class="text-center">Ngày tạo</th>
                <th></th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="navs-order-ordered" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-ordered-order-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-center">Mã đơn hàng</th>
                <th class="text-center">Học viên</th>
                <th class="text-center">Tổng tiền</th>
                <th class="text-center">Giảm giá</th>
                <th class="text-center">Thanh toán</th>
                <th class="text-center">Ngày tạo</th>
                <th></th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="tab-pane fade" id="navs-order-canceled" role="tabpanel">
          <div class="card-datatable table-responsive">
            <table id="table-canceled-order-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
              <thead>
              <tr>
                <th class="text-center">STT</th>
                <th class="text-center">Mã đơn hàng</th>
                <th class="text-center">Học viên</th>
                <th class="text-center">Tổng tiền</th>
                <th class="text-center">Giảm giá</th>
                <th class="text-center">Thanh toán</th>
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
  @include('contents.department.create')
  @include('contents.department.update')
@endsection
@push('pricing-script')
  <script src="{{asset('assets/js/order/index.js')}}"></script>
@endpush

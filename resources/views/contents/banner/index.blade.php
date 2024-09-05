@extends('layouts/contentNavbarLayout')
@section('title', 'Blank layout - Layouts')
@section('content')
  <div class="card p-4">
    <div class="card-datatable table-responsive">
      <table id="table-banner-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <th class="text-right">Ảnh</th>
            {{--                  <th class="text-right">Vai trò</th>--}}
            <th class="text-center">Tên Banner</th>
            <th class="text-center">Trạng thái</th>
            <th></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  @include('contents.banner.create')
  @include('contents.banner.update')
@endsection
@push('pricing-script')
  <script src="{{asset('assets/js/banner/index.js')}}"></script>
@endpush

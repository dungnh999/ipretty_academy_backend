@extends('layouts/contentNavbarLayout')
@section('title', 'Blank layout - Layouts')
@section('content')
    <div class="card p-4">
        <div class="card-datatable table-responsive">
            <table id="table-post-category-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th class="text-right">Tên danh mục</th>
                        <th class="text-center">Đường dẫn</th>
                        <th class="text-center">Loại</th>
                        <th class="text-center">Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('contents.postscategory.create')


@endsection
@push('pricing-script')
    <script src="{{asset('assets/js/postscategory/index.js')}}"></script>
@endpush

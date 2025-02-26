@extends('layouts/contentNavbarLayout')
@section('title', 'Blank layout - Layouts')
@section('content')
    <div class="card p-4">
        <div class="card-datatable table-responsive">
            <table id="table-post-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
                <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th class="text-center">Hình ảnh (560x360)</th>
                        <th class="text-right">Tiêu đề</th>
                        <th class="text-center">Danh mục</th>
                        <th class="text-center">Ngày đăng</th>
                        <th class="text-center">Người đăng</th>
                        <th class="text-center">Lượt xem</th>
                        <th class="text-center">Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('contents.posts.create')
@endsection
@push('pricing-script')
    <script src="{{asset('assets/js/posts/index.js')}}"></script>
@endpush

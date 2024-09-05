@extends('layouts/contentNavbarLayout')
@section('title', 'Danh sách khoá học')
@section('content')
  <div class="row g-4 mb-4">
      <div class="col-lg-7">
        <div class="card p-4">
          <div class="card-datatable table-responsive">
            <table id="table-course-chapter-ipretty" class="datatables-basic table border-top dataTable no-footer dtr-column">
              <thead>
                <tr>
                  <th class="text-center">STT</th>
                  <th class="text-right">Tên khoá học</th>
                  <th class="text-center">Trạng thái</th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card">
          <div class="card p-4">
            <div class="mb-4">
              <button class="btn btn-primary me-2" id="addChapterByCourse111111" onclick="openCreateChapter()">Thêm chương trình học</button>
              <button class="btn btn-danger me-2" id="addDefault">Thêm khảo sát</button>
            </div>
            <div class="text-center d-flex justify-content-center align-items-center h-100 flex-column">
              <div id="kt_docs_jkanban_fixed_height" data-jkanban-height="600" class="kanban-fixed-height w-100">
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
  @include('contents.chapter.create')
@endsection
@push('pricing-script')
  <script type="text/javascript" src="{{asset('assets/js/chapter/index.js')}}"></script>
@endpush

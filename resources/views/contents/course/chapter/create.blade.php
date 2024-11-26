<div class="modal fade show" id="modal-create-chapter" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header pb-3">
          <h5 class="modal-title" id="modalScrollableTitle">Thêm mới bài học</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
        </div>
        <div class="modal-body">
          <div class="row g-6">
            {{-- Form tạo chương trình --}}
            <div class="form-info-chapter item-info-chapter">
                <div class="mb-4">
                  <label for="name-create-chapter-course" class="form-label">Tên chương trình</label>
                  <input type="email" class="form-control" id="name-create-chapter-course" placeholder="Nhập tên chương trình">
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" onclick="closeModalCreatechapter()">Đóng</button>
          <button type="button" class="btn btn-primary" onclick="saveCreateChapter()">@lang('modal.btn-create')</button>
        </div>
      </div>
    </div>
  </div>
  @push('pricing-script')
    <script src="{{asset('assets/js/course/chapter/create.js')}}"></script>
  @endpush
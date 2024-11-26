<div class="modal fade show" id="modal-update-chapter" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header pb-3">
          <h5 class="modal-title" id="modalScrollableTitle">Chỉnh sửa bài học</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
        </div>
        <div class="modal-body">
          <div class="row g-6">
            {{-- Form tạo chương trình --}}
            <div class="form-info-chapter item-info-chapter">
                <div class="mb-4">
                  <label for="name-update-chapter-course" class="form-label">Tên chương trình</label>
                  <input type="email" class="form-control" id="name-update-chapter-course" placeholder="Nhập tên chương trình">
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeModalUpdatechapter()">Đóng</button>
          <button type="button" class="btn btn-primary" onclick="saveUpdateChapter()">@lang('modal.btn-update')</button>
        </div>
      </div>
    </div>
  </div>
  @push('pricing-script')
    <script src="{{asset('assets/js/course/chapter/update.js')}}"></script>
  @endpush
<div class="modal fade show" id="modal-create-lesson" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header pb-3">
          <h5 class="modal-title" id="modalScrollableTitle">Thêm mới chương trình</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
        </div>
        <div class="modal-body">
          <div class="row g-6">
            <div class="card">
                <div class="card-body form-action-create-chapter">
                    <div class="row  mb-4">
                        <div class="col-lg-6">
                          <div class="mb-4">
                            <label for="name-lesson-course" class="form-label">Tên bài học</label>
                            <input type="email" class="form-control" id="name-lesson-course" placeholder="Nhập tên bài học">
                          </div>
                          <div class="mb-4" id="create-type-upload-video-lesson">
                            <label for="inlineRadioOptions"  class="form-label d-block">Video bài học</label>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="type-upload-video" id="inlineRadio3" value="1" disabled="">
                              <label class="form-check-label" for="inlineRadio3">Tải video từ máy (Đang phát triển)</label>
                            </div>
                            <div class="form-check form-check-inline mt-4">
                              <input class="form-check-input" type="radio" name="type-upload-video" id="inlineRadio1" checked value="0">
                              <label class="form-check-label" for="inlineRadio1">Video từ youtube</label>
                            </div>
                            <div class="form-check form-check-inline mt-4">
                              <input class="form-check-input" type="radio" name="type-upload-video" id="inlineRadio1" checked value="0">
                              <label class="form-check-label" for="inlineRadio1">Bài viết</label>
                            </div>
                          </div>
                          <div class="mb-4">
                            <label for="demo-create-lesson" class="form-label d-block">Học thử </label>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="checkbox" name="demo-create-lesson" id="demo-create-lesson">
                              <label class="form-check-label" for="demo-create-lesson">Cho phép học thử</label>
                            </div>
                          </div>
                          <div class="mb-4">
                            <label for="exampleFormControlInput1" class="form-label">Link youtube</label>
                            <input type="text" class="form-control link-youtube" id="link-yotube-course"  placeholder="Nhập link bài học từ youtube">
                          </div>
                          <div class="mb-4">
                            <label class="form-label" for="email">Mô tả bài học</label>
                            <div id="toolbar-lesson-chapter">
                              <button class="ql-bold">Bold</button>
                              <button class="ql-italic">Italic</button>
                            </div>
                            <!-- Create the editor container -->
                            <div id="editor-lesson-chapter" style="height: 200px"></div>
                          </div>
                        </div>
                        <div class="col-lg-6 position-relative">
                          <div class="loader-custom hidden-loader" id="loader-custom"></div>
                          <video id="player-create" poster="https://via.placeholder.com/640x360/000000/FFFFFF?text=No+Video" playsinline controls>
                            <!-- Video giả để hiển thị poster đen -->
                            <source src="" type="video/mp4" />
                          </video>
                        </div>
                      </div>
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeModalCreateLesson()">Đóng</button>
          <button type="button" class="btn btn-primary" onclick="saveCreateLesson()">@lang('modal.btn-create')</button>
        </div>
      </div>
    </div>
</div>
@push('pricing-script')
<script src="{{asset('assets/js/course/lesson/create.js')}}"></script>
@endpush
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
                            <label class="form-label" for="basic-icon-default-email">Link youtube</label>
                            <div class="input-group input-group-merge">
                              <span id="basic-icon-default-email1" class="form-control link-youtube input-group-text" style="padding-right: 25px" >https://www.youtube.com/watch?v=</span>
                              <input type="text" id="link-yotube-course" class="form-control link-youtube" placeholder="john.doe" aria-label="ID khóa học" aria-describedby="basic-icon-default-email1">
                            </div>
                            <div class="form-text"> Lấy ID Youtube : https://www.youtube.com/watch?v={ID} </div>
                          </div>
                          <div class="mb-4">
                            <label class="form-label" for="email">Mô tả bài học</label>
                            <!-- Create the editor container -->
                            <div id="editor-lesson-chapter" style="height: 200px"></div>
                          </div>
                        </div>
                        <div class="col-lg-6 position-relative">
                          <div>
                            <div class="loader-custom hidden-loader" id="loader-custom"></div>
                            <video id="player-create" poster="https://via.placeholder.com/640x360/000000/FFFFFF?text=No+Video" playsinline controls>
                              <!-- Video giả để hiển thị poster đen -->
                              <source src="" type="video/mp4" />
                            </video>
                          </div>
                          <div class="row mt-5 gap-4">
                            <label class="form-label" for="email">Tài liệu</label>
                            <div class="upload">
                                <button class="btn btn-primary">
                                    <label for="upload-file-create-lesson-course">Tải ảnh lên</label>
                                    <input type="file" id="upload-file-create-lesson-course" class="d-none" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint,
text/plain, application/pdf" multiple>
                                </button>
                            </div>
                            <div class="box-file-upload">
                              <div class="d-flex flex-column gap-2 group-file-lesson" id="group-file-create-lesson-course">
                                <h6>Không có tài liệu</h6>
                              </div>
                            </div>
                          </div>
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
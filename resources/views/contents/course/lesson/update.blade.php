<div class="modal fade show" id="modal-update-lesson" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header pb-3">
          <h5 class="modal-title" id="modalScrollableTitle">Chỉnh sửa bài học</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
        </div>
        <div class="modal-body">
          <div class="row g-6">
            <div class="card">
                <div class="card-body form-action-update-chapter">
                    <div class="row  mb-4">
                        <div class="col-lg-6">
                          <div class="mb-4">
                            <label for="name-lesson-course" class="form-label">Tên bài học</label>
                            <input type="email" class="form-control" id="name-update-lesson-course" placeholder="Nhập tên bài học">
                          </div>
                          <div class="mb-4" id="update-type-upload-video-lesson">
                            <label for="inlineRadioOptions"  class="form-label d-block">Video bài học</label>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="type-upload-video" id="inlineRadio3" value="0" disabled="">
                              <label class="form-check-label" for="inlineRadio3">Tải video từ máy (Đang phát triển)</label>
                            </div>
                            <div class="form-check form-check-inline mt-4">
                              <input class="form-check-input" type="radio" name="type-upload-video" id="inlineRadio1" checked value="1">
                              <label class="form-check-label" for="inlineRadio1">Video từ youtube</label>
                            </div>
                          </div>
                          <div class="mb-4">
                            <label for="demo-update-lesson" class="form-label d-block">Học thử </label>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="checkbox" name="demo-update-lesson" id="demo-update-lesson">
                              <label class="form-check-label" for="demo-update-lesson">Cho phép học thử</label>
                            </div>
                          </div>
{{--                          <div class="mb-4">--}}
{{--                            <label for="exampleFormControlInput1" class="form-label">Link youtube</label>--}}
{{--                            <input type="text" class="form-control link-youtube" id="link-update-yotube-course"  placeholder="Nhập link bài học từ youtube">--}}
{{--                          </div>--}}
                          <div class="mb-4">
                            <label class="form-label" for="basic-icon-default-email">Link youtube</label>
                            <div class="input-group input-group-merge">
                              <span id="basic-icon-default-email2" class="form-control link-youtube input-group-text" style="padding-right: 25px" >https://www.youtube.com/watch?v=</span>
                              <input type="text" id="link-update-yotube-course" class="form-control link-update-youtube" placeholder="ID khóa học" aria-label="ID khóa học" aria-describedby="basic-icon-default-email2">
                            </div>
                            <div class="form-text"> Lấy ID Youtube : https://www.youtube.com/watch?v={ID} </div>
                          </div>
                          <div class="mb-4">
                            <label class="form-label" for="email">Mô tả bài học</label>
                            <!-- update the editor container -->
                            <div id="editor-update-lesson-chapter" style="height: 200px"></div>
                          </div>
                        </div>
                        <div class="col-lg-6 position-relative">
                          <div>
                            <div class="loader-custom hidden-loader" id="loader-custom"></div>
                            <video id="player-update" poster="https://via.placeholder.com/640x360/000000/FFFFFF?text=No+Video" playsinline controls>
                              <!-- Video giả để hiển thị poster đen -->
                              <source src="" type="video/mp4" />
                            </video>
                          </div>
                          <div class="row mt-5 gap-4 ">
                            <label class="form-label" for="email">Tài liệu</label>
                            <div class="upload">
                              <button class="btn btn-primary">
                                <label for="upload-file-update-lesson-course">Tải ảnh lên</label>
                                <input type="file" id="upload-file-update-lesson-course" class="d-none" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint,
text/plain, application/pdf" multiple>
                              </button>
                            </div>
                            <div class="box-file-upload">
                              <div class="d-flex flex-column gap-2 group-file-lesson" id="group-file-update-lesson-course">
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
          <button type="button" class="btn btn-secondary" onclick="closeModalUpdateLesson()">Đóng</button>
          <button type="button" class="btn btn-primary" onclick="saveUpdateChapterLesson()">@lang('modal.btn-update')</button>
        </div>
      </div>
    </div>
</div>
@push('pricing-script')
<script src="{{asset('assets/js/course/lesson/update.js')}}"></script>
@endpush
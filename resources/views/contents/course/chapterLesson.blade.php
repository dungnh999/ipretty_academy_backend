{{-- <div class="modal fade" id="modal-create-chapter-lesson" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-fullscreen modal-simple modal-edit-user">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="text-uppercase">@lang('modal.title-create', ['name' => 'chương trình'])</h3>
        </div>
        <form id="editUserForm" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework">
          <div class="row">
            <div class="col-lg-3">
              <div class="text-center d-flex justify-content-center align-items-center h-100 flex-column">
                <div id="kt_docs_jkanban_fixed_height" data-jkanban-height="800" class="kanban-fixed-height w-100">
                </div>
              </div>
            </div>
            <div class="col-lg-">
              <div class="text-center d-flex justify-content-center align-items-center h-100 flex-column">
                kfjdkfhdkfhgdkjfhdhfjkdhjfdhjkfdkjfdhjkfhdhkf
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 text-lg-end mt-2">
              <button type="button" class="btn btn-label-secondary" onclick="closeModalCreateChapterLesson()">@lang('modal.btn-close')</button>
              <button type="button" class="btn btn-primary" onclick="saveCreateChapter()">@lang('modal.btn-create')</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div> --}}

<div class="modal fade show" id="modal-create-chapter-lesson" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header pb-3">
        <h5 class="modal-title" id="modalScrollableTitle">Nội dung bài học</h5>
        <button type="button" class="btn btn-primary" onclick="addFormChapterCourse()">Thêm chương trình</button>
        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" ></button> --}}
      </div>
      <div class="modal-body">
        <div class="row g-6">
          <div class="col-lg-12">
            <div class="accordion stick-top accordion-custom-button course-content-fixed bottom-0 bg-primary p-3 rounded" id="courseContent">
                
            </div>
          </div>
          {{-- <div class="col-12 mt-3">
            <div class="card">
              <div class="card-body p-3">
                <button type="button" class="btn btn-primary" onclick="addFormChapterCourse()">Thêm chương trình</button>
              </div>
            </div>
          </div> --}}
          {{-- <div class="col-lg-9"> --}}
            {{-- <div class="card">
              <div class="card-body form-action-create-chapter"> --}}
                  {{-- <div class="d-flex align-items-center mb-4">
                    <button class="btn p-0 mr-3">
                      <i class='bx bx-left-arrow-alt fs-large'></i>
                    </button>
                    <h5 class="m-0 title-form-update-creat-chapter">Thêm mới chương trình</h5>
                  </div>
                   --}}
                  {{-- Form tạo bài học --}}
                  {{-- <div class="form-info-chapter-lesson d-none item-info-chapter-lesson">
                    <div class="row  mb-4">
                      <div class="col-lg-6">
                        <div class="mb-4">
                          <label for="name-lesson-course" class="form-label">Tên bài học</label>
                          <input type="email" class="form-control" id="name-lesson-course" placeholder="Nhập tên bài học">
                        </div>
                        <div class="mb-4">
                          <label for="inlineRadioOptions" class="form-label d-block">Video bài học</label>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3" disabled="">
                            <label class="form-check-label" for="inlineRadio3">Tải video từ máy (Đang phát triển)</label>
                          </div>
                          <div class="form-check form-check-inline mt-4">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" checked value="option1">
                            <label class="form-check-label" for="inlineRadio1">Video từ youtube</label>
                          </div>
                        </div>
                        <div class="mb-4">
                          <label for="demo-lesson" class="form-label d-block">Học thử </label>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="demo-lesson" id="demo-lesson">
                            <label class="form-check-label" for="demo-lesson">Cho phép học thử</label>
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
                        <video id="player" poster="https://via.placeholder.com/640x360/000000/FFFFFF?text=No+Video" playsinline controls>
                          <!-- Video giả để hiển thị poster đen -->
                          <source src="" type="video/mp4" />
                        </video>
                      </div>
                    </div>
                  </div>
                  <div class="text-right d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="cancelCreateFormCourse()">Huỷ</button>
                    <button type="button" class="btn btn-primary ml-2" onclick="saveCreateChapterLessonCourse()">Lưu lại</button>
                  </div> --}}
              {{-- </div> --}}
            {{-- </div> --}}
          {{-- </div> --}}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/course/chapterLesson.js')}}"></script>
@endpush

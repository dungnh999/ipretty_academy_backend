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
  <div class="modal-dialog modal-dialog-scrollable modal-fullscreen" role="document">
    <div class="modal-content">
      <div class="modal-header pb-3">
        <h5 class="modal-title" id="modalScrollableTitle">Nội dung bài học</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
      </div>
      <div class="modal-body">
        <div class="row g-6">
          <div class="col-12 mb-3">
            <div class="card">
              <div class="card-body p-3">
                <button type="button" class="btn btn-primary" onclick="addFormChapterCourse()">Thêm chương trình</button>
              </div>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="accordion stick-top accordion-custom-button course-content-fixed bottom-0" id="courseContent">
              {{-- <div class="accordion-item active mb-0">
                <div class="accordion-header p-3 border-bottom" id="headingOne">
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#chapterOne" aria-expanded="false" aria-controls="chapterOne"></button>
                    <span class="d-flex flex-column">
                      <span class="h5 mb-0">Course Content</span>
                      <span class="text-body fw-normal"> 5 Bài học | 4.4 min</span>
                    </span>
                  </div>
                </div>
                <div id="chapterOne" class="accordion-collapse collapse show" data-bs-parent="#courseContent" style="">
                  <div class="accordion-body py-4">
                    <div class="d-flex align-items-center gap-1 mb-3">
                      <i class="bx bx-laptop fs-5"></i>
                      <label for="defaultCheck1" class="form-check-label ms-4">
                        <span class="mb-0 h6">Bài học 01</span>
                        <small class="text-body d-block">10 câu hỏi</small>
                      </label>
                    </div>
                    <div class="d-flex align-items-center gap-1 mb-3">
                      <i class="bx bx-question-mark fs-5"></i>
                      <label for="defaultCheck2" class="form-check-label ms-4">
                        <span class="mb-0 h6">Khảo sát</span>
                        <small class="text-body d-block">4.8 min</small>
                      </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-1 mb-3">
                      <input class="form-check-input" type="checkbox" id="defaultCheck3">
                      <label for="defaultCheck3" class="form-check-label ms-4">
                        <span class="mb-0 h6">3. Basic design theory</span>
                        <small class="text-body d-block">5.9 min</small>
                      </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-1 mb-3">
                      <input class="form-check-input" type="checkbox" id="defaultCheck4">
                      <label for="defaultCheck4" class="form-check-label ms-4">
                        <span class="mb-0 h6">4. Basic fundamentals</span>
                        <small class="text-body d-block">3.6 min</small>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="accordion-item mb-0">
                <div class="accordion-header p-3 border-bottom" id="headingTwo">
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#chapterTwo" aria-expanded="false" aria-controls="chapterTwo"></button>
                    <span class="d-flex flex-column">
                      <span class="h5 mb-0">Course Content</span>
                      <span class="text-body fw-normal"> 5 Bài học | 4.4 min</span>
                    </span>
                  </div>
                </div>
                <div id="chapterTwo" class="accordion-collapse collapse" data-bs-parent="#courseContent" style="">
                  <div class="accordion-body py-4">
                    <div class="d-flex align-items-center gap-1 mb-4">
                      <i class="bx bx-laptop fs-5"></i>
                      <label for="defaultCheck1" class="form-check-label ms-4">
                        <span class="mb-0 h6">1. Welcome to this course</span>
                        <small class="text-body d-block">2.4 min</small>
                      </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-1 mb-4">
                      <input class="form-check-input" type="checkbox" id="defaultCheck2" checked="">
                      <label for="defaultCheck2" class="form-check-label ms-4">
                        <span class="mb-0 h6">2. Watch before you start</span>
                        <small class="text-body d-block">4.8 min</small>
                      </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-1 mb-4">
                      <input class="form-check-input" type="checkbox" id="defaultCheck3">
                      <label for="defaultCheck3" class="form-check-label ms-4">
                        <span class="mb-0 h6">3. Basic design theory</span>
                        <small class="text-body d-block">5.9 min</small>
                      </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-1 mb-4">
                      <input class="form-check-input" type="checkbox" id="defaultCheck4">
                      <label for="defaultCheck4" class="form-check-label ms-4">
                        <span class="mb-0 h6">4. Basic fundamentals</span>
                        <small class="text-body d-block">3.6 min</small>
                      </label>
                    </div>
                    <div class="form-check d-flex align-items-center gap-1 mb-0">
                      <input class="form-check-input" type="checkbox" id="defaultCheck5">
                      <label for="defaultCheck5" class="form-check-label ms-4">
                        <span class="mb-0 h6">5. What is ui/ux</span>
                        <small class="text-body d-block">10.6 min</small>
                      </label>
                    </div>
                  </div>
                </div>
              </div> --}}
            </div>
          </div>
          <div class="col-lg-9">
            <div class="card">
              <div class="card-body">
                  <div class="d-flex align-items-center mb-4">
                    <button class="btn p-0 mr-3">
                      <i class='bx bx-left-arrow-alt fs-large'></i>
                    </button>
                    <h5 class="m-0 title-form-update-creat-chapter">Thêm mới chương trình</h5>
                  </div>

                  {{-- Form tạo chương trình --}}
                  <div class="form-info-chapter d-none item-info-chapter">
                    <div class="mb-4">
                      <label for="name-create-chapter-course" class="form-label">Tên chương trình</label>
                      <input type="email" class="form-control" id="name-create-chapter-course" placeholder="Nhập tên chương trình">
                    </div>
                  </div>

                  {{-- Form tạo bài học --}}
                  <div class="form-info-chapter-lesson d-none item-info-chapter-lesson">
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
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/course/chapterLesson.js')}}"></script>
@endpush

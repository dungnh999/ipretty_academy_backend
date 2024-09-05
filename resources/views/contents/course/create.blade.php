<div class="modal fade" id="modal-create-course" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content p-3">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <h3 class="text-uppercase text-center w-100 mb-0">@lang('modal.title-create', ['name' => 'Khoá học'])</h3>
      </div>
      <div class="modal-body">
        <div class="col-lg-12">
          <h4>Thông tin khoá học</h4>
        </div>
        <div class="row">
          <div class="col-lg-7">
            <div class="grop-form-create-course">
              <div class="mt-2">
                <label class="form-label" for="username">Tên khoá học</label>
                <input type="text" id="name-course-create" class="form-control" placeholder="Bài học mới"/>
              </div>
               {{-- Giá bán và giá giảm --}}
               <div class="row mt-2">
                <div class="col-lg-6">
                  <label class="form-label" for="username">Giá bán</label>
                  <input type="number" id="course-price-create" class="form-control" placeholder="Giá bán"/>
                </div>
                <div class="col-lg-6">
                  <label class="form-label" for="username">Giá giảm</label>
                  <input type="number" id="course-price-sale-create" class="form-control" placeholder="Giá giảm"/>
                </div>
              </div>
               {{-- Kết thúc Giá bán và giá giảm --}}

              <div class="col-sm-12 mt-2">
                <label class="form-label" for="email">Mô tả bài học</label>
                <div id="toolbar">
                  <button class="ql-bold">Bold</button>
                  <button class="ql-italic">Italic</button>
                </div>
                <!-- Create the editor container -->
                <div id="editor" style="height: 200px"></div>
              </div>
              <div class="row mt-2">
                <div class="col-sm-6 form-password-toggle">
                  <label class="form-label" for="password">Danh mục khoá học</label>
                  <select id="course-category-create" class="form-select">
                    <option hidden selected>Chọn doanh mục khoá học</option>
                  </select>
                </div>
                <div class="col-sm-6 form-password-toggle">
                  <label class="form-label" for="password">Giảng viên</label>
                  <select id="select-teacher-create-course" class="form-select">
                    <option hidden selected>Chọn giảng viên</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <label class="form-label" for="username">Hình ảnh khoá học</label>
            <div class="review-video-create-course position-relative">
              <img id="preview-banner-create-course" height="300" src="https://upload-dungnh-dev.s3.ap-southeast-1.amazonaws.com/public/image/course/cdDyAwRUytGhmrlh7e4DRzqZ2rYTeU5wRZGQz30A.png"
                class="w-100 rounded object-fit-cover">
              <label for="input-banner-create-course" type="button" style="position: absolute; background: aliceblue; right: 17px; bottom: 17px; border: none; padding: 7px; border-radius: 7px; border-color: red">
                <i class='bx bx-cloud-upload'></i>
                Tải ảnh lên
              </label>
              <input type="file" id="input-banner-create-course" hidden="">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary" onclick="saveCreateCourse()">Lưu Lại</button>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/course/create.js')}}"></script>
@endpush


{{--
 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="text-uppercase">@lang('modal.title-create', ['name' => 'Khoá học'])</h3>
        </div>
        <div id="stepper1" class="bs-stepper">
          <div class="bs-stepper-header" role="tablist">
            <div class="step active" data-target="#account-details">
              <button type="button" class="btn btn-link step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
                <span class="bs-stepper-circle">1</span>
                <span class="bs-stepper-label">Thông tin khoá hoc</span>
              </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#chap-details">
              <button type="button" class="btn btn-link step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                <span class="bs-stepper-circle">2</span>
                <span class="bs-stepper-label">Chương trình bài học</span>
              </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#lesson-details">
              <button type="button" class="btn btn-link step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                <span class="bs-stepper-circle">3</span>
                <span class="bs-stepper-label">Nội dung bài giảng</span>
              </button>
            </div>
          </div>
          <div class="bs-stepper-content">
            <form novalidate>
              <div id="account-details" role="tabpanel" class="content active">
                <div class="row mt-2">
                  <div class="col-sm-6">
                    <label class="form-label" for="username">Tên khoá học</label>
                    <input type="text" id="name-course-create" class="form-control" placeholder="Bài học mới">
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="password">Giảng viên</label>
                    <select id="select-teacher-create-course" class="form-select w-100">
                      <option hidden selected>Chọn giảng viên</option>
                    </select>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="password">Danh mục khoá học</label>
                    <select id="course-category-create" class="form-select">
                      <option hidden selected>Chọn doanh mục khoá học</option>
                    </select>
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="password">Giảng viên</label>
                    <select id="defaultSelect" class="form-select">
                      <option hidden selected>Chọn giảng viên</option>
                      <option value="1">One</option>
                      <option value="2">Two</option>
                      <option value="3">Three</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-12 mt-2">
                  <label class="form-label" for="email">Mô tả bài học</label>
                  <div id="toolbar">
                    <button class="ql-bold">Bold</button>
                    <button class="ql-italic">Italic</button>
                  </div>
                  <!-- Create the editor container -->
                  <div id="editor" style="height: 300px"></div>
                </div>
                <div class="row mt-2">
                  <div class="col-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary btnNext">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Tiếp Tục</span>
                      <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div id="chap-details" role="tabpanel" class="content">
                <div class="row g-3">
                  <div class="col-lg-12">
                    <div class="accordion mt-3" id="list-group-chapter">
                      <div class="card accordion-item active">
                        <h2 class="accordion-header" id="headingOne">
                          <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div class="title-chap">Chương 1 : Bài học không 1</div>
                                <div class="list-group-action-chap me-2">
                                  <a type="button" onclick="openCreateChapter($(this))" class="btn btn-primary btn-sm text-white">Thêm bài giảng</a>
                                </div>
                            </div>
                          </button>
                        </h2>
                        <div id="accordionOne" class="accordion-collapse collapse show" data-bs-parent="#list-group-chapter" style="">
                          <div class="accordion-body">
                          </div>
                        </div>
                      </div>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mt-2" onclick="addChapterContent()">Thêm mới bài học</button>
                  </div>
                  <div class="col-12 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary btnPrevious">
                      <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                      <span class="align-middle d-sm-inline-block d-none">Quay lại</span>
                    </button>
                    <button type="button" class="btn btn-primary btnNext">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Tiếp Tục</span>
                      <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div id="lesson-details" role="tabpanel" class="content">
                <div class="row g-3">
                  <div class="col-sm-6">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" class="form-control" placeholder="johndoe">
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe">
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge">
                      <input type="password" id="password" class="form-control" placeholder="············" aria-describedby="password2">
                      <span class="input-group-text cursor-pointer" id="password2"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="confirm-password">Confirm Password</label>
                    <div class="input-group input-group-merge">
                      <input type="password" id="confirm-password" class="form-control" placeholder="············" aria-describedby="confirm-password2">
                      <span class="input-group-text cursor-pointer" id="confirm-password2"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>
                  <div class="col-12 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary btnPrevious">
                      <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                      <span class="align-middle d-sm-inline-block d-none">Quay lại</span>
                    </button>
                    <button type="button" class="btn btn-primary btnNext" onclick="saveCreateCourse()">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Thêm mới</span>
                      <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
--}}

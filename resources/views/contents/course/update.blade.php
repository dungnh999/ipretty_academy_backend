<div class="modal fade" id="modal-update-course" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content p-3">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <h3 class="text-uppercase text-center w-100 mb-0">@lang('modal.title-update', ['name' => 'Khoá học'])</h3>
        </div>
        <div class="modal-body">
          <div class="col-lg-12">
            <h4>Thông tin khoá học</h4>
          </div>
          <div class="row">
            <div class="col-lg-7">
              <div class="grop-form-update-course">
                <div class="mt-2">
                  <label class="form-label" for="username">Tên khoá học</label>
                  <input type="text" id="name-course-update" class="form-control" placeholder="Bài học mới"/>
                </div>
                 {{-- Giá bán và giá giảm --}}
                 <div class="row mt-2">
                  <div class="col-lg-6">
                    <label class="form-label" for="username">Giá bán</label>
                    <input type="number" id="course-price-update" class="form-control" placeholder="Giá bán"/>
                  </div>
                  <div class="col-lg-6">
                    <label class="form-label" for="username">Giá giảm</label>
                    <input type="number" id="course-price-sale-update" class="form-control" placeholder="Giá giảm"/>
                  </div>
                </div>
                 {{-- Kết thúc Giá bán và giá giảm --}}
  
                <div class="col-sm-12 mt-2">
                  <label class="form-label" for="email">Mô tả bài học</label>
                  <!-- update the editor container -->
                  <div id="description-update-course" style="height: 200px"></div>
                </div>
                <div class="row mt-2">
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="password">Danh mục khoá học</label>
                    <select id="course-category-update" class="form-select">
                      <option hidden selected>Chọn doanh mục khoá học</option>
                    </select>
                  </div>
                  <div class="col-sm-6 form-password-toggle">
                    <label class="form-label" for="password">Giảng viên</label>
                    <select id="select-teacher-update-course" class="form-select">
                      <option hidden selected>Chọn giảng viên</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5">
              <div>
                <label class="form-label" for="username">Hình ảnh khoá học</label>
                <div class="review-video-update-course position-relative">
                  <img id="preview-banner-update-course" height="300" src="https://upload-dungnh-dev.s3.ap-southeast-1.amazonaws.com/public/image/course/cdDyAwRUytGhmrlh7e4DRzqZ2rYTeU5wRZGQz30A.png"
                       class="w-100 rounded object-fit-cover">
                  <label for="input-banner-update-course" type="button" style="position: absolute; background: aliceblue; right: 17px; bottom: 17px; border: none; padding: 7px; border-radius: 7px; border-color: red">
                    <i class='bx bx-cloud-upload'></i>
                    Tải ảnh lên
                  </label>
                  <input type="file" id="input-banner-update-course" hidden="">
                </div>
              </div>
              <div>
                <label class="form-label" for="username">Chứng chỉ</label>
                <div class="review-video-update-course position-relative">
                  <img id="preview-certificate-update-course" height="150" src="https://upload-dungnh-dev.s3.ap-southeast-1.amazonaws.com/public/image/course/cdDyAwRUytGhmrlh7e4DRzqZ2rYTeU5wRZGQz30A.png"
                       class="w-100 rounded object-fit-cover">
                  <label for="input-certificate-update-course" type="button" style="position: absolute; background: aliceblue; right: 17px; bottom: 17px; border: none; padding: 7px; border-radius: 7px; border-color: red">
                    <i class='bx bx-cloud-upload'></i>
                    Tải ảnh lên
                  </label>
                  <input type="file" id="input-certificate-update-course" hidden="">
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mt-2">
                <label class="form-label" for="link-course-update">Mục tiêu</label>
                <input id="target-course-update" class="form-control" placeholder=""></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary" onclick="saveUpdateCourses()">Lưu Lại</button>
        </div>
      </div>
    </div>
  </div>
  @push('pricing-script')
    <script src="{{asset('assets/js/course/update.js')}}"></script>
  @endpush
  
  
 
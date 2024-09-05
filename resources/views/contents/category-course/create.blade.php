<div class="modal fade" id="modal-create-category-course" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-md modal-simple modal-edit-user">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="text-uppercase">@lang('modal.title-create', ['name' => 'Danh mục khoá học'])</h3>
        </div>
        <form id="form-create-category-course-ipretty" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework">
            <div class="col-12 fv-plugins-icon-container">
              <label class="form-label" for="name-create-category-course-ipretty">Tên danh mục khoá học</label>
              <input type="text" id="name-create-category-course-ipretty" class="form-control"
                     placeholder="Nhập tên danh mục khoá học" required>
              <div class="invalid-feedback">Tên danh mục khoá học không được để trống </div>
            </div>
            <div class="col-sm-12 form-password-toggle">
              <label class="form-label" for="password">Loại danh mục khoá học</label>
              <select id="course-category-types-create" class="form-select">
                <option hidden selected>Chọn loại doanh mục khoá học</option>
              </select>
            </div>
            <div class="col-lg-12">
              <label for="exampleFormControlTextarea1" class="form-label">Mô tả</label>
              <textarea class="form-control" id="description-create-category-course" rows="5"></textarea>
            </div>
            <div class="col-12 fv-plugins-icon-container">
              <label class="form-label" for="name-create-user-ipretty">@lang('form.image')</label>
              <div style="position: relative">
                <img id="image-preview-create-category-course" src="https://dashboard.ipretty.edu.vn/ba29a12120c9d59c26de9b91205a3e7e.png" height="200" width="100%" style="object-fit: cover; border-radius: 12px">
                <label for="image-create-category-course-ipretty" type="button" style="position: absolute; background: aliceblue; right: 17px; bottom: 17px; border: none; padding: 7px; border-radius: 7px; border-color: red">
                  <i class='bx bx-cloud-upload'></i>
                  Tải ảnh lên
                </label>
                <input type="file" name="upload" id="image-create-category-course-ipretty" accept="image/*" hidden>
              </div>
            </div>
            <div class="col-12 text-lg-end">
                <button type="button"  class="btn btn-label-secondary" onclick="closeModalCreateCoursesCategory()">@lang('modal.btn-close')</button>
                <button type="button" class="btn btn-primary" onclick="saveCreateCoursesCategory()">@lang('modal.btn-create')</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/category-course/create.js')}}"></script>
@endpush

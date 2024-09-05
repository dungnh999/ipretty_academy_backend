<div class="modal fade" id="modal-update-users" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-md modal-simple modal-edit-user">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3>CHỈNH SỬA TÀI KHOẢN</h3>
        </div>
        <form id="editUserForm" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework">
          <div class="col-12 col-md-6 fv-plugins-icon-container">
            <label class="form-label" for="name-update-user-ipretty">Họ và tên</label>
            <input type="text" id="name-update-user-ipretty" class="form-control"
                   placeholder="Họ và tên">
          </div>
          <div class="col-12 col-md-6 fv-plugins-icon-container">
            <label class="form-label" for="date-update-user-ipretty">Ngày sinh</label>
            <input type="date" id="date-update-user-ipretty" class="form-control"
                   placeholder="Ngày sinh">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="email-update-user-ipretty">Email</label>
            <h6 type="text" id="email-update-user-ipretty">----</h6>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="phone-update-user-ipretty">Số điện thoại</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text">+84</span>
              <input type="text" id="phone-update-user-ipretty" name="modalEditUserPhone"
                     class="form-control phone-number-mask" placeholder="978374837">
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="mb-3  d-flex flex-column">
              <label for="role-update-user-ipretty" class="form-label">Bộ Phận</label>
              <select id="role-update-user-ipretty" class="form-select">
                <option disabled selected>Vui lòng chọn</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="mb-3 d-flex flex-column">
              <label for="position-update-user-ipretty" class="form-label">@lang('form.position')</label>
              <select id="position-update-user-ipretty" class="form-select">
                <option disabled selected>@lang('form.option.default')</option>
                <option value="0">Admin</option>
                <option value="1">Giáo viên</option>
                <option value="2">Nhân viên</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-6" id="gender-update-user">
            <label class="form-label d-block">Giới tính</label>
            <div class="form-check form-check-inline">
              <input name="default-radio-1" class="form-check-input" type="radio" value="1" id="defaultRadio1" checked/>
              <label class="form-check-label" for="defaultRadio1">
                Nam
              </label>
            </div>
            <div class="form-check form-check-inline">
              <input name="default-radio-1" class="form-check-input" type="radio" value="0" id="defaultRadio1"/>
              <label class="form-check-label" for="defaultRadio1">
                Nữ
              </label>
            </div>
          </div>
          <div class="col-12 fv-plugins-icon-container">
            <label class="form-label" for="modalEditUserName">Địa chỉ</label>
            <input type="text" id="address-update-user-ipretty" name="modalEditUserName" class="form-control"
                   placeholder="Địa chỉ">
          </div>
          <div class="col-12 text-lg-end">
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Thoát</button>
            <button type="button" class="btn btn-primary me-sm-3 me-1" onclick="updateDataProfileUser()">Cập nhật</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/users/update.js')}}"></script>
@endpush

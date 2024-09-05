<div class="modal fade" id="modal-create-users" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-md modal-simple modal-edit-user">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="text-uppercase">@lang('modal.title-create', ['name' => 'Tài khoản'])</h3>
        </div>
        <form id="editUserForm" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework">
          <div class="col-12 col-md-6 fv-plugins-icon-container">
            <label class="form-label" for="name-create-user-ipretty">@lang('form.name')</label>
            <input type="text" id="name-create-user-ipretty" data-validate="empty" class="form-control"
                   placeholder="Họ và tên">
          </div>
          <div class="col-12 col-md-6 fv-plugins-icon-container">
            <label class="form-label" for="date-create-user-ipretty">@lang('form.date')</label>
            <input type="date" id="date-create-user-ipretty" class="form-control"
                   placeholder="Ngày sinh">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="email-create-user-ipretty">Email</label>
            <input type="text" id="email-create-user-ipretty" name="modalEditUserEmail" class="form-control"
                   placeholder="abc@domain.com">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label" for="phone-create-user-ipretty">@lang('form.phone')</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text">+84</span>
              <input type="text" id="phone-create-user-ipretty" name="modalEditUserPhone"
                     class="form-control phone-number-mask" placeholder="978374837">
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="mb-3 d-flex flex-column">
              <label for="role-create-user-ipretty" class="form-label">@lang('form.part')</label>
              <select id="role-create-user-ipretty" class="form-select">
                <option disabled selected>@lang('form.option.default')</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="mb-3 d-flex flex-column">
              <label for="position-create-user-ipretty" class="form-label">@lang('form.position')</label>
              <select id="position-create-user-ipretty" class="form-select">
                <option disabled selected>@lang('form.option.default')</option>
                <option value="0">Admin</option>
                <option value="1">Giáo viên</option>
                <option value="2">Nhân viên</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-6" id="group-gender-create-user">
            <label class="form-label d-block">@lang('form.sex.title')</label>
            <div class="form-check form-check-inline">
              <input name="default-radio-1" class="form-check-input" type="radio" value="0" id="gender-male-create-user" checked/>
              <label class="form-check-label" for="defaultRadio1">
                @lang('form.sex.male')
              </label>
            </div>
            <div class="form-check form-check-inline">
              <input name="default-radio-1" class="form-check-input" type="radio" value="1" id="gender-female-create-user"/>
              <label class="form-check-label" for="defaultRadio1">
                @lang('form.sex.female')
              </label>
            </div>
          </div>
          <div class="col-12 fv-plugins-icon-container">
            <label class="form-label" for="address-create-user-ipretty">@lang('form.address')</label>
            <input type="text" id="address-create-user-ipretty" name="modalEditUserName" class="form-control"
                   placeholder="Địa chỉ">
          </div>
          <div class="col-12 text-lg-end">
            <button type="reset" class="btn btn-label-secondary" onclick="closeModalCreateUsers()">@lang('modal.btn-close')</button>
            <button type="button" class="btn btn-primary" onclick="createUserIpretty()">@lang('modal.btn-create')</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/users/create.js')}}"></script>
@endpush

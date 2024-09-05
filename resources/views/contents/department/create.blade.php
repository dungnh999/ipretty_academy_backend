<div class="modal fade" id="modal-create-department" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-md modal-simple modal-edit-user">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="text-uppercase">@lang('modal.title-create', ['name' => 'Bộ Phận'])</h3>
        </div>
        <form id="form-create-department-ipretty" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework">
            <div class="col-12 fv-plugins-icon-container">
              <label class="form-label" for="name-create-department-ipretty">Tên bộ phận</label>
              <input type="text" id="name-create-department-ipretty" class="form-control"
                     placeholder="Nhập tên bộ phận" required>
              <div class="invalid-feedback" bis_skin_checked="1">Tên bộ phận không được để trống </div>
            </div>
            <div class="col-12 text-lg-end">
                <button type="button"  class="btn btn-label-secondary" onclick="closeModalCreateDepartment()">@lang('modal.btn-close')</button>
                <button type="button" class="btn btn-primary" onclick="saveCreateDepartment()">@lang('modal.btn-create')</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/department/create.js')}}"></script>
@endpush

<div class="modal fade" id="modal-create-banner" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-md modal-simple modal-edit-user">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="text-uppercase">@lang('modal.title-create', ['name' => 'Banner'])</h3>
        </div>
        <form id="editUserForm" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework">
            <div class="col-12 fv-plugins-icon-container">
              <label class="form-label" for="name-create-banner-ipretty">Tên banner</label>
              <input type="text" id="name-create-banner-ipretty" class="form-control"
                     placeholder="Họ và tên">
            </div>
            <div class="col-12 fv-plugins-icon-container">
              <label class="form-label" for="name-create-user-ipretty">@lang('form.image')</label>
              <div style="position: relative">
                <img id="image-preview-create-banner" src="https://dashboard.ipretty.edu.vn/ba29a12120c9d59c26de9b91205a3e7e.png" height="200" width="100%" style="object-fit: cover; border-radius: 12px">
                <label for="image-create-banner-ipretty" type="button" style="position: absolute; background: aliceblue; right: 17px; bottom: 17px; border: none; padding: 7px; border-radius: 7px; border-color: red">
                  <i class='bx bx-cloud-upload'></i>
                  Tải ảnh lên
                </label>
                <input type="file" name="upload" id="image-create-banner-ipretty" accept="image/*" hidden>
              </div>
            </div>
            <div class="col-12 text-lg-end">
              <button class="btn btn-label-secondary" onclick="closeModalCreateBanner()">@lang('modal.btn-close')</button>
              <button type="button" class="btn btn-primary" onclick="saveCreateBanner()">@lang('modal.btn-create')</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/banner/create.js')}}"></script>
@endpush

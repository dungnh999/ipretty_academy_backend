<div class="modal fade" id="modal-cropper-image-template" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-lg modal-simple modal-edit-user">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="text-uppercase">KÍCH THƯỚC</h3>
        </div>
        <div class="col-12" style="min-width: 100%">
          <img id="uploadedAvatar" src="" class="w-100" style="max-width: 100%">
        </div>
        <div class="col-lg-12">
          <div id="slider-basic" class="my-4">

          </div>
        </div>
        <div class="col-12 text-lg-end">
          <button class="btn btn-label-secondary" onclick="saveModalCropperImageTemplate()">@lang('modal.btn-close')</button>
          <button type="button" class="btn btn-primary" onclick="saveCropper()">@lang('modal.btn-create')</button>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{ asset(mix('assets/vendor/libs/cropper/js/cropper.js')) }}"></script>
  <script src="{{asset('assets/template/cropper.js')}}"></script>
@endpush

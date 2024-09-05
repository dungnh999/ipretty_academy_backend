<div class="modal fade" id="modal-info-users" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-md modal-simple modal-edit-user">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3>THÔNG TIN ĐĂNG NHẬP</h3>
        </div>
        <h6 class="text-light fw-medium">Tài khoản</h6>
        <p id="username-create-info-user">---</p>
        <h6 class="text-light fw-medium">Mật khẩu</h6>
        <p id="password-create-info-user">---</p>
        <div class="col-12 text-lg-end">
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Đóng</button>
        </div>
      </div>
    </div>
  </div>
</div>
@push('pricing-script')
  <script src="{{asset('assets/js/users/info.js')}}"></script>
@endpush

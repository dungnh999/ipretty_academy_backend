@extends('layouts/contentNavbarLayout')

@section('title', 'Thông tin tài khoản')

@section('page-script')
  <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection

@section('content')
  <div class="row">
    <div class="col-md-12">
      <ul class="nav nav-pills flex-column flex-md-row mb-3">
        <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Tài khoản</a></li>
        <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-notifications')}}"><i class="bx bx-bell me-1"></i> Thông Báo</a></li>
      </ul>
      <div class="card mb-4">
        <!-- Account -->
        <div class="card-body">
          <div class="d-flex align-items-start align-items-sm-center gap-4">
            <img src="{{ Session::get('account_info')['avatar']  }}" alt="user-avatar" class="d-block rounded object-fit-cover" height="100" width="100" id="uploadedAvatar" />
            <div class="button-wrapper">
              <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                <span class="d-none d-sm-block">Cập nhật ảnh</span>
                <i class="bx bx-upload d-block d-sm-none"></i>
                <input type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
              </label>
              <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
            </div>
          </div>
        </div>
        <hr class="my-0">
        <div class="card-body">
          <form id="formAccountSettings" method="POST" onsubmit="return false">
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="firstName" class="form-label">Họ và tên</label>
                <input class="form-control" type="text" id="firstName" name="firstName" value="{{ Session::get('account_info')['name']  }}" autofocus />
              </div>
              <div class="mb-3 col-md-6">
                <label for="lastName" class="form-label">Ngày sinh</label>
                <input class="form-control" type="date" name="lastName" id="date-profile-user" value="{{ Session::get('account_info')['birthday'] }}" />
              </div>
              <div class="mb-3 col-md-6">
                <label for="email" class="form-label">Email</label>
                <h6> {{ Session::get('account_info')['email']  }} </h6>
              </div>
              <div class="mb-3 col-md-6">
                <label for="organization" class="form-label">Bộ phận</label>
                <h6> {{ Session::get('account_info')['department_name']  }} </h6>
              </div>
              <div class="mb-3 col-md-6">
                <label class="form-label" for="phoneNumber">Số điện thoại</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text">VN (+84)</span>
                  <input type="text" id="phoneNumber" name="phoneNumber" value="{{ Session::get('account_info')['phone']  }}" class="form-control" placeholder="98368463" />
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="199 Lý chính thắng" value="{{ Session::get('account_info')['address']  }}" />
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary me-2">Lưu lại</button>
            </div>
          </form>
        </div>
        <!-- /Account -->
      </div>
    </div>
  </div>
@endsection

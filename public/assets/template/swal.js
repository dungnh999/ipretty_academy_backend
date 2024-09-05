function successSwalNotify(message) {
  showToastTemplate(message, "sussess");
}

// function toastDispose(toast) {
//   if (toast && toast._element !== null) {
//     if (toastPlacementExample) {
//       toastPlacementExample.classList.remove(selectedType);
//       DOMTokenList.prototype.remove.apply(toastPlacementExample.classList, selectedPlacement);
//     }
//     toast.dispose();
//   }
// }

function errorSwalNotify(message) {
  showToastTemplate(message, 'danger')
  // Swal.fire({
  //   title: "Lỗi rồi nè !!!",
  //   text: message,
  //   icon: "error",
  //   timer: 1500,
  //   showConfirmButton: false,
  // });
}

function confirmSwalNotify(title) {
  return new Promise((resolve, reject) => {
    Swal.fire({
      title: title,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Đồng ý",
      cancelButtonText: "Đóng"
    }).then((result) => {
      if (result.isConfirmed) {
        resolve(true); // Resolve promise with true if confirmed
      } else {
        reject(false); // Reject promise with false if canceled
      }
    });
  });
}


function showToastTemplate(message = '', type = 'sussess') {
  let background = 'bg-primary';
  switch (type){
    case 'primary':
      background = 'bg-primary';
      break;
    case 'warning':
      background = 'bg-warning';
      break;
    case 'danger':
      background = 'bg-danger';
      break;
    case 'info':
      background = 'bg-info';
      break;
  }
  // Mặc định các tùy chọn
  let defaultOptions = {
    delay: 1500, // Thời gian hiển thị mặc định là 3 giây
    autohide: true, // Tự động ẩn toast sau khi hiển thị
    position: 'top-0 end-0', // Tự động ẩn toast sau khi hiển thị
    background : background// Tự động ẩn toast sau khi hiển thị
  };

  // Kết hợp các tùy chọn mặc định với các tùy chọn được truyền vàoh
  let mergedOptions = {...defaultOptions};

  // Gỡ bỏ toast hiện tại (nếu có)
  const currentToast = document.querySelector('.toast-placement-ex');
  if (currentToast) {
    currentToast.remove();
  }

  // Tạo một toast element từ một chuỗi thông báo
  let toastElement = `
        <div class="bs-toast toast toast-placement-ex m-2 ${mergedOptions.background} ${mergedOptions.position}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="${defaultOptions.autohide}" data-bs-delay="${defaultOptions.delay}">
          <div class="toast-header">
            <i class='bx bx-bell me-2'></i>
            <div class="me-auto fw-medium">Thông Báo</div>
<!--            <small>Hiện tịa</small>-->
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
                ${message}
          </div>
        </div>
    `;

  // Thêm toast element vào trang web
  document.body.insertAdjacentHTML('beforeend', toastElement);
  const toastPlacementExample = document.querySelector('.toast-placement-ex');
  let toastPlacement;
  toastPlacement = new bootstrap.Toast(toastPlacementExample);
  toastPlacement.show();
}

let DataTableBanner;
$(function (){
    loadData();
})

async function loadData(){
  let method = 'GET',
    url = '/banner/get-data-banner',
    param = null,
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
    if(DataTableBanner){
      updateDataTable($('#table-banner-ipretty'), res.data[0].original.data);
  }else {
      drawDataTableBanner(res.data[0].original.data)
  }
}
async function drawDataTableBanner(data){
    let id = $('#table-banner-ipretty'),
        column = [
          { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
          { data: 'image', className: 'text-right' },
          { data: 'title', className: 'text-center' },
          { data: 'status', className: 'text-center' },
          { data: 'action', className: 'text-right' , width: '5%' },
        ],
        button = [
          {
            text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Thêm banner</span>',
            className: 'dt-button button-add-datatable btn btn-primary ml-3',
            action: function ( e, dt, node, config ) {
              openModalCreateBanner();
            }
          }
        ];
    DataTableBanner = await datatableTemplate(id, data , column, button);
}


function changeRunBanner(r){
  Swal.fire({
    title: "Chạy Banner",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusBanner(true, r.data('id') );
    }
  });
}

function changePauseBanner(r){
  Swal.fire({
    title: "Tạm dừng Banner ?",
    text: "Tạm dừng sẽ không còn thấy trên các web client",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: '#004724',
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
      if (result.isConfirmed) {
        changeStatusBanner(false, r.data('id') );
      }
  });
}

async function changeStatusBanner(status, id) {
  let METHOD = 'POST',
      URL = '/banner/change-status',
      PARAM = '',
      DATA = {
        status : status,
        id : id
     };
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  if(res.status == 200){
    successSwalNotify("Thay đổi trạng thái thành công")
    loadData();
  }else{
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

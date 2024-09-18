let DataTableDepartment, DataTableUnActiveDepartment;
$(function (){
  loadData();
})

async function loadData(){
  let method = 'GET',
    url = '/department/get-data-department',
    param = null,
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  if(DataTableDepartment){
      updateDataTable($('#table-department-ipretty'), res.data[0].original.data);
      updateDataTable($('#table-unactive-department-ipretty'), res.data[1].original.data);
  }else {
    drawDataTableDepartment(res)
  }
  dataTotalDepartment(res);
}


async function drawDataTableDepartment(res){
  let id = $('#table-department-ipretty'),
      idUnActive = $('#table-unactive-department-ipretty'),
    column = [
      { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
      { data: 'department_name', className: 'text-center' },
      { data: 'action', className: 'text-center' , width: '5%' },
    ],
    button = [
      {
        text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Thêm Bộ Phận</span>',
        className: 'dt-button button-add-datatable btn btn-primary ml-3',
        action: function ( e, dt, node, config ) {
          openModalCreateDepartment();
        }
      }
    ];
  DataTableDepartment = await datatableTemplate(id, res.data[0].original.data , column, button);
  DataTableUnActiveDepartment = await datatableTemplate(idUnActive, res.data[1].original.data , column, button);
}

function dataTotalDepartment(res){
  $('#totalActiveDepartment').text(res.data[2].totalActive);
  $('#totalUnActiveDepartment').text(res.data[2].totalUnActive);
}


async function changeStatusDepartment(status, id){
  let method = 'post',
    url = '/department/change-status',
    param = null,
    data = {
        id : id,
        status : status
    } ;
  let res = await axiosTemplate(method , url , param , data)
  if(res.data.status == 200){
    successSwalNotify("Thay đổi trạng thái thành công")
    loadData();
  }else{
    errorSwalNotify('Lỗi rồi '+ res);
  }
}


function changeStatusActiceDepartment(r) {
  Swal.fire({
    title: "Thay đổi trạng thái hoạt động",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusDepartment(true, r.data('id') );
    }
  });
}


function changeStatusUnActiceDepartment(r) {
  Swal.fire({
    title: "Thay đổi trạng thái tạm ngưng",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusDepartment(false, r.data('id') );
    }
  });
}

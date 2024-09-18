let dataTableTeacher , dataTableManage , dataTableEmployee ,dataTableStudent
$(function (){
    loadData();
})

async function loadData(){
  let method = 'GET',
      url = '/users/get-data-user',
      param = null,
      data = null ;
  let res = await axiosTemplate(method , url , param , data);
  if(dataTableTeacher || dataTableManage || dataTableEmployee || dataTableStudent ){
    updateDataTable($('#table-teacher-ipretty'), res.data[0].original.data);
    updateDataTable($('#table-manage-ipretty'), res.data[1].original.data);
    updateDataTable($('#table-employee-ipretty'), res.data[2].original.data);
    updateDataTable($('#table-student-ipretty'), res.data[3].original.data);
  }else {
    drawDataTableUsers(res)
  }

  $('#totalUserManage').text(res.data[4].manage);
  $('#totalUserEmployee').text(res.data[4].employee);
  $('#totalUserTeacher').text(res.data[4].teacher);
  $('#totalUserStudent').text(res.data[4].student);
}

async function drawDataTableUsers(res){
  let idTeacher = $('#table-teacher-ipretty'),
      idManage = $('#table-manage-ipretty'),
      idEmployee = $('#table-employee-ipretty'),
      idStudent = $('#table-student-ipretty'),
    column = [
      { data: 'DT_RowIndex', className: 'text-center' },
      { data: 'name', className: 'text-right' },
      { data: 'code', className: 'text-center' },
      { data: 'gender', className: 'text-center' },
      { data: 'verified', className: 'text-center' },
      { data: 'isLocked', className: 'text-center' },
      { data: 'created_at', className: 'text-center' },
      { data: 'action', className: 'text-center' }
    ],
    columnStudent = [
      { data: 'DT_RowIndex', className: 'text-center' },
      { data: 'name', className: 'text-right' },
      { data: 'gender', className: 'text-center' },
      { data: 'verified', className: 'text-center' },
    ]
    button = [
      {
        text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Thêm tài khoản</span>',
        className: 'dt-button button-add-datatable btn btn-primary ml-3',
        action: function ( e, dt, node, config ) {
          openModalCreateUsers();
        }
      }
    ];
  dataTableTeacher = await  datatableTemplate(idTeacher, res.data[0].original.data , column, button);
  dataTableManage = await datatableTemplate(idManage, res.data[1].original.data , column, button);
  dataTableEmployee = await datatableTemplate(idEmployee, res.data[2].original.data , column, button);
  dataTableStudent = await datatableTemplate(idStudent, res.data[3].original.data , columnStudent, []);
}


function changeLockerUser(r) {
  Swal.fire({
    title: "khoá tài khoản",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      lockOrUnlock(true, r.data('id') );
    }
  });
}
function changeUnLockerUser(r) {
  Swal.fire({
    title: "Mở khoá tài khoản",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      lockOrUnlock(false, r.data('id') );
    }
  });
}

function resetPasswordUser(){
  Swal.fire({
    title: "Bạn có chắc reset mật khẩu ?",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      // lockOrUnlock(true, r.data('id') );
    }
  });
}
async function lockOrUnlock(status , id) {
  let METHOD = 'POST',
    URL = '/users/change-status',
    PARAM = '',
    DATA = {
      id : id,
      isLocked : status
    } ;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  if(res.data.status == 200){
    let messager = '';
    if(res.data.data.isLocked){
      messager = 'khoá thành công';
    }else {
      messager = 'Mở khoá thành công';
    }
    console.log(messager);
    successSwalNotify(messager)
    loadData();
  }else {
    errorSwalNotify('Lỗi thay đổi trạng thái');
  }

}

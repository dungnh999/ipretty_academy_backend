let thisDataDepartment;

function openModalUpdateDepartment(r){
  $('#modal-update-department').modal('show');
  thisDataDepartment = r;
  getDetailDepartment();
}



async function getDetailDepartment(){
  let method = 'Get',
    url = '/department/get-detail-department',
    param = {
      id : thisDataDepartment.data('id')
    },
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  if(res.data.status == 200) {
    $('#name-update-department-ipretty').val(res.data.data.department_name)
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

async function saveUpdateDepartment(){
  if(checkValidateSave($('#form-update-department-ipretty'))){
    return false;
  };
  let method = 'POST',
    url = '/department/update',
    param = null,
    data = {
      id : thisDataDepartment.data('id'),
      department_name : $('#name-update-department-ipretty').val()
    } ;
  let res = await axiosTemplate(method , url , param , data)
  if(res.data.status === 200) {
    successSwalNotify("Chỉnh sửa thành công")
    loadData();
    closeModalUpdateDepartment();
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

function closeModalUpdateDepartment(){
  $('#modal-update-department').modal('hide');
}

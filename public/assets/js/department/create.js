function openModalCreateDepartment(){
  $('#modal-create-department').modal('show');
}

async function saveCreateDepartment(){
  if(checkValidateSave($('#form-create-department-ipretty'))){
    return false;
  };
  let method = 'POST',
    url = '/department/create',
    param = null,
    data = {
        department_name : $('#name-create-department-ipretty').val()
    } ;
  let res = await axiosTemplate(method , url , param , data)
  if(res.data.status == 200) {
    successSwalNotify("Thêm mới thành công")
    loadData();
    closeModalCreateDepartment();
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

function closeModalCreateDepartment(){
  $('#modal-create-department').modal('hide');
}

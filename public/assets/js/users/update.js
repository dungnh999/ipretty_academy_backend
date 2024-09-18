let idUserUpdateUser;
function openModalUpdateUsers(r){
  $('#modal-update-users').modal('show');
  idUserUpdateUser = r.data('id');
  $('#role-update-user-ipretty').select2({
    dropdownParent: '#modal-update-users',
  });
  $('#position-update-user-ipretty').select2({
    dropdownParent: '#modal-update-users',
  });
  getRoleUpdateUser();
}
async function getRoleUpdateUser(){
  let METHOD = 'GET',
    URL = '/users/get-data-department',
    PARAM = '',
    DATA = null;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  $('#role-update-user-ipretty').html(res.data[0]);
  getDataProfileUser();
}
async function getDataProfileUser(){
  let METHOD = 'GET',
    URL = '/users/get-profile',
    PARAM = {
        id : idUserUpdateUser
    },
    DATA = null;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  $('#name-update-user-ipretty').val(res.data.data.name)
  $('#email-update-user-ipretty').text(res.data.data.email)
  $('#phone-update-user-ipretty').val(res.data.data.phone)
  $('#date-update-user-ipretty').val(res.data.data.birthday)
  $('#address-update-user-ipretty').val(res.data.data.address)
  $('#gender-update-user input[value="'+ res.data.data.gender +'"]').trigger('click')
  $('#role-update-user-ipretty').val(res.data.data.department_id).trigger('change.select2')
  $('#position-update-user-ipretty').val(res.data.data.menuroles).trigger('change.select2')
}

async function updateDataProfileUser(){
  let nameUpdateProfileUser = $('#name-update-user-ipretty').val(),
      emailUpdateProfileUser = $('#email-update-user-ipretty').text(),
      phoneUpdateProfileUser = $('#phone-update-user-ipretty').val(),
      dateUpdateProfileUser = $('#date-update-user-ipretty').val(),
      addressUpdateProfileUser = $('#address-update-user-ipretty').val(),
      positionUpdateProfileUser = $('#position-update-user-ipretty').val(),
      roleUpdateProfileUser = $('#role-update-user-ipretty').find('option:selected').val();

  let METHOD = 'POST',
    URL = '/users/update',
    PARAM = '',
    DATA = {
      id : idUserUpdateUser,
      name : nameUpdateProfileUser,
      email : emailUpdateProfileUser,
      address : addressUpdateProfileUser,
      phone : phoneUpdateProfileUser,
      birthday : dateUpdateProfileUser,
      department_id : roleUpdateProfileUser,
      position : positionUpdateProfileUser
    };
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  if(res.data.status == 200) {
    successSwalNotify("Chỉnh sửa thành công")
    loadData();
    closeModalUpdateUsers();
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}
function closeModalUpdateUsers(){
  $('#modal-update-users').modal('hide');
}

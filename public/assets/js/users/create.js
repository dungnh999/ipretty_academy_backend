function openModalCreateUsers(){
    $('#modal-create-users').modal('show');
    $('#role-create-user-ipretty').select2({
      dropdownParent: '#modal-create-users',
    });

    $('#position-create-user-ipretty').select2({
      dropdownParent: '#modal-create-users',
    });
    getRoleCreateUser();
}

async function getRoleCreateUser(){
  let METHOD = 'GET',
    URL = '/users/get-data-department',
    PARAM = '',
    DATA = null;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  $('#role-create-user-ipretty').html(res.data[0])
}

async function createUserIpretty(){
  let nameCreateUser = $('#name-create-user-ipretty').val();
    let dateCreateUser = $('#date-create-user-ipretty').val();
    let emailCreateUser = $('#email-create-user-ipretty').val();
    let phoneCreateUser = $('#phone-create-user-ipretty').val();
    let departmentId = $('#role-create-user-ipretty').find('option:selected').val();
    let position = $('#position-create-user-ipretty').find('option:selected').val();
    let sexCreateUser = $('#group-gender-create-user input:checked').val();
    let addressCreateUser = $('#address-create-user-ipretty').val();
    let METHOD = 'POST',
        URL = '/users/create',
        PARAM = '',
        DATA = {
          name : nameCreateUser,
          birthday : dateCreateUser,
          email : emailCreateUser,
          phone : phoneCreateUser,
          department_id : departmentId,
          gender : sexCreateUser,
          address : addressCreateUser,
          position : position
        };
    let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
    if(res.data.status == 200) {
      successSwalNotify("Thêm mới thành công")
      loadData();
      $('#username-create-info-user').text(res.data.data.email);
      $('#password-create-info-user').text(res.data.data.info_password);
      closeModalCreateUsers();
      openModalInfoUsers();
    }else {
      errorSwalNotify(res.data.message);
    }
}

function closeModalCreateUsers(){
  $('#modal-create-users').modal('hide');
}

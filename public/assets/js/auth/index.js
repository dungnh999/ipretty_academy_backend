$(function () {
  $('#password, #email').on('input', function () {
    $('#text-error-login').text('')
  })

  $(document).keypress(function (e) {
    if (e.keyCode == 13) {
      login();
    }
  });

  $('#btn-login').on('click', function () {
    login();
  })
})


async function login() {
  $('#text-error-login').text('')
  let email = $('#email').val();
  let password = $('#password').val();
  let url = '/login',
    method = 'post',
    param = null,
    data = {
      email: email,
      password: password
    };
  let res = await axiosTemplate(method, url, param, data);
  if (res.data.status === 200) {
    $('#text-error-login').text('')
    window.location.href = '/'
  } else {
    $('#text-error-login').text(res.data.message)
  }
}


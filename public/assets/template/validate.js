function checkValidateSave(form){
  let flag = false;
  if(!form[0].checkValidity()){
    form.addClass('was-validated')
    flag = true
  }
  return flag;
}


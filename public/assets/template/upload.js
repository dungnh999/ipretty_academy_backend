async function uploadFileTemplate(file, name){
  let formData = new FormData();
  formData.append('file', file, name)
  let METHOD = 'POST',
    URL = '/upload/post-file',
    PARAM = '',
    DATA = formData;
  let res = await axiosTemplate(METHOD, URL, PARAM, DATA)
  return res;
}

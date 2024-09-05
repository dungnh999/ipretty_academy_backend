async function axiosTemplate(method, url , param , data){
  let res = await axios({
    method: method,
    url: url,
    params : param,
    data : data,
    headers: {
      'Content-Type': 'application/json'
    }
  })
  console.log('Dữ liệu: ', res);
  return res;
}


async function axiosTemplateFile(method, url , param , data){
  let res = await axios({
    method: method,
    url: url,
    params : param,
    data : data,
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
  console.log('Dữ liệu: ', res);
  return res;
}

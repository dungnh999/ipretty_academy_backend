$(function (){
  loadData();
})

async function loadData(){
  let method = 'GET',
    url = '/order/get-data-order',
    param = null,
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  console.log(res);
  // if(DataTableDepartment){
  //   // updateDataTable($('#table-department-ipretty'), res.data[0].original.data);
  //   // updateDataTable($('#table-unactive-department-ipretty'), res.data[1].original.data);
  // }else {
  //   // drawDataTableDepartment(res)
  // }
  // dataTotalDepartment(res);
}

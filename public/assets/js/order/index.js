$(function (){
  loadData();
})

async function loadData(){
  let method = 'GET',
    url = '/order/get-data-order',
    param = null,
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  // if(DataTableDepartment){
  //   // updateDataTable($('#table-department-ipretty'), res.data[0].original.data);
  //   // updateDataTable($('#table-unactive-department-ipretty'), res.data[1].original.data);
  // }else {
    drawDataTableOrder(res)
  // }
  dataTotalDepartment(res);
}



async function drawDataTableOrder(res){
  let id = $('#table-order-checkedout-ipretty'),
      column = [
        { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
        { data: 'order_id', className: 'text-center' },
        { data: 'total', className: 'text-center' },
        { data: 'salePrice', className: 'text-center'},
        { data: 'total', className: 'text-center'},
        { data: 'created_at', className: 'text-center'},
        { data: 'action', className: 'text-center'},

      ],
      button = [];
      DataTableOrderCheckedout = await datatableTemplate(id, res.data[1].original.data , column, button);
}

function dataTotalDepartment(res){
  $('#total-data-order-checkout').text(res.data[0]['total-checkouted']);
}
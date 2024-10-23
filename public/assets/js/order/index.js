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
  let idCheckedOut= $('#table-order-checkedout-ipretty'),
      idOrdered= $('#table-ordered-order-ipretty'),
      idPaid= $('#table-paid-order-ipretty'),
      idCanceled= $('#table-canceled-order-ipretty'),
      column = [
        { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
        { data: 'order_id', className: 'text-center', with: '10%' },
        { data: 'name', className: 'text-left' },
        { data: 'total', className: 'text-center' },
        { data: 'salePrice', className: 'text-center'},
        { data: 'total', className: 'text-center'},
        { data: 'created_at', className: 'text-center'},
        { data: 'action', className: 'text-center'},

      ],
      button = [];
      DataTableOrderCheckedout = await datatableTemplate(idCheckedOut, res.data[1].original.data , column, button);
      DataTableOrderPaid = await datatableTemplate(idPaid, res.data[2].original.data , column, button);
      DataTableOrdered = await datatableTemplate(idOrdered, res.data[3].original.data , column, button);
      DataTableCanceled = await datatableTemplate(idCanceled, res.data[4].original.data , column, button);

}

function dataTotalDepartment(res){
  $('#total-data-order-checkout').text(res.data[0]['total-checkouted']);
  $('#total-data-order-paid').text(res.data[0]['total-paid']);
  $('#total-data-order-oredered').text(res.data[0]['total-ordered']);
  $('#total-data-order-canceled').text(res.data[0]['total-canceled']);

}
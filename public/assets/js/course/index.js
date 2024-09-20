$(function (){
  loadData();
})

async function loadData(){
  let method = 'GET',
    url = '/course/get-data-course',
    param = null,
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  drawDataTableCourse(res);
}


 async  function drawDataTableCourse(res) {
  let id = $('#table-course-active-ipretty'),
      idUnActive = $('#table-course-unactive-ipretty'),
      column = [
        { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
        { data: 'course_name', className: 'text-center ' , width: '30%'},
        { data: 'teacher_name', className: 'text-left' },
        { data: 'category.category_name', className: 'text-center' },
        { data: 'status', className: 'text-center' },
        { data: 'created_at', className: 'text-center' },
        { data: 'action', className: 'text-center' , width: '5%' },
      ],
      button = [
        {
          text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Thêm Khoá Học</span>',
          className: 'dt-button button-add-datatable btn btn-primary ml-3',
          action: function ( e, dt, node, config ) {
            openModalCreateCourse();
          }
        }
      ];
  DataTableDepartment = await datatableTemplate(id, res.data[0].original.data , column, button);
  DataTableUnActiveDepartment = await datatableTemplate(idUnActive, res.data[1].original.data , column, button);
}

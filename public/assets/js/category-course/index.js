let DataTableCoursesCategory, DataTableUnActiveCoursesCategory;
$(function (){
  loadData();
})
async function loadData(){
  let method = 'GET',
    url = '/courses-category/get-data-category',
    param = null,
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  if(DataTableCoursesCategory || DataTableUnActiveCoursesCategory){
    updateDataTable($('#table-category-course-ipretty'), res.data[0].original.data);
    updateDataTable($('#table-unactive-category-course-ipretty'), res.data[1].original.data);
  }else {
    drawDataTableCoursesCategory(res)
  }
  dataTotalCoursesCategory(res)
}
async function drawDataTableCoursesCategory(res){
  let idActive = $('#table-category-course-ipretty'),
      idUnActive = $('#table-unactive-category-course-ipretty'),
    column = [
      { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
      { data: 'category_name', className: 'text-center' },
      { data: 'category_code', className: 'text-center' },
      { data: 'action', className: 'text-center' , width: '5%' },
    ],
    button = [
      {
        text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Thêm Danh mục khoá học</span>',
        className: 'dt-button button-add-datatable btn btn-primary ml-3',
        action: function ( e, dt, node, config ) {
          openModalCreateCoursesCategory();
        }
      }
    ];
  DataTableCoursesCategory = await datatableTemplate(idActive, res.data[0].original.data , column, button);
  DataTableUnActiveCoursesCategory = await datatableTemplate(idUnActive, res.data[1].original.data , column, button);
}
function dataTotalCoursesCategory(res){
  $('#totalActiveCourseCategory').text(res.data[2].totalActive);
  $('#totalUnActiveCourseCategory').text(res.data[2].totalUnActive);
}

function changeStatusActiveCoursesCategory(r){
  Swal.fire({
    title: "Thông báo",
    text: "Bạn có muốn bật hoạt động",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusCoursesCategory(true, r.data('id'));
    }
  });
}
function changeStatusUnActiveCoursesCategory(r){
  Swal.fire({
    title: "Thông báo",
    text: "Bạn có muốn tạm ngưng",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusCoursesCategory(false, r.data('id'));
    }
  });
}

async function changeStatusCoursesCategory(status , id){
  let METHOD = 'POST',
    URL = '/courses-category/change-status',
    PARAM = '',
    DATA = {
      status : status,
      id : id
    };
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  if(res.status == 200){
    successSwalNotify("Thay đổi trạng thái thành công")
    loadData();
  }else{
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

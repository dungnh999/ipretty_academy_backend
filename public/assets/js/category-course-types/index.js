let DataTableCoursesCategoryTypes , DataTableUnActiveCoursesCategoryTypes;
$(function (){
  loadData();
})

async function loadData(){
  let method = 'GET',
    url = '/courses-category-type/get-data-category-type',
    param = null,
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  if(DataTableCoursesCategoryTypes || DataTableUnActiveCoursesCategoryTypes){
    updateDataTable($('#table-category-course-types-ipretty'), res.data[0].original.data);
    updateDataTable($('#table-unactive-category-course-types-ipretty'), res.data[1].original.data);
  }else {
    drawDataTableCourseCategoryTypes(res)
  }
  dataTotalCoursesCategoryTypes(res);
}

async  function drawDataTableCourseCategoryTypes(res) {
  let id = $('#table-category-course-types-ipretty'),
    idUnActive = $('#table-unactive-category-course-types-ipretty'),
    column = [
      { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
      { data: 'category_type_name', className: 'text-center' },
      { data: 'action', className: 'text-center' , width: '5%' },
    ],
    button = [
      {
        text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Thêm loại danh mục</span>',
        className: 'dt-button button-add-datatable btn btn-primary ml-3',
        action: function ( e, dt, node, config ) {
          openModalCreateCourseCategoryTypes();
        }
      }
    ];
  DataTableCoursesCategoryTypes = await datatableTemplate(id, res.data[0].original.data , column, button);
  DataTableUnActiveCoursesCategoryTypes = await datatableTemplate(idUnActive, res.data[1].original.data , column, button);
}

function dataTotalCoursesCategoryTypes(res){
  $('#totalActiveCourseCategoryTypes').text(res.data[2].totalActive);
  $('#totalUnActiveCourseCategoryTypes').text(res.data[2].totalUnActive);
}

function changeStatusActiveCoursesCategoryTypes(r){
  Swal.fire({
    title: "Thông báo",
    text: "Bạn có muốn hoạt động ?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusCoursesCategoryTypes(true, r.data('id'));
    }
  });
}

function changeStatusUnActiveCoursesCategoryTypes(r){
  Swal.fire({
    title: "Thông báo",
    text: "Bạn có muốn tạm ngưng",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusCoursesCategoryTypes(false, r.data('id'));
    }
  });
}

async function changeStatusCoursesCategoryTypes(status , id){
  let METHOD = 'POST',
    URL = '/courses-category-type/change-status',
    PARAM = '',
    DATA = {
      status : status,
      id : id
    };
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  if(res.status === 200){
    successSwalNotify("Thay đổi trạng thái thành công")
    loadData();
  }else{
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

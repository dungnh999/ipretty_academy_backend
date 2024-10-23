let DataTableDepartment, DataTableUnActiveDepartment;

$(function (){
  loadData();
})

async function loadData(){
  let method = 'GET',
    url = '/course/get-data-course',
    param = null,
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  $('#total-tab-active-course').text(res.data[2]['total_active'])
  $('#total-tab-unactive-course').text(res.data[2]['total_UnActive'])

    if (DataTableDepartment || DataTableUnActiveDepartment) {
        updateDataTable($('#table-course-active-ipretty'), res.data[0].original.data);
        updateDataTable($('#table-course-unactive-ipretty'), res.data[1].original.data);
    } else {
        drawDataTableCourse(res)
    }
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



async function changeStatusUnActiveCourses(r) {
  Swal.fire({
    title: "Thông báo",
    text: "Bạn có muốn tạm ngưng",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusCourses(false, r.data('id'));
    }
  });
}
async function changeStatusActiveCourses(r) {
  Swal.fire({
    title: "Thông báo",
    text: "Bạn có muốn bật hoạt động",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changeStatusCourses(true, r.data('id'));
    }
  });
}
async function changeStatusCourses(status , id) {
    let METHOD = 'POST',
      URL = '/course/change-status-course',
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




async function changeStatusUnPublishedCourses(r) {
  Swal.fire({
    title: "Thông báo",
    text: "Bạn có muốn ",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changePublishedCourses(false, r.data('id'));
    }
  });
}
async function changeStatusPublishedCourses(r) {
  Swal.fire({
    title: "Thông báo",
    text: "Bạn có muốn xuất bản",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Đồng ý",
    cancelButtonText: "Đóng"
  }).then((result) => {
    if (result.isConfirmed) {
      changePublishedCourses(true, r.data('id'));
    }
  });
}

async function changePublishedCourses(status , id) {
    let METHOD = 'POST',
      URL = '/course/change-published-course',
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

let fileUpload;
function openModalCreateCoursesCategory(){
  $('#modal-create-category-course').modal('show');
  $('#image-create-category-course-ipretty').unbind('change').on('change', function (){
    let url = URL.createObjectURL(this.files[0]);
    fileUpload = this.files[0];
    $('#image-preview-create-category-course').attr('src', url)
  })

  let $select = $('#course-category-types-create').select2({
    dropdownParent: '#modal-create-category-course',
  });
  $select.data('select2').$container.addClass('w-100');

  getCategoryTypesCreate();
}

async function saveCreateCoursesCategory(){
  if(checkValidateSave($('#form-create-category-course-ipretty'))){
    return false;
  };
  let formData = new FormData();
  formData.append('category_name', $('#name-create-category-course-ipretty').val()),
  formData.append('course_category_attachment', fileUpload);
  formData.append('category_description', $('#description-create-category-course').val());
  formData.append('category_type_id', $('#course-category-types-create').val());
  let method = 'POST',
    url = '/courses-category/create',
    param = null,
    data = formData;
  let res = await axiosTemplate(method , url , param , data)
  if(res.status == 200) {
    successSwalNotify("Thêm mới thành công")
    loadData();
    closeModalCreateCoursesCategory();
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

async function getCategoryTypesCreate(){
  let METHOD = 'get',
    URL = '/courses-category/get-data-category-types',
    PARAM = '',
    DATA = null;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  $('#course-category-types-create').html(res.data.data)
}

function closeModalCreateCoursesCategory(){
  $('#modal-create-category-course').modal('hide');
  $('#name-create-category-course-ipretty').val('');
  $('#description-create-category-course').val('');
}

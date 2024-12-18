let thisIdUpadteCoursesCategory, fileUpdateUploadCoursesCategory = null;
function openModalUpdateCoursesCategory(r){
  $('#modal-update-category-course').modal('show');
  $('#image-update-category-course-ipretty').unbind('change').on('change', function (){
    let url = URL.createObjectURL(this.files[0]);
    fileUpdateUploadCoursesCategory = this.files[0];
    $('#image-preview-update-category-course').attr('src', url)
  })
  thisIdUpadteCoursesCategory = r;

  let $select = $('#course-category-types-update').select2({
    dropdownParent: '#modal-update-category-course',
  });
  getDetail();
}

async function getDetail(){
  let METHOD = 'get',
    URL = '/courses-category/detail',
    PARAM = {
      id : thisIdUpadteCoursesCategory.data('id')
    },
    DATA = null;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  if(res.status === 200){
    $('#name-update-category-course-ipretty').val(res.data.data.category_name)
    $('#image-preview-update-category-course').attr( 'src' ,res.data.data.course_category_attachment)
    $('#description-update-category-course').val(res.data.data.category_description)
    // $('#course-category-types-update').val(res.data.data.category_type.id).trigger('change.select2')
    $('#name-update-category-code-course-ipretty').val(res.data.data.category_code)
  }else {
    console.log('lỗi rồi', res);
  }
}

async function getCategoryTypes(){
  let METHOD = 'get',
    URL = '/courses-category/get-data-category-types',
    PARAM = {
      id : thisIdUpadteCoursesCategory.data('id')
    },
    DATA = null;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  $('#course-category-types-update').html(res.data.data)
  getDetail();
}

async function saveUpdateCoursesCategory() {
  let formData = new FormData();
  formData.append('category_name',  $('#name-update-category-course-ipretty').val())
  formData.append('id',  thisIdUpadteCoursesCategory.data('id'))
  formData.append('course_category_attachment',  fileUpdateUploadCoursesCategory)
  formData.append('category_description',  $('#description-update-category-course').val())
  formData.append('category_type_id',  $('#course-category-types-update').find('option:selected').val())
  formData.append('category_code',  $('#name-update-category-code-course-ipretty').val())

  let METHOD = 'post',
    URL = '/courses-category/update',
    PARAM = null,
    DATA = formData;
  let res = await axiosTemplateFile(METHOD, URL , PARAM, DATA )
  if(res.status == 200) {
    successSwalNotify("Cập nhật thành công")
    loadData();
    closeModalUpdateCoursesCategory();
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}


function closeModalUpdateCoursesCategory(r){
  $('#modal-update-category-course').modal('hide');
}

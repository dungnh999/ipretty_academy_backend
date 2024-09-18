let thisUpdateCourseCategoryTypes;
async function openModalUpdateCourseCategoryTypes(r) {
  $('#modal-update-category-course-types').modal('show');
  thisUpdateCourseCategoryTypes = r;
  getDataUpdate();
}

async function getDataUpdate(){
  let METHOD = 'GET',
    URL = '/courses-category-type/data-update-course-category-type',
    PARAM = {
      id : thisUpdateCourseCategoryTypes.data('id')
    },
    DATA = '';
  let res = await axiosTemplate(METHOD, URL, PARAM, DATA)
  $('#name-update-category-course-types-ipretty').val(res.data.data.category_type_name)
  $('#description-update-category-course-types').val(res.data.data.category_type_description)
}

async function saveUpdateCoursesCategoryTypes(){
   let name = $('#name-update-category-course-types-ipretty').val();
   let description = $('#description-update-category-course-types').val();
  let METHOD = 'POST',
    URL = '/courses-category-type/update-course-category-type',
    PARAM = '',
    DATA = {
      name : name,
      description : description,
      id : thisUpdateCourseCategoryTypes.data('id'),
    };
  let res = await axiosTemplate(METHOD, URL, PARAM, DATA)
  if(res.status == 200) {
    successSwalNotify("Cập nhật thành công")
    loadData();
    closeModalUpdateCourseCategoryTypes();
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

async function closeModalUpdateCourseCategoryTypes() {
  $('#modal-update-category-course-types').modal('hide');
}

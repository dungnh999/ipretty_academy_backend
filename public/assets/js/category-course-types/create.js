async function openModalCreateCourseCategoryTypes() {
  $('#modal-create-category-course-types').modal('show');
}


async function saveCreateCoursesCategoryTypes(){
   let name = $('#name-create-category-course-types-ipretty').val();
   let description = $('#description-create-category-course-types').val();
  let METHOD = 'POST',
    URL = '/courses-category-type/create-course-category-type',
    PARAM = '',
    DATA = {
      name : name,
      description : description,
    };
  let res = await axiosTemplate(METHOD, URL, PARAM, DATA)
  if(res.status == 200) {
    successSwalNotify("Thêm mới thành công")
    loadData();
    closeModalCreateCoursesCategory();
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

async function closeModalCreateCourseCategoryTypes() {
  $('#modal-create-category-course-types').modal('hide');
}

let thisButtonUpdateCourse, fileBannerUpdateCourse , certificateImageUpdateCourse;

async function openModalUpdateCourse(r) {
    $('#modal-update-course').modal('show');
    $('#modal-update-chapter').on('show.bs.modal', function () {
        $('#modal-update-course').addClass('d-none');
    });
    $('#modal-update-chapter').on('hide.bs.modal', function () {
        $('#modal-update-course').removeClass('d-none');
    });
    thisButtonUpdateCourse = r;
    // EDITOR
    editorDescriptionCourse = await editorTemplate('#description-update-course', '#description-tool-update-course');

    // Select2
    let $select = $('#select-teacher-update-course').select2({
        dropdownParent: '#modal-update-course'
    });
    $select.data('select2').$container.addClass('w-100');

    //
    let $select1 = $('#course-category-update').select2({
        dropdownParent: '#modal-update-course'
    });
    $select1.data('select2').$container.addClass('w-100');

    $('#input-banner-update-course').on('change', function () {
        let url = URL.createObjectURL(this.files[0]);
        fileBannerUpdateCourse = this.files[0];
        $('#preview-banner-update-course').attr('src', url);
    });

    $('#input-certificate-update-course').on('change', function () {
        let url = URL.createObjectURL(this.files[0]);
        certificateImageUpdateCourse = this.files[0];
        $('#preview-certificate-update-course').attr('src', url);
    });

    let tagify = new Tagify($('#target-course-update').get(0))


    // Lấy danh sách giảng viên
    getDataTeacherUpdate();

    //Lấy danh sách danh mục
    getDataCategoryCourseUpdate();
    getDetailUpdateCourse();
}

// Lấy chi tiêết khoá học
async function getDetailUpdateCourse(){  
    let id =  thisButtonUpdateCourse.data('id');
    let method = 'GET',
    url = '/course/get-detail-course',
    param = {
        course_id : id
    },
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  if(res.data.status == 200){
    $('#name-course-update').val(res.data.data.course_name);
    $('#course-price-update').val(res.data.data.course_price);
    $('#course-price-sale-update').val(res.data.data.course_sale_price);
    $('#preview-banner-update-course').attr('src' , res.data.data.course_feature_image);
    $('#preview-certificate-update-course').attr('src' , res.data.data.certificate_image);
    $('#course-category-update').val(res.data.data.category_id).trigger('change.select2');
    $('#select-teacher-update-course').val(res.data.data.teacher.id).trigger('change.select2');    
    $('#target-course-update').val(JSON.stringify(res.data.data.course_target));
  }else{
    errorSwalNotify('Lỗi rồi '+ res);
  }
}
async function getDataTeacherUpdate() {
    let METHOD = 'GET',
        URL = '/course/get-teacher',
        PARAM = '',
        DATA = '';
    let res = await axiosTemplate(METHOD, URL, PARAM, DATA);
    $('#select-teacher-update-course').html(res.data[0]);
}
async function getDataCategoryCourseUpdate() {
    let METHOD = 'GET',
        URL = '/course/get-category',
        PARAM = '',
        DATA = '';
    let res = await axiosTemplate(METHOD, URL, PARAM, DATA);
    $('#course-category-update').html(res.data[0]);
}


async function saveUpdateCourses(){
    const dataUpdate = new FormData();
    dataUpdate.append('course_name', $('#name-course-update').val());
    dataUpdate.append('teacher_id', $('#select-teacher-update-course').val());
    dataUpdate.append('course_id',  thisButtonUpdateCourse.data('id'));
    dataUpdate.append('category_id', $('#course-category-update').val());
    dataUpdate.append('course_price', $('#course-price-update').val());
    dataUpdate.append('course_target', $('#target-course-update').val());
    dataUpdate.append('course_sale_price', $('#course-price-sale-update').val());
    dataUpdate.append('course_description', editorDescriptionCourse.root.innerHTML);
    dataUpdate.append('course_feature_image', fileBannerUpdateCourse);
    dataUpdate.append('certificate_image', certificateImageUpdateCourse);
    let method = 'POST',
      url = '/course/update-course',
      param = null,
      data = dataUpdate;
    let res = await axiosTemplateFile(method , url , param , data)
    if(res.status == 200) {
      successSwalNotify("Chỉnh sửa thành công")
      loadData();
      closeModalUpdateCourse();
    }else {
      errorSwalNotify('Lỗi rồi '+ res);
    }
}
  


function closeModalUpdateCourse() {
    $('#modal-update-course').modal('hide');
}
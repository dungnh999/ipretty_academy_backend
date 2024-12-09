let stepperCreate,
    // editorDescriptionCourse,
    certificateImage,
    fileBannerCourse,
    LessonsDataCourse = [
        {
            id: 1,
            lessons_name: 'ssss',
            chapter: []
        }
    ];

async function openModalCreateCourse() {
    $('#modal-create-course').modal('show');

    $('#modal-create-chapter').on('show.bs.modal', function () {
        $('#modal-create-course').addClass('d-none');
    });

    $('#modal-create-chapter').on('hide.bs.modal', function () {
        $('#modal-create-course').removeClass('d-none');
    });

    // EDITOR
    editorDescriptionCourse = await editorTemplate('#editor', '#toolbar');

    // Select2
    let $select = $('#select-teacher-create-course').select2({
        dropdownParent: '#modal-create-course'
    });
    $select.data('select2').$container.addClass('w-100');

    // Select2
    let $select1 = $('#course-category-create').select2({
        dropdownParent: '#modal-create-course'
    });
    $select1.data('select2').$container.addClass('w-100');

    $('#input-banner-create-course').on('change', function () {
        let url = URL.createObjectURL(this.files[0]);
        fileBannerCourse = this.files[0];
        $('#preview-banner-create-course').attr('src', url);
    });

    $('#input-certificate-create-course').on('change', function () {
        let url = URL.createObjectURL(this.files[0]);
        certificateImage = this.files[0];
        $('#preview-certificate-create-course').attr('src', url);
    });

    $('#name-course-create').on('change input', function(){
        $('#link-course-create').val(convertToSlugTemplate($(this).val()))
    })

    let tagify = new Tagify($('#target-course-create').get(0))
        
    // Get data teacher
    getDataTeacher();
    getDataCategoryCourse();
}

async function getDataTeacher() {
    let METHOD = 'GET',
        URL = '/course/get-teacher',
        PARAM = '',
        DATA = '';
    let res = await axiosTemplate(METHOD, URL, PARAM, DATA);
    $('#select-teacher-create-course').html(res.data[0]);
}

async function getDataCategoryCourse() {
    let METHOD = 'GET',
        URL = '/course/get-category',
        PARAM = '',
        DATA = '';
    let res = await axiosTemplate(METHOD, URL, PARAM, DATA);
    $('#course-category-create').html(res.data[0]);
}

async function saveCreateCourse() {
    const data = new FormData()

    data.append('course_name', $('#name-course-create').val());
    data.append('teacher_id', $('#select-teacher-create-course').val());
    data.append('category_id', $('#course-category-create').val());
    data.append('course_price', $('#course-price-create').val());
    data.append('course_target', $('#target-course-create').val());
    data.append('course_price_sale', $('#course-price-sale-create').val());
    data.append('slug_course', $('#link-course-create').val())
    data.append('course_description', editorDescriptionCourse.root.innerHTML);
    data.append('course_feature_image', fileBannerCourse);
                                                                                                                                                data.append('certificate_image', certificateImage);
    // setItemLocalStorage('course', JSON.stringify(course));

    let METHOD = 'POST',
        URL = '/course/create-course',
        PARAM = '',
        DATA = data;
    let res = await axiosTemplateFile(METHOD, URL, PARAM, DATA);
    if(res.data.status == 200) {
        successSwalNotify("Thêm mới thành công")
        loadData();
        closeModalCreateCourse();
    }else {
        errorSwalNotify('Lỗi rồi '+ res);
    }
}

function closeModalCreateCourse() {
    $('#modal-create-course').modal('hide');
}

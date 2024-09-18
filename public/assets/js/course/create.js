// let stepperCreate,
//   // editorDescriptionCourse,
//   fileBannerCourse,
//   LessonsDataCourse = [
//     {
//       id: 1,
//       lessons_name: 'ssss',
//       chapter: []
//     }
//   ];
// async function openModalCreateCourse() {
//   $('#modal-create-course').modal('show');

//   $('#modal-create-chapter').on('show.bs.modal', function () {
//     $('#modal-create-course').addClass('d-none');
//   });

//   $('#modal-create-chapter').on('hide.bs.modal', function () {
//     $('#modal-create-course').removeClass('d-none');
//   });

//   // EDITOR
//   editorDescriptionCourse = await editorTemplate('#editor', '#toolbar');

//   // Select2
//   let $select = $('#select-teacher-create-course').select2({
//     dropdownParent: '#modal-create-course'
//   });
//   $select.data('select2').$container.addClass('w-100');

//   // Select2
//   let $select1 = $('#course-category-create').select2({
//     dropdownParent: '#modal-create-course'
//   });
//   $select1.data('select2').$container.addClass('w-100');

//   $('#input-banner-create-course').on('change', function () {
//     let url = URL.createObjectURL(this.files[0]);
//     fileBannerCourse = this.files[0];
//     $('#preview-banner-create-course').attr('src', url);
//   });

//   $(document).on('input', '.accordion-header input', function () {
//     let item = $(this).parents('button').data('id');
//     LessonsDataCourse[item]['lessons_name'] = $(this).val();
//   });

//   // Get data teacher
//   getDataTeacher();
//   getDataCategoryCourse();
// }

// async function getDataTeacher() {
//   let METHOD = 'GET',
//     URL = '/course/get-teacher',
//     PARAM = '',
//     DATA = '';
//   let res = await axiosTemplate(METHOD, URL, PARAM, DATA);
//   $('#select-teacher-create-course').html(res.data[0]);
// }

// async function getDataCategoryCourse() {
//   let METHOD = 'GET',
//     URL = '/course/get-category',
//     PARAM = '',
//     DATA = '';
//   let res = await axiosTemplate(METHOD, URL, PARAM, DATA);
//   $('#course-category-create').html(res.data[0]);
// }

// function addChapterContent() {
//   $('#course-content').append(`<div class="accordion-item shadow-none border" >
//                                   <div class="accordion-header" id="headingOne" >
//                                     <button type="button" class="accordion-button bg-lighter rounded-0 collapsed" data-id="${
//                                       $('.accordion-item').length
//                                     }" data-bs-target="#chapter-item-${$('.accordion-item').length}" ar>
//                                             <span class="d-flex flex-column w-100">
//                                               <div class="d-flex flex-row justify-content-between align-items-center">
//                                                   <span class="h5 mb-1 w-100" style="max-width: max-content">Chương ${
//                                                     $('.accordion-item').length + 1
//                                                   }:</span>
//                                                   <input id="smallInput" class="form-control form-control mx-2" type="text" placeholder=".form-control-sm">
//                                               </div>
//                                               <span class="fw-normal">0 bài học</span>
//                                             </span>
//                                     </button>
//                                   </div>
//                                   <div id="chapter-item-${
//                                     $('.accordion-item').length
//                                   }" class="accordion-collapse collapse show" data-bs-parent="#courseContent">
//                                     <div class="accordion-body py-3 border-top" >
//                                         <div class="item-chapter-group" bis_skin_checked="1"></div>
//                                       <div class="form-check d-flex align-items-center mb-3" >
//                                         <div class="w-100 mt-3">
//                                           <button type="button" class=" btn btn-primary w-100" onclick="openCreateChapter($(this))">Thêm bài học</button>
//                                         </div>
//                                       </div>
//                                     </div>
//                                   </div>
//                               </div>`);
//   LessonsDataCourse.push({
//     id: $('.accordion-item').length + 1,
//     lessons_name: 'ssss',
//     chapter: []
//   });
// }

// async function saveCreateCourse() {
//   let course = {
//     course_name: $('#name-course-create').val(),
//     teacher_id: $('#select-teacher-create-course').val(),
//     category_id: $('#course-category-create').val(),
//     course_price: $('#course-price-create').val(),
//     course_price_sale: $('#course-price-sale-create').val(),
//     course_description: editorDescriptionCourse.root.innerHTML,
//     banner: fileBannerCourse
//   };

//   setItemLocalStorage('course', JSON.stringify(course));

//   let METHOD = 'POST',
//     URL = '/course/create-course',
//     PARAM = '',
//     DATA = {
//       course: course
//       // lessons : LessonsDataCourse
//     };
//   let res = await axiosTemplateFile(METHOD, URL, PARAM, DATA);
//   console.log(res);
// }

// function closeModalCreateCourse() {
//   $('#modal-create-course').modal('hide');
// }

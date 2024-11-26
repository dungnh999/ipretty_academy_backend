let idChapterLessonCourse, idLesson = 0, editorDescriptionChapterCourse;

async function openModalCreateChapterLesson(r) {
  $('#modal-create-chapter-lesson').modal('show');
  // hideShowSaveCancel(true);
  idChapterLessonCourse = r.data('id');
  getDataChapterforCourse();
}

// Hàm để lấy ID video từ URL YouTube
function getYouTubeVideoId(url) {
  const regex =
    /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
  const matches = url.match(regex);
  return matches ? matches[1] : null;
}

async function getDataChapterforCourse() {
  let method = 'GET',
    url = '/course/get-data-chapter-course',
    param = {
      course_id: idChapterLessonCourse
    },
    data = null;
  let res = await axiosTemplate(method, url, param, data);
  if (res.status === 200) {
    let dataChapter = res.data.data.course_resources.chapters;
    let elListChapter = (dataChapter.length > 0) ? ' ' : '<div class="text-center text-white">Chưa có chương trình học</div>' ;
    dataChapter.map(function (item, index) {
      elListChapter += ` <div class="accordion-item ${index === 0 ? 'active' : ''} mb-2">
                            <div class="accordion-header p-3 border-bottom" id="chapter-${item['chapter_id']}" data-id="${item['chapter_id']}">
                              <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2 align-items-center">
                                  <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#chapter-lesson-${
                                    item['chapter_id']
                                  }" aria-expanded="false" aria-controls="chapterOne">${index + 1}</button>
                                  <span class="d-flex flex-column">
                                    <span class="h5 mb-0">${item['chapter_name']}</span>
                                    <span class="text-body fw-normal"> ${
                                      item['lessons'].length
                                    } Bài học | 4.4 min</span>
                                  </span>
                                  <button type="button" class="btn btn-sm btn-icon btn-warning" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Chỉnh sửa chương trình</span>"   onclick="openModalUpdateChapter($(this))"><i class="bx bx-pencil"></i></button>
                                </div>
                                <div class="toolbox">
                                    <button type="button" onclick="openModalCreateLesson($(this))" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Thêm bài học</span>"><i class="bx bx-plus"></i></button>
                                </div>
                              </div>
                            </div>
                            <div id="chapter-lesson-${item['chapter_id']}" class="accordion-collapse collapse ${
        index === 0 ? 'show' : ''
      }" data-bs-parent="#courseContent" style="">
                              <div class="accordion-body py-4">
                                ${
                                  item['lessons'].length > 0
                                    ? item['lessons'].map(function (lessons) {
                                        return `<div class="d-flex align-items-center justify-content-between mb-3">
                                                  <div class="d-flex align-items-center gap-3">
                                                      <i class="bx bx-laptop fs-xlarge"></i>
                                                      <label for="defaultCheck1" class="form-check-label" onclick="getDetailLessonCourse($(this))" data-idchapter="${item['chapter_id']}" data-id="${lessons['lesson_id']}">
                                                        <span class="mb-0 h6">${lessons['lesson_name']}</span>
                                                        <small class="text-body d-block">10 câu hỏi</small>
                                                      </label>
                                                      <button type="button" class="btn btn-sm btn-icon btn-info" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Chỉnh sửa bài học</span>" data-id="${lessons['lesson_id']}" onclick="openModalUpdateLesson($(this))"><i class="bx bx-pencil"></i></button>
                                                  </div>
                                                  <div>
                                                    ${ lessons['is_demo'] ? '<span class="badge bg-label-info">Học thử</span>' : ''}
                                                  </div>
                                                </div>`
                                      }).join('')
                                    : '<div>Chưa có bài học</div>'
                                }
                              </div>
                            </div>
                          </div>`
    });
    $('#courseContent').html(elListChapter);
    $('[data-bs-toggle="tooltip"]').tooltip();
  }
}


function displayFormLessonData(){
  $('.item-info-chapter').addClass('d-none');
  $('.item-info-chapter-lesson').removeClass('d-none');
}

function addFormChapterCourse(){
  // $('.item-info-chapter').removeClass('d-none');
  // $('.item-info-chapter-lesson').addClass('d-none');
  // hideShowSaveCancel(false);
  openModalCreateChapter();
  closeModalCreateChapterLesson();
}

function hideShowSaveCancel(action){ 
   if(!action){
      $('.form-action-create-chapter').removeClass('d-none')
   }else{
      $('.form-action-create-chapter').addClass('d-none')
   }
}

function saveCreateChapterLessonCourse(){
  if(!$('.item-info-chapter').hasClass('d-none')){
    saveCreateChapter();
  }else {
    if(idLesson != 0 ){
      saveUpdateChapterLesson();
    }else {
      saveCreateChapterLesson();
    }
  }
  hideShowSaveCancel(true)
}

/**
 * Tạo bài giảng
 * */
async function saveCreateChapterLesson() {  
  let videoId = getYouTubeVideoId($('#link-yotube-course').val());
  let METHOD = 'POST',
    URL = '/course/create-chapter-course',
    PARAM = '',
    DATA = {
      lessons_name : $('#name-lesson-course').val(),
      lessons_description : editorDescriptionChapterCourse.root.innerHTML,
      main_attachment: videoId,
      chapter_id : idChapter
    };
  let res = await axiosTemplateFile(METHOD, URL, PARAM, DATA);
  if (res.status === 200) {
    getDataChapterforCourse();
    cleanFormCreateUpdateChapterLesson();
    successSwalNotify("Thêm mới thành công");
  }
}

function cancelCreateFormCourse(){
  cleanFormCreateUpdateChapterLesson();
  hideShowSaveCancel(true);
}

function cleanFormCreateUpdateChapterLesson(action){
  $('.item-info-chapter').addClass('d-none');
  $('.item-info-chapter').find('input').val('');
  $('.item-info-chapter-lesson').addClass('d-none');
  $('.item-info-chapter-lesson').find('input').val('');
  $('.accordion-item').find('button').removeClass('disabled');
}

function closeModalCreateChapterLesson() {
  $('#modal-create-chapter-lesson').modal('hide');
}


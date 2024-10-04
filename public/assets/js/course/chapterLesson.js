let idCourse, idChapter, idLesson = 0, player, editorDescriptionChapterCourse;

async function openModalCreateChapterLesson(r) {
  $('#modal-create-chapter-lesson').modal('show');
  idCourse = r.data('id');
  getDataChapterforCourse();
  player = new Plyr('#player', {
    controls: ['play', 'progress', 'volume', 'fullscreen'],
    settings: ['captions', 'quality', 'speed'],
    youtube: {
      noCookie: true // Sử dụng "https://www.youtube-nocookie.com"
    }
  });

  $('.link-youtube').on('change', function () {
    let url = $(this).val();
    let videoId = getYouTubeVideoId(url);
    if (videoId) {
      $('#loader-custom').removeClass('hidden-loader');
      // Cập nhật video với Plyr
      player.source = {
        type: 'video',
        sources: [
          {
            src: videoId,
            provider: 'youtube'
          }
        ]
      };
    } else {
      alert('Vui lòng nhập một URL YouTube hợp lệ.');
    }

    player.on('canplay', function () {
      console.log('Video đang đợi dữ liệu.');
      $('#loader-custom').removeClass('hidden-loader');
    });

    player.on('canplaythrough', function () {
      // Ẩn loader khi video đã sẵn sàng
      console.log('Video săẵn sàng dữ liệu.');
      $('#loader-custom').addClass('hidden-loader');
    });
  });

  editorDescriptionChapterCourse = await editorTemplate('#editor-lesson-chapter', '#toolbar-lesson-chapter');
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
      course_id: idCourse
    },
    data = null;
  let res = await axiosTemplate(method, url, param, data);
  if (res.status === 200) {
    let dataChapter = res.data.data.course_resources.chapters;
    let elListChapter = '';
    dataChapter.map(function (item, index) {
      elListChapter += ` <div class="accordion-item ${index === 0 ? 'active' : ''} mb-2">
                            <div class="accordion-header p-3 border-bottom" id="chapter-${item['chapter_id']}" data-id="${item['chapter_id']}">
                              <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2">
                                  <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#chapter-lesson-${
                                    item['chapter_id']
                                  }" aria-expanded="false" aria-controls="chapterOne"></button>
                                  <span class="d-flex flex-column">
                                    <span class="h5 mb-0">${item['chapter_name']}</span>
                                    <span class="text-body fw-normal"> ${
                                      item['lessons'].length
                                    } Bài học | 4.4 min</span>
                                  </span>
                                </div>
                                <div class="toolbox">
                                    <button type="button" onclick="addFormChapterLessonCourse($(this))" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Thêm bài học</span>"><i class="bx bx-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-icon btn-warning" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Chỉnh sửa chương trình</span>"><i class="bx bx-pencil"></i></button>
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
                                        return `<div class="d-flex align-items-center gap-1 mb-3">
                                                      <i class="bx bx-laptop fs-xlarge"></i>
                                                      <label for="defaultCheck1" class="form-check-label ms-4" onclick="getDetailLessonCourse($(this))" data-idchapter="${item['chapter_id']}" data-id="${lessons['lesson_id']}">
                                                        <span class="mb-0 h6">${lessons['lesson_name']}</span>
                                                        <small class="text-body d-block">10 câu hỏi</small>
                                                      </label>
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

async function getDetailLessonCourse(r) {
  idLesson = r.data('id')
  idChapter = r.data('idchapter')
  let method = 'GET',
    url = '/course/get-detail-lesson',
    param = {
        lesson_id: idLesson
    },
    data = null ;
  let res = await axiosTemplate(method , url , param , data)
  if (res.status === 200) {
    displayFormLessonData();
    $('.title-form-update-creat-chapter').text('Cập nhật bài học')
    $('#name-lesson-course').val(res.data.data.lesson_name);
    $('#link-yotube-course').val( "https://www.youtube.com/watch?v=" + res.data.data.main_attachment);
    player.source = {
      type: 'video',
      sources: [
        {
          src: res.data.data.main_attachment,
          provider: 'youtube'
        }
      ]
    };
  }
}


function displayFormLessonData(){
  $('.item-info-chapter').addClass('d-none');
  $('.item-info-chapter-lesson').removeClass('d-none');
}

function addFormChapterCourse(){
  $('.item-info-chapter').removeClass('d-none');
  $('.item-info-chapter-lesson').addClass('d-none');
}

function addFormChapterLessonCourse(r){
  $('.item-info-chapter').addClass('d-none');
  $('.item-info-chapter-lesson').removeClass('d-none');
  $('.accordion-item').find('button').addClass('disabled');
  r.parents('.accordion-item').find('button').removeClass('disabled');
  idChapter = r.parents('.accordion-item').find('.accordion-header').data('id');
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
}

async function saveUpdateChapterLesson(){
  let videoId = getYouTubeVideoId($('#link-yotube-course').val());
  let METHOD = 'POST',
    URL = '/course/update-lesson-course',
    PARAM = '',
    DATA = {
      lesson_name : $('#name-lesson-course').val(),
      lesson_description : editorDescriptionChapterCourse.root.innerHTML,
      main_attachment: videoId,
      chapter_id : idChapter,
      lesson_id: idLesson
    };
  let res = await axiosTemplateFile(METHOD, URL, PARAM, DATA);
  if (res.status === 200) {
    getDataChapterforCourse();
    cleanFormCreateUpdateChapterLesson();
    successSwalNotify("Cập nhật thành công");
  }
}

/**
 * Tạo bài giảng
 * */
async function saveCreateChapterLesson() {
  let videoId = getYouTubeVideoId($('#link-yotube-course').val());
  let METHOD = 'POST',
    URL = '/course/create-course',
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

async function saveCreateChapter() {
  let method = 'POST',
    url = '/course/create-chapter-course',
    param = {
      chapter_name: $('#name-create-chapter-course').val(),
      course_id: idCourse
    },
    data = null;
  let res = await axiosTemplate(method, url, param, data);
  if (res.status === 200) {
    getDataChapterforCourse();
    cleanFormCreateUpdateChapterLesson();
    successSwalNotify("Thêm mới thành công");
  }
}

function cancelCreateFormCourse(){
  cleanFormCreateUpdateChapterLesson();
}

function cleanFormCreateUpdateChapterLesson(){
  $('.item-info-chapter').addClass('d-none');
  $('.item-info-chapter').find('input').val('');
  $('.item-info-chapter-lesson').addClass('d-none');
  $('.item-info-chapter-lesson').find('input').val('');
  $('.accordion-item').find('button').removeClass('disabled');
}

function closeModalCreateChapterLesson() {
  $('#modal-create-chapter-lesson').modal('hide');
}


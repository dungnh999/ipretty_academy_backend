let idCreateChapter = null, player;
async function openModalCreateLesson(r) {
    $('#modal-create-lesson').modal('show');
    idCreateChapter = r.parents('.accordion-item').find('.accordion-header').data('id');
    player = new Plyr('#player-create', {
        controls: ['play', 'progress', 'volume', 'fullscreen'],
        settings: ['captions', 'quality', 'speed'],
        youtube: {
          noCookie: true // Sử dụng "https://www.youtube-nocookie.com"
        }
      });
    
      $('.link-youtube').on('change', function () {
        let url = $(this).val();
    
        let videoId = $(this).val();
        // if (videoId) {
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
        // } else {
        //   alert('Vui lòng nhập một URL YouTube hợp lệ.');
        // }
    
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


/**
 * Tạo bài giảng
 * */
async function saveCreateLesson() {  
    let videoId = $('#link-yotube-course').val();
    let type =  $('input[name="type-upload-video"]:checked').val();
    let METHOD = 'POST',
        URL = '/course/create-lesson-course',
        PARAM = '',
        DATA = {
            lesson_name : $('#name-lesson-course').val(),
            lesson_description : editorDescriptionChapterCourse.root.innerHTML,
            is_demo : Number($('#demo-create-lesson').is(':checked')),
            main_attachment: videoId,
            chapter_id : idCreateChapter,
            type_update : type,
        };
    let res = await axiosTemplateFile(METHOD, URL, PARAM, DATA);
    if (res.status === 200) {
      getDataChapterforCourse();
      closeModalCreateLesson();
      successSwalNotify("Thêm mới thành công");
    }
  }


async function closeModalCreateLesson() {
    $('#modal-create-lesson').modal('hide');
    openModalCreateChapterLesson();
}
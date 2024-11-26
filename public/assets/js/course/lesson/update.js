let player_update = null, editorDescriptionChapterCourseUpdate;
async function openModalUpdateLesson(r) {
    $('#modal-update-lesson').modal('show');
    // idChapter = r.parents('.accordion-item').find('.accordion-header').data('id');

    player_update = new Plyr('#player-update', {
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
          player_update.source = {
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
    
        player_update.on('canplay', function () {
          console.log('Video đang đợi dữ liệu.');
          $('#loader-custom').removeClass('hidden-loader');
        });
    
        player_update.on('canplaythrough', function () {
          // Ẩn loader khi video đã sẵn sàng
          console.log('Video săẵn sàng dữ liệu.');
          $('#loader-custom').addClass('hidden-loader');
        });
    });

    editorDescriptionChapterCourseUpdate = await editorTemplate('#editor-update-lesson-chapter', '#toolbar-update-lesson-chapter');
    getDetailLessonCourse(r);
}


async function saveUpdateChapterLesson(){
    let videoId = getYouTubeVideoId($('#link-yotube-course').val());
    let METHOD = 'POST',
      URL = '/course/update-lesson-course',
      PARAM = '',
      DATA = {
        lesson_name : $('#name-update-lesson-course').val(),
        lesson_description : editorDescriptionChapterCourseUpdate.root.innerHTML,
        main_attachment: $('#link-update-yotube-course').val(),
        chapter_id : idChapter,
        lesson_id: idLesson,
        is_demo : Number($('#demo-update-lesson').is(':checked'))
      };
    let res = await axiosTemplateFile(METHOD, URL, PARAM, DATA);
    if (res.status === 200) {
      getDataChapterforCourse();
      closeModalUpdateLesson();
      successSwalNotify("Cập nhật thành công");
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
    console.log(Boolean(res.data.data.is_demo));
    
    if (res.status === 200) {
      $('.title-form-update-creat-chapter').text('Cập nhật bài học')
      $('#name-update-lesson-course').val(res.data.data.lesson_name);
      $('#demo-update-lesson').prop('checked', Boolean(res.data.data.is_demo))
      $('#link-update-yotube-course').val(res.data.data.main_attachment);
      player_update.source = {
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
  


async function closeModalUpdateLesson() {
    player_update.destroy();
    $('#modal-update-lesson').modal('hide');
    openModalCreateChapterLesson();
}
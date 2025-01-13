let player_update = null, editorDescriptionChapterCourseUpdate, fileUpdateMaterial, fileUpdateMaterialHtml;
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


    $('#upload-file-update-lesson-course').on('change', function () {
        let files = this.files;
        fileUpdateMaterial = this.files;
        $.each(files, function (index, file) {
            const fileName = file.name; // Tên file
            fileUpdateMaterialHtml += `<div class="item-file-lesson d-flex gap-2 bg-white p-3 rounded">
                                    <div class="item-file-lesson__icon ">
                                        <i class='bx bxs-file-doc'></i>
                                    </div>
                                    <div class="item-file-lesson__name flex-grow-1 text-truncate text-primaryColor">${fileName}</div>
                                    <div class="item-file-lesson__tool">
                                        <a href="javascript:void(0)">
                                            <i class='bx bxs-download' ></i>
                                        </a>
                                    </div>
                                </div>`
        });
        $('#group-file-update-lesson-course').html(fileUpdateMaterialHtml);
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
        is_demo : Number($('#demo-update-lesson').is(':checked')),
        lesson_material_file : fileUpdateMaterial
      };
    let res = await axiosTemplateFile(METHOD, URL, PARAM, DATA);
    if (res.status === 200) {
      getDataChapterforCourse();
      closeModalUpdateLesson();
      successSwalNotify("Cập nhật thành công");
    }
  }
  


async function getDetailLessonCourse(r) {
    fileUpdateMaterialHtml = '';
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
      $('.title-form-update-creat-chapter').text('Cập nhật bài học')
      $('#name-update-lesson-course').val(res.data.data.lesson_name);
      $('#demo-update-lesson').prop('checked', Boolean(res.data.data.is_demo))
      if(res.data.data.lesson_material){
          let fileMaterial  = res.data.data.lesson_material;
          for (let i = 0  ; i < fileMaterial.length ; i++ ){
              fileUpdateMaterialHtml += `<div class="item-file-lesson d-flex gap-2 bg-white p-3 rounded">
                                        <div class="item-file-lesson__icon ">
                                            <i class='bx bxs-file-doc'></i>
                                        </div>
                                        <div class="item-file-lesson__name flex-grow-1 text-truncate text-primaryColor">${fileMaterial[i].name}</div>
                                        <div class="item-file-lesson__tool">
                                            <a href="${fileMaterial[i].url}" target="_blank">
                                                <i class='bx bxs-download' ></i>
                                            </a>
                                        </div>
                                    </div>`
          }
          $('#group-file-update-lesson-course').html(fileUpdateMaterialHtml);
      }
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
    $('#name-update-lesson-course').val('');
    $('#link-update-yotube-course').val('');
    $('#group-file-update-lesson-course').html('<h6>Không có tài liệu</h6>');

    openModalCreateChapterLesson();
}
let player_update = null, editorDescriptionChapterCourseUpdate, listFileUpdateMaterial = [],
    listFileUploadUpdateMaterial = [], fileUpdateMaterial, fileUpdateMaterialHtml;

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

    $('.link-update-youtube').on('change', function () {
        let videoId = $(this).val();
        // let videoId = getYouTubeVideoId(url);
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

    $(document).on('click', '.remove-file-update', function () {
        const status = $(this).data("status");
        const index = $(this).data("index");

        if (status === "old") {
            listFileUpdateMaterial.splice(index, 1); // Xóa file đã lưu
        } else if (status === "new") {
            listFileUploadUpdateMaterial.splice(index, 1); // Xóa file mới
        }
        displayFiles(); // Cập nhật giao diện
    })

    $('#upload-file-update-lesson-course').on('change', function () {
        let files = Array.from(this.files); // Chuyển FileList thành mảng
        listFileUploadUpdateMaterial = listFileUploadUpdateMaterial.concat(files); // Thêm các tệp mới vào mảng
        displayFiles();
        $(this).val(''); // Reset input để có thể chọn lại cùng tệp
    });

    editorDescriptionChapterCourseUpdate = await editorTemplate('#editor-update-lesson-chapter', '#toolbar-update-lesson-chapter');
    getDetailLessonCourse(r);
}


async function saveUpdateChapterLesson() {
    let METHOD = 'POST',
        URL = '/course/update-lesson-course',
        PARAM = '',
        DATA = {
            lesson_name: $('#name-update-lesson-course').val(),
            lesson_description: editorDescriptionChapterCourseUpdate.root.innerHTML,
            main_attachment: $('#link-update-yotube-course').val(),
            chapter_id: idChapter,
            lesson_id: idLesson,
            is_demo: Number($('#demo-update-lesson').is(':checked')),
            lesson_material_file_old: listFileUpdateMaterial,
            lesson_material_file: listFileUploadUpdateMaterial
        };
    let res = await axiosTemplateFile(METHOD, URL, PARAM, DATA);
    if (res.status === 200) {
        getDataChapterforCourse();
        closeModalUpdateLesson();
        successSwalNotify("Cập nhật thành công");
    }
}


// Hiển thị danh sách file
function displayFiles() {
    const container = $("#group-file-update-lesson-course");
    container.empty();

    // Hiển thị file đã lưu
    listFileUpdateMaterial.forEach((file, index) => {
        container.append(`
           <div class="item-file-lesson d-flex gap-2 bg-white p-3 rounded">
                <div class="item-file-lesson__icon ">
                    <i class='bx bxs-file-doc'></i>
                </div>
                <div class="item-file-lesson__name flex-grow-1 text-truncate text-primaryColor">${file.name}</div>
                <div class="item-file-lesson__tool">
                    <a href="javascript:void(0)" class="remove-file-update" data-status="old" data-index="${index}">
                      <i class='bx bx-trash'></i>
                    </a>
                </div>
            </div>
        `);
    });

    // Hiển thị file mới
    listFileUploadUpdateMaterial.forEach((file, index) => {
        container.append(`
            <div class="item-file-lesson d-flex gap-2 bg-white p-3 rounded">
                <div class="item-file-lesson__icon ">
                    <i class='bx bxs-file-doc'></i>
                </div>
                <div class="item-file-lesson__name flex-grow-1 text-truncate text-primaryColor">${file.name}</div>
                <div class="item-file-lesson__tool">
                    <a href="javascript:void(0)" class="remove-file-update" data-status="new" data-index="${index}">
                      <i class='bx bx-trash'></i>
                    </a>
                </div>
            </div>
        `);
    });
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
        data = null;
    let res = await axiosTemplate(method, url, param, data)
    if (res.status === 200) {
        $('.title-form-update-creat-chapter').text('Cập nhật bài học')
        $('#name-update-lesson-course').val(res.data.data.lesson_name);
        $('#demo-update-lesson').prop('checked', Boolean(res.data.data.is_demo))
        $('#link-update-yotube-course').val(res.data.data.main_attachment)
        if (res.data.data.lesson_material) {
            listFileUpdateMaterial = res.data.data.lesson_material;
            displayFiles();
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
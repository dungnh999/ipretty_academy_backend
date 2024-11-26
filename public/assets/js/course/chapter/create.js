async function openModalCreateChapter() {
    $('#modal-create-chapter').modal('show');
    
}


async function saveCreateChapter() {
    let method = 'POST',
      url = '/course/create-chapter-course',
      param = {
        chapter_name: $('#name-create-chapter-course').val(),
        course_id: idChapterLessonCourse
      },
      data = null;
    let res = await axiosTemplate(method, url, param, data);
    if (res.status === 200) {
      successSwalNotify("Thêm mới thành công");
      getDataChapterforCourse();
      closeModalCreatechapter();
    }
  }


async function closeModalCreatechapter() {
    $('#modal-create-chapter').modal('hide');
    openModalCreateChapterLesson();
}
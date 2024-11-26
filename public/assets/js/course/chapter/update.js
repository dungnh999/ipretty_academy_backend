let idChapterUpdate;
async function openModalUpdateChapter(r) {
    $('#modal-update-chapter').modal('show');
    closeModalCreateChapterLesson();    
    $('#name-update-chapter-course').val(r.parents('.d-flex').find('.flex-column').find('.h5').text())
    idChapterUpdate = r.parents('.accordion-heade').data('id');
}


async function saveUpdateChapter() {
    let method = 'POST',
      url = '/course/update-chapter-course',
      param = {
        chapter_name: $('#name-update-chapter-course').val(),
        chapter_id: idChapterUpdate
      },
      data = null;
    let res = await axiosTemplate(method, url, param, data);
    if (res.status === 200) {
      successSwalNotify("Thêm mới thành công");
      getDataChapterforCourse();
      closeModalUpdatechapter();
    }
  }


async function closeModalUpdatechapter() {
    $('#modal-update-chapter').modal('hide');
    openModalCreateChapterLesson();
}
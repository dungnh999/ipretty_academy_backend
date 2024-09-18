// let dataChaperCreate = [],dataDerChapter, fileChapter = [] , videoChapter, thisOpenCreateChapter, TotalTimeVideo;
// async function openCreateChapter(r){
//   $('#modal-create-chapter').modal('show');
//   thisOpenCreateChapter = r;
//   // EDITOR
//   dataDerChapter = await editorTemplate('#editor-creatr-chapter', '#toolbar-create-chapter');

//   $('#file-create-chapter').on('change', function (){
//     fileChapter.push(this.files);
//     for (let i = 0 ; i < this.files.length ; i++){
//       $('#list-file-create-chapter').append(`<a href="javascript:void(0);" class="list-group-item-primary p-2 mb-2 rounded list-group-item-action d-flex justify-content-between align-items-center">
//                                                 <div class="title-file">
//                                                   <i class='bx bxs-file-doc' ></i>
//                                                   ${this.files[i].name}
//                                                 </div>
//                                                 <i class='bx bx-trash' ></i>
//                                              </a>`)
//     }
//     this.file = []
//   })
//   $('#input-video-create-chapter').on('change', function (){
//     console.log(this.files[0]);
//     $('#image-preview-create-chapter').addClass('d-none');
//     let url = URL.createObjectURL(this.files[0]);
//     $('#video-create-chapter').removeClass('d-none');
//     $('#video-create-chapter').attr('src', url);
//     videoChapter = this.files[0];
//     let totalDuration = 0;
//     let file = this.files[0];
//     if (file.type.match('video.*')) {
//       let video = $('#video-create-chapter');
//       video.preload = 'metadata';
//       $(video).on('loadedmetadata', function() {
//         totalDuration += this.duration;
//         updateTotalDuration(totalDuration);
//       });

//       video.src = URL.createObjectURL(file);
//     }
//   })
// }
// function updateTotalDuration(totalDuration) {
//   let totalHours = Math.floor(totalDuration / 3600);
//   let totalMinutes = Math.floor((totalDuration % 3600) / 60);
//   let totalSeconds = Math.floor(totalDuration % 60);

//   let durationString = pad(totalHours, 2) + ':' + pad(totalMinutes, 2) + ':' + pad(totalSeconds, 2);
//   TotalTimeVideo = durationString;
// }
// function pad(num, size) {
//   let s = num + "";
//   while (s.length < size) s = "0" + s;
//   return s;
// }
// function saveCreateChapter(){
//     let lessons = {
//       lesson_name : '',
//       lesson_description : '',
//       video_lesson : [],
//       file_document : [],
//     };
//     lessons.lesson_name = $('#name-create-chapter-ipretty').val();
//     lessons.lesson_description = dataDerChapter.getText();
//     lessons.video_lesson = videoChapter;
//     lessons.file_document = fileChapter;
//     thisOpenCreateChapter.parents('.accordion-body').find('.item-chapter-group').append(`<div class="item-chapter form-check d-flex align-items-center mb-3 w-100">
//                                               <input class="form-check-input" type="checkbox" id="defaultCheck1" checked="">
//                                               <label for="defaultCheck1" class="form-check-label ms-3 w-100">
//                                                    <div class="d-flex justify-content-between align-items-center">
//                                                         <div class="title-chapter">
//                                                           <span class="mb-0 h6 text-width-overflow">${thisOpenCreateChapter.parent().find('.item-chapter').length + 1}. ${$('#name-create-chapter-ipretty').val()}</span>
//                                                           <span class="text-muted d-block">${TotalTimeVideo}</span>
//                                                         </div>
//                                                          <div class="action-chapter">
//                                                             <button type="button" class="btn btn-sm rounded-pill  btn-icon btn-outline-primary">
//                                                               <span class="tf-icons bx bx-pencil"></span>
//                                                             </button>
//                                                             <button type="button" class="btn btn-sm rounded-pill btn-icon btn-outline-primary" onclick="removeChapterCourse($(this))">
//                                                               <span class="tf-icons bx bx-x"></span>
//                                                             </button>
//                                                          </div>
//                                                     </div>
//                                               </label>
//                                             </div>`)
//     dataChaperCreate.push(lessons);
//     let item = thisOpenCreateChapter.parents('.accordion-item').find('.accordion-button').data('id');
//     LessonsDataCourse[item]['chapter'].push(lessons);
//     thisOpenCreateChapter.parents('.accordion-item').find('[data-bs-target="#'+ thisOpenCreateChapter.parents('.accordion-collapse').attr('id') +'"]').find('.fw-normal').text(thisOpenCreateChapter.parent().find('.item-chapter').length + 1 + ' bài học')
//     closeCreateChapter();
// }
// async function removeChapterCourse(r){
//   let confirm = await confirmSwalNotify('Bạn có muốn xoá bài học?')
//   if (confirm){
//       r.parents('.item-chapter').remove();
//   }
// }
// function closeCreateChapter(){
//   $('#modal-create-chapter').modal('hide');
//   $('#name-create-chapter-ipretty').val('');
//   $('#video-create-chapter').addClass('d-none');
//   $('#image-preview-create-chapter').removeClass('d-none');
//   $('#video-create-chapter').attr('src', '');
//   $('#list-file-create-chapter').empty();
//   dataDerChapter.setText("");
// }
async function openCreateChapter(r) {
  $('#modal-create-chapter').modal('show');
}

async function closeCreateChapter() {
  $('#modal-create-chapter').modal('hide');
  $('#name-create-chapter-ipretty').val('');
}

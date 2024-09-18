$(function (){
    $('#logo-setting-ipretty').on('change', async function (){
      await openModalCropperImageTemplate(this.files[0], previewImgae);
      this.value = ''; // This clears the file input
    })
})


async function previewImgae(url, file) {
  $('#logo-preview-setting-ipretty').attr('src', url);
  let formData = new FormData();
  formData.append('image', file);
  let METHOD = 'POST',
    URL = '/setting/update-logo',
    PARAM = '',
    DATA = formData;
  let res = await axiosTemplate(METHOD, URL, PARAM, DATA)
}

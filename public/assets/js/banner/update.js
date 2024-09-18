let thisIdUpadteBanner, fileUpdateUploadBanner;
function openModalUpdateBanner(r){
  $('#modal-update-banner').modal('show');
  $('#image-update-banner-ipretty').unbind('change').on('change', function (){
    let url = URL.createObjectURL(this.files[0]);
    fileUpdateUploadBanner = this.files[0];
    $('#image-preview-update-banner').attr('src', url)
  })
  thisIdUpadteBanner = r;
  getDetail();
}

async function getDetail(){
  let METHOD = 'get',
    URL = '/banner/detail',
    PARAM = {
      id : thisIdUpadteBanner.data('id')
    },
    DATA = null;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  if(res.status === 200){
      $('#name-update-banner-ipretty').val(res.data.data.title)
      $('#image-preview-update-banner').attr( 'src' ,res.data.data.bannerUrl)
  }else {

  }
}

async function saveUpdateBanner(){
  let formData = new FormData();
  formData.append('title' , $('#name-update-banner-ipretty').val());
  formData.append('id' , thisIdUpadteBanner.data('id'));
  formData.append('bannerUrl',  fileUpdateUploadBanner);
  let METHOD = 'post',
    URL = '/banner/update',
    PARAM = null,
    DATA = formData;
  let res = await axiosTemplate(METHOD, URL , PARAM, DATA )
  if(res.status === 200){
    successSwalNotify("Chỉnh sửa thành công");
    loadData();
    closeModalUpdateBanner();
  }else {
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

function closeModalUpdateBanner(){
  $('#modal-update-banner').modal('hide');
}

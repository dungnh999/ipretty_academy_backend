let fileName;
function openModalCreateBanner(){
  $('#modal-create-banner').modal('show');
  $('#image-create-banner-ipretty').unbind('change').on('change', async function (){
      // await openModalCropperImageTemplate(this.files[0], previewImgae);
    // let res = await uploadFileTemplate(this.files[0]);
    fileName = this.files[0];
    let url = URL.createObjectURL(this.files[0]);
    $('#image-preview-create-banner').attr('src', url)
  })
}

async function saveCreateBanner(){
  console.log(fileName);
  let formData = new FormData();
  formData.append('bannerUrl', fileName);
  formData.append('is_banner', 1);
  formData.append('title', $('#name-create-banner-ipretty').val());
  let METHOD = 'POST',
    URL = '/banner/create',
    PARAM = '',
    DATA = formData;
  let res = await axiosTemplateFile(METHOD, URL , PARAM, DATA )
  if(res.status == 200){
    successSwalNotify("Thêm mới thành công")
    loadData();
    closeModalCreateBanner();
  }else{
    errorSwalNotify('Lỗi rồi '+ res);
  }
}

function closeModalCreateBanner(){
  $('#modal-create-banner').modal('hide');
  $('#name-create-banner-ipretty').val('');
  $('#image-preview-create-banner').attr('src', 'https://dashboard.ipretty.edu.vn/ba29a12120c9d59c26de9b91205a3e7e.png');
}

let cropper, callbackTemplate;
function openModalCropperImageTemplate(file, callback){
  console.log(file, callback);
  $('#uploadedAvatar').attr('src', URL.createObjectURL(file));
  let image = document.getElementById('uploadedAvatar');
  let slider = document.getElementById('slider-basic');
  $('#modal-cropper-image-template').modal('show');
  if (slider.noUiSlider) {
    slider.noUiSlider.destroy();
  }


  if (cropper) {
    cropper.destroy();
  }

  cropper = new Cropper(image, {
    aspectRatio: 2714 / 1200,
    viewMode : 1,
    responsive: true,
    dragMode: 'move',
    center: true,
    minContainerWidth : 720,
    minContainerHeight : 400,
    cropBoxResizable : false,
  });
  callbackTemplate = callback;
  noUiSlider.create(slider, {
    start: [50],
    connect: [!0, !1],
    range: {
      min: 0,
      max: 100
    }
  })
  slider.noUiSlider.on('slide', function(values, handle) {
    cropper.zoomTo(values[handle])
  });}

function saveCropper(){
  let url = cropper.getCroppedCanvas({
    width: 100,
    height: 100,
  });

  url.toBlob(async function (blob) {
    if (typeof callbackTemplate === 'function') {
      let res = await uploadFileTemplate(blob, 'file_cropper.png');
      callbackTemplate(res);
    }
  }, 'image/jpeg');
  $('#modal-cropper-image-template').modal('hide');
}

function saveModalCropperImageTemplate(){
  $('#modal-cropper-image-template').modal('hide')
}

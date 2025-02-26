let editorContentPostCreate, fileAvatarPostCreate;

async function openModalCreatePosts(){
    $('#modal-create-post').modal('show');
    getDataPostCategoryCreate();

    $('#input-avatar-post-create').on('change', function () {
        let url = URL.createObjectURL(this.files[0]);
        fileAvatarPostCreate = this.files[0];
        $('#preview-avatar-post-create').attr('src', url);
    });
}



async function saveCreatePost(){
    const dataRequest = new FormData()

    dataRequest.append('bannerUrl', fileAvatarPostCreate);
    dataRequest.append('title', $('#title-create-post-ipretty').val());
    dataRequest.append('content', editorContentPostCreate.root.innerHTML);
    dataRequest.append('external_url', $('#url-create-post-ipretty').val());
    dataRequest.append('category_id', $('#category-create-post-ipretty').val());
    dataRequest.append('slug', $('#url-create-post-ipretty').val())
    dataRequest.append('is_banner', 1)


    let method = 'POST',
        url = '/posts/create',
        param = null,
        data = dataRequest;
    let res = await axiosTemplateFile(method , url , param , data)
    if(res.data.status == 200) {
        successSwalNotify("Thêm mới thành công")
        loadData();
        closeModalCreatePost();
    }else {
        errorSwalNotify('Lỗi rồi '+ res);
    }
}

async function getDataPostCategoryCreate(){
    let method = 'GET',
        url = '/posts/get-posts-category',
        param = {
            'status' : 1,
        },
        data = {};
    let res = await axiosTemplate(method , url , param , data)
    $('#category-create-post-ipretty').html(res.data[0]);
}


function closeModalCreatePost(){
    $('#name-create-post-ipretty').val('')
    $('#link-create-post-ipretty').val('')
    $('#modal-create-post').modal('hide');

}

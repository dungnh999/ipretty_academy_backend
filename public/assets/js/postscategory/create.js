function openModalCreatePostsCategory(){
    $('#modal-create-post-category').modal('show');
}



async function saveCreatePostCategory(){
    let method = 'POST',
        url = '/posts-category/create',
        param = null,
        data = {
            category_name : $('#name-create-post-category-ipretty').val(),
        };
    let res = await axiosTemplate(method , url , param , data)
    if(res.data.status == 200) {
        successSwalNotify("Thêm mới thành công")
        loadData();
        closeModalCreatePostCategory();
    }else {
        errorSwalNotify('Lỗi rồi '+ res);
    }
}


function closeModalCreatePostCategory(){
    $('#name-create-post-category-ipretty').val('')
    $('#link-create-post-category-ipretty').val('')
    $('#modal-create-post-category').modal('hide');

}

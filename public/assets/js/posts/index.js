let DataTablePost;

$(async function (){
    loadData();

    $('#category-create-post-ipretty').select2({
        dropdownParent: '#modal-create-post',
    });

    $('#type-create-post-ipretty').select2({
        dropdownParent: '#modal-create-post',
    });

    editorContentPostCreate = await editorTemplate('#editor-content-posts-create', '#modal-create-post');
})

async function loadData(){
    let method = 'GET',
        url = '/posts/get-data-posts',
        param = null,
        data = null ;
    let res = await axiosTemplate(method , url , param , data)
    if(DataTablePost){
        updateDataTable($('#table-post-ipretty'), res.data[0].original.data);
    }else {
        drawDataTablePosts(res.data[0].original.data)
    }
}



async function drawDataTablePosts(data){
    let id = $('#table-post-ipretty'),
        column = [
            { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
            { data: 'avatar', className: 'text-center', width: '10%' },
            { data: 'title', className: 'text-right' },
            { data: 'post_category.category_name', className: 'text-center' },
            { data: 'created_at', className: 'text-center'},
            { data: 'created_by.name', className: 'text-center'},
            { data: 'created_at', className: 'text-center'},
            { data: 'status', className: 'text-center' , width: '5%'},
            { data: 'action', className: 'text-right' , width: '5%' },
        ],
        button = [
            {
                text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Thêm bài viết</span>',
                className: 'dt-button button-add-datatable btn btn-primary ml-3',
                action: function ( e, dt, node, config ) {
                    openModalCreatePosts();
                }
            }
        ];
    DataTablePost = await datatableTemplate(id, data , column, button);
}

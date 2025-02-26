let DataTablePostCategory;

$(function (){
    loadData();
})

async function loadData(){
    let method = 'GET',
        url = '/posts-category/get-data-posts-category',
        param = null,
        data = null ;
    let res = await axiosTemplate(method , url , param , data)
    if(DataTablePostCategory){
        updateDataTable($('#table-post-category-ipretty'), res.data[0].original.data);
    }else {
        drawDataTablePostsCategory(res.data[0].original.data)
    }
}


async function drawDataTablePostsCategory(data){
    let id = $('#table-post-category-ipretty'),
        column = [
            { data: 'DT_RowIndex', className: 'text-center' , width: '5%' },
            { data: 'category_name', className: 'text-right' },
            { data: 'category_slug', className: 'text-center' },
            { data: 'category_slug', className: 'text-center'},
            { data: 'status', className: 'text-center' , width: '10%'},
            { data: 'action', className: 'text-right' , width: '5%' },
        ],
        button = [
            {
                text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Thêm danh mục</span>',
                className: 'dt-button button-add-datatable btn btn-primary ml-3',
                action: function ( e, dt, node, config ) {
                    openModalCreatePostsCategory();
                }
            }
        ];
    DataTablePostCategory = await datatableTemplate(id, data , column, button);
}


async function changeStatusPostsCategory(status, id){
    let method = 'post',
        url = '/posts-category/change-status',
        param = null,
        data = {
            id : id,
            status : status
        } ;
    let res = await axiosTemplate(method , url , param , data)
    if(res.data.status == 200){
        successSwalNotify("Thay đổi trạng thái thành công")
        loadData();
    }else{
        errorSwalNotify('Lỗi rồi '+ res);
    }
}


function changeStatusActicePostsCategory(r) {
    Swal.fire({
        title: "Thay đổi trạng thái hoạt động",
        text: "",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Đóng"
    }).then((result) => {
        if (result.isConfirmed) {
            changeStatusPostsCategory(true, r.data('id') );
        }
    });
}


function changeStatusUnActicePostsCategory(r) {
    Swal.fire({
        title: "Thay đổi trạng thái tạm ngưng",
        text: "",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Đồng ý",
        cancelButtonText: "Đóng"
    }).then((result) => {
        if (result.isConfirmed) {
            changeStatusPostsCategory(false, r.data('id') );
        }
    });
}
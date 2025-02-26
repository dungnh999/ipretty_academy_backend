<div class="modal fade" id="modal-create-post-category" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1">
    <div class="modal-dialog modal-md modal-simple modal-edit-user">
        <div class="modal-content p-3">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="text-uppercase">@lang('modal.title-create', ['name' => 'Danh mục bài viết'])</h3>
                </div>
                <form id="editUserForm" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework">
                    <div class="col-12 fv-plugins-icon-container">
                        <label class="form-label" for="name-create-post-category-ipretty">Tên danh mục</label>
                        <input type="text" id="name-create-post-category-ipretty" class="form-control"
                               placeholder="Họ và tên">
                    </div>
{{--                    <div class="col-12 fv-plugins-icon-container">--}}
{{--                        <label class="form-label" for="link-create-post-category-ipretty">Đường dẫn</label>--}}
{{--                        <input type="text" id="link-create-post-category-ipretty" class="form-control"--}}
{{--                               placeholder="bai-viet">--}}
{{--                    </div>--}}

                    <div class="col-12 text-lg-end">
                        <button class="btn btn-label-secondary" type="button"
                                onclick="closeModalCreatePostCategory()">@lang('modal.btn-close')</button>
                        <button type="button" class="btn btn-primary"
                                onclick="saveCreatePostCategory()">@lang('modal.btn-create')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('pricing-script')
    <script src="{{asset('assets/js/postscategory/create.js')}}"></script>
@endpush

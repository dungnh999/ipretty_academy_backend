<div class="modal fade" id="modal-create-post" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-simple modal-edit-user">
        <div class="modal-content p-3">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h3 class="text-uppercase text-center w-100 mb-0">@lang('modal.title-create', ['name' => 'Bài viết'])</h3>
            </div>
            <div class="modal-body">
                <form id="editUserForm" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework">
                    <div class="row mb-4">
                        <div class="col-6 fv-plugins-icon-container">
                            <div class="mb-4 fv-plugins-icon-container">
                                <label class="form-label" for="title-create-post-ipretty">Tiêu đề </label>
                                <input type="text" id="title-create-post-ipretty" class="form-control"
                                       placeholder="Họ và tên">
                            </div>
                            <div class="mb-4 fv-plugins-icon-container">
                                <label class="form-label" for=url-create-post-ipretty">Đường dẫn </label>
                                <input type="text" id="url-create-post-ipretty" class="form-control"
                                       placeholder="Họ và tên">
                            </div>
                            <div class="mb-4 d-flex flex-column">
                                <label class="form-label" for="category-create-post-ipretty">Thể loại </label>
                                <select id="type-create-post-ipretty" class="form-select">
                                    <option selected disabled>---- Vui lòng chọn ----</option>
                                    <option value="1" >Bài viết</option>
                                    <option value="2" >Tin tức</option>
                                </select>
                            </div>
                            <div class="mb-4 d-flex flex-column">
                                <label class="form-label" for="category-create-post-ipretty">Danh mục </label>
                                <select id="category-create-post-ipretty" class="form-select">
                                    <option selected disabled>Dữ liệu rỗng</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 fv-plugins-icon-container">
                            <label class="form-label" for="username">Hình đại diện (560x360)</label>
                            <div class="review-video-create-course position-relative">
                                <img id="preview-avatar-post-create" height="300" src="http://localhost:3000/47cd8596abb0394035d39c2be83091e6.png"
                                     class="w-100 rounded object-fit-cover">
                                <label for="input-avatar-post-create" type="button" style="position: absolute; background: aliceblue; right: 17px; bottom: 17px; border: none; padding: 7px; border-radius: 7px; border-color: red">
                                    <i class='bx bx-cloud-upload'></i>
                                    Tải ảnh lên
                                </label>
                                <input type="file" id="input-avatar-post-create" hidden="">
                            </div>
                        </div>
                    </div>


                    <div class="row mb-4">
                        <div class="col-12 fv-plugins-icon-container">
                            <label class="form-label" for="name-create-post-ipretty">Nội dung </label>
                            <div id="editor-content-posts-create" style="height: 400px"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-12 text-lg-end">
                    <button class="btn btn-secondary" type="button"
                            onclick="closeModalCreatePost()">@lang('modal.btn-close')</button>
                    <button type="button" class="btn btn-primary"
                            onclick="saveCreatePost()">@lang('modal.btn-create')</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('pricing-script')
    <script src="{{asset('assets/js/posts/create.js')}}"></script>
@endpush

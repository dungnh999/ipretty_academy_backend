<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserDepartmentResource;
use App\Repositories\PostCategoryRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PostsCategoryController extends BannerController
{

    private $postCategoryRepository;

    public function __construct(PostCategoryRepository $postCategoryRepositoryRepo)
    {
        $this->postCategoryRepository = $postCategoryRepositoryRepo;
    }

    public function index()
    {
        return view('contents.postscategory.index');
    }

    public function getListPostsCategory(Request $request)
    {
        $data = $this->postCategoryRepository->allPostCategory($request);
        $tablePostsCategory = $this->drawDataTableBanner($data);
        return [$tablePostsCategory];
    }

    public function createPostsCategory(Request $request)
    {
        $input = $request->all();
        $data = $this->postCategoryRepository->handleCreatePostCategory($input);
        return $this->sendSuccess(
            __('messages.saved', ['model' => __('models/userDepartments.singular')]),
            new UserDepartmentResource($data)
        );
    }

    function drawDataTableBanner($data)
    {
        return Datatables::of($data)
            ->addColumn('status', function ($row) {
                if ($row['isPublished']) {
                    return '<span class="badge bg-label-info">Đang chạy</span>';
                } else {
                    return '<span class="badge bg-label-danger">Đã tắt</span>';
                }
            })
            ->addColumn('action', function ($row) {
                $postId = $row['category_id'];
                if (!$row['isPublished']) {
                    return '<div class="d-inline-block text-nowrap" >
                            <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $postId . '" onclick="openModalUpdateBanner($(this))">
                              <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-success rounded-pill btn-sm"  data-id="' . $postId . '"  onclick="changeStatusActicePostsCategory($(this))">
                              <i class="bx bx-check" ></i>
                            </button>
                      </div>';

                } else {
                    return '<div class="d-inline-block text-nowrap" >
                           <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $postId . '" onclick="openModalUpdateBanner($(this))">
                              <i class="bx bx-edit"></i>
                          </button>
                          <button class="btn btn-icon btn-outline-danger rounded-pill btn-sm" data-id="' . $postId . '" onclick="changeStatusUnActicePostsCategory($(this))">
                              <i class="bx bx-x"></i>
                          </button>
                      </div>';
                }
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'image', 'action'])
            ->make(true);
    }

    public function changeStatus(Request $request)
    {
        $id = $request->get('id');
        $userPostsCategory = $this->postCategoryRepository->find($id);

        if (empty($userPostsCategory)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/userDepartments.singular')])
            );
        }

        $userPostsCategory['isPublished'] = (int)$request->get('status');
        $userPostsCategory->save();
        return $this->sendSuccess(
            __('messages.deleted', ['model' => __('models/userDepartments.singular')]),
            new UserDepartmentResource($userPostsCategory)
        );
    }
}

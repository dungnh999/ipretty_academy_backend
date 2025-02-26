<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Resources\PostResource;
use App\Repositories\PostCategoryRepository;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PostsController extends AppBaseController
{
    private $postRepository;
    private $postCategoryRepository;

    public function __construct(PostRepository $postRepository, PostCategoryRepository $postCategoryRepository)
    {
        $this->postRepository = $postRepository;
        $this->postCategoryRepository = $postCategoryRepository;
    }


    public function index()
    {
        return view('contents.posts.index');
    }

    public function getListPosts(Request $request)
    {
        $data = $this->postRepository->allPost($request);
        $tablePosts = $this->drawDataTablePosts($data);
        return [$tablePosts];
    }


    function drawDataTablePosts($data)
    {
        return Datatables::of($data)
            ->addColumn('status', function ($row) {
                if ($row['is_active']) {
                    return '<span class="badge bg-label-info">Đang chạy</span>';
                } else {
                    return '<span class="badge bg-label-danger">Đã tắt</span>';
                }
            })
            ->addColumn('avatar', function ($row) {
                $urlUpload = Env('APP_URL');
                return '<img src="' . $urlUpload . $row['bannerUrl'] . '" width="200" height="100" class="rounded" style="object-fit:cover">';
            })
            ->addColumn('action', function ($row) {
                $postId = $row['category_id'];
                if (!$row['is_active']) {
                    return '<div class="d-inline-block text-nowrap" >
                            <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $postId . '" onclick="openModalUpdatePosts($(this))">
                              <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-success rounded-pill btn-sm"  data-id="' . $postId . '"  onclick="changeStatusActicePostsCategory($(this))">
                              <i class="bx bx-check" ></i>
                            </button>
                      </div>';

                } else {
                    return '<div class="d-inline-block text-nowrap" >
                           <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $postId . '" onclick="openModalUpdatePosts($(this))">
                              <i class="bx bx-edit"></i>
                          </button>
                          <button class="btn btn-icon btn-outline-danger rounded-pill btn-sm" data-id="' . $postId . '" onclick="changeStatusUnActicePostsCategory($(this))">
                              <i class="bx bx-x"></i>
                          </button>
                      </div>';
                }
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'avatar', 'action'])
            ->make(true);
    }

    public function getListPostCategorys()
    {
        $params = request()->query();
        $res = $this->postCategoryRepository->allPostCategory($params);
        $datas = json_decode($res, true);
        $select = '<option selected disabled>---- Vui lòng chọn ----</option>';
        foreach ($datas as $data) {
            $select .= '<option value="'. $data['category_id'] .'">'. $data['category_name'] .'</option>';
        }

        return [$select , 'dâdsađaa'];
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $response = $this->postRepository->handleStorePost($input , $request);

        return $this->sendSuccess(
            __('messages.created', ['model' => __('models/courses.singular')]),
            new PostResource($response)
        );
    }
}

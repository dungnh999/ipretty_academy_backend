<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Repositories\MediaRepository;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Response;

class BannerController extends AppBaseController
{

    private $postRepository;
    private $mediaRepository;

    public function __construct(PostRepository $postRepo, MediaRepository $mediaRepository)
    {
      $this->postRepository = $postRepo;
      $this->mediaRepository = $mediaRepository;
    }

    public function index()
    {
        return view('contents.banner.index');
    }

    public function getListBanner()
    {
      $banner = json_decode(Post::All(), true);
      $dataTablebanner = $this->drawDataTableBanner($banner);
      return [$dataTablebanner];
    }
    function drawDataTableBanner($data){
        return Datatables::of($data)
          ->addColumn('status', function ($row) {
              if($row['is_active']){
                return '<span class="badge bg-label-info">Đang chạy</span>';
              }else{
                return '<span class="badge bg-label-danger">Đã tắt</span>';
              }
          })
          ->addColumn('image', function ($row) {
            $urlUpload = Env('AWS_URL_UPLOAD');
            return '<img src="'. $urlUpload . $row['bannerUrl'] .'" width="200" height="100" class="rounded" style="object-fit:cover"/>';
          })

          ->addColumn('action', function ($row) {
            $postId = $row['post_id'];
            if(!$row['is_active']){
              return '<div class="d-inline-block text-nowrap" >
                          <button class="btn btn-icon btn-outline-success rounded-pill btn-sm"  data-id="'. $postId .'"  onclick="changeRunBanner($(this))">
                              <i class="bx bx-play-circle" ></i>
                          </button>
                          <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="'. $postId .'" onclick="openModalUpdateBanner($(this))">
                              <i class="bx bx-edit"></i>
                          </button>
                      </div>';


            }else{
              return '<div class="d-inline-block text-nowrap" >
                          <button class="btn btn-icon btn-outline-danger rounded-pill btn-sm" data-id="'. $postId .'" onclick="changePauseBanner($(this))">
                              <i class="bx bx-pause-circle"></i>
                          </button>
                           <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="'. $postId .'" onclick="openModalUpdateBanner($(this))">
                              <i class="bx bx-edit"></i>
                          </button>
                      </div>';
            }
          })

          ->addIndexColumn()
          ->rawColumns(['status', 'image', 'action'])
          ->make(true);
    }

    function CreateBanner(Request $request) {
      $input = $request->all();
      $result = $this->postRepository->handleStorePost($input, $request);
      return Response::json([
        'message' => __('auth.login.success_message'),
        'data' => $result
      ], 200);
    }

    function changeStatus(Request $request) {
      $published_post = $this->postRepository->publishedPost($request->get('id'), $request->get('status'));
      return Response::json([
        'message' => __('auth.login.success_message'),
        'data' => $published_post
      ], 200);
    }

    public function detailBanner(Request $request){
      $post = $this->postRepository->getDetailPost($request->get('id'));
      return Response::json([
        'message' => __('auth.login.success_message'),
        'data' => $post
      ], 200);
    }

    public function updateBanner(Request $request){
      $input = $request->all();
      $post_id = $request->get('id');
      $post = $this->postRepository->find($post_id);
      if (empty($post)) {
        return $this->sendError(
          __('messages.not_found', ['model' => __('models/posts.singular')])
        );
      }
      $result = $this->postRepository->handleUpdatePost($input, $post_id, $request);

      if ($result) {
        return $this->sendResponse(
          new PostResource($result),
          __('messages.updated', ['model' => __('models/posts.singular')])
        );
      } else{
        return $this->sendError(
          __('messages.errors.can_not_upload_banner', ['model' => __('models/posts.singular')])
        );
      }
    }
}

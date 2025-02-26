<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\CreateBannerAPIRequest;
use App\Http\Requests\API\CreatePostAPIRequest;
use App\Http\Requests\API\DeletePostAPIRequest;
use App\Http\Requests\API\UpdateBannerAPIRequest;
use App\Http\Requests\API\UpdatePostAPIRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Repositories\MediaRepository;
use Exception;
use Response;
use Validator;


class PostAPIController extends AppBaseController
{
    private $postRepository;
    private $mediaRepository;

    public function __construct(PostRepository $postRepo, MediaRepository $mediaRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->postRepository = $postRepo;
        $this->mediaRepository = $mediaRepository;
    }

    public function index()
    {

        $params = request()->query();

        $posts = $this->postRepository->allPost($params);

        return $this->sendResponse(
            $posts,
            __('messages.retrieved', ['model' => __('models/posts.plural')])
        );
    }

    public function storePost(CreatePostAPIRequest $request)
    {

        $input = $request->all();

        $result = $this->postRepository->handleStorePost($input, $request);
        if ($result) {
            return $this->sendResponse(
                new PostResource($result),
                __('messages.saved', ['model' => __('models/posts.singular')])
            );
        }
    }

    public function createBanner(CreateBannerAPIRequest $request)
    {

        $input = $request->all();

        $result = $this->postRepository->handleStorePost($input, $request);

        if ($result) {
            return $this->sendResponse(
                new PostResource($result),
                __('messages.saved', ['model' => __('models/posts.fields.bannerUrl')])
            );
        }

    }

    public function detailPost($post_id)
    {

        /** @var Lesson $lesson */
        $post = $this->postRepository->find($post_id);

        if (empty($post)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/posts.singular')])
            );
        }

        return $this->sendResponse(
            new PostResource($post),
            __('messages.retrieved', ['model' => __('models/posts.singular')])
        );

    }


    public function detailPostSlug(Request $request)
    {

        /** @var Lesson $lesson */
        $post = $this->postRepository->getDetailPostBySlug($request->get('slug'));

        if (empty($post)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/posts.singular')])
            );
        }

        return $this->sendResponse(
            new PostResource($post),
            __('messages.retrieved', ['model' => __('models/posts.singular')])
        );

    }

    public function updatePost(UpdatePostAPIRequest $request, $post_id)
    {

        $input = $request->all();

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
        } else {
            return $this->sendError(
                __('messages.errors.can_not_upload_banner', ['model' => __('models/posts.singular')])
            );
        }

    }

    public function updateBanner(UpdateBannerAPIRequest $request, $post_id)
    {

        $input = $request->all();

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
                __('messages.updated', ['model' => __('models/posts.fields.bannerUrl')])
            );
        } else {
            return $this->sendError(
                __('messages.errors.can_not_upload_banner', ['model' => __('models/posts.singular')])
            );
        }

    }

    public function destroy(DeletePostAPIRequest $request)
    {
        /** @var Lesson $lesson */

        $postIds = explode(',', $request->post_ids);

        $notFoundPosts = [];

        $foundPosts = [];

        foreach ($postIds as $id) {

            $post = $this->postRepository->find($id);

            if (empty($post)) {
                array_push($notFoundPosts, $id);
            } else {
                array_push($foundPosts, $post);
            }
        }

        if (count($notFoundPosts)) {

            return $this->sendError(
                __('messages.not_found', ['model' => __('models/posts.singular')]),
                404,
                $notFoundPosts
            );

        }

        if (count($foundPosts)) {
            foreach ($foundPosts as $post) {

                $post->delete();
            }
        }

        $posts = $this->postRepository->allPost();

        return $this->sendResponse(
            $posts,
            __('messages.deleted', ['model' => __('models/posts.singular')])
        );
    }

    public function deleteBanner($post_id, Request $request)
    {

        $post = $this->postRepository->find($post_id);

        if (empty($post)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/posts.singular')])
            );
        }

        $media = $this->mediaRepository->findByModelAndId($post_id, $request->media_id);


        if (empty($media)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/posts.fields.bannerUrl')])
            );
        }

        $deletemedia = $this->postRepository->destroyMedia($media, $post_id);

        return $this->sendResponse(
            $deletemedia,
            __('messages.deleted', ['model' => __('models/posts.fields.bannerUrl')])
        );

    }

    public function changePublishedPost($post_id, Request $request)
    {
        $post = $this->postRepository->find($post_id);

        if (empty($post)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/posts.singular')])
            );
        }

        $published_post = $this->postRepository->publishedPost($post_id, $request->is_active);

        $message = 'messages.unpublished';

        if ($request->is_active) {
            $message = 'messages.published';
        }

        return $this->sendResponse(
            new PostResource($published_post),
            __($message, ['model' => __('models/posts.singular')])
        );
    }

    public function getOpinions(Request $request)
    {
        $opinion = $this->postRepository->getCommentUser();

        return $this->sendResponse(
            $opinion,
            __('messages.retrieved', ['model' => __('models/posts.singular')])
        );
    }

    public function getAllBanner(Request $request)
    {
        $banner = $this->postRepository->getBanner();
        return $this->sendResponse(
            $banner,
            __('messages.retrieved', ['model' => __('models/posts.singular')])
        );
    }
}

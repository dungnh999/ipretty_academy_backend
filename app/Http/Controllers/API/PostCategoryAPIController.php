<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\CreatePostCategoryAPIRequest;
use App\Http\Requests\API\DeletePostCategoryAPIRequest;
use App\Http\Requests\API\UpdatePostCategoryAPIRequest;
use App\Http\Resources\PostCategoryResource;
use App\Models\PostCategory;
use App\Repositories\PostCategoryRepository;
use Illuminate\Http\Request;

class PostCategoryAPIController extends AppBaseController
{
    private $postCategoryRepository;
    

    public function __construct(PostCategoryRepository $postCategoryRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->postCategoryRepository = $postCategoryRepo;
    }

    public function index(){

        $params = request()->query();

        $postCategories = $this->postCategoryRepository->allPostCategory($params);

        return $this->sendResponse(
            $postCategories,
            __('messages.retrieved', ['model' => __('models/postCategories.plural')])
        );
    }

    public function storePostCategory(CreatePostCategoryAPIRequest $request){

        // $input = $request->only('category_name');
        $input = $request->all();
        $postCategory = $this->postCategoryRepository->handleStorePostCategory($input);

        return $this->sendResponse(
            new PostCategoryResource($postCategory),
            __('messages.saved', ['model' => __('models/postCategories.singular')])
        );
    }

    public function detailPostCategory($category_id){

        $postCategory = $this->postCategoryRepository->find($category_id);

        if (empty($postCategory)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/postCategories.singular')])
            );
        }

        return $this->sendResponse(
            new PostCategoryResource($postCategory),
            __('messages.retrieved', ['model' => __('models/postCategories.singular')])
        );
    }

    public function updatePostCategory(UpdatePostCategoryAPIRequest $request, $category_id){

        $input = $request->all();

        $postCategory = $this->postCategoryRepository->find($category_id);

        if(empty($postCategory)){
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/postCategories.singular')])
            );
        }

        if ($postCategory->category_name == PostCategory::Post_Category_Name && $input['category_name'] != PostCategory::Post_Category_Name) {
            return $this->sendError(
                __('messages.cannot_update_category_name')
            );
        }

        if ($postCategory->category_name == PostCategory::Post_Category_Name && $input['isPublished'] == 0) {
            return $this->sendError(
                __('messages.cannot_un_published_category')
            );
        }

        $postCategory = $this->postCategoryRepository->handleUpdatePostCategory($input, $category_id);

        return $this->sendResponse(
            new PostCategoryResource($postCategory),
            __('messages.updated', ['model' => __('models/postCategories.singular')])
        );
    }

    public function destroy(DeletePostCategoryAPIRequest $request){

        $categoryIds = explode(',', $request->category_ids);

        $notFoundCategory = [];

        $foundCategory = [];

        foreach($categoryIds as $id) {

            $category = $this->postCategoryRepository->find($id);

            if (empty($category)) {
                array_push($notFoundCategory, $id);
            }else {
                array_push($foundCategory, $category);
            }
        }

        if (count($notFoundCategory)) {

            return $this->sendError(
                __('messages.not_found', ['model' => __('models/postCategories.singular')]),
                404,
                $notFoundCategory
            );
            
        }            

        if (count($foundCategory)) {
            foreach ($foundCategory as $category) {

                $category->delete();
            }
        }

        $categories = $this->postCategoryRepository->allPostCategory();

        return $this->sendResponse(
            $categories,
            __('messages.deleted', ['model' => __('models/postCategories.singular')])
        );
    }
}

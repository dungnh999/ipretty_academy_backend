<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Models\Post;
use App\Models\PostCategory;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// use ProtoneMedia\LaravelFFMpeg\Filesystem\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class PostRepository
 * @package App\Repositories
 * @version September 8, 2021, 4:08 am UTC
 */
class PostRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'content',
        'slug'
    ];

    protected $relations = ['postCategory'];

    protected $relationSearchable = [
        'category_name'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Post::class;
    }

    public function handleStorePost($input, $request = null)
    {
        $user = auth()->user();

        $input["created_by"] = $user->id;

        $slug = CommonBusiness::change_alias($input["title"]);

        $input["slug"] = $slug;

        $model = $this->model->newInstance($input);

        $model->save();

        // if($input["is_banner"] == 1){
//        $model->handleMedia($request);
        // }


        Post::where('post_id', $model->post_id)->update(['updated_at' => NULL]);
        // DB::table('posts')
        // ->where('post_id', $model->post_id)
        // ->update(['updated_at' => NULL]);

        return $model;
    }

    public function allPost($params = null)
    {

        $query = $this->model->newQuery()->with('postCategory')
            ->with('createdBy', function ($q) {
                $q->select('name', 'email', 'id');
            })
            ->orderBy('created_at', 'desc');

        if (isset($params['isBanner']) && $params['isBanner'] == 1) {
            $query = $query->where('is_banner', $params['isBanner'])->where('isTrademark', 0);
        } else if (isset($params['isTrademark']) && $params['isTrademark'] == 1) {
            $query = $query->where('isTrademark', 1)->where('is_banner', 0);
        } else {
            $query = $query->where('isTrademark', 0)->where('is_banner', 0);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $status = explode(',', $params['status']);
            $query = $query->whereIn('is_active', $status);
        }

        if (isset($params['created_at']) && $params['created_at'] != null) {
            $created_at = $params['created_at'];
            $query = $query->whereDate('created_at', '>=', $created_at);
        }

        if (isset($params['updated_at']) && $params['updated_at'] != null) {
            $updated_at = $params['updated_at'];
            $query = $query->whereDate('updated_at', '>=', $updated_at);
        }

        if (isset($params['category_ids']) && $params['category_ids'] != null) {
            $category_ids = explode(',', $params['category_ids']);
            $query = $query->whereIn('category_id', $category_ids);
        }

        if (!empty($params['keyword'])) {
            $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
        }

        // $model = $query->paginate(PERPAGE);
        if (isset($params['paging']) && $params['paging'] == true) {
            if (isset($params['perpage']) && $params['perpage'] != null) {

                $perpage = $params['perpage'];

                $model = $query->paginate($perpage);
            } else {
                $model = $query->paginate(PERPAGE);
            }
        } else {
            $model = $query->get();
        }

        return $model;

    }

    public function handleUpdatePost($input, $post_id, $request = null)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($post_id);

        $slug = CommonBusiness::change_alias($input['title']);

        $model->slug = $slug;

        if (empty($input[MEDIA_COLLECTION["POST_BANNERURL"]]) || $input[MEDIA_COLLECTION["POST_BANNERURL"]] == "null" || $input[MEDIA_COLLECTION["POST_BANNERURL"]] == 'undefined') {
            $input[MEDIA_COLLECTION["POST_BANNERURL"]] = $model[MEDIA_COLLECTION["POST_BANNERURL"]];
        }

        $model->fill($input);
        $model->save();

        if ($input[MEDIA_COLLECTION["POST_BANNERURL"]] != NULL && $input[MEDIA_COLLECTION["POST_BANNERURL"]] != "null" ) {
            $model->handleMedia($request);
        }

        return $model;
    }

    public function destroyMedia($media, $post_id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($post_id);

        $mediaUrl = $media->getUrl();

        $banner = explode(',', $model->bannerUrl);

        $banner = array_diff_key($banner, [$mediaUrl]);

        $model->bannerUrl = implode(',', $banner);

        $model->save();

        $media->delete();

        return $model;

    }

    public function getPost($slug)
    {
        $posts = $this->model
            ->join('post_categories', 'post_categories.category_id', 'posts.category_id')
            ->where('is_banner', 0)
            ->where('is_active', 1)
            ->where('isTrademark', 0)
            ->where('post_categories.category_slug', $slug)
            ->orderBy('posts.created_by', 'DESC')
            ->first();

        // dd($posts);
        return $posts;
    }

    public function getAllPost($slug)
    {
        $posts = $this->model
            ->join('post_categories', 'post_categories.category_id', 'posts.category_id')
            ->where('is_banner', 0)
            ->where('post_categories.category_slug', $slug)
            ->orderBy('posts.created_at', 'DESC')
            ->get();

        // dd($posts);
        return $posts;
    }

    public function getAllBanner($slug)
    {
        $banners = $this->model
            ->join('post_categories', 'post_categories.category_id', 'posts.category_id')
            ->where('is_banner', 1)
            ->where('is_active', 1)
            ->where('post_categories.category_slug', $slug)
            ->get();

        // dd($banners);
        return $banners;
    }

    public function publishedPost($post_id, $is_active)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($post_id);

        if ($model) {
            $model->is_active = $is_active;
            $model->save();
        }
        return $model;
    }

    public function getBanner()
    {
        $banners = $this->model
            ->where('is_active', '1')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get();
        return $banners;
    }

    public function getBrand($params = null)
    {

        $brands = $this->model->where('is_banner', '0')
            ->where('isTrademark', '1')
            ->where('is_active', '1')
            ->orderBy('created_at', 'DESC');

        if (isset($params) && isset($params['limit']) && $params['limit'] != null) {
            $brands = $brands->limit($params['limit']);
        }

        $brands = $brands->get();

        return $brands;
    }

    public function getCommentUser()
    {
        $comment = $this->model->join('post_categories', 'post_categories.category_id', 'posts.category_id')
            ->with('createdBy')
            ->where('posts.is_active', 1)
            ->where('post_categories.category_name', PostCategory::Post_Category_Name)
            ->orderBy('posts.created_at', 'DESC')
            ->limit(3)
            ->get();
        return $comment;
    }

    public function getDataNews()
    {
        $news = $this->model->join('post_categories', 'post_categories.category_id', 'posts.category_id')
            ->with('createdBy')
            ->where('post_categories.category_name', '!=', PostCategory::about_us_category)
            ->where('post_categories.category_name', '!=', PostCategory::recruitment_category)
            ->where('post_categories.category_name', '!=', PostCategory::about_ipretty)
            ->where('post_categories.category_name', '!=', PostCategory::team_of_experts)
            ->where('post_categories.category_name', '!=', PostCategory::terms_policy)
            ->where('post_categories.category_name', '!=', PostCategory::course_training)
            ->where('posts.is_active', '1')
            ->where('posts.is_banner', '0')
            ->where('posts.isTrademark', '0')
            ->orderBy('posts.created_at', 'DESC')
            ->paginate(5);
        return $news;
    }

    public function getDetailPost($post_id)
    {
        $post = $this->model->with('createdBy')
            ->where('post_id', $post_id)
            ->first();
        return $post;
    }

    public function getPostRelated($post_id)
    {
        $post_category = $this->model->where('posts.post_id', $post_id)->first();
        $category_id = $post_category->category_id;
        $post_by_category = $this->model->with('createdBy')
            ->where('post_id', '!=', $post_id)
            ->where('category_id', $category_id)
            ->where('is_active', '1')
            ->where('is_banner', '0')
            ->where('isTrademark', '0')
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->get();
        return $post_by_category;
    }




}

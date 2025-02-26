<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Models\PostCategory;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class PostCategoryRepository
 * @package App\Repositories
 * @version September 17, 2021, 3:26 am UTC
*/

class PostCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        return PostCategory::class;
    }

    public function handleStorePostCategory($input){

        $model = $this->model->newInstance($input);

        $slug = CommonBusiness::change_alias($input['category_name']);
        // var_dump($slug);

        $model->category_slug = $slug;

        $model->save();

        PostCategory::where('category_id', $model->category_id)->update(['updated_at' => NULL]);

        return $model;
    }

    public function allPostCategory($params = null){
    
        $query = $this->model->newQuery();

        if (!empty($params['keyword'])) {
            $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword']);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $status = explode(',', $params['status']);
            $query = $query->whereIn('isPublished', $status);
        }

        if (isset($params['created_at']) && $params['created_at'] != null) {
            $created_at = $params['created_at'];
            // $query = $query->whereDate('created_at', '>=', $created_at);
            $query = $query->where('created_at', '>=', $created_at.' 00:00:00');
        }

        if (isset($params['updated_at']) && $params['updated_at'] != null) {
            $updated_at = $params['updated_at'];
            // $query = $query->whereDate('updated_at', '>=', $updated_at);
            $query = $query->where('updated_at', '>=', $updated_at.' 00:00:00');
        }

        $query = $query->orderBy('created_at', 'DESC');
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

    public function handleCreatePostCategory($input){
        DB::beginTransaction();
        try {
            $input['category_slug'] = CommonBusiness::change_alias($input['category_name']);
            $model = $this->model->newInstance($input);
            $model->save();

            DB::commit();
            return $model;
        } catch (\Exception $e) {
            DB::rollBack();
//            Log::error("Error creating category: " . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi tạo danh mục'], 500);
        }
    }

    public function handleUpdatePostCategory($input, $category_id){

        $query = $this->model->newQuery();

        $model = $query->findOrFail($category_id);

        $slug = CommonBusiness::change_alias($model->category_name);
        // var_dump($model->category_name);

        $model->fill($input);

        $model->category_slug = $slug;

        $model->save();

        return $model;
    }
}

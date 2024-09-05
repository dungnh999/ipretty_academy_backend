<?php

namespace App\Repositories;

use App\Models\FAQLike;
use App\Repositories\BaseRepository;

/**
 * Class FAQLikeRepository
 * @package App\Repositories
 * @version November 15, 2021, 1:33 am +07
*/

class FAQLikeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
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
        return FAQLike::class;
    }

    public function likeOrDislikeFAQQuestion($input)
    {
        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    public function changeLikeOrDislikeFAQQuestion($checkLiked, $input)
    {
        $model = $checkLiked;

        $model->status = $input['status'];

        $model->save();

        return $model;
    }

    public function checkLiked($input)
    {
        $model = $this->model->newQuery();

        $model = $model->where('question_id', $input['question_id'])
        ->where('user_id', $input['user_id'])
        ->first();

        return $model;
    }
}

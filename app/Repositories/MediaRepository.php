<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Models\Lesson;
use App\Repositories\BaseRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class LessonRepository
 * @package App\Repositories
 * @version September 8, 2021, 5:13 pm UTC
*/

class MediaRepository extends BaseRepository
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
        return Media::class;
    }

    public function findByModelAndId ($model_id, $media_id) {
        // dd($media_id);

        $query = $this->model->newQuery();

        $model = $query->where('model_id', $model_id)->where('uuid', $media_id)->first();
        
        return $model;
    }
 
}

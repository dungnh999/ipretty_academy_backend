<?php

namespace App\Repositories;

use App\Models\CommentFAQ;
use App\Repositories\BaseRepository;

/**
 * Class CommentFAQRepository
 * @package App\Repositories
 * @version November 15, 2021, 2:06 am +07
*/

class CommentFAQRepository extends BaseRepository
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
        return CommentFAQ::class;
    }

    public function commentQuestion($input)
    {
        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }
}

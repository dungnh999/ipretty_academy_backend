<?php

namespace App\Repositories;

use App\Models\CourseLeader;
use App\Repositories\BaseRepository;

/**
 * Class CourseLeaderRepository
 * @package App\Repositories
 * @version October 5, 2021, 3:50 am UTC
*/

class CourseLeaderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'course_id',
        'leader_id'
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
        return CourseLeader::class;
    }

    public function isJoined($leader_id, $course_id)
    {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $course_id)->where('leader_id', $leader_id)->first();

        return $model;
    }

    public function deleteLeaderInCourse($course_id, $leader_id)
    {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', $course_id)->where('leader_id', $leader_id)->delete();

        return $model;
    }
}

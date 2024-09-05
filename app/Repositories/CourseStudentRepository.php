<?php

namespace App\Repositories;

use App\Models\Answer;
use App\Models\CourseStudent;
use App\Models\User;
use App\Repositories\BaseRepository;

/**
 * Class CourseStudentRepository
 * @package App\Repositories
 * @version September 23, 2021, 3:27 pm UTC
*/

class CourseStudentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'course_id',
        'student_id',
        'percent_finish',
        'isPassed'
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
        return CourseStudent::class;
    }

    public function create ($input) {

        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    public function isJoined ($student_id, $course_id) {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $course_id)->where('student_id', $student_id)->first();

        return $model;
    }

    public function findByCourse ($course_id, $user_id) {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $course_id)
                ->where('student_id', $user_id)
                ->where('isCompleted', 1)
                ->whereNotNull('completed_at')
                ->first();

        return $model;

    }

    public function deleteStudentInCourse ($course_id, $student_id) {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', $course_id)->where('student_id', $student_id)->delete();

        return $model;
    }

    public function confirmNotice ($course_id, $student_id) {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', $course_id)->where('student_id', $student_id)->first();

        if ($model) {
            
            $model->isNoticed = true;
            $model->save();
        }

        return $model;
    }

    // public function featureCourses($params = null)
    // {

    //     $query = $this->model->newQuery();

    //     $model = $query->whereNotNull('rating')
    //             ->selectRaw('
    //             course_id, avg(rating) as total_rating')
    //             ->groupBy('course_id')
    //             ->with('courseName')
    //             ->orderBy('total_rating', 'desc')
    //             ->limit(10)
    //             ->get();

    //     return $model;
    // }

}

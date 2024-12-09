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

    public function getCommentAndRating() {
        $subQuery = CourseStudent::selectRaw('MAX(id) as max_id')
            ->whereNotNull('comment')
            ->groupBy('student_id');

        $feedbacks = CourseStudent::query()
            ->select(
                'courses_students.id as feedback_id',
                'courses_students.course_id',
                'courses_students.comment',
                'courses_students.rating',
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->join('users', 'courses_students.student_id', '=', 'users.id')
            ->whereIn('courses_students.id', $subQuery)
            ->orderBy('courses_students.course_id')
            ->orderBy('courses_students.rating', 'desc')
            ->get();

        return $feedbacks;
    }

    public function getCommentAndRatingByCourse() {
        $subQuery = CourseStudent::selectRaw('MAX(id) as max_id')
            ->whereNotNull('comment')
            ->groupBy('student_id');

        $totalRating = CourseStudent::where('course_id', 9)
            ->whereNotNull('comment')
            ->count('rating'); // Tổng điểm đánh giá của khóa học

        // Tính toán số lượng feedbacks theo từng mức sao
        $star1 = CourseStudent::where('course_id', 9)
            ->whereNotNull('comment')
            ->where('rating', 1)
            ->count();

        $star2 = CourseStudent::where('course_id', 9)
            ->whereNotNull('comment')
            ->where('rating', 2)
            ->count();

        $star3 = CourseStudent::where('course_id', 9)
            ->whereNotNull('comment')
            ->where('rating', 3)
            ->count();

        $star4 = CourseStudent::where('course_id', 9)
            ->whereNotNull('comment')
            ->where('rating', 4)
            ->count();

        $star5 = CourseStudent::where('course_id', 9)
            ->whereNotNull('comment')
            ->where('rating', 5)
            ->count();

        // Tính phần trăm cho mỗi mức sao
        $percentStar1 = $totalRating > 0 ? ($star1 / $totalRating) * 100 : 0;
        $percentStar2 = $totalRating > 0 ? ($star2 / $totalRating) * 100 : 0;
        $percentStar3 = $totalRating > 0 ? ($star3 / $totalRating) * 100 : 0;
        $percentStar4 = $totalRating > 0 ? ($star4 / $totalRating) * 100 : 0;
        $percentStar5 = $totalRating > 0 ? ($star5 / $totalRating) * 100 : 0;

        $feedbacks = CourseStudent::query()
            ->select(
                'courses_students.id as feedback_id',
                'courses_students.course_id',
                'courses_students.comment',
                'courses_students.rating',
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                'users.avatar as user_avatar'
            )
            ->join('users', 'courses_students.student_id', '=', 'users.id')
            ->where('courses_students.course_id', '=' , 9)
            ->paginate(2); // Số lượng feedbacks mỗi trang là 10

        return [
            'comment' => $feedbacks,
            'total_rating' => $totalRating,
            'rating_persen'=> [
                'start_1' => round($percentStar1),
                'start_2' => round($percentStar2),
                'start_3' => round($percentStar3),
                'start_4' => round($percentStar4),
                'start_5' => round($percentStar5),
            ]
        ];
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

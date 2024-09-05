<?php

namespace App\Repositories;

use App\Jobs\PushNotificationJoinCourse;
use App\Models\Course;
use App\Models\LearningProcess;
use App\Repositories\BaseRepository;

/**
 * Class LearningProcessRepository
 * @package App\Repositories
 * @version October 10, 2021, 1:40 am +07
*/

class LearningProcessRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'lesson_id',
        'survey_id',
        'process',
        'isPassed',
        'completed_at',
        'started_at'
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
        return LearningProcess::class;
    }

    public function findByCondition ($course_id, $survey_id, $user_id) {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $course_id)
            ->where('student_id', $user_id)
            ->where('survey_id', $survey_id)
            ->where('isDraft', 0)
            ->whereNotNull('completed_at')
            ->first();

        return $model;
    }

    public function findByCourseAndSurvey ($course_id, $survey_id, $user_id) {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $course_id)
            ->where('student_id', $user_id)
            ->where('survey_id', $survey_id)
            ->first();
        // dd($survey_id);

        return $model;
    }

    public function getLearningProcess ($input) {

        $user = auth()->user();

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $input["course_id"])
            ->where('student_id', $user->id)
            ->where('survey_id', $input["survey_id"])
            ->where('isDraft', 0)
            ->whereNotNull('completed_at')
            ->first();

        return $model;
    }

    public function createProcessLearning($input, $student_id, $course = null):void
    {
        $chapters = Course::find($input["course_id"])->chapters;
        $chapter_lesson_ids = [];
        $chapter_survey_ids = [];
        
        foreach ($chapters as $key => $chapter) {
            $lessonIds = $chapter->lessons->pluck('lesson_id');
            $chapter_lesson_ids = array_merge($chapter_lesson_ids, $lessonIds->toArray());

            $survey_id = $chapter->survey_id;
            if ($survey_id) {
                array_push($chapter_survey_ids, $survey_id);
            }
        }

        if (count($chapter_lesson_ids)) {
            foreach ($chapter_lesson_ids as $key => $lesson_id) {
                LearningProcess::create([
                    "course_id" => $input['course_id'],
                    "lesson_id" => $lesson_id,
                    "student_id" => $student_id
                ]);
            }
        }


        if (count($chapter_survey_ids)) {
            foreach ($chapter_survey_ids as $key => $survey_id) {
                LearningProcess::create([
                    "course_id" => $input['course_id'],
                    "survey_id" => $survey_id,
                    "student_id" => $student_id
                ]);
            }
        }

        if ($course) {

            $job = (new PushNotificationJoinCourse($course, $student_id));

            dispatch($job);
    
            // event(new \App\Events\PushNotification($student_id));

        }

    }

    public function deleteStudentProcessLearning ($course_id, $student_id)
    {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', $course_id)->where('student_id', $student_id)->delete();

        return $model;
    }

    public function checkValidProcessByLesson ($course_id, $lesson_id, $user_id)
    {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $course_id)
            ->where('student_id', $user_id)
            ->where('lesson_id', $lesson_id)
            ->first();

        return $model;
    }

    public function findByLesson($course_id, $lesson_id, $user_id)
    {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $course_id)
            ->where('student_id', $user_id)
            ->where('lesson_id', $lesson_id)
            ->where('isDraft', 0)
            ->whereNotNull('completed_at')
            ->first();

        return $model;
    }

    public function getLearningProcessesForUser ($course_id, $student_id) {

        $query = $this->model->newQuery();

        $model = $query->where('course_id', '=', $course_id)
            ->where('student_id', $student_id)
            ->get();

        return $model;
    }
}

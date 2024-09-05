<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Jobs\PushNotificationWhenStudentCompletedCourse;
use App\Models\Answer;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\LearningProcess;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Survey;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class AnswerRepository
 * @package App\Repositories
 * @version October 7, 2021, 5:34 pm +07
*/

class AnswerRepository extends BaseRepository
{
    use CommonBusiness;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'question_id',
        'option_id',
        'answer_by',
        'survey_id',
        'point'
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
        return Answer::class;
    }

    public function create($input) {

        DB::beginTransaction();

        $user = auth()->user();

        $input["answer_by"] = $user->id;

        $answer_data = $input['answer_data'];

        $answers = $answer_data["answers"];
    
        $success = false;

        try {
            //code...
            foreach ($answers as $key => $answer) {

                if (isset($answer["answer_id"]) && $answer["answer_id"] != null) {

                    $model = $this->model->newQuery();

                    $newAnswer = $model->where('answer_id', $answer["answer_id"])->where('answer_by', $user->id)->first();
                    if ($newAnswer) {

                        $newAnswer->question_id = $answer["question_id"];

                        $newAnswer->option_id = implode(',', $answer["option_id"]);

                        $newAnswer->save();
                    }
                } else {
                    $input["question_id"] = $answer["question_id"];

                    $input["option_id"] = implode(',', $answer["option_id"]);

                    $model = $this->model->newInstance($input);

                    $model->save();
                }
            }

            $success = true;

        } catch (\Throwable $th) {
            //throw $th;
            $success = false;
            DB::rollBack();
        }

        if ($success) {

            $survey = Survey::where('survey_id', $input["survey_id"])->with('answers')->with('answers.question')->first();

            $answers = $this->model->newQuery()->where('survey_id', $input["survey_id"])->where('answer_by', $user->id)->get();

            $course_student = CourseStudent::where("course_id", $input["course_id"])->where('student_id', $user->id)->first();

            $percent_achieved = 0;

            if (count($answers)) {

                foreach ($answers as $key => $answer) {

                    $option_ids = explode(',', $answer->option_id);

                    if ($answer->question->question_type == "MultipleChoice") {

                        $options = QuestionOption::whereIn('option_id', $option_ids)->get();
            
                        $rightOptions = $answer->question->right_options->pluck('option_id')->toArray();     
                        
                        $countRightOption = 0;
                        foreach ($options as $key => $option) {
                            if ($option->right_answer == true) {
                                $countRightOption = $countRightOption + 1;
                            }
                        }

                        if (count($rightOptions) == $countRightOption && count($option_ids) == count($rightOptions)) {

                            $percent_achieved = $percent_achieved + $answer->question->percent_achieved;
                        }else {
                            $percent_achieved = $percent_achieved + 0;
                        }

                    }else {
                        $option = QuestionOption::whereIn('option_id', $option_ids)->first();
                        if ($option && $option->right_answer == true) {
                            $percent_achieved = $percent_achieved + $answer->question->percent_achieved;
                        }else {
                            $percent_achieved = $percent_achieved + 0;
                        }

                    }

                }
            }

            $learning_process = LearningProcess::where('course_id', $input["course_id"])
            ->where('survey_id', $input["survey_id"])
            ->where('student_id', $user->id)
            ->first();

            if ($learning_process) {
                if (!$learning_process->started_at) {
                    $learning_process->started_at = date(Carbon::now());
                    if ($course_student && !$course_student->started_at) {
                        $course_student->started_at = date(Carbon::now());
                        $course_student->save();
                    }
                }

                if (isset($input["isDraft"])) {
                    $learning_process->isDraft = $input["isDraft"];

                    if (!$input["isDraft"]) {
                        $learning_process->completed_at = date(Carbon::now());
                    }
                }

                if ($percent_achieved >= $survey->percent_to_pass && isset($input["isDraft"]) && !$input["isDraft"]) {
                    $learning_process->isPassed = true;
                }

                $learning_process->process = $percent_achieved;
                $learning_process->save();

                $full_learning_processes = LearningProcess::where('course_id', $input["course_id"])
                ->where('student_id', $user->id)
                ->pluck('isPassed')->toArray();

                $has_learning_processes = array_filter($full_learning_processes, function($value) {
                    return $value;
                });

                $average_learning_processes = count($has_learning_processes)/ count($full_learning_processes) * 100;

                if ($course_student) {
                    $course_student->percent_finish = $average_learning_processes;
                    $course_student->save();
                }

                $my_learning_processes = LearningProcess::where('course_id', $input["course_id"])->where('student_id', $user->id)
                ->selectRaw("count(case when isPassed then 1 else null end) as number_passes, count(process_id) as number_processes")->first();

                if ($my_learning_processes && isset($input["isDraft"]) && $input["isDraft"] == 0) {
                    // $course_student = CourseStudent::where("course_id", $input["course_id"])->where('student_id', $user->id)->first();
                    if ($course_student) {
                        if ($my_learning_processes->number_passes == $my_learning_processes->number_processes) {
                            // var_dump($user->name);
                            $job = (new PushNotificationWhenStudentCompletedCourse($input["course_id"], $user));
                            dispatch($job);
                            // $this->pushNotificationForUser('teacher,employee,student');
                            $course_student->isPassed = true;
                            $course_student->completed_at = date(Carbon::now());
                            $course_student->save();
                        }
                    }

                }
            }
        }

        DB::commit();

        $response["success"] = $success;

        return $response;

    }

    public function findBySurvey ($id, $userId = null) {

        $query = $this->model->newQuery();

        $model = $query->where('survey_id', $id);

        if ($userId) {
            $model = $model->where('answer_by', $userId);
        }

        $model = $model->first();

        return $model;
    }

    public function getResultDoingSurvey ($id, $userId = null) {

        $query = $this->model->newQuery();

        $model = $query->where('survey_id', $id);

        if ($userId) {
            $model = $model->where('answer_by', $userId);
        }

        $model = $model->get();

        return $model;
    }

    public function deleteOldAnswers ($survey_id, $user_id) {
        $query = $this->model->newQuery();

        $model = $query->where('survey_id', $survey_id)->where('answer_by', $user_id)->delete();

        return $model;

    }
    
}

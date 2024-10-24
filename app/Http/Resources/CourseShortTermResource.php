<?php

namespace App\Http\Resources;

use App\Models\Chapter;
use App\Models\ChapterLesson;
use App\Models\CourseStudent;
use App\Models\LearningProcess;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Yajra\DataTables\Html\Editor\Fields\Boolean;
use Illuminate\Support\Facades\Auth;


class CourseShortTermResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {


        $user = Auth::guard('api')->user();

        $chapters = Chapter::where('course_id', $this->course_id)->with('lessons')->with('survey')->get();


        $course_resources["chapters"] = ChapterShortTermResource::collection($chapters);

        $studentCourse = CourseStudent::where('student_id', '=', $user->id)->where('course_id', '=', $this->course_id)->first();

        $stepLearning = LearningProcess::where('course_id', '=', $this->course_id)->where('isPassed', 'false')->first();

        $number_course_lesson = collect($course_resources["chapters"])->pluck('number_lesson')->sum() ;

        $numberLearning = LearningProcess::where('course_id', '=', $this->course_id)->where('isPassed', true)->count();

        $percentDoneLear = 0;
        if($number_course_lesson > 0 ){
            $percentDoneLear = number_format(($numberLearning / $number_course_lesson ) * 100 , 2);
        }

        $learningProcess = ($user) ? User::find($user->id)->learningProcess($this->course_id): [];

        $students = $this->students;


        $scoreRating = CourseStudent::whereNotNull('rating')->where('course_id', $this->course_id)->selectRaw("sum(rating) as scoreRating, count(case when rating > 0 then 1 else null end) as rating_round ")->first();

        $isCompletedCourse = 0;
        $isConfirmNotice = 0;
        $percentResult = "";
        $completed_at = "";
        $rating = "";
        $comment = "";

        $resultACourse = $this->studentResultCourse($this->course_id, $user->id);

        if ($resultACourse && $resultACourse->isPassed && $resultACourse->completed_at) {
            $isCompletedCourse = true;
            $isConfirmNotice = $resultACourse->isNoticed;
            $percentResult = $resultACourse->percent_finish;
            $rating = $resultACourse->rating;
            $comment = $resultACourse->comment;
            $completed_at = $resultACourse->completed_at;
        }

        $response =
        [
            'course_id' => $this->course_id,
            'course_name' => $this->course_name,
            'course_created_by' => $this->course_created_by,
            'created_by' => new AuthorResource($this->createdBy),
            'teacher_id' => $this->teacher_id,
            'teacher' => new AuthorResource($this->teacher),
            'course_feature_image' => $this->course_feature_image,
            'certificate_image' => $this->certificate_image,
            'course_description' => $this->course_description,
            'course_target' => $this->course_target ? json_decode($this->course_target) : "",
            'category_id' => $this->category_id,
            'category' => $this->category->category_name,
            'isDraft' => $this->isDraft,
            'is_published' => $this->is_published,
            'course_type' => $this->course_type,
            'unit_currency' => $this->unit_currency,
            'created_at' => $this->created_at->format('d-m-Y H:i'),
            'updated_at' => $this->updated_at->format('m/Y'),
            'course_resources' => $course_resources,
            'learningProcess' => $learningProcess,
            'number_of_students' => count($students),
            'scoreRating' => $scoreRating->scoreRating,
            'rating_round' => $scoreRating->rating_round,
            'isCompletedCourse' => $isCompletedCourse,
            'isConfirmNotice' => $isConfirmNotice,
            'percentResult' => $percentResult,
            'comment' => $comment,
            'rating' => $rating,
            'completed_at' => $completed_at ? $completed_at->format('Y-m-d H:i') : "",
            'course_sale_price' => $this->course_sale_price,
            'number_course_lesson' => $number_course_lesson,
            'number_learning' => $numberLearning,
            'percent_done' => $percentDoneLear,
            'step_learning' => $stepLearning,
            'is_register' => ($studentCourse) ? (int)true : (int)false
        ];

        if (!empty($this->course_price)) {
            $response["course_price"] = $this->course_price;
        }

//        if (!empty($this->course_sale_price)) {
//            $response["course_sale_price"] = $this->course_sale_price;
//        }

        if (!empty($this->deadline)) {
            $response["deadline"] = $this->deadline->format('Y-m-d H:i');
        }

        if (!empty($this->startTime)) {
            $response["startTime"] = $this->startTime->format('Y-m-d H:i');
        }

        if (!empty($this->endTime)) {
            $response["endTime"] = $this->endTime->format('Y-m-d H:i');
        }

        return $response;
    }
}

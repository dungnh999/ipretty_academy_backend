<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Jobs\PushNotificationWhenStudentCompletedCourse;
use App\Models\CourseCategoryTypes;
use App\Models\CourseStudent;
use App\Models\LearningProcess;
use App\Models\Lesson;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

// use FFMpeg;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class LessonRepository
 * @package App\Repositories
 * @version September 8, 2021, 5:13 pm UTC
 */
class LessonRepository extends BaseRepository
{
    use CommonBusiness;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'lesson_name',
        'lesson_description',
        'lesson_content',
        'lesson_attachment',
        'lesson_author',
        'main_attachment',
        'lesson_duration'
    ];

    protected $relations = ['user'];

    protected $relationSearchable = [
        'name'
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
        return Lesson::class;
    }

    public function create($input, $request = null)
    {


        $model = $this->model->newInstance($input);

        $user = auth()->user();

        $model->lesson_author = $user->id;

        $model->save();

        if ($request->get('type_update') != true) {
            $mainAttachment = CommonBusiness::handleMedia($model, $request, MEDIA_COLLECTION["LESSON_MAIN_ATTACHMENT"], true);
            if ($mainAttachment) {
                $pathInfo = pathinfo($mainAttachment->file_name);

                $durationInSeconds = 0;

                if (
                    $pathInfo["extension"] == 'mp4'
                    || $pathInfo["extension"] == 'wmv'
                    || $pathInfo["extension"] == 'avi'
                ) {
                    $path = $mainAttachment->id . '/' . $mainAttachment->file_name;

                    $media = FFMpeg::fromDisk('public')->open($path);

                    $durationInSeconds = $media->getDurationInSeconds();

                }

                var_dump($mainAttachment->getUrl());

                $model->main_attachment = $mainAttachment->getUrl();

                $model->lesson_duration = $durationInSeconds;

                $model->save(); //remember to save again

                $response["main_media"] = true;

            } else {

                $model->main_attachment = null;

                $model->save();

                $response["main_media"] = false;

            }

            $lessonAttachments = CommonBusiness::handleMedia($model, $request, MEDIA_COLLECTION["LESSON_ATTACHMENT"]);

            if ($lessonAttachments) {

                $model->lesson_attachment = $lessonAttachments;

                $model->save(); //remember to save again

                $response["media"] = true;

            } else {

                $model->lesson_attachment = null;

                $model->save();

                $response["media"] = false;

            }
        } else {
            $model->main_attachment = $request->get('main_attachment');
            $response["media"] = true;
        }

        if ($request->file(MEDIA_COLLECTION['LESSON_MATERIAL'])) {

            $lessonFile = CommonBusiness::handleMediaJsonFull($model, $request, MEDIA_COLLECTION["LESSON_MATERIAL"]);
            $model->lesson_material = $lessonFile;
            $model->save(); //remember to save again
        }

        $response["lesson"] = $model;

        return $model;
    }

    public function allLesson($params = null)
    {

        $query = $this->model->newQuery()->with('user')->orderBy('created_at', 'desc');

        if (!empty($params['keyword'])) {
            $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
        }

        $model = $query->paginate(PERPAGE);

        return $model;

    }

    public function detail($request)
    {
        $query = $this->model->newQuery();
        return $query->findOrFail($request['lesson_id']);
    }

    public function updateLesson($request)
    {
        $dataListFileOld = $request->get('lesson_material_file_old');
        $query = $this->model->newQuery();
        $model = $query->findOrFail($request['lesson_id']);
        $model->lesson_name = $request->get('lesson_name');
        $model->lesson_description = $request->get('lesson_description');
        $model->lesson_description = $request->get('is_demo');
        $model->main_attachment = $request->get('main_attachment');
        if ($request->file(MEDIA_COLLECTION['LESSON_MATERIAL'])) {
            $lessonFile = CommonBusiness::handleMediaJsonFull($model, $request, MEDIA_COLLECTION["LESSON_MATERIAL"]);
            if(!$dataListFileOld) {
                $model->lesson_material = $lessonFile;
                $model->save(); //remember to save again
            } else {
                $listFileOld = $dataListFileOld;
                $listfileNew = json_decode($lessonFile, true);
                $listFileSave = array_merge($listFileOld, $listfileNew);
                $model->lesson_material = json_encode($listFileSave);
                $model->save(); //remember to save again
            }
        } else {
            $model->lesson_material = json_encode($dataListFileOld);
            $model->save(); //remember to save again
        }
        return $model;
    }

    public function update($input, $id, $request = null)
    {

        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $response["main_media"] = true;

        $response["media"] = true;

        if ($request->file(MEDIA_COLLECTION["LESSON_MAIN_ATTACHMENT"])) {

            $mainAttachment = CommonBusiness::handleMedia($model, $request, MEDIA_COLLECTION["LESSON_MAIN_ATTACHMENT"], true);

            if ($mainAttachment) {

                $pathInfo = pathinfo($mainAttachment->file_name);

                $durationInSeconds = 0;
                // if (
                //     $pathInfo["extension"] == 'mp4'
                //     || $pathInfo["extension"] == 'wmv'
                //     || $pathInfo["extension"] == 'avi'
                // ) {
                //     $path = $mainAttachment->id . '/' . $mainAttachment->file_name;

                //     $media = FFMpeg::fromDisk('public')->open($path);

                //     $durationInSeconds = $media->getDurationInSeconds();
                // }
                // var_dump($mainAttachment->getUrl());

                $input["main_attachment"] = $mainAttachment->getUrl();

                // $input["lesson_duration"] = $durationInSeconds;

                $response["main_media"] = true;
            } else {

                $input["main_attachment"] = $model->main_attachment;

                // $input["lesson_duration"] = $model->lesson_duration;

                $response["main_media"] = false;
            }
        }

        if ($request->file(MEDIA_COLLECTION["LESSON_ATTACHMENT"])) {

            $lessonAttachments = CommonBusiness::handleMedia($model, $request, MEDIA_COLLECTION["LESSON_ATTACHMENT"]);

            // dd($lessonAttachments);

            if ($lessonAttachments) {

                if ($model->lesson_attachment) {

                    // dd($model->lesson_attachment);

                    $input["lesson_attachment"] = $model->lesson_attachment . ',' . $lessonAttachments;
                } else {

                    $input["lesson_attachment"] = $lessonAttachments;
                }

                $response["media"] = true;
            } else {

                $input["lesson_attachment"] = $model->lesson_attachment;

                $response["media"] = false;
            }
        }

        $model->fill($input);

        $model->save();

        $response["lesson"] = $model;

        return $response;
    }

    public function destroyMedia($media, $id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $mediaUrl = $media->getUrl();

        $lessonAttachments = explode(',', $model->lesson_attachment);

        $lessonAttachments = array_diff_key($lessonAttachments, [$mediaUrl]);

        $model->lesson_attachment = implode(',', $lessonAttachments);

        $model->save();

        $media->delete();

        return $model;
    }

    public function finishLesson($input, $learning_process)
    {

        $user = auth()->user();

        $query = $this->model->newQuery();

        $model = $query->findOrFail($input["lesson_id"]);

        $response = [
            "finished" => true
        ];

        if ($model) {

            $learning_process->started_at = date(Carbon::now());
            $learning_process->save();

            $duration = $model->lesson_duration;
            $view_duration = $input["view_duration"];

            if (($view_duration + 10) < $duration) {

                $response["finished"] = false;
                return $response;
            }

            $learning_process->process = 100;
            $learning_process->isPassed = 1;
            $learning_process->completed_at = date(Carbon::now());
            $learning_process->save();

            $course_student = CourseStudent::where('course_id', $input["course_id"])->where('student_id', $user->id)->first();

            $full_learning_processes = LearningProcess::where('course_id', $input["course_id"])
                ->where('student_id', $user->id)
                ->pluck('isPassed')->toArray();

            $has_learning_processes = array_filter($full_learning_processes, function ($value) {
                return $value;
            });

            $average_learning_processes = count($has_learning_processes) / count($full_learning_processes) * 100;
// dd($average_learning_processes);
            if ($course_student) {
                $course_student->started_at = date(Carbon::now());
                $course_student->percent_finish = $average_learning_processes;
                $course_student->save();
            }

            $my_learning_processes = LearningProcess::where('course_id', $input["course_id"])->where('student_id', $user->id)
                ->selectRaw("count(case when isPassed then 1 else null end) as number_passes, count(process_id) as number_processes")->first();

            if ($my_learning_processes) {
                // $course_student = CourseStudent::where("course_id", $input["course_id"])->where('student_id', $user->id)->first();
                if ($course_student) {
                    if ($my_learning_processes->number_passes == $my_learning_processes->number_processes) {
                        $job = (new PushNotificationWhenStudentCompletedCourse($input["course_id"], $user));
                        dispatch($job);
                        // $this->pushNotificationForUser('teacher,employee,student');
                        $course_student->isPassed = true;
                        $course_student->completed_at = date(Carbon::now());
                        $course_student->save();
                    }
                }
            }
            return $response;
        }
    }

    public function step($request) {
            return 1;
//        $query = $this->model->newQuery();
//        return $query->where('uuid', '=', $request->get('id'))->first();
    }
}

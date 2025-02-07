<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLessonAPIRequest;
use App\Http\Requests\API\UpdateLessonAPIRequest;
use App\Models\Lesson;
use App\Repositories\LessonRepository;
use App\Repositories\CourseLessonChapterReposity;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\DeleteLessonAPIRequest;
use App\Http\Requests\API\FinishLessonAPIRequest;
use App\Http\Resources\LearningProcessResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\LessonStepResource;
use App\Models\LearningProcess;
use App\Repositories\LearningProcessRepository;
use App\Repositories\MediaRepository;
use Response;

/**
 * Class LessonController
 * @package App\Http\Controllers\API
 */

class LessonAPIController extends AppBaseController
{
    /** @var  LessonRepository */
    private $lessonRepository;
    private $courseLessonChapterRepository;
    private $mediaRepository;
    private $learningProcessRepository;

    public function __construct(CourseLessonChapterReposity $courseLessonChapterRepository ,LessonRepository $lessonRepo, MediaRepository $mediaRepository, LearningProcessRepository $learningProcessRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->lessonRepository = $lessonRepo;
        $this->mediaRepository = $mediaRepository;
        $this->learningProcessRepository = $learningProcessRepository;
        $this->courseLessonChapterRepository = $courseLessonChapterRepository;
    }

    public function index(Request $request)
    {

        $params = request()->query();

        $lessons = $this->lessonRepository->allLesson($params);

        return $this->sendResponse(
            $lessons,
            __('messages.retrieved', ['model' => __('models/lessons.plural')])
        );
    }

    public function step(Request $request)
    {
        $lesson = $this->courseLessonChapterRepository->step($request);
        return $this->sendResponse(
            new LessonStepResource($lesson),
            __('messages.retrieved', ['model' => __('models/lessons.singular')])
        );
    }

    public function store(CreateLessonAPIRequest $request)
    {
        $input = $request->all();
        // dd($input["main_attachment"]);
        $result = $this->lessonRepository->create($input, $request);

        if ($result["media"] && $result["main_media"]) {
            return $this->sendResponse(
                new LessonResource($result["lesson"]),
                __('messages.saved', ['model' => __('models/lessons.singular')])
            );
        }
        return $this->sendResponseWithError(
            new LessonResource($result["lesson"]),
            __('messages.saved', ['model' => __('models/lessons.singular')]),
            !$result["media"] ? __('messages.errors.upload_attachment_fail', ['model' => __('models/lessons.fields.lesson_attachment')])
            : __('messages.errors.upload_video_fail', ['model' => __('models/lessons.fields.main_attachment')])
        );

    }

    public function show($id)
    {
        /** @var Lesson $lesson */
        $lesson = $this->lessonRepository->find($id);

        if (empty($lesson)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/lessons.singular')])
            );
        }

        return $this->sendResponse(
            new LessonResource($lesson),
            __('messages.retrieved', ['model' => __('models/lessons.singular')])
        );
    }

    public function update($id, UpdateLessonAPIRequest $request)
    {
        // dd($request->all());

        $input = $request->all();

        /** @var Lesson $lesson */
        $lesson = $this->lessonRepository->find($id);

        if (empty($lesson)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/lessons.singular')])
            );
        }

        $result = $this->lessonRepository->update($input, $id, $request);

        // return $this->sendResponse(
        //     new LessonResource($lesson),
        //     __('messages.updated', ['model' => __('models/lessons.singular')])
        // );

        if ($result["media"] && $result["main_media"]) {

            if($input['delete_lesson_attachment']) {

                $delete_lesson_attachment = $input['delete_lesson_attachment'];
    
                $deletelessonattachments = explode(',', $delete_lesson_attachment);

                if (count($deletelessonattachments)) {
    
                    foreach ($deletelessonattachments as $deletelessonattachment) {
        
                        $media = $this->mediaRepository->findByModelAndId($id, $deletelessonattachment);

                        if ($media) {
                            $this->lessonRepository->destroyMedia($media, $id);
                        }
        
                    }

                }
    
            }

            return $this->sendResponse(

                new LessonResource($result["lesson"]),

                __('messages.updated', ['model' => __('models/lessons.singular')])

            );
        }
        return $this->sendResponseWithError(
            new LessonResource($result["lesson"]),
            __('messages.saved', ['model' => __('models/lessons.singular')]),
            !$result["media"] ? __('messages.errors.upload_attachment_fail', ['model' => __('models/lessons.fields.lesson_attachment')])
            : __('messages.errors.upload_video_fail', ['model' => __('models/lessons.fields.main_attachment')])
        );
    }

    public function destroy(DeleteLessonAPIRequest $request)
    {
        /** @var Lesson $lesson */

        $lessonIds = explode(',', $request->lesson_ids);

        $notFoundLessons = [];

        $foundLessons = [];

        foreach($lessonIds as $id) {

            $lesson = $this->lessonRepository->find($id);

            if (empty($lesson)) {
                array_push($notFoundLessons, $id);
            }else {
                array_push($foundLessons, $lesson);
            }
        }

        if (count($notFoundLessons)) {

            return $this->sendError(
                __('messages.not_found', ['model' => __('models/lessons.singular')]),
                404,
                $notFoundLessons
            );
            
        }            

        if (count($foundLessons)) {
            foreach ($foundLessons as $lesson) {

                $lesson->delete();
            }
        }

        $lessons = $this->lessonRepository->allLesson();

        return $this->sendResponse(
            $lessons,
            __('messages.deleted', ['model' => __('models/lessons.singular')])
        );
    }

    public function deleteAttachment ($id, Request $request) {

        $lesson = $this->lessonRepository->find($id);

        if (empty($lesson)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/lessons.singular')])
            );
        }

        $media = $this->mediaRepository->findByModelAndId($id, $request->media_id);
        

        if (empty($media)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/lessons.fields.lesson_attachment')])
            );
        }  

        $deletemedia = $this->lessonRepository->destroyMedia($media, $id);

        return $this->sendResponse(
            $deletemedia,
            __('messages.deleted', ['model' => __('models/lessons.fields.lesson_attachment')])
        );

    }

    public function finishLesson (FinishLessonAPIRequest $request) {

        $user = auth()->user();

        $learningProcess = $this->learningProcessRepository->checkValidProcessByLesson($request->course_id, $request->lesson_id, $user->id);

        if(empty($learningProcess)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/learningProcesses.singular')])
            );
        }

        if ($learningProcess->isPassed) {
            return $this->sendResponse(
                new LearningProcessResource($learningProcess),
                __('messages.updated', ['model' => __('models/learningProcesses.singular')])
            );
        }

        $lesson = $this->lessonRepository->find($request->lesson_id);

        if ($lesson) {

            $duration = $lesson->lesson_duration;
            $view_duration = $request->view_duration;

            if ( ($view_duration + 10) < $duration ) {

                return $this->sendError(
                    __('messages.not_finished_video')
                );
            }
        }
        
        $input = $request->all();

        $response = $this->lessonRepository->finishLesson($input, $learningProcess);

        if ($response["finished"] == true) {

            $learningProcess = $this->learningProcessRepository->findByLesson($request->course_id, $request->lesson_id, $user->id);
            
            return $this->sendResponse(
                    new LearningProcessResource($learningProcess),
                    __('messages.updated', ['model' => __('models/learningProcesses.singular')])
                );

        }
    }
}

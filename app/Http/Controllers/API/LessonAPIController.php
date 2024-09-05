<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLessonAPIRequest;
use App\Http\Requests\API\UpdateLessonAPIRequest;
use App\Models\Lesson;
use App\Repositories\LessonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\DeleteLessonAPIRequest;
use App\Http\Requests\API\FinishLessonAPIRequest;
use App\Http\Resources\LearningProcessResource;
use App\Http\Resources\LessonResource;
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
    private $mediaRepository;
    private $learningProcessRepository;

    public function __construct(LessonRepository $lessonRepo, MediaRepository $mediaRepository, LearningProcessRepository $learningProcessRepository)
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
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/lessons",
     *      summary="Get a listing of the Lessons.",
     *      tags={"Lesson"},
     *      description="Get all Lessons",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Lesson")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {

        $params = request()->query();

        $lessons = $this->lessonRepository->allLesson($params);

        return $this->sendResponse(
            $lessons,
            __('messages.retrieved', ['model' => __('models/lessons.plural')])
        );
    }

    /**
     * @param CreateLessonAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/lessons",
     *      summary="Store a newly created Lesson in storage",
     *      tags={"Lesson"},
     *      description="Store Lesson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Lesson that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Lesson")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Lesson"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/lessons/{id}",
     *      summary="Display the specified Lesson",
     *      tags={"Lesson"},
     *      description="Get Lesson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Lesson",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Lesson"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @param int $id
     * @param UpdateLessonAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/lessons/{id}",
     *      summary="Update the specified Lesson in storage",
     *      tags={"Lesson"},
     *      description="Update Lesson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Lesson",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Lesson that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Lesson")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Lesson"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/lessons/{id}",
     *      summary="Remove the specified Lesson from storage",
     *      tags={"Lesson"},
     *      description="Delete Lesson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Lesson",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
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

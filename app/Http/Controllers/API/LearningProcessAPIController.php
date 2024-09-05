<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLearningProcessAPIRequest;
use App\Http\Requests\API\UpdateLearningProcessAPIRequest;
use App\Models\LearningProcess;
use App\Repositories\LearningProcessRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\LearningProcessResource;
use App\Http\Resources\UserResource;
use App\Repositories\CourseRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Response;

/**
 * Class LearningProcessController
 * @package App\Http\Controllers\API
 */

class LearningProcessAPIController extends AppBaseController
{
    /** @var  LearningProcessRepository */
    private $learningProcessRepository;
    private $userRepository;
    private $courseRepository;

    public function __construct(LearningProcessRepository $learningProcessRepo, UserRepository $userRepository, CourseRepository $courseRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->learningProcessRepository = $learningProcessRepo;
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Display a listing of the LearningProcess.
     * GET|HEAD /learningProcesses
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $learningProcesses = $this->learningProcessRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            LearningProcessResource::collection($learningProcesses),
            __('messages.retrieved', ['model' => __('models/learningProcesses.plural')])
        );
    }

    /**
     * Store a newly created LearningProcess in storage.
     * POST /learningProcesses
     *
     * @param CreateLearningProcessAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateLearningProcessAPIRequest $request)
    {
        $input = $request->all();

        $learningProcess = $this->learningProcessRepository->create($input);

        return $this->sendResponse(
            new LearningProcessResource($learningProcess),
            __('messages.saved', ['model' => __('models/learningProcesses.singular')])
        );
    }

    /**
     * Display the specified LearningProcess.
     * GET|HEAD /learningProcesses/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var LearningProcess $learningProcess */
        $learningProcess = $this->learningProcessRepository->find($id);

        if (empty($learningProcess)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/learningProcesses.singular')])
            );
        }

        return $this->sendResponse(
            new LearningProcessResource($learningProcess),
            __('messages.retrieved', ['model' => __('models/learningProcesses.singular')])
        );
    }

    /**
     * Update the specified LearningProcess in storage.
     * PUT/PATCH /learningProcesses/{id}
     *
     * @param int $id
     * @param UpdateLearningProcessAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLearningProcessAPIRequest $request)
    {
        $input = $request->all();

        /** @var LearningProcess $learningProcess */
        $learningProcess = $this->learningProcessRepository->find($id);

        if (empty($learningProcess)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/learningProcesses.singular')])
            );
        }

        $learningProcess = $this->learningProcessRepository->update($input, $id);

        return $this->sendResponse(
            new LearningProcessResource($learningProcess),
            __('messages.updated', ['model' => __('models/learningProcesses.singular')])
        );
    }

    /**
     * Remove the specified LearningProcess from storage.
     * DELETE /learningProcesses/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var LearningProcess $learningProcess */
        $learningProcess = $this->learningProcessRepository->find($id);

        if (empty($learningProcess)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/learningProcesses.singular')])
            );
        }

        $learningProcess->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/learningProcesses.singular')])
        );
    }

    public function getLearningProcess (Request $request) {
        $user = auth()->user();

        $learningProcess = $this->learningProcessRepository->findByCondition($request->course_id, $request->survey_id, $user->id);

        if (empty($learningProcess)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/learningProcesses.singular')])
            );
        }

        $learningProcess["rework"] = false;

        if ($learningProcess->completed_at && $learningProcess->completed_at < Carbon::now()->subDays(1)) {
            $learningProcess["rework"] = true;
        }

        return $this->sendResponse(
            new LearningProcessResource($learningProcess),
            __('messages.retrieved', ['model' => __('models/learningProcesses.singular')])
        );
    }

    public function getLearningProcessesForUser ($course_id, $student_id) {

        $course = $this->courseRepository->find($course_id, ['course_id', 'course_name']);
        $learningProcesses = $course->chaptersWithLessonSurveyForUser($course_id, $student_id);
        
        $user = $this->userRepository->find($student_id);
        $user["isShorterm"] = true;

        $response["student"] = new UserResource($user);
        $response["learningProcesses"] = $learningProcesses;
        
        return $this->sendResponse(
            $response,
            __('messages.retrieved', ['model' => __('models/learningProcesses.plural')])
        );

    }
}

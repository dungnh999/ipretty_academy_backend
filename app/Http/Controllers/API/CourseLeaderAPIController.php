<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCourseLeaderAPIRequest;
use App\Http\Requests\API\UpdateCourseLeaderAPIRequest;
use App\Models\CourseLeader;
use App\Repositories\CourseLeaderRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CourseLeaderResource;
use App\Models\User;
use App\Repositories\CourseRepository;
use App\Repositories\EventRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Response;

/**
 * Class CourseLeaderController
 * @package App\Http\Controllers\API
 */

class CourseLeaderAPIController extends AppBaseController
{
    /** @var  CourseLeaderRepository */
    private $courseLeaderRepository;
    private $courseRepository;
    private $eventRepository;

    public function __construct(CourseLeaderRepository $courseLeaderRepo, CourseRepository $courseRepository, UserRepository $userRepository, EventRepository $eventRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->courseLeaderRepository = $courseLeaderRepo;
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the CourseLeader.
     * GET|HEAD /courseLeaders
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $courseLeaders = $this->courseLeaderRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            CourseLeaderResource::collection($courseLeaders),
            __('messages.retrieved', ['model' => __('models/courseLeaders.plural')])
        );
    }

    /**
     * Store a newly created CourseLeader in storage.
     * POST /courseLeaders
     *
     * @param CreateCourseLeaderAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCourseLeaderAPIRequest $request)
    {
        $input = $request->all();

        $courseLeader = $this->courseLeaderRepository->create($input);

        return $this->sendResponse(
            new CourseLeaderResource($courseLeader),
            __('messages.saved', ['model' => __('models/courseLeaders.singular')])
        );
    }

    /**
     * Display the specified CourseLeader.
     * GET|HEAD /courseLeaders/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CourseLeader $courseLeader */
        $courseLeader = $this->courseLeaderRepository->find($id);

        if (empty($courseLeader)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseLeaders.singular')])
            );
        }

        return $this->sendResponse(
            new CourseLeaderResource($courseLeader),
            __('messages.retrieved', ['model' => __('models/courseLeaders.singular')])
        );
    }

    /**
     * Update the specified CourseLeader in storage.
     * PUT/PATCH /courseLeaders/{id}
     *
     * @param int $id
     * @param UpdateCourseLeaderAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCourseLeaderAPIRequest $request)
    {
        $input = $request->all();

        /** @var CourseLeader $courseLeader */
        $courseLeader = $this->courseLeaderRepository->find($id);

        if (empty($courseLeader)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseLeaders.singular')])
            );
        }

        $courseLeader = $this->courseLeaderRepository->update($input, $id);

        return $this->sendResponse(
            new CourseLeaderResource($courseLeader),
            __('messages.updated', ['model' => __('models/courseLeaders.singular')])
        );
    }

    /**
     * Remove the specified CourseLeader from storage.
     * DELETE /courseLeaders/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CourseLeader $courseLeader */
        $courseLeader = $this->courseLeaderRepository->find($id);

        if (empty($courseLeader)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseLeaders.singular')])
            );
        }

        $courseLeader->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/courseLeaders.singular')])
        );
    }

    public function addLeadersIntoCourse(Request $request)
    {

        $course = $this->courseRepository->find($request->course_id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $now = date(Carbon::now());

        if ((isset($course->startTime) && $course->endTime != null && $course->endTime < $now) ||
            (isset($course->deadline) && $course->deadline != null && $course->deadline < $now)
        ) {
            return $this->sendError(
                __('messages.course_is_ended', ['model' => __('models/courses.singular')]),
                422
            );
        }

        $leader_ids = explode(',', $request->leader_ids);

        // dd($leader_ids);

        $course_leaders = $course->leaders->pluck("id")->toArray();

        // dd($course_leaders);

        $deleteLeaderIds = array_filter($course_leaders, function ($leader_id) use ($leader_ids) {
            return !in_array($leader_id, $leader_ids);
        });

        // dd($deleteLeaderIds);


        if (count($deleteLeaderIds)) {
            foreach ($deleteLeaderIds as $key => $leader_id) {

                $this->courseLeaderRepository->deleteLeaderInCourse($request->course_id, $leader_id);
                $this->eventRepository->deleteEventStudent($leader_id, $course);

                $leader = User::find($leader_id);

                if (!$leader->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {
                    $leader->permissions()->detach();
                }

            }
        }

        if (count($leader_ids)) {
            $events = $course->events;

            foreach ($leader_ids as $key => $leader_id) {

                $foundLeader = $this->userRepository->checkValidUser($leader_id);

                $courseLeader = $this->courseLeaderRepository->isJoined($leader_id, $request->course_id);

                if (!$courseLeader && $foundLeader == 'localStudent') {
                    $input["leader_id"] = $leader_id;
                    $input["course_id"] = $request->course_id;

                    $this->courseLeaderRepository->create($input);
                    $this->eventRepository->createEventStudent($leader_id, $events);
                    
                    $leader = User::find($leader_id);
                    if (!$leader->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {
                        $leader->givePermissionTo(
                            PERMISSION["VIEW_COURSE"],
                            PERMISSION["MANAGE_STUDENTS"]
                        );
                        $leader->save();
                    }
                }
            }
        }

        return $this->sendResponse(
            $course->leaders,
            __('messages.added_leaders')
        );
    }

    public function removeLeaderOfCourse($course_id, $leader_id) {

        $courseLeader = $this->courseLeaderRepository->deleteLeaderInCourse($course_id, $leader_id);

        return $this->sendSuccess(
            __('messages.deleted', ['model' => __('models/courseLeader.singular')])
        );
    }
}

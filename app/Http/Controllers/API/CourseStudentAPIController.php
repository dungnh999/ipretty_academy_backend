<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCourseStudentAPIRequest;
use App\Http\Requests\API\UpdateCourseStudentAPIRequest;
use App\Models\CourseStudent;
use App\Repositories\CourseStudentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CourseStudentResource;
use App\Repositories\CourseRepository;
use App\Repositories\EventRepository;
use App\Repositories\LearningProcessRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Response;

/**
 * Class CourseStudentController
 * @package App\Http\Controllers\API
 */

class CourseStudentAPIController extends AppBaseController
{
    /** @var  CourseStudentRepository */
    private $courseStudentRepository;
    private $courseRepository;
    private $userRepository;
    private $learningProcessRepository;
    private $eventRepository;

    public function __construct(CourseStudentRepository $courseStudentRepo, CourseRepository $courseRepository, 
    UserRepository $userRepository, LearningProcessRepository $learningProcessRepository, EventRepository $eventRepository)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });

        $this->courseStudentRepository = $courseStudentRepo;
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
        $this->learningProcessRepository = $learningProcessRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the CourseStudent.
     * GET|HEAD /courseStudents
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $courseStudents = $this->courseStudentRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            CourseStudentResource::collection($courseStudents),
            __('messages.retrieved', ['model' => __('models/courseStudents.plural')])
        );
    }

    /**
     * Store a newly created CourseStudent in storage.
     * POST /courseStudents
     *
     * @param CreateCourseStudentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCourseStudentAPIRequest $request)
    {

        $course = $this->courseRepository->find($request->course_id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $now = date(Carbon::now());

        if ((isset($course->endTime) && $course->endTime != null && $course->endTime < $now) ||
            (isset($course->deadline) && $course->deadline != null && $course->deadline < $now)
            ) {
            return $this->sendError(
                __('messages.course_is_ended', ['model' => __('models/courses.singular')]), 422
            );
        }

        $user = auth()->user();

        $courseStudent = $this->courseStudentRepository->isJoined($user->id, $request->course_id);

        if ($courseStudent) {
            return $this->sendError(
                __('messages.joined'), 422
            );
        }

        $input = $request->all();

        $input['student_id'] = $user->id;

        $courseStudent = $this->courseStudentRepository->create($input);

        $this->learningProcessRepository->createProcessLearning($input, $user->id);

        return $this->sendResponse(
            new CourseStudentResource($courseStudent),
            __('messages.join_course')
        );
    }

    /**
     * Display the specified CourseStudent.
     * GET|HEAD /courseStudents/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CourseStudent $courseStudent */
        $courseStudent = $this->courseStudentRepository->find($id);

        if (empty($courseStudent)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseStudents.singular')])
            );
        }

        return $this->sendResponse(
            new CourseStudentResource($courseStudent),
            __('messages.retrieved', ['model' => __('models/courseStudents.singular')])
        );
    }

    /**
     * Update the specified CourseStudent in storage.
     * PUT/PATCH /courseStudents/{id}
     *
     * @param int $id
     * @param UpdateCourseStudentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCourseStudentAPIRequest $request)
    {
        $input = $request->all();

        /** @var CourseStudent $courseStudent */
        $courseStudent = $this->courseStudentRepository->find($id);

        if (empty($courseStudent)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseStudents.singular')])
            );
        }

        $courseStudent = $this->courseStudentRepository->update($input, $id);

        return $this->sendResponse(
            new CourseStudentResource($courseStudent),
            __('messages.updated', ['model' => __('models/courseStudents.singular')])
        );
    }

    /**
     * Remove the specified CourseStudent from storage.
     * DELETE /courseStudents/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CourseStudent $courseStudent */
        $courseStudent = $this->courseStudentRepository->find($id);

        if (empty($courseStudent)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseStudents.singular')])
            );
        }

        $courseStudent->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/courseStudents.singular')])
        );
    }

    public function addStudentsIntoCourse (Request $request) {

        $course = $this->courseRepository->find($request->course_id);
        // $eventStudents = $course->eventStudents(229)->toArray();
        // dd($eventStudents);
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

        $student_ids = explode(',', $request->student_ids);

        $course_students = $course->students->pluck("id")->toArray();

        $deleteStudentIds = array_filter($course_students, function($student_id) use($student_ids) {
            return !in_array($student_id, $student_ids);
        });

        if (count($deleteStudentIds)) {
            foreach ($deleteStudentIds as $key => $student_id) {

                $this->courseStudentRepository->deleteStudentInCourse($request->course_id, $student_id);

                $this->learningProcessRepository->deleteStudentProcessLearning($request->course_id, $student_id);

                $this->eventRepository->deleteEventStudent($student_id, $course);

            }
        }


        if (count($student_ids)) {
            $events = $course->events;

            foreach ($student_ids as $key => $student_id) {

                $foundStudent = $this->userRepository->checkValidUser($student_id);

                $courseStudent = $this->courseStudentRepository->isJoined($student_id, $request->course_id);

                if (!$courseStudent && ($foundStudent == "localStudent" || $foundStudent == "freeStudent")) {

                    $input["student_id"] = $student_id;
                    $input["course_id"] = $request->course_id;

                    $this->courseStudentRepository->create($input);
                    $this->learningProcessRepository->createProcessLearning($input, $student_id);
                    $this->eventRepository->createEventStudent($student_id, $events);
                    
                }
            }
        }
        $students = $this->courseRepository->find($request->course_id)->students;
        return $this->sendResponse(
            $students,
            __('messages.added_students')
        );

    }

    public function commentAndRatingCourse(Request $request, $course_id)
    {
        $user = auth()->user();
        $course = $this->courseRepository->find($course_id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $course_student = CourseStudent::where('course_id', $course_id)->where('student_id', $user->id)->first();
        if(!$course_student){
            return $this->sendError(
                __('messages.user_not_found_in_course')
            );
        }


        if ($course_student->isPassed != 1) {
            return $this->sendError(
                __('messages.user_not_complete_course')
            );
        }

        // if ($course_student->rating > 0 || $course_student->comment != null ) {
        //     return $this->sendError(
        //         __('messages.rated_or_commented')
        //     );
        // }

        $data = $request->all();
        $reponse = $this->courseRepository->commentAndRatingCourse($course_id,$user->id,$data);

        return $this->sendResponse(
            $reponse,
            __('messages.comment_course_successfully')
        );
    }

    public function getListRankCourseCategory()
    {
        $reponse = $this->courseRepository->getFeaturedCategory();
        return $this->sendResponse(
            $reponse,
            __('messages.get_list_rank_course_category_successfully')
        );
    }

    public function getListRankCourse()
    {
        $reponse = $this->courseRepository->getListRankCourse();
        return $this->sendResponse(
            $reponse,
            __('messages.get_list_rank_course_successfully')
        );
    }

    public function confirmNotice (Request $request) {
        $user = auth()->user();
        $course = $this->courseRepository->find($request->course_id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $courseStudent = $this->courseStudentRepository->isJoined($user->id, $request->course_id);

        if (!$courseStudent->isPassed) {
            return $this->sendError(
                __('messages.not_passed'), 422
            );
        }

        $courseStudent = $this->courseStudentRepository->confirmNotice($request->course_id, $user->id);

        return $this->sendResponse(
            new CourseStudentResource($courseStudent),
            __('messages.updated', ['model' => __('models/courseStudents.singular')])
        );

    }

    public function getListCommentAndRatingByCourse(){
        $reponse = $this->courseStudentRepository->getCommentAndRatingByCourse();
        return $this->sendSuccess(
            __('messages.get_list_comment_and_rating'), $reponse
        );
    }


    public function getListCommentAndRating(){
        $reponse = $this->courseStudentRepository->getCommentAndRating();
        return $this->sendSuccess(
            __('messages.get_list_comment_and_rating'), $reponse
        );
    }
}

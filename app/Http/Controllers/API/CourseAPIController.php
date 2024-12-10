<?php

namespace App\Http\Controllers\API;

use App\Contract\CommonBusiness;
use App\Exports\MemberCourseExport;
use App\Exports\ReportBusinessCoursesExport;
use App\Exports\ReportDetailCourseExport;
use App\Exports\StatistialCourseExport;
use App\Http\Requests\API\CreateCourseAPIRequest;
use App\Http\Requests\API\UpdateCourseAPIRequest;
use App\Models\Course;
use App\Repositories\CourseRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\DeleteCoursesAPIRequest;
use App\Http\Resources\CourseForUserResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseShortTermResource;
use App\Http\Resources\CourseShortTermForLandingResource;
use App\Jobs\AddMemberIntoEvent;
use App\Jobs\PushNotificationWhenNewCourse;
use App\Jobs\PushNotificationWhenUpdateCourse;
use App\Jobs\PushNotificationWhenUpdateListStudentCourse;
use App\Jobs\RemoveMemberOutEvent;
use App\Notifications\NewCourse;
use App\Repositories\CourseStudentRepository;
use App\Repositories\LearningProcessRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Auth;

/**
 * Class CourseController
 * @package App\Http\Controllers\API
 */
class CourseAPIController extends AppBaseController
{

    use CommonBusiness;

    /** @var  CourseRepository */
    private $courseRepository;
    private $userRepository;
    private $courseStudentRepository;
    private $learningProcessRepository;

    public function __construct(CourseRepository        $courseRepo, UserRepository $userRepository,
                                CourseStudentRepository $courseStudentRepository, LearningProcessRepository $learningProcessRepository)
    {
//    $this->middleware(function ($request, $next) {
//
//      $user = auth()->user();
//      if ($user) {
//        \App::setLocale($user->lang);
//      }
//      return $next($request);
//
//    });

        $this->courseRepository = $courseRepo;
        $this->userRepository = $userRepository;
        $this->courseStudentRepository = $courseStudentRepository;
        $this->learningProcessRepository = $learningProcessRepository;
    }

    public function index(Request $request)
    {
        $courses_ended = $this->courseRepository->checkCoursesEnded();

        $params = request()->query();

        $courses = $this->courseRepository->allCourses($params);

        return $this->sendResponse(
            $courses,
            __('messages.retrieved', ['model' => __('models/courses.plural')])
        );
    }

    /**
     * @OA\Get(
     *     path=route('courses.list'),
     *     summary="Danh sách khoá học",
     *     description="Lấy tất cả danh sách khoá học",
     *     tags={"Courses"},
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="data", type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="course_id", type="integer", example=5),
     *                             @OA\Property(property="course_name", type="string", example="Khoá học mới 01"),
     *                             @OA\Property(property="slug_course", type="string", example="khoa-hoc-moi-01"),
     *                             @OA\Property(property="course_created_by", type="integer", example=1),
     *                             @OA\Property(property="teacher_id", type="integer", example=6),
     *                             @OA\Property(property="course_feature_image", type="string",
     *                                 example="http://localhost:8000/public/6/Screenshot-2023-11-08-at-16.19.38.png"),
     *                             @OA\Property(property="course_description", type="string", example="NHS"),
     *                             @OA\Property(property="count_viewer", type="integer", example=153),
     *                             @OA\Property(property="category_id", type="integer", example=1),
     *                             @OA\Property(property="course_version", type="integer", example=6),
     *                             @OA\Property(property="course_price", type="integer", example=100000),
     *                             @OA\Property(property="created_at", type="string", example="2023-11-15 04:11"),
     *                             @OA\Property(property="updated_at", type="string", example="2024-01-18T06:05:49.000000Z"),
     *                             @OA\Property(property="deleted_at", type="string", nullable=true, example=null),
     *                             @OA\Property(property="deadline", type="string", nullable=true, example=null),
     *                             @OA\Property(property="course_type", type="string", example="Business"),
     *                             @OA\Property(property="is_published", type="integer", example=1),
     *                             @OA\Property(property="status", type="integer", example=1),
     *                             @OA\Property(property="startTime", type="string", example="2023-11-16 01:00"),
     *                             @OA\Property(property="endTime", type="string", example="2023-11-30 02:10"),
     *                             @OA\Property(property="course_sale_price", type="integer", example=50000),
     *                             @OA\Property(property="course_target",
     *                                 type="array",
     *                                 @OA\Items(
     *                                     type="object",
     *                                     @OA\Property(property="value", type="string", example="ĐẠT MỤC TIÊU")
     *                                 )
     *                             ),
     *                             @OA\Property(property="certificate_image", type="string",
     *                                 example="http://localhost:8000/public/7/Screenshot-2023-11-08-at-16.19.38.png"),
     *                             @OA\Property(property="isDraft", type="integer", example=1),
     *                             @OA\Property(property="unit_currency", type="string", example="VND"),
     *                             @OA\Property(property="published_at", type="string", nullable=true, example=null),
     *                             @OA\Property(property="teacher_name", type="string", example="Dr_Kwon"),
     *                             @OA\Property(property="category_name", type="string", example="LÀM ĐẸP")
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     * )
     */

    public function getListCourse(Request $request)
    {
        $params = request()->query();
        $courses = $this->courseRepository->getAllCourse($params);
        return $this->sendResponse(
            $courses,
            __('messages.retrieved', ['model' => __('models/courses.plural')])
        );
    }

    public function store(CreateCourseAPIRequest $request)
    {

        $input = $request->all();
        $response = $this->courseRepository->createCourse($input, $request);

        if (!$response["validJson"]) {

            $data['chapter'] = __('messages.is_required', ['model' => __('models/courses.fields.chapter')]);

            return $this->sendError(
                __('messages.course_is_required', ['model' => __('models/courses.fields.course_resources')]),
                422,
                $data
            );
        }

        if (count($response["isRequiredField"])) {
            $isRequiredField = $response["isRequiredField"];

            foreach ($isRequiredField as $key => $error) {
                $data[$key] = __('messages.is_required', ['model' => __('models/courses.fields.' . $error)]);
            }

            return $this->sendError(
                __('messages.course_is_required', ['model' => __('models/courses.fields.course_resources')]), 422,
                $data
            );
        }

        if (
            count($response["isNotFoundField"]["lessons"]) ||
            count($response["isNotFoundField"]["surveys"])
        ) {

            $isNotFoundField = $response["isNotFoundField"];

            foreach ($isNotFoundField as $key => $field) {

                if (!count($isNotFoundField[$key])) {
                    unset($isNotFoundField[$key]);
                }
            }

            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.fields.course_resources')]),
                422,
                $isNotFoundField
            );
        }

        if ($response["model"]) {

            if ($response["model"]->is_published) {

                $course = $response["model"];

                $job = (new PushNotificationWhenNewCourse($course));
                dispatch($job);

                // $students = $course->students;

                // $this->pushNotificationForUser('admin', $students);
            }

            return $this->sendResponse(
                new CourseResource($response["model"]),
                __('messages.created', ['model' => __('models/courses.singular')])
            );
        }
    }

    public function show($id)
    {

        /** @var Course $course */
        $course = $this->courseRepository->find($id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $now = date(Carbon::now());

        if ($course->endTime != null && $course->endTime < $now && !$course->isDraft && $course->is_published) {
            $course->isDraft = 1;
            $course->save();
        }

        return $this->sendResponse(
            new CourseResource($course),
            __('messages.retrieved', ['model' => __('models/courses.singular')])
        );
    }

    public function checkCourseForMe(Request $request)
    {
        $course = $this->courseRepository->checkCouseForMe($request);
        return $this->sendResponse(
            [
                "is_register" => ($course) ? 1 : 0
            ],
            __('messages.retrieved')
        );
    }

    public function update($id, UpdateCourseAPIRequest $request)
    {
        /** @var Course $course */

        $course = $this->courseRepository->find($id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $user = auth()->user();

        // if (!$user->hasRole(['admin']) && $user->id != $course->teacher_id) {

        //     return $this->sendError(
        //         __('messages.errors.not_permission'), 403
        //     );
        // }

        $input = $request->all();

        if ($course->course_type != $input["course_type"]) {
            if (count($course->students) || count($course->leaders)) {
                $data = [
                    "course_type" => __('messages.errors.cannot_change_type_of_course')
                ];
                return $this->sendError(
                    __('messages.errors.cannot_change_type_of_course'),
                    422,
                    $data
                );
            }
        }

        // $now = date(Carbon::now());
        $now = Carbon::now()->toDateTimeString();

        if (
            isset($course->endTime) && $course->endTime != null && $course->endTime < $now &&
            (($course->is_published == 0 && $request->is_published == 1) ||
                ($request->isDraft == 0 && $course->isDraft == 1)
            ) && isset($request->endTime) && $request->endTime != null && $request->endTime == $course->endTime

        ) {
            return $this->sendError(
                __('messages.cannot_published'),
                403
            );
        }

        $response = $this->courseRepository->updateCourse($input, $id, $request);

// dd($response);
        if (!$response["validJson"]) {
            // return $this->sendError(
            //     __('messages.invalid_format', ['model' => __('models/courses.fields.course_resources')]), 422
            // );
            $data['chapter'] = __('messages.is_required', ['model' => __('models/courses.fields.chapter')]);

            return $this->sendError(
                __('messages.course_is_required', ['model' => __('models/courses.fields.course_resources')]),
                422,
                $data
            );
        }

        if (count($response["isRequiredField"])) {
            $isRequiredField = $response["isRequiredField"];

            foreach ($isRequiredField as $key => $error) {
                $data[$key] = __('messages.is_required', ['model' => __('models/courses.fields.' . $error)]);
            }

            return $this->sendError(
                __('messages.course_is_required', ['model' => __('models/courses.fields.course_resources')]),
                422,
                $data
            );
        }

        if (
            count($response["isNotFoundField"]["chapters"]) ||
            count($response["isNotFoundField"]["lessons"]) ||
            count($response["isNotFoundField"]["surveys"])
        ) {

            $isNotFoundField = $response["isNotFoundField"];

            foreach ($isNotFoundField as $key => $field) {

                if (!count($isNotFoundField[$key])) {
                    unset($isNotFoundField[$key]);
                }
            }

            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.fields.course_resources')]),
                422,
                $isNotFoundField
            );
        }


        $student_new = $response["student_new"];

        if ($response["model"]) {

            if ($response["model"]->is_published && $response["model"]->isDraft == false) {
                $job = (new PushNotificationWhenUpdateCourse($course, $user->id, $student_new));
                dispatch($job);
            }

            if (
                $response["model"]->is_published &&
                $course->is_published != $response["model"]->is_published &&
                $response["model"]->isDraft == false
            ) {

                $new_course = $response["model"];
                $job = (new PushNotificationWhenNewCourse($new_course));
                dispatch($job);

            }

            if ($response["model"]->is_published && $response["model"]->teacher_id != $course->teacher_id) {

                $new_course = $response["model"];

                $new_teacher = $new_course->teacher;
                $message = ", bạn vừa được thêm vào phụ trách khóa học : " . $new_course->course_name;

                $message = $new_teacher->name ? $new_teacher->name . $message : $new_teacher->email . $message;

                $new_teacher->notify(new NewCourse($course, $message));

                event(new \App\Events\PushNotification($new_teacher->id));

                $events = $course->events;
                // var_dump($events);
                $job = (new RemoveMemberOutEvent($course->teacher_id, $course));

                dispatch($job);

                $addJob = (new AddMemberIntoEvent($response["model"]->teacher_id, $events));

                dispatch($addJob);

            }

            return $this->sendResponse(
                new CourseResource($response["model"]),
                __('messages.updated', ['model' => __('models/courses.singular')])
            );
        }
    }

    public function destroy($id)
    {
        /** @var Course $course */
        $course = $this->courseRepository->find($id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $course->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/courses.singular')])
        );
    }

    public function deleteMuitipleCourse(DeleteCoursesAPIRequest $request)
    {

        $courseIds = explode(',', $request->course_ids);

        $notFoundCourses = [];

        $foundCourses = [];

        foreach ($courseIds as $id) {

            $course = $this->courseRepository->find($id);

            if (empty($course)) {
                array_push($notFoundCourses, $id);
            } else {
                array_push($foundCourses, $course);
            }
        }

        if (count($notFoundCourses)) {

            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.plural')]),
                404,
                $notFoundCourses
            );
        }

        if (count($foundCourses)) {
            foreach ($foundCourses as $course) {

                $course->delete();
            }
        }

        $courses = $this->courseRepository->allCourses();

        return $this->sendResponse(
            $courses,
            __('messages.deleted', ['model' => __('models/courses.plural')])
        );
    }

    public function getStudentsOfCourse(Request $request, $id)
    {
        /** @var Course $course */
        $course = $this->courseRepository->find($id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $params = request()->query();

        $students = $this->userRepository->getStudentsOfCourse($id, $params);

        if (isset($params["export"]) && $params["export"]) {
            $folder_file = Carbon::parse(Carbon::now())->format('Y_m_d_H_i_s') . '_' . $course->course_id . '_' . '_students_export';
            mkdir(storage_path('/app/' . $folder_file), 0700);

            if (count($students)) {
                (new MemberCourseExport($students, $course->course_name))->store($folder_file . '/' . "-students-of-course-export.xlsx");
            }

            return response()->file(storage_path('/app/' . $folder_file . '/' . "-students-of-course-export.xlsx"))->deleteFileAfterSend(true);
        }

        return $this->sendResponse($students, __('messages.retrieved', ['model' => __('models/users.plural')]));

    }

    public function getLeadersOfCourse(Request $request, $id)
    {

        /** @var Course $course */
        $course = $this->courseRepository->find($id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $params = request()->query();

        $leaders = $this->userRepository->getLeadersOfCourse($id, $params);

        // dd($leaders);

        if (isset($params["export"]) && $params["export"]) {
            $folder_file = Carbon::parse(Carbon::now())->format('Y_m_d_H_i_s') . '_' . $course->course_id . '_' . '_students_export';
            mkdir(storage_path('/app/' . $folder_file), 0700);

            if (count($leaders)) {
                (new MemberCourseExport($leaders, $course->course_name, true))->store($folder_file . '/' . "-students-of-course-export.xlsx");
            }

            return response()->file(storage_path('/app/' . $folder_file . '/' . "-students-of-course-export.xlsx"))->deleteFileAfterSend(true);
        }

        return $this->sendResponse($leaders, __('messages.retrieved', ['model' => __('models/users.plural')]));

    }

    public function getPopularCategoriesCourse(Request $request)
    {

    }

    public function cloneCouse($course_id)
    {
        $course = $this->courseRepository->cloneCouse($course_id);
        return $this->sendResponse(
            $course,
            __('messages.clone_course_successfully')
        );
    }

    public function getDetailCourseOfUser($course_id)
    {

        $user = auth()->user();
        $course = $this->courseRepository->find($course_id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $new_course = $this->courseRepository->updateViewCount($course);
        return $this->sendResponse(
            new CourseShortTermResource($new_course),
            __('messages.retrieved', ['model' => __('models/courses.singular')])
        );

    }

    public function getDetailCourseOfUserBySlug(Request $request)
    {
        $slug_course = $request->get('slug');
        $course = $this->courseRepository->getDetailCourseBySlug($slug_course);
        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }
//        $new_course = $this->courseRepository->updateViewCount($course);
        if (Auth::guard('api')->user()) {
            return $this->sendResponse(
                new CourseShortTermResource($course),
                __('messages.retrieved', ['model' => __('models/courses.singular')])
            );
        } else {
            return $this->sendResponse(
                new CourseShortTermForLandingResource($course),
                __('messages.retrieved', ['model' => __('models/courses.singular')])
            );
        }
    }

    public function getDetailCourseOfUserLearningBySlug(Request $request)
    {
        $slug_course = $request->get('slug');
        $course = $this->courseRepository->getDetailCourseBySlug($slug_course);
        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $new_course = $this->courseRepository->updateViewCount($course);
        return $this->sendResponse(
            new CourseShortTermResource($new_course),
            __('messages.retrieved', ['model' => __('models/courses.singular')])
        );

    }


    public function changePublishCourse($course_id, Request $request)
    {

        $course = $this->courseRepository->find($course_id);

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $isDraft = $request->isDraft;

        if (!$isDraft) {
            $checkToPublished = $this->courseRepository->checkToPublished($course);

            if (!$checkToPublished) {
                return $this->sendError(
                    __('messages.missing_lesson_or_survey'), 422
                );
            }
        }

        $now = date(Carbon::now());
        if ($course->endTime != null && $course->endTime < $now && !$isDraft) {
            return $this->sendError(
                __('messages.cannot_published')
            );
        }

        $courseUnPublished = $this->courseRepository->changePublishCourse($course_id, $isDraft);

        $message = 'messages.course_unpublished';

        if (!$isDraft) {
            $message = 'messages.course_published';
        }

        return $this->sendSuccess(
            __($message)
        );
    }

    public function checkIsJoinedCourse($course_id)
    {
        $user = auth()->user();
        if (is_numeric($course_id)) {
            $course = $this->courseRepository->find($course_id);
        } else {
            $course = $this->courseRepository->getDetailCourseBySlug($course_id);
        }

        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $now = date(Carbon::now());

        if (isset($course->startTime) && $course->startTime != null && $course->startTime > $now) {
            return $this->sendError(
                __('messages.course_not_start', ['model' => __('models/courses.singular')]),
                422
            );
        }

//        dd($user->id, $course->course_id);
        $courseStudent = $this->courseStudentRepository->isJoined($user->id, $course->course_id);
        if (empty($courseStudent)) {
//            if (($course->course_type == "Business" && $course->course_price > 0) || $course->course_type == "Group") {
//                return $this->sendError(
//                    __('messages.not_join_course')
//                );
//
//            } else if ($course->course_type == "Local" || ($course->course_type == "Business" && $course->course_price == 0)) {
//
//
//            }
            $input['student_id'] = $user->id;

            $input['course_id'] = $course_id;

            $courseStudent = $this->courseStudentRepository->create($input);

            $this->learningProcessRepository->createProcessLearning($input, $user->id, $course);

            $events = $course->events;

            $job = (new AddMemberIntoEvent($user->id, $events));

            dispatch($job);
        }

        return $this->sendSuccess(
            __('messages.start_learning'), $courseStudent
        );
    }

    public function searchCourse()
    {

        $params = request()->query();

        $courses = $this->courseRepository->SearchallCourses($params);

        return $this->sendResponse(
            $courses,
            __('messages.search', ['model' => __('models/courses.plural')])
        );
    }

    public function featureCourses()
    {
        $params = request()->query();
        $featureCourses = $this->courseRepository->featureCourses($params);
        return $this->sendResponse(
            $featureCourses,
            __('messages.retrieved', ['model' => __('models/courses.plural')])
        );
    }

    public function reportBusinessCourses()
    {

        $params = request()->query();
        $businessCourses = $this->courseRepository->reportBusinessCourses($params);
        if (isset($params["export"]) && $params["export"]) {

            $folder_file = Carbon::parse(Carbon::now())->format('Y_m_d_H_i_s') . '_' . 'report_business_course_export.';

            mkdir(storage_path('/app/' . $folder_file), 0700);

            if (count($businessCourses)) {

                (new ReportBusinessCoursesExport($businessCourses))->store($folder_file . '/' . "-report-business-course-export.xlsx");

            }

            return response()->file(storage_path('/app/' . $folder_file . '/' . "-report-business-course-export.xlsx"))->deleteFileAfterSend(true);
        }

        return $this->sendResponse(
            $businessCourses,
            __('messages.retrieved', ['model' => __('models/courses.fields.businessCoursesReport')])
        );
    }

    public function reportDetailCourses()
    {
        $params = request()->query();

        $businessCourses = $this->courseRepository->reportDetailCourses($params);

        if (isset($params["export"]) && $params["export"]) {

            $folder_file = Carbon::parse(Carbon::now())->format('Y_m_d_H_i_s') . '_' . 'report_detail_course_export.';

            mkdir(storage_path('/app/' . $folder_file), 0700);

            if (count($businessCourses)) {

                (new ReportDetailCourseExport($businessCourses))->store($folder_file . '/' . "-report-detail-course-export.xlsx");

            }

            return response()->file(storage_path('/app/' . $folder_file . '/' . "-report-detail-course-export.xlsx"))->deleteFileAfterSend(true);

        }

        return $this->sendResponse(
            $businessCourses,
            __('messages.retrieved', ['model' => __('models/courses.fields.reportDetailCourses')])
        );
    }

    public function getBussinessCourses()
    {

        $businessCourses = $this->courseRepository->getBussinessCourses();

        return $this->sendResponse(
            $businessCourses,
            __('messages.retrieved', ['model' => __('models/courses.fields.businessCourses')])
        );
    }

    public function statisticalCourses(Request $request)
    {
        $params = request()->query();

        $statisticalCourses = $this->courseRepository->statisticalCourses($params);

        if (isset($params["export"]) && $params["export"]) {

            $folder_file = Carbon::parse(Carbon::now())->format('Y_m_d_H_i_s') . '_' . 'statistical_course_export.';

            mkdir(storage_path('/app/' . $folder_file), 0700);

            if (count($statisticalCourses)) {
                (new StatistialCourseExport($statisticalCourses))->store($folder_file . '/' . "-statistical-course-export.xlsx");
            }
            return response()->file(storage_path('/app/' . $folder_file . '/' . "-statistical-course-export.xlsx"))->deleteFileAfterSend(true);
        }

        return $this->sendResponse(
            $statisticalCourses,
            __('messages.retrieved', ['model' => __('models/courses.fields.statisticalCourses')])
        );
    }

    public function statisticalBusiness(Request $request)
    {
        $params = request()->query();

        $statisticalBusiness = $this->courseRepository->statisticalBusiness($params);

        return $this->sendResponse(
            $statisticalBusiness,
            __('messages.retrieved', ['model' => __('models/courses.fields.statisticalCourses')])
        );
    }

    public function getRelatedCourses($course_id)
    {
        $course = $this->courseRepository->find($course_id);
        // dd($course);
        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }

        $relatedCourses = $this->courseRepository->getRelatedCourses($course);

        return $this->sendResponse(
            $relatedCourses,
            __('messages.retrieved', ['model' => __('models/courses.fields.relatedCourses')])
        );
    }

    public function downloadCertificate($course_id)
    {
        $course = $this->courseRepository->find($course_id);
        if (empty($course)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courses.singular')])
            );
        }
        $certificate_url = $course->getMedia(MEDIA_COLLECTION["CERTIFICATE_IMAGE"]);
        return response()->download($certificate_url[0]->getPath(), $certificate_url[0]->file_name);
    }

    public function getListCourseByCategory(Request $request) {
        $input = $request->all();
        $courses = $this->courseRepository->getCourseByCategory($input);
        return $this->sendResponse(
            $courses,
            __('messages.retrieved', ['model' => __('models/courses.plural')])
        );
    }
}

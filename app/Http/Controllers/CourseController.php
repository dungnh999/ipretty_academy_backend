<?php

namespace App\Http\Controllers;

use App\Contract\CommonBusiness;
use App\Jobs\PushNotificationWhenNewCourse;
use App\Models\Chapter;
use App\Models\ChapterLesson;
use App\Models\Course;
use App\Models\Lesson;
use App\Repositories\CourseCategoryRepository;
use App\Repositories\LessonRepository;
use App\Repositories\CourseRepository;
use App\Repositories\ChapterRepository;
use App\Repositories\CourseStudentRepository;
use App\Repositories\LearningProcessRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Http\Resources\ChapterResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;

class CourseController extends AppBaseController
{
    use CommonBusiness;

    /** @var  CourseRepository */
    private $courseRepository;
    private $userRepository;
    private $courseStudentRepository;
    private $learningProcessRepository;
    private $uploadFile;
    private $chapterRepository;
    private $lessonRepository;

    private $courseCategoryRepository;

    public function __construct(
        CourseCategoryRepository  $courseCategoryRepo,
        CourseRepository          $courseRepo,
        UserRepository            $userRepository,
        CourseStudentRepository   $courseStudentRepository,
        LearningProcessRepository $learningProcessRepository,
        chapterRepository         $chapterRepo,
        LessonRepository          $lessonRepository
    )
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });

        $this->courseRepository = $courseRepo;
        $this->chapterRepository = $chapterRepo;
        $this->userRepository = $userRepository;
        $this->courseStudentRepository = $courseStudentRepository;
        $this->learningProcessRepository = $learningProcessRepository;
        $this->courseCategoryRepository = $courseCategoryRepo;
        $this->lessonRepository = $lessonRepository;
    }

    public function index()
    {
        return view('contents.course.index');
    }

    public function getDataCourse()
    {
        $params = request()->query();
        $courses = $this->courseRepository->allCourses($params);
        $collect = collect($courses);

        $dataCourseActive = $collect->where('status', ENUM_ACTIVE)->sortByDesc('is_published');
        $dataCourseUnActive = $collect->where('status', ENUM_UNACTIVE)->all();

        $dataTableActive = $this->drawDataTableCourse($dataCourseActive);
        $dataTableUnActive = $this->drawDataTableCourse($dataCourseUnActive);

        $total = [
            'total_active' => count($dataCourseActive),
            'total_UnActive' => count($dataCourseUnActive),
        ];
        return [$dataTableActive, $dataTableUnActive, $total];
    }

    function drawDataTableCourse($data)
    {
        return Datatables::of($data)
            ->addColumn('status', function ($row) {
                if ($row['is_published']) {
                    return '<span class="badge bg-label-info">Phát hành</span>';
                } else {
                    return '<span class="badge bg-label-danger">Dừng phát hành</span>';
                }
            })
            ->addColumn('teacher_name', function ($row) {
                $avatar = $row['teacher']['avatar'];
                $name = $row['teacher']['name'];
                $email = $row['teacher']['email'];
                return $this->getNameAvatarDataTable($name, $avatar, $email);
            })
            ->addColumn('course_name', function ($row) {
                return '<div class="d-flex justify-content-center align-items-center user-name" >
                     <div class="d-flex flex-column" >
                          <a href="javascript:void(0)" class="text-body text-truncate">
                              <span class="fw-medium">'
                    . $row['course_name'] .
                    '</span>
                          </a>
                          <div class="row">
                             <small class="text-muted">' . count($row['chapters']) . ' bài học </small>
                            <small class="text-muted">0 bài giảng</small>
                          </div>
                    </div>
                  </div>';
            })
            ->addColumn('action', function ($row) {
                $course_id = $row['course_id'];
                if ($row['status']) {
                    if (!$row['is_published']) {
                        return '<div class="d-inline-block text-nowrap" >
            <button class="btn-sm rounded-pill btn-icon bg-label-primary align-middle border-0" data-id="' .
                            $course_id .
                            '" onclick="openModalCreateChapterLesson($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Thêm bài giảng</span>">
                              <i class="bx bx-book-open"></i>
                          </button>
                          <button class="btn-sm rounded-pill btn-icon bg-label-warning align-middle border-0" data-id="' .
                            $course_id .
                            '" onclick="openModalUpdateCoursesCategory($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Chỉnh sửa</span>">
                              <i class="bx bx-edit"></i>
                          </button>
                          <button class="btn-sm rounded-pill btn-icon bg-label-success align-middle border-0" data-id="' .
                            $course_id .
                            '" onclick="changeStatusUnActiveCoursesCategory($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Phát hành</span>">
                              <i class="bx bx-play"></i>
                          </button>
                          <button class="btn-sm rounded-pill btn-icon bg-label-danger align-middle border-0" data-id="' .
                            $course_id .
                            '" onclick="changeStatusUnActiveCoursesCategory($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Tạm ngưng</span>">
                              <i class="bx bx-x"></i>
                          </button>
                      </div>';
                    } else {
                        return '<div class="d-inline-block text-nowrap" >
                          <button class="btn-sm rounded-pill btn-icon bg-label-primary align-middle border-0" data-id="' .
                            $course_id .
                            '" onclick="openModalCreateChapterLesson($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Thêm bài giảng</span>">
                                <i class="bx bx-book-open"></i>
                            </button>
                            <button class="btn-sm rounded-pill btn-icon bg-label-warning align-middle border-0" data-id="' .
                            $course_id .
                            '" onclick="openModalUpdateCoursesCategory($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Chỉnh sửa</span>">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn-sm rounded-pill btn-icon bg-label-info align-middle border-0" data-id="' .
                            $course_id .
                            '" onclick="changeStatusUnActiveCoursesCategory($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Ngưng phát hành</span>">
                              <i class="bx bx-pause"></i>
                          </button>
                            <button class="btn-sm rounded-pill btn-icon bg-label-danger align-middle border-0" data-id="' .
                            $course_id .
                            '" onclick="changeStatusUnActiveCoursesCategory($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Tạm ngưng</span>">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>';
                    }

                } else {
                    return '<div class="d-inline-block text-nowrap" >
                       <button class="btn-sm rounded-pill btn-icon bg-label-warning align-middle border-0" data-id="' .
                        $course_id .
                        '" onclick="openModalUpdateCoursesCategory($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                       <button class="btn-sm rounded-pill btn-icon bg-label-success align-middle border-0" data-id="' .
                        $course_id .
                        '" onclick="changeStatusActiveCoursesCategory($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Bật hoạt động</span>">
                          <i class="bx bx-check"></i>
                      </button>
                  </div>';
                }
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'course_name', 'teacher_name', 'action'])
            ->make(true);
    }

    function getDataTeacher(Request $request)
    {
        $courses = $this->courseRepository->getDataTeacher('');
        $selectTeacher = '<option disabled selected> --- Vui lòng chọn --- </option>';
        foreach ($courses as $data) {
            $selectTeacher .= '<option value="' . $data['id'] . '">' . $data['name'] . '</option>';
        }
        return [$selectTeacher];
    }

    public function getDataCategory(Request $request)
    {
        $courseCategory = $this->courseCategoryRepository->allCategories('');
        $collect = collect($courseCategory);
        $dataCourseCategory = $collect->where('isPublished', true);
        $selectCourseCategory = '<option disabled selected> --- Vui lòng chọn --- </option>';
        foreach ($dataCourseCategory as $data) {
            $selectCourseCategory .= '<option value="' . $data['category_id'] . '">' . $data['category_name'] . '</option>';
        }
        return [$selectCourseCategory];
    }

    public function createCourse(Request $request)
    {
        $input = $request->all();
        $response = $this->courseRepository->createCourse($input, $request);
//        if (!$response["validJson"]) {
//
//            $data['chapter'] = __('messages.is_required', ['model' => __('models/courses.fields.chapter')]);
//
//            return $this->sendError(
//                __('messages.course_is_required', ['model' => __('models/courses.fields.course_resources')]),
//                422,
//                $data
//            );
//        }
//
//        if (count($response["isRequiredField"])) {
//            $isRequiredField = $response["isRequiredField"];
//
//            foreach ($isRequiredField as $key => $error) {
//                $data[$key] = __('messages.is_required', ['model' => __('models/courses.fields.' . $error)]);
//            }
//
//            return $this->sendError(
//                __('messages.course_is_required', ['model' => __('models/courses.fields.course_resources')]), 422,
//                $data
//            );
//        }
//
//        if (
//            count($response["isNotFoundField"]["lessons"]) ||
//            count($response["isNotFoundField"]["surveys"])
//        ) {
//
//            $isNotFoundField = $response["isNotFoundField"];
//
//            foreach ($isNotFoundField as $key => $field) {
//
//                if (!count($isNotFoundField[$key])) {
//                    unset($isNotFoundField[$key]);
//                }
//            }
//
//            return $this->sendError(
//                __('messages.not_found', ['model' => __('models/courses.fields.course_resources')]),
//                422,
//                $isNotFoundField
//            );
//        }

//        if ($response["model"]) {
//
//            if ($response["model"]->is_published) {
//
//                $course = $response["model"];
//
//                $job = (new PushNotificationWhenNewCourse($course));
//                dispatch($job);
//
//                // $students = $course->students;
//
//                // $this->pushNotificationForUser('admin', $students);
//            }
//
//
//        }
        return $this->sendSuccess(
            __('messages.created', ['model' => __('models/courses.singular')]),
            new CourseResource($response)
        );
    }

    public function getDataChapterCourse(Request $request)
    {
        $params = request()->query();
        $chapter = $this->courseRepository->getDetail($params['course_id']);

        return $this->sendResponse(
            new CourseResource($chapter),
            __('messages.saved', ['model' => __('models/userDepartments.singular')])
        );
    }

    public function createChapterCourse(Request $request)
    {
        $chapter = $this->chapterRepository->create($request);
        return $this->sendResponse(
            new ChapterResource($chapter),
            __('messages.saved', ['model' => __('models/userDepartments.singular')])
        );
    }

    public function getDetailLessonCourse(Request $request)
    {
        $LessonsDetail = $this->lessonRepository->detail($request);
        return $this->sendResponse(
            new LessonResource($LessonsDetail),
            __('messages.saved', ['model' => __('models/userDepartments.singular')])
        );
    }

    public function updateLessonCourse(Request $request)
    {
        $Lesson = $this->lessonRepository->updateLesson($request);
        return $this->sendResponse(
            new LessonResource($Lesson),
            __('messages.saved', ['model' => __('models/userDepartments.singular')])
        );
    }
}

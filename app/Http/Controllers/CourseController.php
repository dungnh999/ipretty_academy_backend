<?php

namespace App\Http\Controllers;

use App\Contract\CommonBusiness;
use App\Models\Chapter;
use App\Models\ChapterLesson;
use App\Models\Course;
use App\Models\Lesson;
use App\Repositories\CourseCategoryRepository;
use App\Repositories\chapterRepository;
use App\Repositories\LessonRepository;
use App\Repositories\CourseRepository;
use App\Repositories\CourseStudentRepository;
use App\Repositories\LearningProcessRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Http\Resources\ChapterResource;
use App\Http\Resources\courseResource;
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
    $this->uploadFile = $uploadFile;
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

    $dataCourseActive = $collect->where('status', ENUM_ACTIVE)->all();
    $dataCourseUnActive = $collect->where('status', ENUM_UNACTIVE)->all();

    $dataTableActive = $this->drawDataTableCourse($dataCourseActive);
    $dataTableUnActive = $this->drawDataTableCourse($dataCourseUnActive);
    return [$dataTableActive, $dataTableUnActive];
  }

  function drawDataTableCourse($data)
  {
    return Datatables::of($data)
      ->addColumn('status', function ($row) {
        if ($row['is_published']) {
          return '<span class="badge bg-label-info">Đang chạy</span>';
        } else {
          return '<span class="badge bg-label-danger">Đã tắt</span>';
        }
      })
      ->addColumn('teacher_name', function ($row) {
        return '<div class="d-flex justify-content-start align-items-center user-name" >
                     <div class="avatar-wrapper">
                        <div class="avatar avatar-sm me-3" >
                          <img src="' .
          $row['teacher']['avatar'] .
          '" alt="Avatar" class="rounded-circle object-fit-cover">
                        </div>
                     </div>
                     <div class="d-flex flex-column" >
                          <a href="javascript:void(0)" class="text-body text-truncate">
                              <span class="fw-medium">' .
          $row['teacher']['name'] .
          '</span>
                          </a>
                          <small class="text-muted">' .
          $row['teacher']['email'] .
          '</small>
                    </div>
                  </div>';
      })
      ->addColumn('action', function ($row) {
        $course_id = $row['course_id'];
        if ($row['status']) {
          return '<div class="d-inline-block text-nowrap" >
                    <button class="btn-sm rounded-pill btn-icon bg-label-success align-middle border-0" data-id="' .
            $course_id .
            '" onclick="openModalCreateChapterLesson($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Thêm bài giảng</span>">
                          <i class="bx bx-book-open"></i>
                      </button>
                      <button class="btn-sm rounded-pill btn-icon bg-label-warning align-middle border-0" data-id="' .
            $course_id .
            '" onclick="openModalUpdateCoursesCategory($(this))" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title="<span>Chỉnh sửa</span>">
                          <i class="bx bx-edit"></i>
                      </button>
                      <button class="btn-sm rounded-pill btn-icon bg-label-danger align-middle border-0" data-id="' .
            $course_id .
            '" onclick="changeStatusUnActiveCoursesCategory($(this))">
                          <i class="bx bx-x"></i>
                      </button>
                  </div>';
        } else {
          return '<div class="d-inline-block text-nowrap" >
                       <button class="btn-sm rounded-pill btn-icon bg-label-warning align-middle border-0" data-id="' .
            $course_id .
            '" onclick="openModalUpdateCoursesCategory($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                       <button class="btn-sm rounded-pill btn-icon bg-label-success align-middle border-0" data-id="' .
            $course_id .
            '" onclick="changeStatusActiveCoursesCategory($(this))">
                          <i class="bx bx-check"></i>
                      </button>
                  </div>';
        }
      })
      ->addIndexColumn()
      ->rawColumns(['status', 'teacher_name', 'action'])
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
    DB::beginTransaction();

    // if(isset($request->file('course')['banner'])){
    //   $file = $this->uploadFile->uploadFileLocal($request->file('course')['banner']);
    //   $course['course_feature_image'] = $file['key'];
    //   $course->save();
    // }
    // /*
    // * Tạo Chương học
    // * **/
    // $course_version = 1;
    // foreach ($request->get('lessons') as $key => $chapter){
    //   $chapterData = Chapter::create([
    //       'chapter_name' => $chapter['lessons_name'],
    //       'course_id' => $course['course_id'],
    //       'course_version' => $course['course_version'],
    //     ]);
    //     foreach ($chapter['chapter'] as $keyLessons => $lessons){
    //       $LessonsData = Lesson::create([
    //         'lesson_name' => $lessons['lesson_name'],
    //         'lesson_description' => $lessons['lesson_description'],
    //         'main_attachment' => '',
    //         'lesson_duration' => 1111111,
    //       ]);
    //       if(isset($request->file('lessons')[$key]['chapter'][$keyLessons]['video_lesson'])){
    //         $file = $this->uploadFile->uploadFileLocal($request->file('lessons')[$key]['chapter'][$keyLessons]['video_lesson']);
    //         $LessonsData['main_attachment'] = $file['key'];
    //         $LessonsData->save();
    //       }
    //       $ChapterLessonsData = ChapterLesson::create([
    //         'chapter_id' => $chapterData['chapter_id'],
    //         'lesson_id' => $LessonsData['lesson_id'],
    //         'number_order' => 0,
    //       ]);
    //     }
    // }

    $LessonsData = Lesson::create([
      'lesson_name' => $request['lessons_name'],
      'lesson_description' => $request['lessons_description'],
      'main_attachment' => $request['main_attachment'],
      'lesson_duration' => 1111111,
    ]);
//    if (isset($request->file('lessons')[$key]['chapter'][$keyLessons]['video_lesson'])) {
//      $file = $this->uploadFile->uploadFileLocal($request->file('lessons')[$key]['chapter'][$keyLessons]['video_lesson']);
//      $LessonsData['main_attachment'] = $file['key'];
//      $LessonsData->save();
//    }
    $ChapterLessonsData = ChapterLesson::create([
      'chapter_id' => $request['chapter_id'],
      'lesson_id' => $LessonsData['lesson_id'],
      'number_order' => 0,
    ]);
    DB::commit();

    return $this->sendResponse(
      new LessonResource($LessonsData),
      __('messages.saved', ['model' => __('models/userDepartments.singular')])
    );
  }

  public function getDataChapterCourse(Request $request)
  {
    $params = request()->query();
    $chapter = $this->courseRepository->getDetail($params['course_id']);

    return $this->sendResponse(
      new courseResource($chapter),
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

  public function getDetailLessonCourse(Request $request){
    $LessonsDetail = $this->lessonRepository->detail($request);
    return $this->sendResponse(
      new LessonResource($LessonsDetail),
      __('messages.saved', ['model' => __('models/userDepartments.singular')])
    );
  }

  public function updateLessonCourse(Request $request){
    $Lesson = $this->lessonRepository->updateLesson($request);
    return $this->sendResponse(
      new LessonResource($Lesson),
      __('messages.saved', ['model' => __('models/userDepartments.singular')])
    );
  }
}

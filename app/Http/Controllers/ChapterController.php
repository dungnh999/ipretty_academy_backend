<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChapterResource;
use Illuminate\Http\Request;
use App\Repositories\CourseRepository;
use App\Repositories\ChapterRepository;
use App\Models\Course;
use App\Models\Chapter;
use Yajra\DataTables\DataTables;

class ChapterController extends AppBaseController
{
  private $courseRepository;
  private $chapterRepository;

  public function __construct(CourseRepository $courseRepo, ChapterRepository $chapterRepo)
  {
    $this->courseRepository = $courseRepo;
    $this->chapterRepository = $chapterRepo;
  }

  public function index()
  {
    return view('contents.chapter.index');
  }

  public function getDataCourse()
  {
    $params = request()->query();
    $courses = $this->courseRepository->allCourses($params);
    $collect = collect($courses);

    $dataCourseActive = $collect->where('is_published', ENUM_ACTIVE)->all();
    $dataTableActive = $this->drawDataTableCourse($dataCourseActive);

    return [$dataTableActive];
  }

  public function getDataChapterCourse(Request $request)
  {
    $params = request()->query();
    $chapter = $this->chapterRepository->getAllChapterCourse($params);
    $course = Course::where('course_id', $params['course_id'])->first();

    $collection = collect($chapter);
    $customArray = $collection
      ->map(function ($course) {
        return [
          'title' =>
            '  <div class="row" style="text-align: left;">
                  <h5>' .
            $course['chapter_name'] .
            '</h5>
                </div>',
        ];
      })
      ->toArray();
    return [$customArray, $course];
  }

  public function create(Request $request)
  {
    $chapterData = Chapter::create([
      'chapter_name' => $request->get('chapter_name'),
      'course_id' => $request->get('course_id'),
      'number_order' => Chapter::where('course_id', $request->get('course_id'))->count() + 1,
    ]);
    return $this->sendResponse(
      new ChapterResource($chapterData),
      __('messages.saved', ['model' => __('models/userDepartments.singular')])
    );
    return $chapterData;
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
        $departmentId = $row['course_id'];
        return '<div class="d-inline-block text-nowrap" >
                      <button class="btn-sm rounded-pill btn-icon bg-label-info align-middle border-0" data-id="' .
          $departmentId .
          '" onclick="getDataChapterforCourse($(this))">
                          <i class="bx bx-info-circle"></i>
                      </button>
                  </div>';
      })
      ->addIndexColumn()
      ->rawColumns(['status', 'teacher_name', 'action'])
      ->make(true);
  }

  function createChapterCourse()
  {
  }
}

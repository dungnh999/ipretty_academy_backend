<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseCategoryResource;
use App\Http\Resources\UserDepartmentResource;
use App\Models\CourseCategory;
use App\Models\CourseCategoryTypes;
use App\Repositories\CourseCategoryRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CoursesCategoryController extends AppBaseController
{


  private $courseCategoryRepository;

  public function __construct(CourseCategoryRepository $courseCategoryRepo)
  {
    $this->middleware(function ($request, $next) {

      $user = auth()->user();
      if ($user) {
        \App::setLocale($user->lang);
      }
      return $next($request);
    });
    $this->courseCategoryRepository = $courseCategoryRepo;
  }

  public function index()
  {
    return view('contents.category-course.index');
  }

  public function getListCategory(Request $request)
  {
    $params = $request->query();
    $courseCategories = $this->courseCategoryRepository->allCategories($params);
    $collect = collect($courseCategories);

    $dataActive = $collect->where('isPublished', true);
    $dataUnActive = $collect->where('isPublished', false);

    $dataTableActive = $this->drawDataTableDepartment($dataActive);
    $dataTableUnActive = $this->drawDataTableDepartment($dataUnActive);

    $dataTotal = [
      'totalActive' => count($dataActive),
      'totalUnActive' => count($dataUnActive),
    ];
    return [$dataTableActive, $dataTableUnActive, $dataTotal];
  }
  public function CreateCoursesCategory(Request $request)
  {
    $input = $request->all();
    $courseCategory = $this->courseCategoryRepository->createCourseCategory($input, $request);

    return $this->sendResponse(
      new CourseCategoryResource($courseCategory),
      __('messages.saved', ['model' => __('models/courseCategories.singular')])
    );
  }

  private function drawDataTableDepartment($data)
  {
    return Datatables::of($data)
      ->addColumn('action', function ($row) {
        $departmentId = $row['category_id'];
        if ($row['isPublished']) {
          return '<div class="d-inline-block text-nowrap" >
                      <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="openModalUpdateCoursesCategory($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                      <button class="btn btn-icon btn-outline-danger rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="changeStatusUnActiveCoursesCategory($(this))">
                          <i class="bx bx-x"></i>
                      </button>
                  </div>';
        } else {
          return '<div class="d-inline-block text-nowrap" >
                       <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="openModalUpdateCoursesCategory($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                       <button class="btn btn-icon btn-outline-success rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="changeStatusActiveCoursesCategory($(this))">
                          <i class="bx bx-check"></i>
                      </button>
                  </div>';
        }
      })
      ->addIndexColumn()
      ->rawColumns(['action'])
      ->make(true);
  }

  public function getDetail(Request $request)
  {
    $id = $request->get('id');
    $courseCategory = $this->courseCategoryRepository->find($id);
    if (empty($courseCategory)) {
      return $this->sendError(
        __('messages.not_found', ['model' => __('models/courseCategories.singular')])
      );
    }
    return $this->sendResponse(
      new CourseCategoryResource($courseCategory),
      __('messages.retrieved', ['model' => __('models/courseCategories.singular')])
    );
  }

  public function getCategoryTypes(Request $request)
  {
    $dataCategoryTypes = CourseCategoryTypes::all();
    $collect = collect($dataCategoryTypes);
    $dataActive = $collect->where('isPublished', ENUM_ACTIVE);
    $selectCategoryTypes = '<option disabled selected> --- Vui lòng chọn --- </option>';
    foreach ($dataActive as $data) {
      $selectCategoryTypes .= '<option value="' . $data['id'] . '">' . $data['category_type_name'] . '</option>';
    }

    if (empty($dataActive)) {
      return $this->sendError(
        __('messages.not_found', ['model' => __('models/courseCategories.singular')])
      );
    }
    return $this->sendResponse(
      $selectCategoryTypes,
      __('messages.retrieved', ['model' => __('models/courseCategories.singular')])
    );
  }

  public function UpdateCoursesCategory(Request $request)
  {
    $input = $request->all();
    /** @var CourseCategory $courseCategory */
    $courseCategory = $this->courseCategoryRepository->find($input['id']);

    if (empty($courseCategory)) {
      return $this->sendError(
        __('messages.not_found', ['model' => __('models/courseCategories.singular')])
      );
    }
    $courseCategory = $this->courseCategoryRepository->updateCourseCategory($input, $input['id'], $request);

    return $this->sendResponse(
      new CourseCategoryResource($courseCategory),
      __('messages.updated', ['model' => __('models/courseCategories.singular')])
    );
  }

  public function ChangeStatusCoursesCategory(Request $request)
  {
    $category_id = $request->get('id');
    $courseCategory = $this->courseCategoryRepository->find($category_id);

    if (empty($courseCategory)) {
      return $this->sendError(
        __('messages.not_found', ['model' => __('models/courseCategories.singular')])
      );
    }

    $published_category = $this->courseCategoryRepository->publishedCategory($category_id, $request->get('status'));

    $message = 'messages.unpublished';

    if ($request->get('status')) {
      $message = 'messages.published';
    }

    return $this->sendResponse(
      new CourseCategoryResource($published_category),
      __($message, ['model' => __('models/courseCategories.singular')])
    );
  }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseCategoryResource;
use App\Http\Resources\CourseCategoryTypesResource;
use App\Http\Resources\UserDepartmentResource;
use App\Models\CourseCategoryTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CoursesCategoryTypesController extends AppBaseController
{
  public function index()
  {
    return view('contents.course-category-types.index');
  }

  public function getListCategoryTypes(Request $request)
  {
      $data = CourseCategoryTypes::all();
      $collect = collect($data);
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

  public function createCourseCategoryTypes(Request $request)
  {
      $user = Auth::user();
      $dataCreate = CourseCategoryTypes::create([
          'category_type_name' => $request->get('name'),
          'category_type_description' => $request->get('description'),
          'created_by' => $user->id ,
      ]);
      $dataCreate->save();
    return $this->sendResponse(
      new CourseCategoryTypesResource($dataCreate),
      __('messages.saved', ['model' => __('models/userDepartments.singular')])
    );
  }

  public function dataUpdateCourseCategoryTypes(Request $request){
      $dataCourseTypes = CourseCategoryTypes::find($request->get('id'));
      return $this->sendResponse(
        new CourseCategoryTypesResource($dataCourseTypes),
        __('messages.saved', ['model' => __('models/userDepartments.singular')])
      );
  }

  public function updateCourseCategoryTypes(Request $request){
    $dataUpdate = CourseCategoryTypes::find($request->get('id'));
    $dataUpdate['category_type_name'] = $request->get('name');
    $dataUpdate['category_type_description'] = $request->get('description');
    $dataUpdate->save();
    return $this->sendResponse(
      new CourseCategoryTypesResource($dataUpdate),
      __('messages.saved', ['model' => __('models/userDepartments.singular')])
    );
  }

  public function changeStatusCoursesCategoryTypes(Request $request)
  {
    $dataUpdate = CourseCategoryTypes::find($request->get('id'));
    $dataUpdate['isPublished'] = $request->get('status');
    $dataUpdate->save();
    return $this->sendResponse(
      new CourseCategoryTypesResource($dataUpdate),
      __('messages.saved', ['model' => __('models/userDepartments.singular')])
    );
  }

  private function drawDataTableDepartment($data)
  {
    return Datatables::of($data)
      ->addColumn('action', function ($row) {
        $departmentId = $row['category_type_id'];
        if ($row['isPublished']) {
          return '<div class="d-inline-block text-nowrap" >
                      <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="openModalUpdateCourseCategoryTypes($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                      <button class="btn btn-icon btn-outline-danger rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="changeStatusUnActiveCoursesCategoryTypes($(this))">
                          <i class="bx bx-x"></i>
                      </button>
                  </div>';
        } else {
          return '<div class="d-inline-block text-nowrap" >
                       <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="openModalUpdateCourseCategoryTypes($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                       <button class="btn btn-icon btn-outline-success rounded-pill btn-sm" data-id="' . $departmentId . '" onclick="changeStatusActiveCoursesCategoryTypes($(this))">
                          <i class="bx bx-check"></i>
                      </button>
                  </div>';
        }
      })
      ->addIndexColumn()
      ->rawColumns(['action'])
      ->make(true);
  }
}

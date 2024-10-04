<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserDepartmentResource;
use App\Models\Post;
use App\Models\UserDepartment;
use App\Repositories\UserDepartmentRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DepartmentController extends AppBaseController
{

  private $userDepartmentRepository;

  public function __construct(UserDepartmentRepository $userDepartmentRepo)
  {
    $this->middleware(function ($request, $next) {

      $user = auth()->user();
      if ($user) {
        \App::setLocale($user->lang);
      }
      return $next($request);
    });
    $this->userDepartmentRepository = $userDepartmentRepo;
  }

  public function index()
  {
      return view('contents.department.index');
  }

  public function CreateDepartment(Request $request)
  {
    $userDepartment = $this->userDepartmentRepository->create($request->all());
    return $this->sendSuccess(
      __('messages.saved', ['model' => __('models/userDepartments.singular')]),
        new UserDepartmentResource($userDepartment)
    );
  }


  public function getListDepartment(Request $request)
  {
    $banner = json_decode(UserDepartment::All(), true);
    $collect = collect($banner);
    $dataActive = $collect->where('isActive', ENUM_ACTIVE)->all();
    $dataUnActive = $collect->where('isActive', ENUM_UNACTIVE)->all();
    $dataTableActiveDepartment = $this->drawDataTableDepartment($dataActive);
    $dataTableUnActiveDepartment = $this->drawDataTableDepartment($dataUnActive);
    $dataTotal = [
      'totalActive' => count($dataActive),
      'totalUnActive' => count($dataUnActive),
    ];
    return [$dataTableActiveDepartment, $dataTableUnActiveDepartment,$dataTotal];
  }

  private function drawDataTableDepartment($data){
    return Datatables::of($data)
      ->addColumn('action', function ($row) {
        $departmentId = $row['department_id'];
        if($row['isActive']){
          return '<div class="d-inline-block text-nowrap" >
                      <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="'. $departmentId .'" onclick="openModalUpdateDepartment($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                      <button class="btn btn-icon btn-outline-danger rounded-pill btn-sm" data-id="'. $departmentId .'" onclick="changeStatusUnActiceDepartment($(this))">
                          <i class="bx bx-x"></i>
                      </button>
                  </div>';
        }else{
          return '<div class="d-inline-block text-nowrap" >
                       <button class="btn btn-icon btn-outline-warning rounded-pill btn-sm" data-id="'. $departmentId .'" onclick="openModalUpdateDepartment($(this))">
                          <i class="bx bx-edit"></i>
                      </button>
                       <button class="btn btn-icon btn-outline-success rounded-pill btn-sm" data-id="'. $departmentId .'" onclick="changeStatusActiceDepartment($(this))">
                          <i class="bx bx-check"></i>
                      </button>
                  </div>';
        }
      })

      ->addIndexColumn()
      ->rawColumns(['action'])
      ->make(true);
  }

  public function getDetailDepartment(Request $request)
  {
      $id = $request->get('id');
      $userDepartment = $this->userDepartmentRepository->find($id);

      if (empty($userDepartment)) {
        return $this->sendError(
          __('messages.not_found', ['model' => __('models/userDepartments.singular')])
        );
      }

      return $this->sendSuccess(
        __('messages.retrieved', ['model' => __('models/userDepartments.singular')]) ,
        new UserDepartmentResource($userDepartment)
      );
  }

  public function UpdateDepartment(Request $request)
  {
    $input = $request->all();
    $userDepartment = $this->userDepartmentRepository->find($request->get('id'));

    if (empty($userDepartment)) {
      return $this->sendError(
        __('messages.not_found', ['model' => __('models/userDepartments.singular')])
      );
    }

    $userDepartment = $this->userDepartmentRepository->update($input, $request->get('id'));

    return $this->sendSuccess(
        __('messages.updated', ['model' => __('models/userDepartments.singular')]),
        new UserDepartmentResource($userDepartment)
    );
  }

  public function changeStatus(Request $request)
  {
    $id = $request->get('id');
    $userDepartment = $this->userDepartmentRepository->find($id);

    if (empty($userDepartment)) {
      return $this->sendError(
        __('messages.not_found', ['model' => __('models/userDepartments.singular')])
      );
    }

    $userDepartment['isActive'] = (int)$request->get('status');
    $userDepartment->save();
    return $this->sendSuccess(
      __('messages.deleted', ['model' => __('models/userDepartments.singular')]),
        new UserDepartmentResource($userDepartment)
    );
  }

}

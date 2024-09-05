<?php

namespace App\Http\Controllers;

use App\Contract\CommonBusiness;
use App\Http\Requests\API\LockAccountAPIRequest;
use App\Http\Resources\UserResource;
use App\Jobs\PushNotificationWhenActiveAccount;
use App\Jobs\PushNotificationWhenNewAccount;
use App\Models\User;
use App\Models\UserDepartment;
use App\Repositories\AuthenticationRepository;
use App\Repositories\CourseRepository;
use App\Repositories\UserDepartmentRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Config;

class UsersController extends AppBaseController
{
  use CommonBusiness;

  private $userRepository;
  private $authenticationRepository;
  private $courseRepository;
  private $userDepartmentRepository;
  private $user;

  public function __construct(
    UserRepository $userRepo,
    AuthenticationRepository $authenticationRepository,
    CourseRepository $courseRepository,
    UserDepartmentRepository $userDepartmentRepo
  ) {
    $this->middleware(function ($request, $next) {
      $user = auth()->user();
      if ($user) {
        $this->user = $user;
        \App::setLocale($user->lang);
      }
      return $next($request);
    });

    $this->userRepository = $userRepo;
    $this->authenticationRepository = $authenticationRepository;
    $this->courseRepository = $courseRepository;
    $this->userDepartmentRepository = $userDepartmentRepo;
  }

  public function index()
  {
    return view('contents.users.index');
  }

  /**
   * Lấy danh sách nhân viên
   * ENUM :
   * 0 : nhân viên
   * 1 : giáo viên
   * 2 : học viên
   */
  public function getListUsers(Request $request)
  {
    // Thiếu nhân viên
    $employee = json_decode(
      Users::where('menuroles', ENUM_POSITION_EMPLOYEE)
        ->orderBy('isLocked', 'asc')
        ->get(),
      true
    );
    $student = json_decode(
      Users::where('menuroles', ENUM_POSITION_STUDENT)
        ->orderBy('isLocked', 'asc')
        ->get(),
      true
    );
    $teacher = json_decode(
      Users::where('menuroles', ENUM_POSITION_TEACHER)
        ->orderBy('isLocked', 'asc')
        ->get(),
      true
    );
    $manage = json_decode(
      Users::where('menuroles', ENUM_POSITION_ADMIN)
        ->orderBy('isLocked', 'asc')
        ->get(),
      true
    );

    $dataTableTeacher = $this->drawDataTableUsers($teacher);
    $dataTableManage = $this->drawDataTableUsers($manage);
    $dataTableEmployee = $this->drawDataTableUsers($employee);
    $dataTableStudent = $this->drawDataTableUsers($student);

    $dataTotal = [
      'manage' => count($manage),
      'teacher' => count($teacher),
      'student' => count($student),
      'employee' => count($employee),
    ];
    return [$dataTableTeacher, $dataTableManage, $dataTableEmployee, $dataTableStudent, $dataTotal];
  }
  public function getDataDepartment(Request $request)
  {
    $department = json_decode(UserDepartment::All(), true);
    $departmentActive = collect($department)->where('isActive', ENUM_ACTIVE);
    $selectDepartment = '<option disabled selected> --- Vui lòng chọn --- </option>';
    foreach ($departmentActive as $data) {
      $selectDepartment .= '<option value="' . $data['department_id'] . '">' . $data['department_name'] . '</option>';
    }
    return [$selectDepartment];
  }
  /**
   * VẼ DATATABLE
   * ENUM :
   */
  function drawDataTableUsers($data)
  {
    return Datatables::of($data)
      ->addColumn('status', function ($row) {
        return '<span class="badge bg-label-warning">Resigned</span>';
      })
      ->addColumn('verified', function ($row) {
        if ($row['email_verified_at']) {
          return '<span class="badge bg-label-success">' . ENUM_TEXT_ACTIVE . '</span>';
        } else {
          return '<span class="badge bg-label-secondary">' . ENUM_TEXT_INACTIVE . '</span>';
        }
      })
      ->addColumn('role', function ($row) {
        switch ($row['menuroles']) {
          case 'admin':
            return '<span class="text-truncate d-flex align-items-center">
                        <span class="badge badge-center rounded-pill bg-label-secondary w-px-30 h-px-30 me-2">
                            <i class="bx bx-mobile-alt bx-xs"></i>
                        </span>
                        Admin
                    </span>';
          case 'employee':
            return '<span class="text-truncate d-flex align-items-center">
                        <span class="badge badge-center rounded-pill bg-label-warning w-px-30 h-px-30 me-2">
                            <i class="bx bx-user bx-xs"></i>
                        </span>
                        Nhân viên
                      </span>';
          default:
            return '';
        }
      })
      ->addColumn('name', function ($row) {
        return '<div class="d-flex justify-content-start align-items-center user-name" >
                     <div class="avatar-wrapper">
                        <div class="avatar avatar-sm me-3" >
                          <img src="' .
          $row['avatar'] .
          '" alt="Avatar" class="rounded-circle object-fit-cover">
                        </div>
                     </div>
                     <div class="d-flex flex-column" >
                          <a href="app-user-view-account.html" class="text-body text-truncate">
                              <span class="fw-medium">' .
          $row['name'] .
          '</span>
                          </a>
                          <small class="text-muted">' .
          $row['email'] .
          '</small>
                    </div>
                  </div>';
      })
      ->addColumn('gender', function ($row) {
        switch ($row['gender']) {
          case ENUM_MALE:
            return "<i class='text-danger bx bx-male-sign'></i>";
          case ENUM_FEMALE:
            return "<i class='text-primary bx bx-female-sign'></i>";
          default:
            return '';
        }
      })
      ->addColumn('created_at', function ($row) {
        return $this->formartDateTime($row['created_at']);
      })
      ->addColumn('isLocked', function ($row) {
        if ($row['isLocked']) {
          return '<span class="badge bg-label-danger">' . TEXT_LOCK . '</span>';
        }
        return '<span class="badge bg-label-success">' . TEXT_OPEN . '</span>';
      })
      ->addColumn('action', function ($row) {
        if ($row['isLocked']) {
          return '<div class="d-inline-block text-nowrap" >
                    <button class="btn-sm rounded-pill btn-icon bg-label-success align-middle border-0"  data-id="' .
            $row['id'] .
            '" onclick="changeUnLockerUser($(this))">
                        <i class="bx bx-lock-open"></i>
                    </button>
                </div>
                <div class="d-inline-block text-nowrap" >
                    <button class="btn-sm rounded-pill btn-icon bg-label-warning align-middle border-0" onclick="openModalUpdateUsers()">
                        <i class="bx bx-edit"></i>
                    </button>
                </div>
                 <div class="d-inline-block text-nowrap" >
                    <button class="btn-sm rounded-pill btn-icon bg-label-success align-middle border-0" onclick="resetPasswordUser()">
                        <i class="bx bx-reset"></i>
                    </button>
                </div>
                ';
        } else {
          return '
                <div class="d-inline-block text-nowrap" >
                    <button class="btn-sm rounded-pill btn-icon bg-label-danger align-middle border-0" data-id="' .
            $row['id'] .
            '" onclick="changeLockerUser($(this))">
                        <i class="bx bx-lock"></i>
                    </button>
                </div>
                <div class="d-inline-block text-nowrap" >
                    <button class="btn-sm rounded-pill btn-icon bg-label-warning align-middle border-0" data-id="' .
            $row['id'] .
            '" onclick="openModalUpdateUsers($(this))">
                        <i class="bx bx-edit"></i>
                    </button>
                </div>
                <div class="d-inline-block text-nowrap" >
                    <button class="btn-sm rounded-pill btn-icon bg-label-success align-middle border-0" onclick="resetPasswordUser()">
                        <i class="bx bx-reset"></i>
                    </button>
                </div>
                ';
        }
      })
      ->addIndexColumn()
      ->rawColumns(['status', 'name', 'role', 'verified', 'gender', 'isLocked', 'action'])
      ->make(true);
  }
  public function CreateUserAccount(Request $request)
  {
    if (!Auth::check()) {
      return $this->sendError(__('messages.errors.unauthorized'));
    }
    $input = $request->all();
    $newUser = $this->authenticationRepository->register($input, $request);
    if (isset($newUser['isExist'])) {
      return $this->sendResponseWithError([], __('auth.registration.exist'), 200);
    }

    $job = (new PushNotificationWhenNewAccount($newUser));
    dispatch($job);

    if ($newUser) {
      return $this->sendSuccess(__('auth.registration.success_message'), $newUser);
    } else {
      return $this->sendError(__('auth.registration.unknown_error'), 503);
    }
  }
  public function updateUserAccount(Request $request)
  {
    $input = $request->all();
    $user = $this->userRepository->find($request->get('id'));
    $userUpdate = $this->userRepository->updateMyProfile($input, $user, $request);
    if ($userUpdate) {
      return $this->sendSuccess(__('auth.registration.success_message'), $userUpdate);
    } else {
      return $this->sendError(__('auth.registration.unknown_error'), 503);
    }
  }
  public function activate($id, $token)
  {
    $redirect = env('IPRETTY_PLATFORM') . '/#/';
    $active_url = env('ACTIVE_URL') ? env('ACTIVE_URL') : 'confirm-success';
    try {
      $user = User::find($id);
      if (!$user) {
        return redirect($redirect . '?error=' . __('messages.user_not_exist'))->with(
          'error',
          __('messages.user_not_exist')
        );
      }
      // check if token is expired
      if (
        Carbon::parse($user->updated_at)
          ->addDays(1)
          ->isPast()
      ) {
        return redirect($redirect . '?error=' . __('messages.expired_token'))->with(
          'error',
          __('messages.expired_token')
        );
      }

      if ($user->activation_token != $token && $user->email_verified_at == null) {
        return redirect($redirect . '?error=' . __('messages.invalid_token'))->with(
          'error',
          __('messages.invalid_token')
        );
      }

      if ($user->markEmailAsVerified()) {
        $job = new PushNotificationWhenActiveAccount($user);
        dispatch($job);
        return redirect(
          $redirect . $active_url . '?email=' . $user->email . '&token=' . $user->activation_token . '&logout=1'
        )->with('verifySuccess', __('messages.verification_successfully'));
      }
    } catch (\Exception $e) {
      return response()->json(
        [
          'errors' => [
            '_messages' => ['There is error while activating your account. ' . $e->getMessage()],
          ],
        ],
        500
      );
    }
    return response()->json(
      [
        'errors' => [
          '_messages' => ['There is unknown error while activating your account.'],
        ],
      ],
      500
    );
  }
  public function lockUser(LockAccountAPIRequest $request)
  {
    $lockUser = $this->userRepository->find($request->get('id'));
    if (empty($lockUser)) {
      return $this->sendError(__('messages.not_found', ['model' => __('models/users.singular')]), 404);
    }
    $input = $request->all();
    $user = $this->userRepository->lockOrUnlock($input, $lockUser);
    $message = 'messages.account_locked';
    if (!$input['isLocked']) {
      $message = 'Thành công';
    }
    return $this->sendSuccess(__($message), new UserResource($user));
  }

  public function getProfileUser(Request $request)
  {
    $user = $this->userRepository->getUserDetail($request->get('id'));
    $message = 'messages.account_locked';
    return $this->sendSuccess(__($message), new UserResource($user));
  }
}

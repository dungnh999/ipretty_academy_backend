<?php

namespace App\Http\Controllers\API;

use App\Contract\CommonBusiness;
use App\Exports\UserTemplateExport;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateUserAPIRequest;
use App\Http\Requests\API\ImportAPIRequest;
use App\Http\Requests\API\InviteUserAPIRequest;
use App\Http\Requests\API\LockAccountAPIRequest;
use App\Http\Requests\API\ResendEmailActiveAPIRequest;
use App\Http\Requests\API\UpdateMyProfileAPIRequest;
use App\Http\Requests\API\UploadAvatarAPIRequest;
use App\Http\Requests\API\UploadMediaAPIRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\OverviewStudentResource;
use App\Http\Resources\UserResource;
use App\Imports\InviteUserImport;
use App\Imports\UserImport;
use App\Models\User;
use App\Models\UserDepartment;
use App\Notifications\SignupActivate;
use App\Repositories\AuthenticationRepository;
use App\Repositories\CourseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use \DateTime;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
// use Spatie\Permission\Models\Role;
use App\Models\Role;
use App\Repositories\UserDepartmentRepository;
use Illuminate\Support\Facades\Hash;

class UserAPIController extends AppBaseController
{
    use CommonBusiness;

    private $userRepository;
    private $authenticationRepository;
    private $courseRepository;
    private $userDepartmentRepository;
    private $user;

    public function __construct(UserRepository $userRepo, AuthenticationRepository $authenticationRepository, CourseRepository $courseRepository, UserDepartmentRepository $userDepartmentRepo)
    {
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

    public function getMyInformation()
    {
        if (Auth::check()) {
            $me = Auth::user();
            return response()->json([
                'success' => true,
                'user' => new UserResource($me)
            ]);
        } else {
            return $this->sendError(
                __('messages.errors.unauthorized')
            );
        }
    }

    public function updateAvatar(UploadAvatarAPIRequest $request)
    {
        if (!Auth::check()) {
            return $this->sendError(
                __('messages.errors.unauthorized')
            );
        }

        $userId = Auth::user()->id;

        $user = $this->userRepository->updateAvatar($userId, $request);

        return $this->sendResponse(new UserResource($user), __('auth.users.avatar_update_success') );
    }

    public function setLang(Request $request) {

        if (!Auth::check()) {
            return $this->sendError(
                __('messages.errors.unauthorized')
            );
        }

        $user = Auth::user();

        $input = $request->all();

        $user = $this->userRepository->updateMyProfile($input, $user);

        return $this->sendResponse(new UserResource($user), __('auth.users.update_success'));

    }

    public function updateMyProfile(UpdateMyProfileAPIRequest $request) {

        if (!Auth::check()) {
            return $this->sendError(
                __('messages.errors.unauthorized')
            );
        }

        $user = Auth::user();

        $input = $request->all();

        $user = $this->userRepository->updateMyProfile($input, $user, $request);

        // return $this->sendResponse($user, __('auth.users.update_success'));
        return $this->sendResponse(
            new UserResource($user),
            __('auth.users.update_success')
        );
    }

    public function CreateUserAccount (CreateUserAPIRequest $request) {


        if (!Auth::check()) {
            return $this->sendError(
                __('messages.errors.unauthorized')
            );
        }

        // $user = Auth::user();

        $input = $request->all();

        $newUser = $this->authenticationRepository->register($input, $request);

        return $this->sendSuccess(__('auth.registration.success_message'));

    }

    public function sendRequestResetPassword (Request $request) {

        $request->validate([
            'email' => 'required|email|exists:users',
        ], [
            'email.exists' => __('auth.emails.not_exist')
        ]);

        $input = $request->all();

        $user = User::where('email',  $input['email'])->get()->first();

        $resetPassword = $this->userRepository->submitForgetPasswordForm($input, $user);

        if ($resetPassword) {
            return $this->sendSuccess(__('auth.reset_password.success_message'));
        }else {
            return $this->sendError(__('auth.reset_password.unknown_error'), 503);
        }
    }

    public function getUserList (Request $request) {
        if (!Auth::check()) {
            return $this->sendError(
                __('messages.errors.unauthorized')
            );
        }

        $user = Auth::user();

        $userRoles = explode(',', $user->menuroles);

        if (!in_array('admin', $userRoles) && !in_array('teacher', $userRoles)) {
            return $this->sendError(
                __('messages.errors.not_permission'), 403
            );
        }

        $params = request()->query();

        $users = $this->userRepository->getUserList($params);

        return $this->sendResponse($users, __('messages.retrieved', ['model' => __('models/users.plural')]));

    }

    public function getUserDetail ($id) {

        $user = Auth::user();

        // $userRoles = explode(',', $user->menuroles);

        // if (!in_array('admin', $userRoles) && !in_array('teacher', $userRoles)) {
        //     return $this->sendError(
        //         __('messages.errors.not_permission'),
        //         403
        //     );
        // }

        $userDetail = $this->userRepository->find($id);

        if (empty($userDetail)) {

            return $this->sendError(__('messages.not_found', ['model' => __('models/users.singular')]), 404);
        }

        $isFullInformation = false;

        if ($user->hasRole(['admin']) || $user->id == $id) {
            $isFullInformation = true;
        }

        $userDetail["isFullInformation"] = $isFullInformation;

        $userDetail['department'] = $userDetail->department;

        return $this->sendResponse(
            new UserResource($userDetail),
            __('messages.retrieved', ['model' => __('models/users.singular')])
        );

        // return $this->sendResponse($userDetail, __('messages.retrieved', ['model' => __('models/users.singular')]));

    }


    public function updateUserProfile($id, UpdateMyProfileAPIRequest $request)
    {

        $user = $this->userRepository->find($id);

        if (empty($user)) {

            return $this->sendError(__('messages.not_found', ['model' => __('models/users.singular')]), 404);
        }

        $input = $request->all();

        $userUpdate = $this->userRepository->updateMyProfile($input, $user, $request);

        return $this->sendResponse(
            new UserResource($userUpdate),
            __('auth.users.update_success')
        );
    }

    public function myCourses (Request $request) {

        $params = request()->query();

        $courses = $this->courseRepository->getCoursesByCondition($params);

        return $this->sendResponse(
            $courses,
            __('messages.retrieved', ['model' => __('models/courses.singular')])
        );
    }

    public function getUserByRole (Request $request) {
        $params = request()->query();
        $template = 0;
        if (isset($params['role']) && $params['role'] != null) {

            $validRoles = ['student', 'teacher', 'admin', 'leader', 'employee', 'user'];
            $role = $params['role'] ;
            if (!in_array($params['role'], $validRoles)) {
                return $this->sendError(__('validation.role.in', ['attribute' => __('models/users.fields.role'), 'values' => implode(',', $validRoles) ] ), 404);
            }

            $users = $this->userRepository->getUserByRole($params['role'], $params);
            $oneColumn = isset($params["oneColumn"]) ? $params["oneColumn"] : 0;
            $userDepartments = $this->userDepartmentRepository->paginate(10);

            if (isset($params["export"]) && $params["export"]) {
                $folder_file = Carbon::parse(Carbon::now())->format('Y_m_d_H_i_s') . '_' . '_accounts_export';
                mkdir(storage_path('/app/' . $folder_file), 0700);

                if (count($users)) {
                    (new UserTemplateExport($users, $template, $role, $oneColumn, $userDepartments))->store($folder_file . '/' . "-accounts-export.xlsx");
                    return response()->file(storage_path('/app/' . $folder_file . '/' . "-accounts-export.xlsx"))->deleteFileAfterSend(true);
                }
                else {
                    return $this->sendError(__('validation.required', ['attribute' => __('models/users.fields.role')]), 422);
                }
            }

            return $this->sendResponse(
                $users,
                __('messages.retrieved', ['model' => __('models/users.singular')])
            );
        }else {
            return $this->sendError(__('validation.required', ['attribute' => __('models/users.fields.role')]), 422);
        }

    }

    public function getPositions(Request $request) {

        $params = request()->query();

        // dd($params);

        $positions = $this->userRepository->getPositions($params);

        return $this->sendResponse(
            $positions,
            __('messages.retrieved', ['model' => __('models/users.fields.position')])
        );
    }

    public function resendEmailActive (ResendEmailActiveAPIRequest $request) {

        $existUser = $this->userRepository->findByEmail($request->email);

        if (empty($existUser)) {

            return $this->sendError(__('messages.not_found', ['model' => __('models/users.fields.email')]), 404);
        }

        if ($existUser->email_verified_at != null) {

            return $this->sendError(__('messages.activated', ['model' => __('models/users.fields.email')]), 404);

        }

        $existUser->notify(new SignupActivate($existUser));

        return $this->sendSuccess(__('auth.registration.resend_active_email'));

    }

    public function getFreeCourses (Request $request) {
        $params = request()->query();

        $courses = $this->courseRepository->getFreeCourses($params);

        return $this->sendResponse(
            $courses,
            __('messages.retrieved', ['model' => __('models/courses.singular')])
        );
    }

    public function lockUser (LockAccountAPIRequest $request, $user_id) {
        $lockUser = $this->userRepository->find($user_id);
        if (empty($lockUser)) {
            return $this->sendError(__('messages.not_found', ['model' => __('models/users.singular')]), 404);
        }
        $input = $request->all();
        $user = $this->userRepository->lockOrUnlock($input, $lockUser);
        $message = 'messages.account_locked';
        if (!$input["isLocked"]) {
            $message = 'messages.account_unlocked';
        }

        return $this->sendResponse(
            new UserResource($user),
            __($message)
        );
    }

    public function reActivateAccount ($user_id) {
        $activeUser = $this->userRepository->find($user_id);

        if (empty($activeUser)) {
            return $this->sendError(__('messages.not_found', ['model' => __('models/users.singular')]), 404);
        }

        $user = $this->userRepository->activeAccount($activeUser);

        return $this->sendResponse(
            new UserResource($user),
            __('auth.users.active_success')
        );
    }

    public function inviteUser (InviteUserAPIRequest $request) {

        $input = $request->all();

        $this->authenticationRepository->register($input);

        return $this->sendSuccess(__('auth.registration.success_invite'));
    }

    public function downloadTemplate (Request $request) {

        // $input = $request->all();
        $template = 1;

        $params = request()->query();

        $userDepartments = $this->userDepartmentRepository->paginate(10);

        $oneColumn = isset($params["oneColumn"]) ? $params["oneColumn"] : 0;
        $role_user = isset($params["role"]) ? $params["role"] : 'admin';
        $folder_file = Carbon::parse(Carbon::now())->format('Y_m_d_H_i_s') . '_' . '_template_export';

        mkdir(storage_path('/app/' . $folder_file), 0700);

        // dd($oneColumn);

        $users = [];
        (new UserTemplateExport($users, $template, $role_user, $oneColumn, $userDepartments))->store($folder_file . '/' . "-template-export.xlsx");

        return response()->file(storage_path('/app/' . $folder_file . '/' . "-template-export.xlsx"))->deleteFileAfterSend(true);
    }

    public function importUser(ImportAPIRequest $request) {

        $params = request()->query();

        // dd($params['role']);

        if ($request->hasFile('importFile')) {

            $file = $request->file('importFile');

            $import = new InviteUserImport;

            $users = Excel::toCollection($import, $file);

            $rows = $users[0];

            // dd($rows);

            if (count($rows) == 2 && $rows[1][0] == null) {
                return $this->sendError(__('import.import_file_empty'), 422);
            }

            $failRows = [
                "name_required" => [],
                "invalid_format" => [],
                "exists_email" => [],
                "invalid_format_code" => [],
                "exists_code" => [],
                "length_code" => [],
                "exists_department_id" => [],
                "department_id_required" => [],
                "exists_role" => [],
                "role_required" => [],
                "invalid_format_phone" => [],
                "phone_is_required" => [],
                "invalid_role_and_department" => [],
                "no_data" => [],
                "email_required" => []
            ];

            $successRows = [];

            for ($i = 1; $i < count($rows); $i++) {

                if ($rows[$i][0] != null) {

                    $fail = 0;

                    $success = 0;

                    if ( $rows[$i][1] == null && $rows[$i][2] == null && $rows[$i][3] == null && $rows[$i][4] == null && ($params['role'] != 'teacher' && isset($rows[$i][5]) && $rows[$i][5] == null)) {

                        array_push($failRows["no_data"], $i);

                        $fail = $fail + 1;

                    }

                    if ($rows[$i][1] == null) {

                        array_push($failRows["name_required"], $i);

                        $fail = $fail + 1;

                    }

                    if ($rows[$i][3] != null) {

                        if (!$this->checkemail($rows[$i][3])) {

                            array_push($failRows["invalid_format"], $rows[$i][3]);

                            $fail = $fail + 1;

                        }else {

                            $user = User::where('email', '=', $rows[$i][3])->first();

                            if ($user) {

                                array_push($failRows["exists_email"], $rows[$i][3]);

                                $fail = $fail + 1;

                            }
                        }
                    } else {

                        array_push($failRows["email_required"], $i);

                        $fail = $fail + 1;

                    }

                    $params = request()->query();
                    if (isset($params['role']) && $params['role'] != null) {
                        $role = Role::where('name', '=', $params['role'])->first();

                        if (!$role) {

                            array_push($failRows["exists_role"], $rows[$i][4]);

                            $fail = $fail + 1;

                        }else {
                            if($role->name == 'teacher') {
                                $department = UserDepartment::where('department_name', DERARTMENT_NAME_EDUCATE)->select('department_id')->first();
                                if (!$department) {

                                    array_push($failRows["exists_department_id"], DERARTMENT_NAME_EDUCATE);

                                    $fail = $fail + 1;
                                }
                            }
                        }
                    }

                    if($role->name != 'user' && $role->name != 'teacher') {

                        if ($params['role'] != 'teacher') {

                            if ($rows[$i][5] != null) {

                                $userDepartment = UserDepartment::where('department_id', '=', $rows[$i][5])->first();

                                if (!$userDepartment) {

                                    array_push($failRows["exists_department_id"], $rows[$i][5]);

                                    $fail = $fail + 1;
                                }

                            }else {

                                array_push($failRows["department_id_required"], $i);

                                $fail = $fail + 1;
                            }

                        }

                    }

                    if ($rows[$i][2] != null) {

                        if (strlen($rows[$i][2]) < 5) {

                            array_push($failRows["length_code"], $rows[$i][2]);

                            $fail = $fail + 1;

                        }else if (!$this->checkcode($rows[$i][2])) {

                            array_push($failRows["invalid_format_code"], $rows[$i][2]);

                            $fail = $fail + 1;

                        }else {

                            $user = User::where('code', '=', $rows[$i][2])->first();

                            if ($user) {

                                array_push($failRows["exists_code"], $rows[$i][2]);

                                $fail = $fail + 1;
                            }
                        }
                    } else {

                        $randomString = Str::random(5);

                        do {

                            $exist_code = User::where('code', $randomString)->first();

                            if ($exist_code) {

                                $randomString = Str::random(5);
                            }

                        } while ($exist_code);

                        $code = $randomString;
                    }

                    if ($rows[$i][4] != null) {

                        if (!$this->checkphone($rows[$i][4])) {

                            array_push($failRows["invalid_format_phone"], $rows[$i][4]);

                            $fail = $fail + 1;
                        }
                    }else {
                        array_push($failRows["phone_is_required"], $i);

                        $fail = $fail + 1;
                    }


                    if(isset($department)) {

                        $department_id = $department->department_id;

                    }else {

                        if($role->name != 'user' && $params['role'] != 'teacher') {

                            if (isset($rows[$i][5]) && $rows[$i][5] != null) {

                                $department_id =  $rows[$i][5];

                            }
                        }
                    }

                    if ($params['role'] != 'teacher') {

                        if ($rows[$i][5] == 1) {

                            $role->name = 'teacher';

                        }

                    }

                    if ($fail == 0) {

                        $password = Str::random(6);

                        $user = new User([
                            'name' => $rows[$i][1],
                            'code' => isset($code) ? $code : $rows[$i][2],
                            'email' => $rows[$i][3],
                            'phone' => $rows[$i][4],
                            'department_id' => isset($department_id) ? $department_id : null,
                            'activation_token' => Str::random(60),
                            'password' => Hash::make($password),
                        ]);

                        if ($params['role'] == 'teacher') {
                            if ($user->assignRole(['employee'])) {
                                $user->menuroles = 'employee';
                                $user->save();
                            }
                            if ($user->givePermissionTo(
                                PERMISSION["MANAGE_COURSES"],
                                PERMISSION["VIEW_COURSE"],
                                PERMISSION["UPDATE_COURSE"],
                                PERMISSION["DELETE_COURSE"],
                                PERMISSION["MANAGE_LEADERS"],
                                PERMISSION["MANAGE_STUDENTS"]
                            )) {

                                $user->save();

                            }
                        }else {
                            if ($user->assignRole($role->name)) {

                                $user->menuroles = $role->name;

                                $user->save();
                            }
                        }

                        $user->notify(new SignupActivate($user, $password));

                        $success = $i + 1;

                        array_push($successRows, $success);

                    }
                }
            }
            if ((count($failRows["invalid_format"]) ||
                count($failRows["invalid_format_code"]) ||
                count($failRows["length_code"]) ||
                count($failRows["exists_email"]) ||
                count($failRows["exists_department_id"]) ||
                count($failRows["department_id_required"]) ||
                count($failRows["role_required"]) ||
                count($failRows["invalid_format_phone"]) ||
                count($failRows["phone_is_required"]) ||
                count($failRows["exists_role"]) ||
                count($failRows["invalid_role_and_department"]) ||
                count($failRows["no_data"]) ||
                count($failRows["email_required"]) ||
                count($failRows["name_required"]) ||
                count($failRows["exists_code"])) && !count($successRows) ) {

                return $this->sendError(__('import.fail_import_user'), 422, $failRows );

            }else if ((count($failRows["invalid_format"]) ||
                count($failRows["invalid_format_code"]) ||
                count($failRows["exists_email"]) ||
                count($failRows["length_code"]) ||
                count($failRows["exists_department_id"]) ||
                count($failRows["department_id_required"]) ||
                count($failRows["role_required"]) ||
                count($failRows["invalid_format_phone"]) ||
                count($failRows["phone_is_required"]) ||
                count($failRows["exists_role"]) ||
                count($failRows["invalid_role_and_department"]) ||
                count($failRows["no_data"]) ||
                count($failRows["email_required"]) ||
                count($failRows["name_required"]) ||
                count($failRows["exists_code"])) && count($successRows)) {

                return $this->sendResponseWithError($successRows, __('import.success_import_user'), $failRows);
            }
            return $this->sendSuccess(__('import.success_import_user'));
        }else {
            return $this->sendError(__('import.missing_file'));
        }
    }

    public function inviteUserByImport (ImportAPIRequest $request) {

        if ($request->hasFile('importFile')) {

            $file = $request->file('importFile');

            $import = new InviteUserImport;

            $users = Excel::toCollection($import, $file);

            $rows = $users[0];

            $failRows = [
                "invalid_format" => [],
                "exists_email" => [],
            ];
            $successRows = [];

            if (count($rows) == 2 && $rows[1][0] == null ) {
                return $this->sendError(__('import.import_file_empty'), 422);
            }

            for ($i = 1; $i < count($rows); $i++) {
                $fail = 0;
                $success = 0;

                if ($rows[$i][0] != null) {

                    if (!$this->checkemail($rows[$i][0])) {
                        array_push($failRows["invalid_format"], $rows[$i][0]);
                        $fail = $fail + 1;
                    }else {
                        $user = User::where('email', '=', $rows[$i][0])->first();

                        if ($user) {
                            array_push($failRows["exists_email"], $rows[$i][0]);
                            $fail = $fail + 1;
                        }
                    }
                }

                if ($fail == 0) {

                    $input["email"] = $rows[$i][0];
                    $newUser = $this->authenticationRepository->register($input);

                    if ($newUser) {
                        $success = $i + 1;

                        array_push($successRows, $success);
                    }
                }
            }

            if ((count($failRows["invalid_format"]) || count($failRows["exists_email"])) && !count($successRows) ) {

                return $this->sendError(__('import.fail_import_invite'), 422, $failRows );

            }else if ((count($failRows["invalid_format"]) || count($failRows["exists_email"])) && count($successRows)) {

                return $this->sendResponseWithError($successRows, __('import.success_import_invite'), $failRows);

            }

            return $this->sendSuccess(__('import.success_import_invite'));

        } else {
            return $this->sendError(__('import.missing_file'));
        }


    }

    function checkemail($str)
    {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
    }

    function checkcode($str)
    {
        return (!preg_match("/^\S*$/", $str)) ? FALSE : TRUE;
    }

    function checkphone($str)
    {
        return (!preg_match("/(0)[0-9]/", $str)) || (preg_match("/[a-z]/", $str)) ? FALSE : TRUE;
    }

    public function feature_teachers () {
        $teachers = $this->userRepository->feature_teachers();

        return $this->sendResponse(
            $teachers,
            __('messages.retrieved', ['model' => __('models/users.plural')])
        );
    }

    public function feature_members () {
        $members = $this->userRepository->feature_members();
        return $this->sendResponse(
            $members,
            __('messages.retrieved', ['model' => __('models/users.plural')])
        );
    }

    public function logout(Request $request) {

        $this->user->token()->revoke();

        return $this->sendSuccess(__('auth.login.logout_successfully'));
    }

    public function overviewDataForUser () {

        $user = $this->user;

        $overviewData = $user->myOverviewCourse();

        return $this->sendResponse(
            new OverviewStudentResource($overviewData),
            __('messages.retrieved', ['model' => __('models/users.plural')])
        );
    }

    public function uploadMedia (UploadMediaAPIRequest $request) {

        $user = $this->user;

        if ($request->hasFile('image_attachment') && $request->file('image_attachment')->isValid()) {
            $media = $user->addMediaFromRequest('image_attachment')->toMediaCollection();
            $mediaUrl = $media->getUrl();
            $media->url = $mediaUrl;
            return $this->sendResponse(
                new MediaResource($media),
                __('messages.uploaded', ['model' => __('models/users.fields.image_attachment')])
            );
        }

        return $this->sendError(__('messages.upload_image_fail'));

    }

}

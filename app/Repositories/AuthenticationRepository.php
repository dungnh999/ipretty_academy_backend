<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserDepartment;
use App\Notifications\SignupActivate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * Class AuthenticationRepository
 * @package App\Repositories
 * @version July 7, 2021, 10:37 am UTC
 */
class AuthenticationRepository extends BaseRepository
{
  /**
   * @var array
   */
  protected $fieldSearchable = [];

  /**
   * Return searchable fields
   *
   * @return array
   */
  public function getFieldsSearchable()
  {
    return $this->fieldSearchable;
  }

  /**
   * Configure the Model
   **/
  public function model()
  {
    return User::class;
  }

  /**
   * @Override
   */
  public function login($input)
  {
    if (!auth()->attempt($input)) {
      return null;
    } else {
      $user = auth()->user();

      if (!$user->hasVerifiedEmail()) {
        $response['active'] = false;
        return $response;
      }

      if ($user->isLocked) {
        $response['isLocked'] = true;
        return $response;
      }

      $accessToken = auth()
        ->user()
        ->createToken('authToken')->accessToken;
      $response['user'] = $user;
      $response['accessToken'] = $accessToken;

      return $response;
    }
  }

  public function register($input, $request = null)
  {
      //    // Kiểm tra tồn tại
    if (User::where('email', $input['email'])->count() != 0) {
      $response['is-exist'] = 1;
      return $response;
    }
    $password = $input['password'] ?? Str::random(6);
    $department_id = $input['department_id'] ?? null;
    $gender = $input['gender'] ?? 'Male';
    $birthday = $input['birthday'] ?? null;
    $address = $input['address'] ?? null;
    $name = $input['name'] ?? null;
    $phone = $input['phone'] ?? null;
    $about = $input['about'] ?? null;
    $role = $input['position'] ?? ENUM_PREFIX_ROLE_EMPLOYEE;

    // role : 1 Giáo viên , 0 Quản lý , 2 Học viên , 3 Nhân viên
    $prefix = TEXT_PREFIX_ROLE_EMPLOYEE;
    $count = User::where('menuroles', $role)->count();
    switch ($role) {
      case ENUM_POSITION_ADMIN:
        $prefix = TEXT_PREFIX_ROLE_MANAGER;
        break;
      case ENUM_POSITION_TEACHER:
        $prefix = TEXT_PREFIX_ROLE_TEACHER;
        break;
      case ENUM_PREFIX_ROLE_EMPLOYEE:
        $prefix = TEXT_PREFIX_ROLE_EMPLOYEE;
        break;
      case ENUM_POSITION_STUDENT:
        $prefix = TEXT_PREFIX_ROLE_USER;
        break;
    }

    // render code
    $code = $prefix . str_pad($count + 1, 8, '0', STR_PAD_LEFT);

    $meta = new \stdClass();
    if (!empty($input['company'])) {
      $meta->company = $input['company'];
    }

    if (!empty($input['position'])) {
      $meta->position = $input['position'];
    }

    if (!empty($input['department'])) {
      $meta->department = $input['department'];
    }
    $user = User::create([
      'email' => $input['email'],
      'password' => Hash::make($password),
      'activation_token' => Str::random(60),
      'department_id' => $department_id,
      'gender' => $gender,
      'code' => $code,
      'name' => $name,
      'phone' => $phone,
      'menuroles' => $role,
      'address' => $address,
      'birth_day' => $birthday,
      'about' => $about,
      'meta' => json_encode($meta),
    ]);

    $user->save();

    //    switch ($role) {
    //      case ENUM_POSITION_ADMIN:
    //        $permissionsAdmin = PERMISSION;
    //        foreach ($permissionsAdmin as $key => $permissionAdmin) {
    //          $user->givePermissionTo($permissionAdmin);
    //          $user->save();
    //        }
    //        break;
    //      case ENUM_PREFIX_ROLE_TEACHER:
    //        break;
    //      case ENUM_PREFIX_ROLE_EMPLOYEE:
    //        break;
    //      case ENUM_PREFIX_ROLE_USER:
    //        $prefix = TEXT_PREFIX_ROLE_USER;
    //        break;
    //    }
    //
    //    if ($user->assignRole(explode(',', $role))) {
    //      $user->menuroles = $role;
    //      $user->save();
    //    }
    //    if (isset($input['role'])) {
    //      if (
    //        isset($input['isTeacher']) &&
    //        $input['isTeacher'] &&
    //        $input['isTeacher'] != 'false' &&
    //        $input['role'] == 'employee'
    //      ) {
    //        if (
    //          $user->givePermissionTo(
    //            PERMISSION['MANAGE_COURSES'],
    //            PERMISSION['VIEW_COURSE'],
    //            PERMISSION['UPDATE_COURSE'],
    //            PERMISSION['DELETE_COURSE'],
    //            PERMISSION['MANAGE_LEADERS'],
    //            PERMISSION['MANAGE_STUDENTS']
    //          )
    //        ) {
    //          $user->save();
    //        }
    //      } elseif (isset($input['role']) && $input['role'] == 'admin') {
    //
    //      }
    //    }
    //    $user->handleMedia($request);
    $user->menuroles = $role;
    $user->notify(new SignupActivate($user, $password));
    $user['info_password'] = $password;
    return $user;
  }

  public function changePassword($input)
  {
    $hashedPassword = Auth::user()->password;
    if (Hash::check($input['current_password'], $hashedPassword)) {
      $user = Auth::user();
      $user->password = bcrypt($input['new_password']);
      User::where('id', $user->id)->update(['password' => $user->password]);

      return true;
    } else {
      return false;
    }
  }

  public function loginByToken($input)
  {
    $query = $this->model->newQuery();
    $user = $query
      ->where('email', $input['email'])
      ->where('activation_token', $input['token'])
      ->first();
    if ($user) {
      Auth::login($user);
      if (!$user->hasVerifiedEmail()) {
        $response['active'] = false;
        return $response;
      }

      if ($user->isLocked) {
        $response['isLocked'] = true;
        return $response;
      }

      $accessToken = auth()
        ->user()
        ->createToken('authToken')->accessToken;
      $response['user'] = $user;
      $response['accessToken'] = $accessToken;

      $user->activation_token = '';
      $user->save();

      return $response;
    } else {
      $response['emailOrToken'] = false;
      return $response;
    }
  }
}

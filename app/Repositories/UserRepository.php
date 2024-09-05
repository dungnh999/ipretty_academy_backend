<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Models\Course;
use App\Models\District;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Models\Post;
use App\Models\Province;
use App\Models\ResetPassword;
use App\Models\SilcoinCampaign;
use App\Models\UserAddresses;
use App\Models\Ward;
use App\Notifications\ResetPasswordRequest;
use App\Notifications\SignupActivate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version July 8, 2021, 7:13 am UTC
 */
class UserRepository extends BaseRepository
{
  use CommonBusiness;

  /**
   * @var array
   */
  protected $fieldSearchable = [
    'email', 'name', 'phone', 'address', 'code'
  ];

  protected $relations = ['department'];

  protected $relationSearchable = [
    'department_name'
  ];


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

  public function updateMyProfile($data_user, $user, $request = null)
  {

    $query = $this->model->newQuery();

    if (!empty($data_user['birthday'])) {
      $newDate = date("Y-m-d", strtotime($data_user['birthday']));
      $user->birth_day = $newDate;
    }

    if (!empty($data_user['phone']))
      $user->phone = $data_user['phone'];

    if (!empty($data_user['name']))
      $user->name = $data_user['name'];

    if (!empty($data_user['address']))
      $user->address = $data_user['address'];

    if (!empty($data_user['id_number']))
      $user->id_number = $data_user['id_number'];

    if (!empty($data_user['lang']))
      $user->lang = $data_user['lang'];

    if (!empty($data_user['code']))
      $user->code = $data_user['code'];

    if (!empty($data_user['gender']))
      $user->gender = $data_user['gender'];

    if (!empty($data_user['about'])) {
      $user->about = $data_user['about'];
    } else {
      $user->about = null;
    }

    if (isset($data_user['department_id']) && $data_user['department_id'] != "null")
      $user->department_id = $data_user['department_id'];

    $meta = new \stdClass;

    if (isset($user->meta) && $user->meta != null) {
      $meta = json_decode($user->meta);
    }

    if (!empty($data_user['company'])) {
      $meta->company = $data_user['company'];
    } else {
      $meta->company = "";
    }

    if (!empty($data_user['position'])) {
      $meta->position = $data_user['position'];
    } else {
      $meta->position = "";
    }

    if (!empty($data_user['department_name'])) {
      // dd(2);
      $meta->department = $data_user['department_name'];
      $user->department_id = null;
    } else {
      $meta->department = "";
    }

    if (isset($data_user['isLocked'])) {
      $user->isLocked = $data_user['isLocked'];
    }

//    if (isset($data_user['role'])) {
//      $permissionsAdmin = PERMISSION;
//
//      $role = $data_user['role'];
//      $user->roles()->detach();
//
//      $user->assignRole($role);
//      $user->menuroles = $role;
//
//      if ($role == 'user') {
//        $user->permissions()->detach();
//
//      } else if ($role == 'admin') {
//        foreach ($permissionsAdmin as $key => $permissionAdmin) {
//          $user->givePermissionTo($permissionAdmin);
//        }
//      }
//    }

//    if (isset($data_user['isTeacher']) && ($user->hasRole('employee') || (isset($data_user['role']) && $data_user['role'] == 'employee'))) {
//      $isTeacher = $data_user['isTeacher'];
//      $permissionsTeacher = PERMISSION_TEACHER;
//      $user->permissions()->detach();
//
//      if ($isTeacher) {
//        foreach ($permissionsTeacher as $key => $permissionTeacher) {
//          $user->givePermissionTo($permissionTeacher);
//        }
//      } else {
//        foreach ($permissionsTeacher as $key => $permissionTeacher) {
//          $user->revokePermissionTo($permissionTeacher);
//        }
//      }
//    }

//    if (!empty($data_user['email_verified_at'])) {
//      $user->email_verified_at = $data_user['email_verified_at'];
//      $user->activation_token = null;
//    }
//
//    $user->meta = json_encode($meta);
//
//    $user->handleMedia($request);

    $user->save();

    $model = $query->find($user->id);

    return $model;
  }

  public function registerAccountUser($input)
  {
    $user = User::create([
      'name' => $input['name'],
      'email' => $input['email'],
      'joined_by_referral_code' => isset($input['joined_by_referral_code']) ? $input['joined_by_referral_code'] : NULL,
      'password' => Hash::make($input['password']),
      'activation_token' => Str::random(60),
      'referral_campaign' => isset($input['referral_campaign']) ? $input['referral_campaign'] : null,
    ]);

    $user->assignRole('user');
    $this->generateReferralCode($user);

    $user->notify(new SignupActivate($user, $input['password']));

    return $user;
  }

  protected function generateReferralCode($user)
  {
    $referralCode = $user->id . $this->randPass();
    $user->referral_code = $referralCode;
    $user->save();
  }

  function randPass($length = 5, $strength = 5)
  {
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength >= 1) {
      $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength >= 2) {
      $vowels .= "AEUY";
    }
    if ($strength >= 4) {
      $consonants .= '23456789';
    }
    if ($strength >= 8) {
      $consonants .= '@#$%';
    }
    $consonants_1 = '@#$%';
    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
      if ($alt == 1) {
        $password .= $consonants[(rand() % strlen($consonants))];
        $alt = 0;
      } else {
        $password .= $vowels[(rand() % strlen($vowels))];
        $alt = 1;
      }
      $especially = $consonants_1[(rand() % strlen($consonants_1))];
    }
    return $password . $especially;
  }

  public function handleSilcoinForUser($input, $user_id)
  {
    $silcoin = 0;
    if (isset($input['silcoin']) && $input['silcoin'] != "") {
      $silcoin = $input['silcoin'];
    }
    $check_type = is_numeric($input['silcoin']);
    if ($silcoin == 0 || !$check_type) {
      return true;
    }
    $get_user = User::where('id', $user_id)->first();
    $get_user->silcoin = $get_user->silcoin + $silcoin;
    $get_user->save();
    return $get_user;
  }

  public function changeUserPoints($inputData, $userOrId)
  {
    $user = $userOrId;
    if (is_numeric($userOrId)) {
      $user = User::where('id', $userOrId)->first();
      if (!$user) return false;
    }
    $user->point = $user->point + $inputData['points'];
    $user->save();
    return true;
  }

  public function getMe($user)
  {
    $user = $user->with('address')
      ->with('address.province')
      ->with('address.district')
      ->with('address.ward')
      ->first();
    return $user;
  }

  public function getListProvice()
  {
    $province = Province::all()->pluck("name", "id");
    return $province;
  }

  public function getListDistric($province_id = NULL)
  {
    if ($province_id != NULL) {
      $distric = District::where('province_id', $province_id)->get();
    } else {
      $distric = District::all()->pluck("name", "id");
    }
    return $distric;
  }

  public function getListWard($province_id = NULL, $district_id = NULL)
  {
    if ($province_id != NULL || $district_id == NULL) {
      $ward = Ward::where('province_id', $province_id)->where('district_id', $district_id)->get();
    } else {
      $ward = Ward::all()->pluck("name", "id");
    }
    return $ward;
  }

  public function updateAvatar($id, $request = null)
  {
    $query = $this->model->newQuery();

    $model = $query->findOrFail($id);

    // override media
    $model->handleMedia($request);

    $model->save();

    return $model;
  }

  public function show($id)
  {
    $query = $this->model->newQuery();

    $model = $query->findOrFail($id);

    return $model;
  }

  public function createShipping($data_user, $user, $type)
  {
    $user_addresses = UserAddresses::where('user_id', '=', $user->id)->get();
    if (isset($data_user['is_default']) && count($user_addresses) > 0) {
      foreach ($user_addresses as $item) {
        $item->is_default = 0;
        $item->save();
      }
    }

    if ($type == 'create') {
      $user_address = UserAddresses::create([
        'name' => $user['name'],
        'user_id' => $user['id'],
        'address' => isset($data_user['address']) ? $data_user['address'] : NULL,
        'province_id' => $data_user['province_id'],
        'district_id' => $data_user['district_id'],
        'ward_id' => $data_user['ward_id'],
        'is_default' => isset($data_user['is_default']) ? 1 : 0,
        'is_shipping' => true,
        'phone_shipping' => isset($data_user['phone_shipping']) ? $data_user['phone_shipping'] : NULL
      ]);
    } else {
      $user_address = UserAddresses::find($data_user['id']);
      $user_address->address = $data_user['address'];
      $user_address->province_id = $data_user['province_id'];
      $user_address->district_id = $data_user['district_id'];
      $user_address->ward_id = $data_user['ward_id'];
      $user_address->is_default = isset($data_user['is_default']) ? 1 : 0;
      $user_address->save();
    }
    $addresses = User::where('users.id', $user->id)
      ->leftJoin('user_addresses', 'users.id', '=', 'user_addresses.user_id')
      ->leftJoin('province', 'user_addresses.province_id', '=', 'province.id')
      ->leftJoin('district', 'user_addresses.district_id', '=', 'district.id')
      ->leftJoin('ward', 'user_addresses.ward_id', '=', 'ward.id')
      ->select('user_addresses.id', 'user_addresses.user_id', 'user_addresses.address', 'user_addresses.is_default', 'user_addresses.province_id', 'user_addresses.district_id', 'user_addresses.ward_id', 'province.name as province_name', 'ward.name as ward_name', 'district.name as district_name')
      ->get();
    return $addresses;
  }

  public function getListAddress($userId)
  {
    $addresses = User::where('users.id', $userId)
      ->leftJoin('user_addresses', 'users.id', '=', 'user_addresses.user_id')
      ->leftJoin('province', 'user_addresses.province_id', '=', 'province.id')
      ->leftJoin('district', 'user_addresses.district_id', '=', 'district.id')
      ->leftJoin('ward', 'user_addresses.ward_id', '=', 'ward.id')
      ->select(DB::raw("CONCAT(IFNULL(user_addresses.address, ''),' ', ward.name,' ', district.name,' ', province.name) as full_address"), 'user_addresses.*')
      ->get();
    // dd($addresses);
    return $addresses;
  }

  public function getUsersByIds($userIdsList)
  {
    $query = $this->model->newQuery();
    $users = $query->whereIn('id', $userIdsList)->select('id', 'name', 'email', 'avatar')->get();
    return $users;
  }

  public function submitForgetPasswordForm($input, $user)
  {
    $resetPassword = ResetPassword::create([
      'email' => $input['email'],
      'token' => Str::random(60),
    ]);

    $isAdminPage = 0;

    if (isset($input["isAdminPage"]) && $input["isAdminPage"]) {
      $isAdminPage = $input["isAdminPage"];
    }
    // dd($resetPassword);
    $resetPassword->notify(new ResetPasswordRequest($resetPassword->token, $user->email, $user->name, $isAdminPage));

    return $resetPassword;
  }

  public function getUserListByAllQuery($params)
  {

    $search = [];

    if (!empty($params['keyword'])) {

      $search = $this->searchArray($this->fieldSearchable, $params['keyword']);
    }

    $query = $this->allQuery($search, null, null, $this->relations);

    $users = $query->paginate($this->perpage);

    return $users;

  }

  public function getUserList($params)
  {

    // dd($params['keyword']);

    $query = $this->model->newQuery()->with('department')->where('menuroles', 'not like', '%admin%');
    // dd($query->toSql());

    if (!empty($params['account_type'])) {

      $account_type = $params['account_type'];

      if ($account_type == 'internal') {

        $query = $query->whereNotNull('department_id')->where('menuroles', 'not like', '$teacher%');
      } else if ($account_type == 'employee') {

        $query = $query->whereNotNull('department_id');
      } else if ($account_type == 'external') {

        $query = $query->whereNull('department_id');
      }
    }

    // $query = $this->allQuery($search, null, null, $this->relations);

    if (!empty($params['keyword'])) {

      $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);

      // return $query->toSql();
    }


    $users = $query->paginate(PERPAGE);

    return $users;

  }

  public function searchArray($fieldSearchable, $keyword)
  {

    if (count($fieldSearchable) > 0) {
      return array_fill_keys($fieldSearchable, $keyword);
    }

    return [];
  }

  public function getUserDetail($id)
  {
    $query = $this->model->newQuery()->with('department');
    $user = $query->find($id);
    return $user;

  }

  public function getUserByRole($findRole, $params = null)
  {

    $table = $this->model->getTable();
    $user = auth()->user();
    $query = $this->model->newQuery();
    // ->with('department', function ($q) {
    //     $q->select('department_id', 'department_name');
    // });
    if ($findRole == "admin") {
      $model = $query->role($findRole);

    } else if ($findRole == "student") {
      $model = $query;
      if (isset($params['type_account']) && $params['type_account'] != null) {

        $type_account = $params['type_account'];

        $types_account = explode(',', $type_account);

        if (count($type_account) < 2) {
          $model = $query->whereHas("roles", function ($q) use ($type_account) {
            $q->where("name", $type_account);
          });
        } else {
          $model = $query->whereHas("roles", function ($q) {
            $q->where(function ($oq) {
              $oq->orwhere("name", "user")
                ->orwhere("name", "employee");
            });
          });
        }


      } else {
        if (isset($params['course_type']) && $params['course_type'] != null) {
          $course_type = $params['course_type'];
          if ($course_type == "Local") {
            $model = $query->whereHas("roles", function ($q) {
              $q->where(function ($oq) {
                $oq->orwhere("name", "employee");
              });
            });
          }
        } else {
          $model = $query->whereHas("roles", function ($q) {
            $q->where(function ($oq) {
              $oq->orwhere("name", "user")
                ->orwhere("name", "employee");
            })->where("name", "!=", "admin")
              ->where("name", "!=", "teacher");
          });
        }
      }

      if (isset($params['department_ids']) && $params['department_ids'] != null) {
        $department_ids = explode(',', $params['department_ids']);
        $model = $model->whereIn('department_id', $department_ids);
      }

      if (isset($params['positions']) && $params['positions'] != null) {
        $positions = explode(',', $params['positions']);
        $model = $model->where(function ($q) use ($positions) {
          foreach ($positions as $key => $position) {
            // $q->orwhere('meta->position', '=', "'$position'");
            $q->whereRaw("json_unquote(json_extract(`meta`, '$.\"position\"')) = " . "'$position'");
          }
        });

      }

    } else if ($findRole == "leader") {

      $model = $query->whereHas("roles", function ($q) {
        $q->where("name", "employee")->where("name", "!=", "teacher");
      });

      if (isset($params['department_ids']) && $params['department_ids'] != null) {
        $department_ids = explode(',', $params['department_ids']);
        $model = $model->whereIn('department_id', $department_ids);
      }

      if (isset($params['positions']) && $params['positions'] != null) {
        $positions = explode(',', $params['positions']);
        $model = $model->where(function ($q) use ($positions) {
          foreach ($positions as $key => $position) {
            // $q->orwhere('meta->position', '=', "'$position'");
            $q->whereRaw("json_unquote(json_extract(`meta`, '$.\"position\"')) = " . "'$position'");
          }
        });
      }

    } else if ($findRole == "teacher") {
      // dd($query->whereHas("roles", function ($q) {
      //     $q->where("name", "employee");
      // })->get());
      $model = $query->whereHas("roles", function ($q) {
        $q->where("name", "employee");
      })->whereHas('permissions', function ($q) {
        $q->where("name", PERMISSION["MANAGE_COURSES"]);
      });
    } else if ($findRole == "user") {
      $model = $query->whereHas("roles", function ($q) {
        $q->where("name", "user")->where("name", "!=", "admin");
      });
    } else if ($findRole == "employee") {
      $model = $query->whereHas("roles", function ($q) {
        $q->where("name", "employee");
      });
    }

    if (!empty($params['keyword'])) {

      $model = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
    }

    if (isset($params['departments']) && $params['departments'] != null) {
      $departments = explode(',', $params['departments']);
      if ($findRole == "user") {
        $model = $model->where(function ($q) use ($departments) {
          foreach ($departments as $key => $department) {
            $q->whereRaw("json_unquote(json_extract(`meta`, '$.\"department\"')) = " . "'$department'");
          }
        });
      } else {
        $model = $model->whereIn('department_id', $departments);
      }
    }

    if (isset($params['status']) && $params['status'] != null) {

      $status = explode(',', $params['status']);
      $all = [1, 2, 3];
      $excludeStatus = implode(',', array_diff($all, $status));
      if (count($status) == count($all) && array_diff($status, $all) == array_diff($all, $status)) {
        $model = $model->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->whereNotNull('email_verified_at')->where('isLocked', 0);
          })
            ->orWhere('isLocked', 1)
            ->orwhereNull('email_verified_at');
        });
      } else if ($excludeStatus == '3') {
        $model = $model->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->whereNotNull('email_verified_at')->where('isLocked', 0);
          })
            ->orWhere('isLocked', 1);
        });
      } else if ($excludeStatus == '2') {
        $model = $model->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->whereNotNull('email_verified_at')->where('isLocked', 0);
          })
            ->orwhereNull('email_verified_at');
        });
      } else if ($excludeStatus == '1') {
        $model = $model->where(function ($q) {
          $q->orWhere('isLocked', 1)
            ->orwhereNull('email_verified_at');
        });
      } else if ($excludeStatus == '1,2') {
        $model = $model->where(function ($q) {
          $q->orwhereNull('email_verified_at');
        });
      } else if ($excludeStatus == '1,3') {
        $model = $model->where(function ($q) {
          $q->orWhere('isLocked', 1);
        });
      } else if ($excludeStatus == '2,3') {
        $model = $model->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->whereNotNull('email_verified_at')->where('isLocked', 0);
          });
        });
      }

      // if (in_array(1, $status) && in_array(2, $status) && in_array(3, $status)) {
      //     $model = $model->where(function($q) {
      //         $q->whereNotNull('email_verified_at')->where('isLocked', 0);
      //     });
      // } else if ($status == 2) {
      //     $model = $model->where(function ($q) {
      //         $q->where('isLocked', 1);
      //     });
      // } else if ($status == 3) {
      //     $model = $model->where(function ($q) {
      //         $q->whereNull('email_verified_at');
      //     });
      // }
    }

    if (isset($params['sortFields']) && $params['sortFields'] != null && isset($params['sortValue']) && $params['sortValue'] != null) {
      // dd(2);
      $sortFields = explode(',', $params['sortFields']);
      $sortValue = explode(',', $params['sortValue']);

      for ($i = 0; $i < count($sortFields); $i++) {

        if ($sortFields[$i] == 'department') {
          $query = $query->with(['department' => function ($query) use ($sortValue, $i) {
            $query->orderBy('department_name', "$sortValue[$i]");
          }]);
        } else if ($sortFields[$i] == 'status') {

          $query = $query->orderBy("isLocked", "$sortValue[$i]")->orderBy("email_verified_at", "$sortValue[$i]");
        } else if ($sortFields[$i] != 'department' && $sortFields[$i] != 'status') {

          $query = $query->orderBy("$sortFields[$i]", "$sortValue[$i]");
        }
      }

      if (!in_array('department', $sortFields)) {
        $query = $query->with('department', function ($q) {
          $q->select('department_id', 'department_name');
        });

      }
    } else {
      $query = $query->with('department', function ($q) {
        $q->select('department_id', 'department_name');
      })->orderBy('name', 'desc');
    }


    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {

        $perpage = $params['perpage'];

        $model = $query->paginate($perpage);
      } else {
        $model = $query->paginate(PERPAGE);
      }
    } else {
      $model = $query->get();
    }


    return $model;
  }

  public function getStudentsOfCourse($id, $params)
  {
    $user = auth()->user();
    $table = $this->model->getTable();

    $query = $this->model->newQuery()
      ->select('users.*',
        'courses_students.course_id',
        'courses_students.student_id',
        'courses_students.percent_finish',
        'courses_students.isPassed',
        'courses_students.started_at',
        'courses_students.completed_at')
      ->join('courses_students', 'courses_students.student_id', '=', "$table.id")
      ->where('courses_students.course_id', '=', $id)
      ->with('department', function ($q) {
        $q->select('department_id', 'department_name');
      })
      ->with('roles', function ($q) {
        $q->select('roles.name as role');
      });

    $query = $query->whereHas('courses', function ($q) use ($id) {
      $q->where('courses.course_id', '=', $id);
    });

    if (!empty($params['keyword'])) {

      $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
    }

    if (isset($params['account_type']) && $params['account_type'] != null) {
      // $account_type = $params['account_type'];
      // $query = $query->whereHas('roles', function($q) use($account_type) {
      //     $q->where('name', $account_type);
      // });

      $type_account = $params['account_type'];

      $type_account = explode(',', $params['account_type']);

      // dd($type_account[0]);

      if (count($type_account) < 2) {
        $model = $query->whereHas("roles", function ($q) use ($type_account) {
          $q->where("name", $type_account[0]);
        });
      } else {
        $model = $query->whereHas("roles", function ($q) {
          $q->where(function ($oq) {
            $oq->orwhere("name", "user")
              ->orwhere("name", "employee");
          });
        });
      }
    }

    if (isset($params['status_learning']) && $params['status_learning'] != null) {

      $status_learning = explode(',', $params['status_learning']);

      // dd($status[0]);

      if (count($status_learning) == 1) {

        if ($status_learning[0] == 'finished') {

          $query = $query->where('courses_students.isPassed', '=', 1);

        } else if ($status_learning[0] == 'learning') {

          $query = $query->where('courses_students.isPassed', '<', 1);

        }

      } else if (count($status_learning) > 1) {

        $query = $query->where(function ($q) {
          $q->where('courses_students.isPassed', '<=', 1);
        });

      }
    }

    if (isset($params['status']) && $params['status'] != null) {

      $status = explode(',', $params['status']);
      $all = [1, 2, 3];
      $excludeStatus = implode(',', array_diff($all, $status));
      if (count($status) == count($all) && array_diff($status, $all) == array_diff($all, $status)) {
        $query = $query->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->whereNotNull('email_verified_at')->where('isLocked', 0);
          })
            ->orWhere('isLocked', 1)
            ->orwhereNull('email_verified_at');
        });
      } else if ($excludeStatus == '3') {
        $query = $query->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->whereNotNull('email_verified_at')->where('isLocked', 0);
          })
            ->orWhere('isLocked', 1);
        });
      } else if ($excludeStatus == '2') {
        $query = $query->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->whereNotNull('email_verified_at')->where('isLocked', 0);
          })
            ->orwhereNull('email_verified_at');
        });
      } else if ($excludeStatus == '1') {
        $query = $query->where(function ($q) {
          $q->orWhere('isLocked', 1)
            ->orwhereNull('email_verified_at');
        });
      } else if ($excludeStatus == '1,2') {
        $query = $query->where(function ($q) {
          $q->orwhereNull('email_verified_at');
        });
      } else if ($excludeStatus == '1,3') {
        $query = $query->where(function ($q) {
          $q->orWhere('isLocked', 1);
        });
      } else if ($excludeStatus == '2,3') {
        $query = $query->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->whereNotNull('email_verified_at')->where('isLocked', 0);
          });
        });
      }
    }

    if (isset($params['startTime']) && $params['startTime'] != null && isset($params['endTime']) && $params['endTime'] != null) {
      $startTime = $params['startTime'];
      $endTime = $params['endTime'];

      $query = $query->whereDate('started_at', '>=', $startTime)->whereDate('completed_at', '<=', $endTime);

    } else if (isset($params['startTime']) && $params['startTime'] != null) {

      $startTime = $params['startTime'];
      $query = $query->whereDate('started_at', '>=', $startTime);

    } else if (isset($params['endTime']) && $params['endTime'] != null) {

      $endTime = $params['endTime'];
      $query = $query->whereDate('completed_at', '>=', $endTime);
    }

    if (isset($params['achievements']) && $params['achievements'] != null) {

      $achievements = explode(',', $params['achievements']);

      // dd($achievements);

      if (count($achievements) == 1) {

        $values = explode('-', $params['achievements']);

        $query = $query->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values[1]);

      } else if (count($achievements) == 2) {

        $values_first = explode('-', $achievements[0]);

        $values_second = explode('-', $achievements[1]);

        $query = $query->where(function ($newQue) use ($values_first, $values_second) {
          $newQue->orwhere(function ($q) use ($values_first) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_first[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_first[1]);
          })->orwhere(function ($q) use ($values_second) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_second[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_second[1]);
          });
        });

      } else if (count($achievements) == 3) {

        $values_first = explode('-', $achievements[0]);

        $values_second = explode('-', $achievements[1]);

        $values_third = explode('-', $achievements[2]);

        $query = $query->where(function ($newQue) use ($values_first, $values_second, $values_third) {
          $newQue->orwhere(function ($q) use ($values_first) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_first[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_first[1]);
          })->orwhere(function ($q) use ($values_second) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_second[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_second[1]);
          })->orwhere(function ($q) use ($values_third) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_third[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_third[1]);
          });
        });

      } else if (count($achievements) == 4) {

        $values_first = explode('-', $achievements[0]);

        $values_second = explode('-', $achievements[1]);

        $values_third = explode('-', $achievements[2]);

        $values_fourt = explode('-', $achievements[3]);

        $query = $query->where(function ($newQue) use ($values_first, $values_second, $values_third, $values_fourt) {
          $newQue->orwhere(function ($q) use ($values_first) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_first[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_first[1]);
          })->orwhere(function ($q) use ($values_second) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_second[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_second[1]);
          })->orwhere(function ($q) use ($values_third) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_third[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_third[1]);
          })->orwhere(function ($q) use ($values_fourt) {
            $q->whereRaw("CAST(percent_finish as SIGNED integer) >= " . $values_fourt[0] . " and CAST(percent_finish as SIGNED integer) <= " . $values_fourt[1]);
          });;
        });

      }

    }

    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {

        $perpage = $params['perpage'];

        $model = $query->paginate($perpage);

      } else {

        $model = $query->paginate(PERPAGE);

      }
    } else {

      $model = $query->get();

    }


    return $model;

  }

  public function getLeadersOfCourse($id, $params)
  {
    $query = $this->model->newQuery()
      ->select('users.*')
      ->with('department', function ($q) {
        $q->select('department_id', 'department_name');
      });

    $query = $query->whereHas('leaderCourses', function ($q) use ($id) {
      $q->where('courses.course_id', '=', $id);
    });

    if (!empty($params['keyword'])) {

      $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);

    }

    if (isset($params['departments']) && $params['departments'] != null) {

      $departments = explode(',', $params['departments']);

      $query = $query->whereIn('department_id', $departments);
    }

    if (isset($params['positions']) && $params['positions'] != null) {

      $positions = explode(',', $params['positions']);

      $query = $query->where(function ($q) use ($positions) {
        foreach ($positions as $key => $position) {
          $q->whereRaw("json_unquote(json_extract(`meta`, '$.\"position\"')) = " . "'$position'");
        }
      });
    }

    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {

        $perpage = $params['perpage'];

        $model = $query->paginate($perpage);
      } else {
        $model = $query->paginate(PERPAGE);
      }
    } else {
      $model = $query->get();
    }


    return $model;

  }

  public function checkValidUser($userId)
  {

    $query = $this->model->newQuery();

    $model = $query->find($userId);

    $role = null;

    if ($model) {

      $role = $this->checkRole($model);
    }

    return $role;
  }

  public function findByEmail($email)
  {
    $query = $this->model->newQuery();

    $model = $query->where('email', $email)->first();

    return $model;
  }

  public function getPositions($params = null)
  {

    $query = $this->model->newQuery();

    $positions = [];

    if (isset($params['course_id']) && $params['course_id'] != null) {

      $course_id = $params['course_id'];

      $query = $query->whereHas("leaderCourses", function ($q) use ($course_id) {
        $q->where("courses.course_id", $course_id);
      });
    }

    if (isset($params['roles']) && $params['roles'] != null) {

      $model = $query->whereHas("roles", function ($q) {
        $q->where("name", "employee")->where("name", "!=", "teacher");
      });

    }

    $model = $query->whereNotNull('meta')->get();

    if (count($model)) {
      foreach ($model as $key => $user) {

        $meta = json_decode($user->meta);

        if (isset($meta->position) && $meta->position != null) {

          array_push($positions, $meta->position);
        }
      }
    }

    return array_unique($positions);

  }

  public function getListUserByRole()
  {
    // $users = $this->model->selectRaw('count(*) as total,menuroles')->whereNotNull('email_verified_at')->where('menuroles', '!=' ,'admin')->groupBy('menuroles')->get();
    // $course = Course::whereNull('deleted_at')->count();
    // $sum_student = 0;
    // $sum_teacher = 0;
    // foreach($users as  $user){
    //     if($user->menuroles == 'employee'){
    //         $sum_teacher = $user->total;
    //     }else {
    //         $sum_student = $user->total;
    //     }
    // }
    // $data = [
    //     "sum_course" => $course,
    //     "sum_student" => $sum_student,
    //     "sum_teacher" => $sum_teacher,
    // ];

    $students = User::whereHas('roles', function ($q) {
      $q->where(function ($w) {
        $w->orwhere('roles.name', 'user')
          ->orwhere('roles.name', 'employee');
      });
    })->whereHas('courses')->get()->count();

    $teachers = User::whereHas('roles', function ($q) {
      $q->where('roles.name', 'employee');
    })->whereHas('permissions', function ($q) {
      $q->where('name', PERMISSION["MANAGE_COURSES"]);
    })->get()->count();

    $courses = Course::where('is_published', 1)->where(function ($q) {
      $q->orwhere('course_type', 'Business')
        ->orwhere('course_type', 'Local');
    })->get()->count();

    $data = [
      "sum_course" => $courses,
      "sum_student" => $students,
      "sum_teacher" => $teachers,
    ];

    return $data;
  }

  public function activeAccount($user)
  {

    $now = date(Carbon::now());

    $user->email_verified_at = $now;

    $user->save();

    return $user;
  }

  public function lockOrUnlock($input, $user)
  {
    $user->isLocked = $input['isLocked'];
    $user->save();
    return $user;
  }

  public function feature_teachers()
  {
    $query = $this->model->newQuery();

    $model = $query->select('id', 'email', 'name', 'avatar')
      ->whereHas('roles', function ($q) {
        $q->where('name', 'employee');
      })
      ->whereHas('permissions', function ($q) {
        $q->where('name', PERMISSION["MANAGE_COURSES"]);
      })
      ->withCount(['studentsFollow'])
      ->withCount(['studentsFollowCurMonth'])
      ->withCount(['studentsFollowPrevMonth'])
      ->withAvg('studentsFollow', 'rating')
      ->withAvg('studentsFollowCurMonth', 'rating')
      ->withAvg('studentsFollowPrevMonth', 'rating')
      ->orderBy('students_follow_count', 'desc')
      ->orderBy('students_follow_avg_rating', 'desc')
      ->limit(10)
      ->get();

    foreach ($model as $key => $teacher) {
      $current_quantity = $teacher->students_follow_cur_month_count + $teacher->students_follow_cur_month_avg_rating;
      $prev_quantity = $teacher->students_follow_prev_month_count + $teacher->students_follow_prev_month_avg_rating;
      $teacher->rate = $prev_quantity ? round(($current_quantity - $prev_quantity) * 100 / $prev_quantity, 2) : 100;
    }
    return $model;

  }

  public function feature_members()
  {
    $query = $this->model->newQuery();

    $model = $query->select('id', 'email', 'name', 'avatar')
      ->whereDoesntHave('roles', function ($q) {
        $q->where('name', 'admin');
      })
      ->withCount(['certifacates'])
      ->withCount(['coursesRegister'])
      ->orderBy('courses_register_count', 'desc')
      ->orderBy('certifacates_count', 'desc')
      ->limit(10)
      ->get();

    foreach ($model as $key => $member) {
      $roles = $member->roles->toArray();
      $permissions = $member->permissions->toArray();
      if ($roles && count($roles)) {
        if ($roles[0]['name'] == 'employee' && in_array(PERMISSION_TEACHER['MANAGE_COURSES'], $permissions)) {
          $member->role = __('auth.roles.teacher');
        } else {
          $member->role = __('auth.roles.' . $roles[0]['name']);
        }
      }
      unset($member->roles);
      unset($member->permissions);

    }

    return $model;
  }

  public function setLang($input, $user)
  {
    $query = $this->model->newQuery();

    $query = $query->where('id', $user->id)->first();

    $query->lang = $input['lang'];

    $query->save();

    return $query;
  }

}

<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\DiscountCode;
use App\Models\UserActivateDiscountCode;
use App\Repositories\BaseRepository;
use App\Contract\CommonBusiness;
use Carbon\Carbon;

/**
 * Class DiscountCodeRepository
 * @package App\Repositories
 * @version November 5, 2021, 2:03 pm +07
 */

class DiscountCodeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'discount_code',
        'title',
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
        return DiscountCode::class;
    }

    public function generateDiscountCode($length = 10, $strength = 10)
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
            $consonants .= 'aeuybdghjmnpqrstvzBDGHJLMNPQRSTVWXZ0123456789';
        }
        $consonants_1 = '123456789';
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
        $result =  $password . $especially;
        $is_discount_code = $this->checkDiscountCode($result);
        if ($is_discount_code) {
            $this->generateDiscountCode();
        }
        return $result;
    }

    public function checkDiscountCode($discount_code)
    {
        $discount_code = DiscountCode::where('discount_code', $discount_code)->first();
        if (!$discount_code) {
            return false;
        }
        return true;
    }

    public function checkUsingDiscountCode($user, $discount_code_str)
    {
        $response = [
            'isExist' => true,
            'isUsed' => false,
            'isExpired' => false,
            'isOutOfStock' => false,
            'discountCode' => ''
        ];

        $discount_code = DiscountCode::where('discount_code', $discount_code_str)->first();
        if (!$discount_code) {
            $response['isExist'] = false;
            return $response;
        }
        // $check_using_discount_code = UserActivateDiscountCode::where('discount_code_id', $discount_code->id)->where('user_id', $user_id )->first();
        $usedDiscountCodes = $user->myDiscountCodeUsed()->pluck('discount_code')->toArray();
        $checkIsUsed = array_filter($usedDiscountCodes, function ($code) use ($discount_code_str) {
            return $code == $discount_code_str;
        });

        if ($checkIsUsed) {
            $response['isUsed'] = true;
            return $response;
        }

        if ($discount_code->count <= 0) {
            $response['isOutOfStock'] = true;
            return $response;
        }
        $now = date(Carbon::now());
        if ($discount_code->expired_at < $now) {
            $response['isExpired'] = true;
            return $response;
        }
        $response['discountCode'] = $discount_code;
        return $response;
    }

    public function allDiscountCode($params = null)
    {
        $query = $this->model->newQuery()
            ->orderBy('created_at', 'desc');

        if (isset($params['type']) && $params['type'] != null) {
            $status = explode(',', $params['type']);
            $query = $query->whereIn('type', $status);
        }
        $now = date(Carbon::now());
        if (isset($params['status']) && $params['status'] != null) {
            $status = explode(',', $params['status']);
            $all = [0, 1, 2];
            $expired_at = implode(',', array_diff($all, $status));
            // var_dump($expired_at);
            if (count($status) == count($all) && array_diff($status, $all) == array_diff($all, $status)) {
                $query = $query;
            } else if ($expired_at == '0') {
                $query = $query->where(function ($q) use ($now) {
                    $q->orWhere('expired_at', '>', $now)
                        ->orWhere(function ($oq) use ($now) {
                            $oq->where('expired_at', '>=', $now)
                                ->where('time_start', '<=', $now);
                        });
                });
            } else if ($expired_at == '1') {
                $query = $query->where(function ($q) use ($now) {
                    $q->orWhere('expired_at', '<=', $now)
                        ->orWhere('time_start', '>', $now);
                });
            } else if ($expired_at == '2') {
                $query = $query->where(function ($q) use ($now) {
                    $q->orWhere('expired_at', '<=', $now)
                        ->orWhere(function ($oq) use ($now) {
                            $oq->where('expired_at', '>=', $now)
                                ->where('time_start', '<=', $now);
                        });
                });
            } else if ($expired_at == '1,2') {
                $query = $query->where('expired_at', '<=', $now);
            } else if ($expired_at == '0,2') {
                $query = $query->where('expired_at', '>=', $now)
                    ->where('time_start', '<=', $now);
            } else if ($expired_at == '0,1') {
                $query = $query->where('time_start', '>', $now);
            }
        }

        if (!empty($params['keyword'])) {
            $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword']);
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

    public function getDiscountCodeAvailableForUser($userId)
    {
        $now = Carbon::now()->toDateTimeString();

        $query = $this->model->newQuery();

        $query = $query->whereDoesntHave('discountCodeUsed', function ($q) use ($userId) {
            $q->where('user_id', '=', $userId);
        });

        $query = $query->whereDate('expired_at', '>=', $now)->whereDate('time_start', '<=', $now);

        $model = $query->get();

        return $model;
    }
}

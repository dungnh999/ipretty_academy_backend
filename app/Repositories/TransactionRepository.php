<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
//use App\Jobs\PushNotificationTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class TransactionRepository
 * @package App\Repositories
 * @version November 26, 2021, 3:08 pm +07
*/

class TransactionRepository extends BaseRepository
{
    use CommonBusiness;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'transaction_code',
        'payment_method',
        'status'
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

    protected $relations = ['buyer'];

    protected $relationSearchable = [
        'name'
    ];
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Transaction::class;
    }

    public function create ($input) {

        $exist_model = $this->model->newQuery();

        $randomString = Str::random(10);

        do {
            $exist_code = $exist_model->where('transaction_code', $randomString)->first();

            if ($exist_code) {

                $randomString = Str::random(10);
            }
        } while ($exist_code);

        $input['transaction_code'] = $randomString;

        // $input['user_id'] = auth()->user()->id;

        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    public function checkTransactionCode ($email, $code) {

        $query = $this->model->newQuery();

        $model = $query->with('buyer', function($q) use($email) {
            $q->where('email', $email);
        })->where('transaction_code', $code)->first();

        return $model;

    }

    public function approveOrRejectTransaction ($transaction, $status) {

        $user = auth()->user();

        $transaction->status = $status;

        $transaction->confirmedBy = $user->id;

        $transaction->confirmed_at = Carbon::now()->format('Y-m-d H:i:00');

        $transaction->save();

        // $member = User::where('id', $transaction->user_id)->first();

        // dd($member->id);

//        $job = (new PushNotificationTransaction($transaction, $user, $status));
//
//        dispatch($job);

        return $transaction;

    }


    public function getTransactionHistories($userId, $params = null)
    {
        $query = $this->model->newQuery();

        $query = $query->transactionHistories($userId)->with('order')->orderBy('created_at', 'desc');

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

    public function allTransactions ($params = null) {
        $query = $this->model->newQuery();
        $query = $query->with('order')->with('buyer', function($q) {
            $q->select('id', 'email', 'name', 'avatar', 'address', 'phone');
        })
        ->orderBy('created_at', 'desc')
        ->orderBy('updated_at', 'desc');

        if (!empty($params['keyword'])) {

            $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
        }

        if (isset($params['created_at']) && $params['created_at'] != null) {
            $created_at = $params['created_at'];
            $query = $query->whereDate('created_at', '>=', $created_at);
        }

        if (isset($params['updated_at']) && $params['updated_at'] != null) {
            $updated_at = $params['updated_at'];
            $query = $query->whereDate('updated_at', '>=', $updated_at);
        }

        if (isset($params['buyers']) && $params['buyers'] != null) {
            $buyers = explode(',', $params['buyers']);
            $query = $query->whereIn('user_id', $buyers);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $status = explode(',', $params['status']);
            $whereids = "status";
            $str = "";
            $i = 1; // to append AND in query

            foreach ($status as $item) {
                $str .= "FIND_IN_SET( '$item', $whereids)";
                if ($i < count($status)) {
                    $str .= " OR "; // use OR as per use
                }
                $i++;
            }
            $query = $query->whereRaw($str);
        }

        if (isset($params['payment_methods']) && $params['payment_methods'] != null) {
            $payment_methods = explode(',', $params['payment_methods']);
            $whereids = "payment_method";
            $str = "";
            $i = 1; // to append AND in query

            foreach ($payment_methods as $item) {
                $str .= "FIND_IN_SET( '$item', $whereids)";
                if ($i < count($payment_methods)) {
                    $str .= " OR "; // use OR as per use
                }
                $i++;
            }
            $query = $query->whereRaw($str);
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

    public function analysisBusiness($params = null) {
        $month = 0;
        $now = Carbon::now();
        $year = Carbon::now()->year;
        $week = 0;
        $table = $this->model->getTable();
        $query = $this->model->newQuery();

        $subQuery = $query->where("$table.status", 'approved')
        ->join('orders', 'orders.order_id', "$table.order_id");

        if (isset($params['year']) && $params['year'] != null && isset($params['week']) && $params['week'] != null) {
            $year = $params['year'];
            $week = $params['week'];
            $dates = $this->getStartAndEndDate($week, $year);
            // dd($dates);
            $subQuery = $subQuery->selectRaw('DATE_FORMAT(confirmed_at, "%W") AS date, SUM(orders.grandTotal) as total')
            ->whereRaw('Date(confirmed_at) >= Date("' . $dates["start_date"] . '") AND Date(confirmed_at) <= Date("' . $dates['end_date'].'")')
            ->groupByRaw('DATE_FORMAT(confirmed_at, "%d-%m-%Y")');
            // dd($subQuery->get()->toArray());

            // dd($subQuery->get());
        } else if (isset($params['year']) && $params['year'] != null && isset($params['month']) && $params['month'] != null) {
            $month = $params['month'];
            $year = $params['year'];

            $subQuery = $subQuery->selectRaw('DATE_FORMAT(confirmed_at, "%d") AS date, SUM(orders.grandTotal) as total')
            ->whereRaw('MONTH(confirmed_at) = ' . $month . ' AND YEAR(confirmed_at) = ' . $year)
            ->groupByRaw('DATE_FORMAT(confirmed_at, "%d-%m-%Y")');

        } else if (isset($params['year']) && $params['year'] != null) {
            $year = $params['year'];

            $subQuery = $subQuery->selectRaw('DATE_FORMAT(confirmed_at, "%c") AS date, SUM(orders.grandTotal) as total')
                ->whereRaw('YEAR(confirmed_at) = ' . $year)
                ->groupByRaw('DATE_FORMAT(confirmed_at, "%m-%Y")');
        } else if (isset($params['month']) && $params['month'] != null) {
            $month = $params['month'];

            $subQuery = $subQuery->selectRaw('DATE_FORMAT(confirmed_at, "%d") AS date, SUM(orders.grandTotal) as total')
                ->whereRaw('MONTH(confirmed_at) = ' . $month)
                ->groupByRaw('DATE_FORMAT(confirmed_at, "%d-%m-%Y")');
        } else {
            $subQuery = $subQuery->selectRaw('DATE_FORMAT(confirmed_at, "%c") AS date, SUM(orders.grandTotal) as total')
                ->whereRaw('confirmed_at < "' . $now . '" and confirmed_at >= Date_add("' . $now . '",interval - 12 month)')
                ->groupByRaw('DATE_FORMAT(confirmed_at, "%m-%Y")');
        }

        // $subQuery = $query->selectRaw('DATE_FORMAT(confirmed_at, "%c") AS date, SUM(orders.grandTotal) as total')
        // ->join('orders', 'orders.order_id', "$table.order_id")
        // ->whereRaw('confirmed_at < "' . $now . '" and confirmed_at >= Date_add("'.$now.'",interval - 12 month)')
        // ->where("$table.status", 'approved')
        // ->groupByRaw('DATE_FORMAT(confirmed_at, "%d-%m-%Y")');

        // dd($subQuery->get());

        $str = "";
        $rangeDayInMonth = range(1, 12);
        if ($month) {
            // dd($month);
            $countDayInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $rangeDayInMonth = range(1, $countDayInMonth);
        }

        if (!$week) {
            foreach ($rangeDayInMonth as $key => $day) {
                $str = $str . "SUM(IF(date = " . $day . ", total, 0)) AS '" . $day . "'";
                if ($key < count($rangeDayInMonth) - 1) {
                    $str = $str . ',';
                }
            }
        }else {

            $rangeDayInWeek = array();
            for ($i = 0; $i < 7; $i++) {
                $rangeDayInWeek[$i] = jddayofweek($i, 1);
            }
            foreach ($rangeDayInWeek as $key => $day) {
                $str = $str . "SUM(IF(date = '" . $day . "', total, 0)) AS '" . $day . "'";
                if ($key < count($rangeDayInWeek) - 1) {
                    $str = $str . ',';
                }
            }
        }

        $model = DB::query()->from($subQuery, 'sub')->selectRaw($str);

        $model = $model->first();
        // dd($model);
        return $model;
    }

    public function getStartAndEndDate($week, $year)
    {
        $dateTime = new DateTime();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('Y-m-d');
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('Y-m-d');
        return $result;
    }
}

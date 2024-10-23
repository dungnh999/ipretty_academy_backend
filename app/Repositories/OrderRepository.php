<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Models\Course;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Repositories\BaseRepository;

/**
 * Class OrderRepository
 * @package App\Repositories
 * @version November 25, 2021, 2:55 pm +07
*/

class OrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'status',
        'total',
        'grandTotal',
        'discount_code'
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
        return Order::class;
    }

    public function createOrderCourse($request, $user){
      $course = $request->get('data');
      $collect = collect($course);
      $course_ids = $collect->pluck('course_id')->toArray();
      $total_price = Course::totalPrice($course_ids);
      $sale_price = 0;
      $grandTotal = $total_price + round($total_price * 10 / 100,2) - $sale_price;

      $input["user_id"] = $user->id;
      $input['discount_code'] = $request->discount_code;
      $input['total'] = $total_price;
      $input['grandTotal'] = $grandTotal;
      $input['salePrice'] = $sale_price;

      $model = $this->model->newInstance($input);

      $model->save();


      if (count($course_ids)) {
        foreach ($course_ids as $key => $course_id) {
          $course_price = Course::where('course_id', $course_id)->first()->course_price;
          OrderItem::create([
            'course_id' => $course_id,
            'order_id' => $model->order_id,
            'course_price' => $course_price
          ]);
        }
      }

      return $model;
    }

    public function createOrder($request, $user) {

        $course_ids = $request->course_ids;
        $total_price = Course::totalPrice($course_ids);

        $sale_price = 0;
        if ($request->discount_code) {
            $discount_code = DiscountCode::priceOfCode($request->discount_code)->first();
            if ($discount_code) {
                $type = $discount_code->type;
                if ($type == "money") {
                    $sale_price = $discount_code->sale_price;
                }else {
                    $sale_price = round($discount_code->sale_price / 100 * $total_price, 2);
                }
            }
        }

        $grandTotal = $total_price + round($total_price * 10 / 100,2) - $sale_price;
        // dd($grandTotal);


        if (isset($request->order_id) && $request->order_id != null) {

            $model = $this->model->newQuery();

            $model = $model->findOrFail($request->order_id);

            $model->orderItems->each(function($q) {
                $q->delete();
            });

            $input['discount_code'] = $request->discount_code;
            $input['total'] = $total_price;
            $input['grandTotal'] = $grandTotal;
            $input['salePrice'] = $sale_price;

            $model->fill($input);
            // dd($model);
            $model->save();

        } else {

            $input["user_id"] = $user->id;
            $input['discount_code'] = $request->discount_code;
            $input['total'] = $total_price;
            $input['grandTotal'] = $grandTotal;
            $input['salePrice'] = $sale_price;

            $model = $this->model->newInstance($input);

            $model->save();
        }

        if (count($course_ids)) {
            foreach ($course_ids as $key => $course_id) {
                $course_price = Course::where('course_id', $course_id)->first()->course_price;
                OrderItem::create([
                    'course_id' => $course_id,
                    'order_id' => $model->order_id,
                    'course_price' => $course_price
                ]);
            }
        }

        return $model;
    }

    public function checkOrderOfUser($order_id, $user_id) {
        $model = $this->model->newQuery();

        $model = $model->where('order_id', $order_id)->where('user_id', $user_id)->first();

        return $model;
    }

    public function updateStatus($order_id, $status) {
        $model = $this->model->newQuery();

        $model = $model->findOrFail($order_id);

        $model->status = $status;

        $model->save();

        return $model;

    }

    public function getDataOrder() {
      $query = $this->model->newQuery()
        ->with('createdBy', function($q) {
          $q->select('name', 'email', 'id', 'avatar');
        })
        // ->with('transaction', function($q) {
        //   $q->select('status');
        // })
        ->orderBy('created_at', 'desc');

      if (isset($params['created_at']) && $params['created_at'] != null) {
        $created_at = $params['created_at'];
        $query = $query->whereDate('created_at', '>=', $created_at);
      }

      if (isset($params['updated_at']) && $params['updated_at'] != null) {
        $updated_at = $params['updated_at'];
        $query = $query->whereDate('updated_at', '>=', $updated_at);
      }

      if (!empty($params['keyword'])) {
        $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
      }

      if (isset($params['paging']) && $params['paging']) {
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

    public function getOrderForUser($userId) {

    }

    public function getOrderById($id) {
        $model = $this->model->newQuery();

        $model = $model->with('courses')->where('status', 'ordered')->where('order_id', $id)->with('transaction')->first();

        return $model;

    }

    public function deleteOrderNoItem($order_id)
    {

        $model = $this->model->newQuery();

        $model = $model->where('order_id', $order_id)->whereDoesntHave('orderItems')->first();

        return $model;
    }
}

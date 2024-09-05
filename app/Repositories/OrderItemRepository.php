<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\BaseRepository;

/**
 * Class OrderItemRepository
 * @package App\Repositories
 * @version November 25, 2021, 3:18 pm +07
*/

class OrderItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'course_id',
        'order_id',
        'course_price'
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
        return OrderItem::class;
    }

    public function checkCourseOrdered ($orderItemId, $courseId) {

        $query = $this->model->newQuery();

        $model = $query->where('order_item_id', $orderItemId)->where('course_id', $courseId)->first();

        return $model;
    }

    public function getItemCourseOrder($orderId) {

      $query = $this->model->newQuery();

      $model = $query->where('order_id', $orderId);

      $model = $model->get();

      return $model;
    }

    public function delectOrderItems ($order_id, $course_id) {

        $query = $this->model->newQuery();

        $model = $query->where('order_id', $order_id)->where('course_id', $course_id)->delete();

        return $model;
    }

    public function removeOrderItemCanceled($order_id)
    {
        $query = $this->model->newQuery();

        $model = $query->where('order_id', $order_id)->delete();

        return $model;
    }
}

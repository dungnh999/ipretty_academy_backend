<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\BaseRepository;

/**
 * Class CartItemRepository
 * @package App\Repositories
 * @version November 26, 2021, 2:34 pm +07
*/

class CartItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
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
        return CartItem::class;
    }

    public function removeCartItemOrdered($cart_id, $course_ids) {

        $query = $this->model->newQuery();

        $model = $query->where('cart_id', $cart_id)->whereIn('course_id', $course_ids)->delete();

        return $model;

    }

    public function updateUsedCartItems ($cart_id, $course_ids) {
        
        $query = $this->model->newQuery();

        $query = $query->where('cart_id', $cart_id)->whereIn('course_id', $course_ids)->update(array('isUsed' =>  1));

        return $query;
    }

    public function removeCartItemByCourseAndUser($course_ids, $user_id) {
        $query = $this->model->newQuery();

        $model = $query->whereIn('course_id', $course_ids)->whereHas('cart', function($q) use($user_id) {
            $q->where('user_id', $user_id);
        })->get();

        $cart_ids = $model->pluck('cart_id')->toArray();

        Cart::whereIn('id', $cart_ids)->update(['status' => 'pending']);

        foreach ($model as $key => $item) {
            $item->delete();
        }

        return $model;
    }   
}

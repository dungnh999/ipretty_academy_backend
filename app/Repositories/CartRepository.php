<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\BaseRepository;

/**
 * Class CartRepository
 * @package App\Repositories
 * @version November 8, 2021, 9:51 am +07
*/

class CartRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'cart_token',
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

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Cart::class;
    }

    public function addToCart($request)
    {

        if (isset($request->cart_id) && $request->cart_id != null) {

            $model = $this->model->newQuery();

            $model = $model->findOrFail($request->cart_id);
            
        }else {

            $input["user_id"] = $request->user_id;

            $input["cart_token"] = $request->cart_token;

            $model = $this->model->newInstance($input);

            $model->save();

        }
        

        $cart_item = CartItem::create([
            'cart_id' => $model->id,
            'course_id' => $request->course_id,
        ]);

        return $model;

    }

    public function checkCart($cart_id, $course_id, $isUsed = false) {

        // dd($cart_id);
        $model = $this->model->newQuery();  

        $model = $model->where('id', $cart_id)->whereHas('cartItems', function($q) use($course_id, $isUsed) {
            $q->where('course_id', $course_id)->where('isUsed', $isUsed);
        })->first();

        return $model;
    }

    public function getCartByTokenOrId($userId = null, $cartToken = null) {

        $model = $this->model->newQuery()->where('status', 'pending')->with('courses')->withSum('courses', 'course_price')->orderby('created_at', 'desc');

        if ($userId && $cartToken) {
            $model = $model->where('user_id', $userId)->where('cart_token', $cartToken)->first();

        }else if ($userId) {
            $model = $model->where('user_id', $userId)->whereNull('cart_token')->first();
            
        }else if ($cartToken){
            $model = $model->where('cart_token', $cartToken)->whereNull('user_id')->first();

        }

        // dd($model);
        return $model;
    }

    public function updateCartExist ($cart_token, $user) {

        $query = $this->model->newQuery()->where('status', 'pending')->with('courses')->withSum('courses', 'course_price');

        $newQuery = $this->model->newQuery()->where('status', 'pending')->with('courses')->withSum('courses', 'course_price');

        $model = $query->where('cart_token', $cart_token)->whereNull('user_id')->first();

        if ($model) {

            $model->user_id = $user->id;

            $model->save();
        }else {

            $model = $newQuery->where('cart_token', '=', $cart_token)->where('user_id', '=', $user->id)->first();
            
        }
        // dd($model);      

        return $model;
    }

    public function updateCartStatus ($cart, $status) {

        $cartItems = $cart->cartItems->count();
        $cartItemsUsed = $cart->cartItemsUsed->count();

        if ($cartItems == $cartItemsUsed) {
            
            $cart->status = $status;

            $cart->save();
        }

        return $cart;
    }
}

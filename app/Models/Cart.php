<?php

namespace App\Models;

use Eloquent as Model;



/**
 * Class Cart
 * @package App\Models
 * @version November 8, 2021, 9:51 am +07
 *
 * @property integer $user_id
 * @property string $cart_token
 * @property string $status
 */
class Cart extends Model
{

    public $table = 'cart';

    public $timestamps = true;

    public $fillable = [
        'user_id',
        'cart_token',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'cart_token' => 'string',
        'status' => 'string',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function cartItems() {
        return $this->hasMany('App\Models\CartItem', 'cart_id');
    }

    public function cartItemsUsed() {
        return $this->hasMany('App\Models\CartItem', 'cart_id')->where('isUsed', 1);
    }

    public function cartItemsWithCourses() {
        return $this->hasMany('App\Models\CartItem', 'cart_id')->with('course', function($q) {
            $q->select('courses.course_id', '.courses.course_name', 'courses.course_price', 'courses.course_feature_image', 'courses.unit_currency');
        });
    }

    // public function courses() {
    //     return $this->hasManyThrough('App\Models\Course', 'App\Models\CartItem', 'cart_id', 'course_id', 'id', 'course_id')
    //         ->select('courses.course_id', '.courses.course_name', 'courses.course_price', 'courses.course_feature_image', 'courses.unit_currency');
    // }

    public function courses() {
        return $this->hasManyThrough('App\Models\Course', 'App\Models\CartItem', 'cart_id', 'course_id', 'id', 'course_id')
            ->where('cart_item.isUsed', 0)
            ->select('courses.course_id', '.courses.course_name', 'courses.course_price', 'courses.course_feature_image', 'courses.unit_currency', 'cart_item.id as cart_item_id');
    }
}

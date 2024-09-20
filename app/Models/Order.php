<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Order
 * @package App\Models
 * @version November 25, 2021, 2:55 pm +07
 *
 * @property integer $user_id
 * @property integer $status
 * @property number $total
 * @property integer $grandTotal
 * @property string $discount_code
 */
class Order extends Model
{
    // use SoftDeletes;


    public $table = 'orders';
    
    protected $dates = ['deleted_at'];

    public $timestamps = true;

    protected $primaryKey = 'order_id';

    public $fillable = [
        'user_id',
        'status',
        'total',
        'grandTotal',
        'discount_code',
        'salePrice',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'order_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'string',
        'total' => 'float',
        'grandTotal' => 'integer',
        'discount_code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function orderBy()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItem', 'order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function courses()
    {   
        // return $this->hasManyThrough('App\Models\Course', 'App\Models\CartItem', 'cart_id', 'course_id', 'id', 'course_id')
        return $this->hasManyThrough('App\Models\Course', 'App\Models\OrderItem', 'order_id', 'course_id', 'order_id', 'course_id')
        ->select('courses.course_id', '.courses.course_name', 'courses.course_price', 'courses.course_feature_image');
    }
    
    public function transaction()
    {
        return $this->hasOne('App\Models\Transaction', 'order_id');
    }
}

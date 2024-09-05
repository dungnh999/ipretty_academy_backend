<?php

namespace App\Models;

use Eloquent as Model;



/**
 * Class OrderItem
 * @package App\Models
 * @version November 25, 2021, 3:18 pm +07
 *
 * @property integer $course_id
 * @property integer $order_id
 * @property integer $course_price
 */
class OrderItem extends Model
{


    public $table = 'order_items';

    protected $primaryKey = 'order_item_id';

    public $timestamps = true;

    public $fillable = [
        'course_id',
        'order_id',
        'course_price'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'order_item_id' => 'integer',
        'course_id' => 'integer',
        'order_id' => 'integer',
        'course_price' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}

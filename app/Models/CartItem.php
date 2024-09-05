<?php

namespace App\Models;

use Eloquent as Model;

class CartItem extends Model
{

    public $table = 'cart_item';

    public $timestamps = true;

    public $fillable = [
        'course_id',
        'quantity',
        'status',
        'cart_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'course_id' => 'integer',
        'quantity' => 'integer',
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

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    public function cart()
    {
        return $this->belongsTo('App\Models\Cart', 'cart_id');
    }
}

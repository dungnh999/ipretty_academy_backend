<?php

namespace App\Models;

use Eloquent as Model;
use PDO;

class DiscountCode extends Model
{

    public $table = 'discount_code';

    public $fillable = [
        'discount_code',
        'title',
        'time_start',
        'type',
        'created_by',
        'expired_at',
        'count',
        'sale_price'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'discount_code' => 'string',
        'title' => 'string',
        'type' => 'string',
        'created_by' => 'integer',
        'time_start' => 'datetime:Y-m-d H:i',
        'expired_at' => 'datetime:Y-m-d H:i',
        'count' => 'integer',
        'sale_price' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'sale_price' => 'required',
        'title' => 'required',
        'expired_at' => 'required',
        'time_start' => 'required',
        // 'count' => 'integer',
        // 'count' => 'required',
        'discount_code' => 'required',
    ];

    public function scopePriceOfCode ($query, $code) {
        return $query->where('discount_code', $code)->select('id', 'sale_price', 'type');
    }

    public function discountCodeUsed() {
        return $this->hasMany('App\Models\UserActivateDiscountCode', 'discount_code_id');
    }
}

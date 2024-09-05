<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transaction
 * @package App\Models
 * @version November 26, 2021, 3:08 pm +07
 *
 * @property string $transaction_code
 * @property string $payment_method
 * @property integer $order_id
 * @property integer $user_id
 * @property string $status
 */
class Transaction extends Model
{
    use SoftDeletes;

    public $table = 'transactions';
    
    protected $dates = ['deleted_at'];

    protected $primaryKey = 'transaction_id';

    public $timestamps = true;


    public $fillable = [
        'transaction_code',
        'payment_method',
        'order_id',
        'user_id',
        'status',
        'confirmedBy',
        'confirmed_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'transaction_id' => 'integer',
        'transaction_code' => 'string',
        'payment_method' => 'string',
        'order_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'string',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
        'confirmed_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function confirmedBy()
    {
        return $this->belongsTo('App\Models\User', 'confirmedBy');
    }

    public function buyer()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id')->with('courses');
    }

    public function scopeTransactionHistories($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }


}

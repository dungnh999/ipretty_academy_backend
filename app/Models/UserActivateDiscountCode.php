<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivateDiscountCode extends Model
{
    use HasFactory;

    public $table = 'user_activate_discount_code';

    public $timestamps = true;
    
    public $fillable = [
        'user_id',
        'discount_code_id',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

}

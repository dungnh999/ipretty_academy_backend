<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class UserAddresses extends Model
{
    use HasFactory;
    public $table = 'user_addresses';
    protected $dates = ['deleted_at'];
    public $fillable = [
        'user_id',
        'name',
        'address',
        'province_id',
        'district_id',
        'ward_id',
        'is_default',
        'created_at',
        'updated_at',
        'phone_shipping'
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function province(){
        return $this->belongsTo('App\Models\Province','id');
    }
    public function district(){
        return $this->belongsTo('App\Models\District','id');
    }
    public function ward(){
        return $this->belongsTo('App\Models\Ward','id');
    }
}

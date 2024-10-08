<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDepartment extends Model
{
    use SoftDeletes;


    public $table = 'user_departments';
    

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'department_id';

    public $fillable = [
        'department_name'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'department_id' => 'integer',
        'department_name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}

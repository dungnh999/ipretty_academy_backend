<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class SessionLesson extends Model
{
    use SoftDeletes;


    public $table = 'sessions_lessons';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'session_id',
        'lesson_id',
        'count_views'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'session_id' => 'integer',
        'lesson_id' => 'integer',
        'count_views' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}

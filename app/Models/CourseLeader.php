<?php

namespace App\Models;

use Eloquent as Model;



/**
 * Class CourseLeader
 * @package App\Models
 * @version October 5, 2021, 3:50 am UTC
 *
 * @property integer $course_id
 * @property integer $leader_id
 */
class CourseLeader extends Model
{


    public $table = 'courses_leaders';

    public $timestamps = false;

    public $fillable = [
        'course_id',
        'leader_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'course_id' => 'integer',
        'leader_id' => 'integer'
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

    public function leader()
    {
        return $this->belongsTo('App\Models\User', 'leader_id', 'id');
    }
    
}

<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class CourseStudent
 * @package App\Models
 * @version September 23, 2021, 3:27 pm UTC
 *
 * @property integer $course_id
 * @property integer $student_id
 * @property integer $percent_finish
 * @property boolean $isPassed
 */
class CourseStudent extends Model
{
    public $table = 'courses_students';

    public $timestamps = true;

    public $fillable = [
        'course_id',
        'student_id',
        'percent_finish',
        'isPassed',
        'completed_at',
        'started_at',
        'rating',
        'comment',
        'isNoticed',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'course_id' => 'integer',
        'rating' => 'integer',
        'student_id' => 'integer',
        'comment' => 'string',
        'percent_finish' => 'integer',
        'isPassed' => 'boolean',
        'completed_at' => 'datetime:Y-m-d H:i',
        'started_at' => 'datetime:Y-m-d H:i',
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
        return $this->belongsTo('App\Models\Course', 'course_id','course_id');
    }
    public function courseName()
    {
        return $this->belongsTo('App\Models\Course', 'course_id','course_id')->select(['course_id', 'course_name', 'teacher_id', 'course_feature_image']);
    }

    public function student()
    {
        return $this->belongsTo('App\Models\User', 'student_id', 'id');
    }

    public function students()
    {
        return $this->belongsTo('App\Models\User', 'student_id', 'id')->whereHas('roles', function($q) {
            $q->where('name', 'user');
        });
    }
}

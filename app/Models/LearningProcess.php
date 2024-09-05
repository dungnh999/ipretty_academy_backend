<?php

namespace App\Models;

use Eloquent as Model;



/**
 * Class LearningProcess
 * @package App\Models
 * @version October 10, 2021, 1:40 am +07
 *
 * @property integer $lesson_id
 * @property integer $survey_id
 * @property integer $process
 * @property boolean $isCompleted
 * @property string $completed_at
 * @property string $started_at
 */
class LearningProcess extends Model
{


    public $table = 'learning_processes';

    public $timestamps = false;

    protected $primaryKey = 'process_id';

    public $fillable = [
        'lesson_id',
        'survey_id',
        'student_id',
        'course_id',
        'process',
        'isPassed',
        'isDraft',
        'completed_at',
        'started_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'process_id' => 'integer',
        'lesson_id' => 'integer',
        'survey_id' => 'integer',
        'course_id' => 'integer',
        'student_id' => 'integer',
        'process' => 'integer',
        'isPassed' => 'boolean',
        'isDraft' => 'boolean',
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


    public function survey()
    {
        return $this->belongsTo('App\Models\Survey', 'survey_id');
    }

    public function lesson()
    {
        return $this->belongsTo('App\Models\Lesson', 'lesson_id');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\User', 'student_id');
    }
    
}

<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Survey
 * @package App\Models
 * @version September 17, 2021, 7:46 am UTC
 *
 * @property string $survey_title
 * @property integer $created_by
 * @property string $survey_duration
 * @property integer $percent_to_pass
 * @property integer $question_per_page
 */
class Survey extends Model
{
    public $table = 'surveys';
    
    protected $primaryKey = 'survey_id';

    public $fillable = [
        'survey_title',
        'survey_description',
        'created_by',
        'survey_duration',
        'percent_to_pass',
        'question_per_page'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'survey_id' => 'integer',
        'survey_title' => 'string',
        'survey_description' => 'string',
        'created_by' => 'integer',
        'survey_duration' => 'string',
        'percent_to_pass' => 'integer',
        'question_per_page' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function questions ()
    {
        return $this->hasMany('App\Models\Question', 'survey_id');
    }

    public function chapter () {
        return $this->belongsTo('App\Models\Chapter', 'survey_id', 'survey_id');
    }

    public function answers()
    {
        return $this->hasMany('App\Models\Answer', 'survey_id');
    }

    public function learningProcess($student_id, $survey_id)
    {
        return $this->hasMany('App\Models\LearningProcess', 'survey_id')->where('student_id', $student_id)->where('survey_id', $survey_id)->first();
    }

    public function learningProcessForCourse()
    {
        return $this->hasOne('App\Models\LearningProcess', 'survey_id');
    }
}

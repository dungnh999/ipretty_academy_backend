<?php

namespace App\Models;

use Eloquent as Model;



/**
 * Class Answer
 * @package App\Models
 * @version October 7, 2021, 5:34 pm +07
 *
 * @property integer $question_id
 * @property integer $option_id
 * @property integer $answer_by
 * @property integer $survey_id
 * @property integer $point
 */
class Answer extends Model
{
    public $table = 'answers';

    protected $primaryKey = 'answer_id';


    public $fillable = [
        'question_id',
        'option_id',
        'answer_by',
        'survey_id',
        'percent_achieved'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'answer_id' => 'integer',
        'question_id' => 'integer',
        'option_id' => 'string',
        'answer_by' => 'integer',
        'survey_id' => 'integer',
        'percent_achieved' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'question_id');
    }

    // public function option()
    // {
    //     return $this->belongsTo('App\Models\QuestionOption', 'option_id');
    // }

    public function answerBy()
    {
        return $this->belongsTo('App\Models\User', 'answer_by');
    }

    public function survey()
    {
        return $this->belongsTo('App\Models\Survey', 'survey_id');
    }
    
}

<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Question
 * @package App\Models
 * @version September 18, 2021, 10:34 am UTC
 *
 * @property string $question_title
 * @property string $question_description
 * @property string $question_type
 * @property integer $number_order
 * @property string $question_attachments
 * @property boolean $has_attachment
 * @property integer $session_id
 */
class Question extends Model implements HasMedia
{
    use InteractsWithMedia;

    public $table = 'questions';

    public $timestamps = false;

    protected $primaryKey = 'question_id';

    public $fillable = [
        'question_title',
        'question_description',
        'question_type',
        'number_order',
        'question_attachments',
        'has_attachment',
        'percent_achieved',
        'survey_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'question_id' => 'integer',
        'question_title' => 'string',
        'question_type' => 'string',
        'number_order' => 'integer',
        'has_attachment' => 'boolean',
        'survey_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'question_type' => 'MultipleChoice,SingleChoice'
    ];

    public function options()
    {
        return $this->hasMany('App\Models\QuestionOption', 'question_id');
    }

    public function right_options()
    {
        return $this->hasMany('App\Models\QuestionOption', 'question_id')->where('right_answer', true);
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MEDIA_COLLECTION["QUESTION_ATTACHMENTS"])
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
    }

    public function answer()
    {
        return $this->belongsTo('App\Models\Answer', 'question_id');
    }

    public function answerBy($user_id, $survey_id)
    {
        return $this->hasMany('App\Models\Answer', 'question_id')->where('answer_by', $user_id)->where('survey_id', $survey_id)->first();
    }
}

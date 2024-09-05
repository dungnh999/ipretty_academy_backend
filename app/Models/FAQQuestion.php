<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class FAQQuestion
 * @package App\Models
 * @version October 27, 2021, 2:49 pm +07
 *
 * @property string $question_name
 * @property string $answer_name
 * @property integer $number_order
 * @property integer $faq_id
 */
class FAQQuestion extends Model
{
    use SoftDeletes;


    public $table = 'faq_questions';
    
    protected $primaryKey = 'question_id';

    protected $dates = ['deleted_at'];

    public $timestamps = false;

    public $fillable = [
        'question_name',
        'answer_name',
        'number_order',
        'faq_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'question_id' => 'integer',
        'number_order' => 'integer',
        'faq_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function faq () {
        return $this->belongsTo('App\Models\FrequentlyAskedQuestions', 'faq_id');
    }

    public function likes() {
        return $this->hasMany('App\Models\FAQLike', 'question_id', 'question_id')->where('status', 'Like');
    }

    public function dislikes() {
        return $this->hasMany('App\Models\FAQLike', 'question_id', 'question_id')->where('status', 'Dislike');
    }

    public function comments() {
        return $this->hasMany('App\Models\CommentFAQ', 'question_id', 'question_id')->whereNull('parent_id')->with('comment_by', function($q) {
            $q->select('id', 'name', 'email', 'avatar');
        })->with('child_comments');
    }

    
}

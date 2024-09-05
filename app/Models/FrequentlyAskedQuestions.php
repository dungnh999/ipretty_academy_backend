<?php

namespace App\Models;

use Eloquent as Model;



/**
 * Class FrequentlyAskedQuestions
 * @package App\Models
 * @version October 11, 2021, 10:18 am +07
 *
 * @property string $title
 * @property integer $body
 * @property integer $create_by
 * @property string $attachments
 */
class FrequentlyAskedQuestions extends Model
{


    public $table = 'frequently_asked_questions';

    public $timestamps = true;
    
    public $fillable = [
        'title',
        'body',
        'created_by',
        // 'attachments',
        // 'type_category_id',
        'isPublished'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'body' => 'string',
        'created_by' => 'integer',
        'updated_at' => 'datetime:Y-m-d H:i',
        // 'type_category_id' => 'integer',
        // 'attachments' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function createdBy(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }


    public function faqQuestions()
    {
        return $this->hasMany('App\Models\FAQQuestion', 'faq_id', 'id')->with('comments')->withCount('likes')->withCount('dislikes');
    }

}

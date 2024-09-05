<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class QuestionOption
 * @package App\Models
 * @version September 18, 2021, 10:40 am UTC
 *
 * @property integer $question_id
 * @property string $option_body
 * @property boolean $right_answer
 * @property string $option_attachments
 * @property boolean $is_image
 */
class QuestionOption extends Model implements HasMedia
{
    use InteractsWithMedia;

    public $table = 'question_options';

    public $timestamps = false;

    protected $primaryKey = 'option_id';

    public $fillable = [
        'question_id',
        'option_body',
        'right_answer',
        'option_attachments',
        'option_attachmant_name',
        'is_image'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'option_id' => 'integer',
        'question_id' => 'integer',
        'option_body' => 'string',
        'right_answer' => 'boolean',
        'is_image' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MEDIA_COLLECTION["OPTION_ATTACHMENTS"])
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
    }

    public function question () {
        return $this->belongsTo('App\Models\Question', 'question_id');
    }
}

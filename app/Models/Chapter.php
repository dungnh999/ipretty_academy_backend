<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Chapter extends Model
{
    use SoftDeletes;

    public $table = 'chapters';

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'chapter_id';


    public $fillable = [
        'chapter_name',
        'course_id',
        'survey_id',
        'number_order',
        'course_version'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'chapter_id' => 'integer',
        'chapter_name' => 'string',
        'course_id' => 'integer',
        'survey_id' => 'integer',
        'course_version' => 'integer',
        'number_order' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'chapter_name' > 'required'
    ];

    public function lessons()
    {
        return $this->belongsToMany('App\Models\Lesson', 'chapters_lessons', 'chapter_id', 'lesson_id');
    }

    public function lessonsExamView()
    {
        return $this->belongsToMany('App\Models\Lesson', 'chapters_lessons', 'chapter_id', 'lesson_id')->select('lessons.lesson_id', 'chapter_id', 'lesson_name');
    }

    public function survey()
    {
        return $this->hasOne('App\Models\Survey', 'survey_id', 'survey_id');
    }

    public function surveyExamView()
    {
        return $this->hasOne('App\Models\Survey', 'survey_id', 'survey_id')->select('survey_id', 'survey_title');
    }

    public function course() {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

}

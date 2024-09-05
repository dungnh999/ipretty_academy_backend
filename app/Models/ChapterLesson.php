<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterLesson extends Model
{
    use HasFactory;

    public $table = 'chapters_lessons';

    public $timestamps = false;

    public $fillable = [
        'id',
        'chapter_id',
        'lesson_id',
        'number_order',
    ];

    public function chapter() 
    {
        return $this->belongsTo('App\Models\Chapter', 'chapter_id');
    }

    public function lesson()
    {
        return $this->belongsTo('App\Models\Lesson', 'lesson_id');
    }

}

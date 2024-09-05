<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQLike extends Model
{
    public $table = 'faq_likes';
    
    public $fillable = [
        'id',
        'user_id',
        'question_id',
        'status',
        'created_at',
        'updated_at',
    ];
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQCategory extends Model
{
    public $table = 'faq_category';
    public $fillable = [
        'id',
        'category_name',
    ];

    // public function frequently_asked_questions(){
    //     return $this->hasMany('App\Models\FrequentlyAskedQuestions','type_category_id');
    // }
}


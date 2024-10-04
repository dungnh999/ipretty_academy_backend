<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Session extends Model
{
    // use SoftDeletes;
    public $table = 'sessions';
    
    // protected $dates = ['deleted_at'];

    protected $primaryKey = 'session_id';

    public $timestamps = false;

    public $fillable = [
        'session_name',
        'survey_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'session_id' => 'integer',
        'session_name' => 'string',
        'survey_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function questions()
    {
        return $this->hasMany('App\Models\Question', 'session_id');
    }
}

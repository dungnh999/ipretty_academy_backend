<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Event extends Model
{

    public $table = 'events';
    
    public $fillable = [
        'id',
        'title',
        'description',
        'course_id',
        'create_by',
        'distance_time_reminder',
        'color',
        'time_start',
        'time_end',
        'status_reminder',
        'distance_time_reminder_2'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'course_id' => 'integer',
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'create_by' => 'integer',
        'distance_time_reminder' => 'integer',
        'distance_time_reminder_2' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    // public static $rules = [
    //     'title' => 'required',
    //     'status_reminder' => 'required',
    //     'time_end' => 'required|date_format:"Y-m-d H:i:00"|after_or_equal:time_start',
    //     'time_start' => 'required|date_format:"Y-m-d H:i:00"|after_or_equal:now',
    // ];

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    public function eventStudent () {
        return $this->hasMany('App\Models\EventStudent', 'event_id');
    }
    
    
}

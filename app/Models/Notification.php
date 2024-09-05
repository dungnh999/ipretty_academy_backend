<?php

namespace App\Models;

use Eloquent as Model;

class Notification extends Model
{

    public $table = 'notifications';
    
    public $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'checked',
    ];
    protected $casts = [
        'id' => 'string',
        'type' => 'string',
        'data' => 'string',
        'read_at' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}

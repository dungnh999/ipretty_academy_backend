<?php

namespace App\Models;

use Eloquent as Model;



/**
 * Class PushNotification
 * @package App\Models
 * @version November 23, 2021, 11:45 am +07
 *
 * @property boolean $isPublished
 * @property integer $created_by
 * @property string $notification_cat
 * @property integer $group_receivers
 * @property string $notification_message
 */
class PushNotification extends Model
{

    public $table = 'push_notifications';

    protected $primaryKey = 'notification_id';

    public $timestamps = true;

    public $fillable = [
        'isPublished',
        'created_by',
        'notification_title',
        'notification_cat',
        'group_receivers',
        'notification_message'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'notification_id' => 'integer',
        'isPublished' => 'boolean',
        'created_by' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
        'notification_cat' => 'string',
        'group_receivers' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
    
}

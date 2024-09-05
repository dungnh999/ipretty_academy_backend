<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class ReportContact
 * @package App\Models
 * @version November 18, 2021, 10:15 am +07
 *
 * @property string $report_title
 * @property string $report_content
 * @property string $attachment
 * @property integer $reporter
 * @property boolean $isReport
 * @property boolean $isSended
 */
class ReportContact extends Model implements HasMedia
{

    use InteractsWithMedia;

    public $table = 'report_contacts';

    public $timestamps = true;

    protected $primaryKey = 'report_id';
    
    public $fillable = [
        'report_title',
        'report_content',
        'attachments',
        'reporter_id',
        'isReport',
        'isSended',
        'reporter_email',
        'reporter_phone',
        'reporter_name',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'report_id' => 'integer',
        'attachments' => 'array',
        'report_title' => 'string',
        'report_content' => 'string',
        'reporter_email' => 'string',
        'reporter_phone' => 'string',
        'reporter_name' => 'string',
        'reporter_id' => 'integer',
        'isReport' => 'boolean',
        'isSended' => 'boolean'
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
        $this->addMediaCollection('attachments');
            // ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
            // ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;
    }

    // remember to save before run this method
    public function handleMedia($request = null): void
    {
        if ($request == null) {
            return;
        }

        // Store Image
        if ($request->hasFile('attachments') && $request->file('attachments')->isValid()) {
            $this->addMediaFromRequest('attachments')->toMediaCollection('attachments');
            $this->attachments = $this->getFirstMediaUrl('attachments');
            $this->save(); //remember to save again
        } else {
            // TODO: throw exception
        }
    }

    public function reporter()
    {
        return $this->belongsTo('App\Models\User', 'reporter_id')->select('name', 'id', 'email', 'avatar');
    }    
}

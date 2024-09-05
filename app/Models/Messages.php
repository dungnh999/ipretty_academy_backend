<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Contract\CommonBusiness;
class Messages extends Model implements HasMedia
{
    public $table = 'messages';
    use InteractsWithMedia;
    use CommonBusiness;

    protected $mediaCollection = [
        "is_attachment" => "is_attachment",
    ];
    public $fillable = [
        'sender_id',
        'body',
        'type',
        'is_attachment',
        'receiver_id',
        'receiver_seen'
    ];

    protected $casts = [
        'id' => 'integer',
        'sender_id' => 'integer',
        'body' => 'string',
        'is_attachment' => 'string',
        'receiver_id' => 'integer',
        'receiver_seen' => 'boolean'
    ];

    public static $rules = [
        'receiver_id' => 'required',
    ];


    public function registerMediaCollections(): void
    {
        // $this->addMediaCollection($this->mediaCollection["is_attachment"])
        //     ->acceptsMimeTypes(['video/mp4', 'video/x-ms-wmv', 'video/avi'])
        //     ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;

        $this->addMediaCollection($this->mediaCollection["is_attachment"])
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif',
             'application/pdf', 'application/xls', 'application/xlsx', 'application/doc',
             'application/docx', 'application/ppt', 'application/pptx', 'application/zip',
             'application/msword',
             'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
             'application/vnd.openxmlformats-officedocument.presentationml.presentation',
             'application/vnd.ms-powerpoint',
             'application/vnd.ms-excel',
             'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
             'application/vnd.rar'])
             ->singleFile();
    }

    public function handleMedia($request = null)
    {
        if ($request == null) {
            return false;
        }
        try {
            if ($request->hasFile('is_attachment') && $request->file('is_attachment')->isValid()) {

                $file = $request->file('is_attachment');
                // $this->addMediaFromRequest('main_attachment')
                $this->addMedia($file)
                    ->usingFileName(
                        CommonBusiness::change_alias($file->getClientOriginalName())
                    )
                    ->toMediaCollection($this->mediaCollection["is_attachment"]);
                $this->is_attachment = $this->getFirstMediaUrl($this->mediaCollection["is_attachment"]);
                $this->type = 'file';
                $this->save(); 
                return true;
            } 
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
        
    }

    public function statusLatestMessage($receiver_id) {
        return $this->hasOne('App\Models\UserMessageStatus', 'lasted_message_seen_id', 'id')->where('user_id', $receiver_id)->latest()->first();
    }
    
}

<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Contract\CommonBusiness;

class Lesson extends Model implements HasMedia
{
    use SoftDeletes;

    use InteractsWithMedia;

    use CommonBusiness;

    public $table = 'lessons';
    
    protected $dates = ['deleted_at'];

    protected $primaryKey = 'lesson_id';

    protected $mediaCollection = [
        "lesson_attachment" =>  MEDIA_COLLECTION["LESSON_ATTACHMENT"],
        "main_attachment" =>  MEDIA_COLLECTION["LESSON_MAIN_ATTACHMENT"],
        "lesson_material" => MEDIA_COLLECTION["LESSON_MATERIAL"]
    ];
    
    public $fillable = [
        'lesson_name',
        'lesson_description',
        'lesson_content',
        'lesson_material',
        'is_demo',
        'lesson_attachment',
        'lesson_author',
        'main_attachment',
        'lesson_duration'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'lesson_id' => 'integer',
        'lesson_name' => 'string',
        'lesson_description' => 'string',
        'lesson_material' => 'string',
        'lesson_content' => 'string',
        'lesson_attachment' => 'array',
        'main_attachment' => 'string',
        'is_demo' => 'integer',
        'lesson_author' => 'integer',
        'lesson_duration' => 'string',
        'created_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'lesson_name' => 'required',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->mediaCollection["main_attachment"])
            // ->acceptsMimeTypes(['video/mp4', 'video/x-ms-wmv', 'video/avi'])
            ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;

        $this->addMediaCollection($this->mediaCollection["lesson_attachment"]);

        $this->addMediaCollection($this->mediaCollection["lesson_material"]);

        // ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif',
            //  'application/pdf', 'application/xls', 'application/xlsx', 'application/doc',
            //  'application/docx', 'application/ppt', 'application/pptx', 'application/zip',
            //  'application/msword',
            //  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            //  'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            //  'application/vnd.ms-powerpoint',
            //  'application/vnd.ms-excel',
            //  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            //  'application/vnd.rar']);
    }

    public function handleMedia($request = null)
    {
        if ($request == null) {
            return;
        }
        try {
            if ($request->hasFile('main_attachment') && $request->file('main_attachment')->isValid()) {

                $file = $request->file('main_attachment');
                // $this->addMediaFromRequest('main_attachment')
                $this->addMedia($file)
                    ->usingFileName(
                        CommonBusiness::change_alias($file->getClientOriginalName())
                    )
                    ->toMediaCollection($this->mediaCollection["main_attachment"]);
                $this->main_attachment = $this->getFirstMediaUrl('main_attachment');
                $this->main_attachment_name = $file->getClientOriginalName();
                $this->save(); //remember to save again
                return true;
            } else if ($request->hasFile('lesson_attachment') && $request->file('lesson_attachment')->isValid()) {

            } else {
                // TODO: throw exception

            }
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'lesson_author');
    }

    public function chapters()
    {
        return $this->belongsToMany('App\Models\Chapter', 'chapters_lessons', 'lesson_id', 'chapter_id' );
    }

    public function learningProcess($student_id, $lesson_id)
    {
        return $this->hasMany('App\Models\LearningProcess', 'lesson_id')->where('student_id', $student_id)->where('lesson_id', $lesson_id)->first();
    }

    public function learningProcessForCourse()
    {
        return $this->hasOne('App\Models\LearningProcess', 'lesson_id');
    }
    
}

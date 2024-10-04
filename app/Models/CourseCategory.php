<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Contract\CommonBusiness;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCategory extends Model implements HasMedia
{
    use SoftDeletes;

    use InteractsWithMedia;

    use CommonBusiness;

    public $table = 'course_categories';

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'category_id';

    protected $mediaCollection = [
        "course_category_attachment" => MEDIA_COLLECTION["COURSE_CATEGORY_ATTACHMENT"]
    ];

    public $fillable = [
        'category_name',
        'category_description',
        'category_code',
        'created_by',
        'category_type_id',
        'course_category_attachment',
        'created_at',
        'updated_at',
        'isPublished',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'isPublished' => 'boolean',
        'category_id' => 'integer',
        'category_name' => 'string',
        'category_type_id' => 'integer',
        'course_category_attachment' => 'string',
        'created_by' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
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
        return $this->belongsTo(User::class, 'created_by');
    }

    public function categoryType()
    {
        return $this->belongsTo(CourseCategoryTypes::class, 'category_type_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->mediaCollection["course_category_attachment"])
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

//        if ($request == null) {
//            return;
//        }
//        try {
        if ($request->hasFile('course_category_attachment') && $request->file('course_category_attachment')->isValid()) {
            $file = $request->file('course_category_attachment');
            // $this->addMediaFromRequest('main_attachment')
            $newMedia = $this->addMedia($file)
                ->usingFileName(
                    CommonBusiness::change_alias($file->getClientOriginalName())
                )
                ->toMediaCollection($this->mediaCollection["course_category_attachment"]);
            $this->course_category_attachment = str_replace(public_path(), '', $newMedia->getPath());
            $this->save(); //remember to save again
            return true;
        } else {
            // TODO: throw exception

        }


//        } catch (\Throwable $th) {
//            //throw $th;
//            return false;
//        }
    }

    public function courses()
    {
        return $this->hasMany('App\Models\Course', 'category_id', 'category_id');
    }

    public function coursesWithStudents()
    {
        return $this->hasManyThrough('App\Models\CourseStudent', 'App\Models\Course', 'category_id', 'course_id');
    }

    public function studentsCurrentMonth()
    {

        $month = $this->getCurPrevMonth();

        return $this->hasManyThrough('App\Models\CourseStudent', 'App\Models\Course', 'category_id', 'course_id')
            ->whereDate('courses_students.created_at', '<=', $month['currentMonth'])->whereDate('courses_students.created_at', '>', $month['previousMonth']);
    }

    public function studentsPrevMonth()
    {

        $month = $this->getCurPrevMonth();

        return $this->hasManyThrough('App\Models\CourseStudent', 'App\Models\Course', 'category_id', 'course_id')
            ->whereDate('courses_students.created_at', '<=', $month['previousMonth'])->whereDate('courses_students.created_at', '>', $month['prevPreviousMonth']);
    }

    public function coursesOfMonth()
    {
        $month = $this->getCurPrevMonth();

        return $this->hasMany('App\Models\Course', 'category_id', 'category_id')
            ->whereDate('courses.created_at', '<=', $month['currentMonth'])->whereDate('courses.created_at', '>', $month['previousMonth']);
    }

    public function coursesOfPrevMonth()
    {
        $month = $this->getCurPrevMonth();

        return $this->hasMany('App\Models\Course', 'category_id', 'category_id')
            ->whereDate('courses.created_at', '<=', $month['previousMonth'])->whereDate('courses.created_at', '>', $month['prevPreviousMonth']);
    }

}

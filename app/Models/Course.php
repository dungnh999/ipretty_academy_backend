<?php

namespace App\Models;

use App\Contract\CommonBusiness;
use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use SoftDeletes;

    use InteractsWithMedia;

    use CommonBusiness;

    public $table = 'courses';
    
    protected $dates = ['deleted_at' , 'published_at'];

    protected $primaryKey = 'course_id';

    protected $mediaCollection = MEDIA_COLLECTION["COURSE_FEATURE_IMAGE"];

    protected $certificateCollection = MEDIA_COLLECTION["CERTIFICATE_IMAGE"];


    public $fillable = [
        'course_name',
        'course_created_by',
        'teacher_id',
        'course_feature_image',
        'course_description',
        'count_viewer',
        'category_id',
        'course_version',
        'course_price',
        'certificate_image',
        'startTime',
        'endTime',
        'deadline',
        'course_type',
        'course_sale_price',
        'course_target',
        'is_published',
        'isDraft',
        'unit_currency',
        'published_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'course_id' => 'integer',
        'course_name' => 'string',
        'course_feature_image' => 'string',
        'course_created_by' => 'integer',
        'course_type' => 'string',
        // 'course_target' => 'string',
        'teacher_id' => 'integer',
        'count_viewer' => 'integer',
        'category_id' => 'integer',
        'course_version' => 'integer',
        'course_price' => 'integer',
        'course_sale_price' => 'integer',
        'certificate_image' => 'string',
        'created_at' => 'datetime:Y-m-d H:i',
        'startTime' => 'datetime:Y-m-d H:i',
        'endTime' => 'datetime:Y-m-d H:i',
        'deadline' => 'datetime:Y-m-d H:i',
        'published_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    // public function getFormattedPriceAttribute()
    // {
    //     return number_format($this->attributes['course_price'], 0, ',', '.');
    // }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->mediaCollection)
        ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;
        $this->addMediaCollection($this->certificateCollection)
        ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;

    }

    // remember to save before run this method
    public function handleMedia($request = null, $collectionName = null, $collection = null): void
    {
        if ($request == null) {
            return;
        }
        // Store Image
        if ($request[$collectionName] && is_string($request[$collectionName])) {

            $base64 = $this->check_base64_image($request[$collectionName]);
            if ($base64['isValidFormat'] && $base64['isValidSize']) {

            }else {
                return;
            }
        } else if ($request->hasFile($collectionName) && $request->file($collectionName)->isValid() && !is_string($request[$collectionName])) {
            
            $file = $request->file($collectionName);
            // var_dump('sdasdds');
            // var_dump($collectionName);
            // var_dump('sdasdds2');
            // $this->addMediaFromRequest('main_attachment')
            $newMedia = $this->addMedia($file)
                ->usingFileName(
                    CommonBusiness::change_alias($file->getClientOriginalName())
                )
                ->toMediaCollection($collectionName);
            // $newMedia = $this->addMediaFromRequest($collectionName)->toMediaCollection($collectionName);
            // $this[$collectionName] = $newMedia->getUrl();
  
            $this[$collectionName] = str_replace(public_path(), '', $newMedia->getPath());


            $this->save(); //remember to save again
        } else {
            // TODO: throw exception
        }

    }

    public function leaders () {
        return $this->belongsToMany('App\Models\User', 'courses_leaders', 'course_id', 'leader_id');
    }

    public function teacher () {
        return $this->belongsTo('App\Models\User', 'teacher_id');
    }
    public function teacherName () {
        return $this->belongsTo('App\Models\User', 'teacher_id')->select(['name','avatar', 'id']);
    }

    public function createdBy () {
        return $this->belongsTo('App\Models\User', 'course_created_by');
    }

    public function category () {
        return $this->belongsTo('App\Models\CourseCategory', 'category_id');
    }

    public function certificate () {
        return $this->belongsTo('App\Models\Certificate', 'certificate_id');
    }

    public function chapters () {
        return $this->hasMany('App\Models\Chapter', 'course_id');
    }

    public function chaptersForExamView () {
        return $this->hasMany('App\Models\Chapter', 'course_id')->select('chapter_id', 'chapter_name', 'course_id', 'survey_id');
    }

    public function chaptersWithLessonSurveyForUser ($course_id, $student_id) {
        return $this->load(['chaptersForExamView.lessonsExamView.learningProcessForCourse' => function($q) use($course_id, $student_id){
            $q->where('student_id', $student_id)->where('course_id', $course_id);
        }, 'chaptersForExamView.surveyExamView.learningProcessForCourse' => function($q) use($course_id, $student_id) {
            $q->where('student_id', $student_id)->where('course_id', $course_id);
        }]);
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\User', 'courses_students', 'course_id', 'student_id');
    }

    public function studentsOfMonth()
    {
        $month = $this->getCurPrevMonth();
        return $this->belongsToMany('App\Models\User', 'courses_students', 'course_id', 'student_id')->whereDate('courses_students.created_at', '<=', $month['currentMonth'])->whereDate('courses_students.created_at', '>', $month['previousMonth']);
    }

    public function studentsOfPrevMonth()
    {
        $month = $this->getCurPrevMonth();
        return $this->belongsToMany('App\Models\User', 'courses_students', 'course_id', 'student_id')->whereDate('courses_students.created_at', '<=', $month['previousMonth'])->whereDate('courses_students.created_at', '>', $month['prevPreviousMonth']);
    }

    public function studentsLearning()
    {
        return $this->belongsToMany('App\Models\User', 'courses_students', 'course_id', 'student_id')->wherePivot('isPassed', 0);
    }

    public function studentsFinish()
    {
        return $this->belongsToMany('App\Models\User', 'courses_students', 'course_id', 'student_id')->wherePivot('isPassed', 1);
    }

    public function studentResultById($student_id)
    {
        return $this->hasMany('App\Models\CourseStudent', 'course_id')->where('student_id', $student_id)->first();
    }

    public function studentResult()
    {
        return $this->hasMany('App\Models\CourseStudent', 'course_id');
    }

    public function studentResults()
    {
        return $this->hasMany('App\Models\CourseStudent', 'course_id');
    }

    public function studentResultCourse($course_id, $student_id)
    {
        return $this->hasOne('App\Models\CourseStudent', 'course_id')->where('course_id', $course_id)->where('student_id', $student_id)->first();
    }

    public function scopeTotalViewer ($query) {
        return $query->selectRaw("sum(case when count_viewer then count_viewer else 0 end) as count_viewer");
    }

    public function scopeTotalViewerMonth ($query, $cur_month, $prev_month, $columnName) {
        return $query->selectRaw('sum(case when count_viewer AND DATE(courses.created_at) <= DATE("' . $cur_month . '") AND DATE(courses.created_at) > DATE("' . $prev_month . '") then count_viewer else 0 end) as ' . $columnName);

    }

    // public function rating()
    // {
    //     return $this->hasMany('App\Models\CourseStudent', 'course_id');
    // }

    // public function avgRating()
    // {
    //     $result = $this->hasMany('App\Models\CourseStudent')
    //         ->selectRaw('avg(rating) as avgRating')->first();

    //     return $result;
    // }
    public function avgRating()
    {
        return round($this->studentResult()->avg('rating'), 1);

    }

    public function scopeBusinessCourses ($query)
    {
        return $query->where('course_type', 'Business')
            ->where('is_published', 1)
            ->where('isDraft', 0)->where('course_price', '>', 0);
    }

    public function scopeTotalPrice ($query, $course_ids) {
        return $query->whereIn('course_id', $course_ids)->sum('course_price');
    }

    public function transactions () {
        return $this->hasManyThrough('App\Models\Transaction', 'App\Models\OrderItem', 'course_id', 'order_id', 'course_id', 'order_id');
    }

    public function orders () {
        return $this->hasManyThrough('App\Models\Order', 'App\Models\OrderItem', 'course_id', 'order_id', 'course_id', 'order_id');
    }

    public function orderItems () {
        return $this->hasMany('App\Models\OrderItem', 'course_id');
    }

    public function studentResultRatingAvg()
    {
        return $this->hasMany('App\Models\CourseStudent', 'course_id')->whereNotNull('rating')->where('rating', '>', 0);
    }

    public function studentById($student_id)
    {
        return $this->belongsToMany('App\Models\User', 'courses_students', 'course_id', 'student_id')->wherePivot('courses_students.student_id', $student_id)->first();
    }

    public function events() {
        $now = CommonBusiness::getTimeNowJob();

        return $this->hasMany('App\Models\Event', 'course_id')->where('events.time_end', '>', $now);
    }

    public function eventStudents($user_id) {
        return $this->hasManyThrough('App\Models\EventStudent', 'App\Models\Event', 'course_id', 'event_id', 'course_id', 'id')->where('user_id', $user_id)->get();
    }
}

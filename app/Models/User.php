<?php

namespace App\Models;

use App\Contract\CommonBusiness;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use App\Models\UserAddresses;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    use HasFactory;
    use HasApiTokens;
    use InteractsWithMedia;
    use CommonBusiness;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'phone',
        'address',
        'avatar',
        'meta',
        'id_number',
        'point',
        'birth_day',
        'activation_token',
        'department_id',
        'lang',
        'menuroles',
        'code',
        'gender',
        'latest_active_at',
        'isLocked',
        'about',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'deleted_at', 'activation_token', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d H:i',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
        'birth_day' => 'datetime:Y-m-d',
        'avatar' => 'string',
        'about' => 'string',
        'address' => 'string',
        'isLocked' => 'boolean',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $attributes = [
        'menuroles' => 0,
    ];

        /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'email' => 'email|required',
        'password' => 'required',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;

        $this->addMediaCollection('image_attachment')
            ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;
    }

    // remember to save before run this method
    public function handleMedia($request = null): void
    {
        if ($request == null) {
            return;
        }

        // Store Image
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $this->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            $this->avatar = $this->getFirstMediaUrl('avatar');
            $this->save(); //remember to save again
        } else {
            // TODO: throw exception
        }
    }

    // remember to save before run this method
    public function handleImageUpload($request = null)
    {
        if ($request == null) {
            return false;
        }
        // Store Image
        if ($request->hasFile('image_attachment') && $request->file('image_attachment')->isValid()) {
            $media = $this->addMediaFromRequest('image_attachment')->toMediaCollection('image_attachment');
            $mediaUrl = $media->getUrl();
            return $mediaUrl;
        } else {
            return false;
            // TODO: throw exception
        }
    }

    public function socialAccounts() {
        return $this->hasMany('App\Models\SocialAccount');
    }

    public function department() {
        return $this->belongsTo('App\Models\UserDepartment', 'department_id');
    }

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course', 'courses_students', 'student_id', 'course_id');
    }

    public function leaderCourses()
    {
        return $this->belongsToMany('App\Models\Course', 'courses_leaders', 'leader_id', 'course_id');
    }

    public function course($id)
    {
        return $this->belongsToMany('App\Models\Course', 'courses_students', 'student_id', 'course_id')->where('course_id', '=', $id);
    }

    public function courseStudent ()
    {
        return $this->belongsTo('App\Models\CourseStudent', 'id', 'student_id');
    }

    public function learningProcess ($course_id) {
        return $this->hasMany('App\Models\LearningProcess', 'student_id', 'id')->where('course_id', $course_id)->get();
    }

    public function certifacates () {
        return $this->hasMany('App\Models\CourseStudent', 'student_id', 'id')->where('isPassed', 1);
    }

    public function coursesLearning () {
        return $this->hasMany('App\Models\CourseStudent', 'student_id', 'id')->where('isPassed', 0)->whereNotNull('started_at');
    }

    public function coursesLearningNotStart () {
        return $this->hasMany('App\Models\CourseStudent', 'student_id', 'id')->where('isPassed', 0)->whereNull('started_at');
    }

    public function coursesTeaching()
    {
        return $this->hasMany('App\Models\Course', 'teacher_id', 'id');
    }

    public function courseStudentById($id)
    {
        return $this->belongsTo('App\Models\CourseStudent', 'id', 'student_id')->where('course_id', $id)->first();
    }

    public function studentsFollow()
    {
        return $this->hasManyThrough('App\Models\CourseStudent', 'App\Models\Course', 'teacher_id', 'course_id', 'id', 'course_id')
        ->where('rating', '>', 0)->whereNotNull('rating');
    }

    public function studentsFollowTeacher()
    {
        return $this->hasManyThrough('App\Models\CourseStudent', 'App\Models\Course', 'teacher_id', 'course_id', 'id', 'course_id');
    }

    public function studentsFollowCurMonth()
    {
        $month = $this->getCurPrevMonth();
        return $this->hasManyThrough('App\Models\CourseStudent', 'App\Models\Course', 'teacher_id', 'course_id', 'id', 'course_id')
            ->where('rating', '>', 0)->whereNotNull('rating')
            ->whereDate('courses_students.created_at', '<=', $month['currentMonth'])->whereDate('courses_students.created_at', '>', $month['previousMonth']);
    }

    public function studentsFollowPrevMonth()
    {
        $month = $this->getCurPrevMonth();
        return $this->hasManyThrough('App\Models\CourseStudent', 'App\Models\Course', 'teacher_id', 'course_id', 'id', 'course_id')
            ->where('rating', '>', 0)->whereNotNull('rating')
            ->whereDate('courses_students.created_at', '<=', $month['previousMonth'])->whereDate('courses_students.created_at', '>', $month['prevPreviousMonth']);
    }

    public function coursesRegister()
    {
        return $this->hasMany('App\Models\CourseStudent', 'student_id', 'id');
    }

    public function coursesOrdered () {
        return $this->hasMany('App\Models\Order', 'user_id')->with('courses');
        // return $this->hasManyThrough('App\Models\OrderItem', 'App\Models\Order', 'user_id', 'order_id', 'id', 'order_id')->with('course');
    }

    public function orderById($orderId)
    {
        return $this->hasOne('App\Models\Order', 'user_id')->where('order_id', $orderId)->first();
    }

    public function myDiscountCodeUsed()
    {
        return $this->hasManyThrough('App\Models\DiscountCode', 'App\Models\UserActivateDiscountCode', 'user_id', 'id', 'id', 'user_id');
    }

    public function transactions () {
        return $this->hasMany('App\Models\Transaction', 'user_id')->with('order');
    }

    public function my_exam()
    {
        return $this->hasMany('App\Models\LearningProcess', 'student_id', 'id')->whereNull('lesson_id');
    }

    public function examPassed()
    {
        return $this->hasMany('App\Models\LearningProcess', 'student_id', 'id')->whereNull('lesson_id')->where('isPassed', 1);
    }

    public function examFail()
    {
        return $this->hasMany('App\Models\LearningProcess', 'student_id', 'id')->whereNull('lesson_id')->where('isPassed', 0)->whereNotNull('started_at')->whereNotNull('completed_at');
    }

    public function examDoingAndPending()
    {
        return $this->hasMany('App\Models\LearningProcess', 'student_id', 'id')->whereNull('lesson_id')->where('isPassed', 0)->whereNull('started_at');
    }

    public function myOverviewCourse()
    {
        return $this->loadCount('courses', 'coursesLearning', 'certifacates as courses_completed_count',
        'coursesLearningNotStart', 'my_exam', 'examPassed', 'examFail', 'examDoingAndPending');
    }

    public function courseWasBought ($course_id) {
        return $this->hasOne('App\Models\CourseStudent', 'student_id', 'id')->where('course_id', $course_id)->first();
    }

    public function courseWasOrder ($course_id) {
        return $this->hasManyThrough('App\Models\OrderItem', 'App\Models\Order', 'user_id', 'order_id', 'id', 'order_id')->where('orders.status', '!=', 'canceled')->where('course_id', $course_id)->first();
    }

    public function messagesSendedIsNotDelete ()
    {
        // return $this->hasMany('App\Models\UserMessageStatus', 'user_id', 'id')->whereNotNull('delete_id_mess')->whereRaw("CAST(lasted_message_seen_id as SIGNED integer) > CAST(delete_id_mess as SIGNED integer)")->where('partner_id', '!=', 1);
        return $this->hasMany('App\Models\UserMessageStatus', 'user_id', 'id')->where('partner_id', '!=', 1)->where(function($q) {
            $q->orWhereRaw('lasted_message_seen_id > delete_id_mess')
            ->orWhereNull('delete_id_mess');
        });
    }

    public function events () {
        return $this->hasManyThrough('App\Models\Event','App\Models\EventStudent', 'user_id', 'id', 'id', 'event_id')->with('course');
    }
}

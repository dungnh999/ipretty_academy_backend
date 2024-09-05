<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventStudent extends Model
{
    use HasFactory;
    public $table = 'event_students';
    
    public $fillable = [
        'user_id',
        'status',
        'event_id'
    ];

    public function course()
    {
        return $this->hasOneThrough('App\Models\Course', 'App\Models\Event', 'id', 'course_id', 'id', 'course_id'); // 3: khoá chính bảng trung gian, 4: khoá chính bảng đích, 5: khoá chính hiện tại, 6: khoá chính bảng đích
    }
}

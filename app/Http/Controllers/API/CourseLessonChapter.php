<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseLessonChapter extends AppBaseController
{
    private $courseLessonChapter;

    public function __construct(CourseLessonChapter $courseLessonChapter)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });

        $this->courseLessonChapter = $courseLessonChapter;
    }
}

<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Jobs\AddMemberIntoEvent;
use App\Jobs\RemoveMemberOutEvent;
use App\Models\Chapter;
use App\Models\ChapterLesson;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLeader;
use App\Models\CourseStudent;
use App\Models\LearningProcess;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Survey;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Users;
use App\Repositories\BaseRepository;
use App\Http\Resources\ChapterResource;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;

/**
 * Class CourseRepository
 * @package App\Repositories
 * @version September 7, 2021, 5:35 pm UTC
 */
class CourseRepository extends BaseRepository
{
  use CommonBusiness;

  /**
   * @var array
   */
  protected $fieldSearchable = ['course_name', 'course_description', 'course_target'];

  /**
   * Return searchable fields
   *
   * @return array
   */
  public function getFieldsSearchable()
  {
    return $this->fieldSearchable;
  }

  protected $relations = ['category'];

  protected $relationSearchable = ['category_name'];

  /**
   * Configure the Model
   **/
  public function model()
  {
    return Course::class;
  }

  public function createCourse($input, $request = null)
  {
    // dd($input['course_target']);
    // dd(json_decode($input['courses_resources']));
    // dd("sss");
    DB::beginTransaction();
    $course_version = 1;
    $input['course_version'] = $course_version;

    $is_published = false;

    if (isset($input['is_published']) && $input['is_published']) {
      $is_published = $input['is_published'];
      $now = date(Carbon::now());

      $input['published_at'] = $now;
    }

    $user = auth()->user();

    // $userRoles = $user->getRoleNames();

    // if (in_array('admin', $userRoles->toArray())) {

    //     $input["is_approved"] = true;
    // }else {

    //     $input["is_approved"] = false;
    // }
    // var_dump($input['course_target']);
    $input['course_target'] = json_encode($input['course_target'], JSON_UNESCAPED_SLASHES);

    $input['course_created_by'] = $user->id;

    $model = $this->model->newInstance($input);

    $model->save();

    //        if (isset($request->student_ids) && $model->course_type == "Group") {
    //
    //            $student_ids = explode(',', $request->student_ids);
    //
    //            foreach ($student_ids as $key => $student_id) {
    //
    //                $courses_students = CourseStudent::create([
    //                    "course_id" => $model->course_id,
    //                    "student_id" => $student_id
    //                ]);
    //            }
    //        }
    //
    //        if (isset($request->leader_ids) && $model->course_type == "Group") {
    //
    //            $leader_ids = explode(',', $request->leader_ids);
    //
    //            foreach ($leader_ids as $key => $leader_id) {
    //
    //                $courses_leaders = CourseLeader::create([
    //                    "course_id" => $model->course_id,
    //                    "leader_id" => $leader_id
    //                ]);
    //
    //                $leader = User::find($leader_id);
    //                if (!$leader->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {
    //                    $leader->givePermissionTo(
    //                        PERMISSION["VIEW_COURSE"],
    //                        PERMISSION["MANAGE_STUDENTS"]
    //                    );
    //                    $leader->save();
    //                }
    //            }
    //        }

    $model->handleMedia($request, MEDIA_COLLECTION['COURSE_FEATURE_IMAGE'], $model);

    $model->handleMedia($request, MEDIA_COLLECTION['CERTIFICATE_IMAGE'], $model);

    $courses_resources = $input['courses_resources'];

    $isInValidField = $this->checkValidCourseResources($courses_resources, $is_published);

    if (
      !$isInValidField['validJson'] ||
      count($isInValidField['isRequiredField']) ||
      count($isInValidField['isNotFoundField']['lessons']) ||
      count($isInValidField['isNotFoundField']['surveys'])
    ) {
      DB::rollBack();
      return $isInValidField;
    } else {
      $this->insertOrUpdateCourseResouces($courses_resources, $model, null, $request);
    }
    DB::commit();
    $isInValidField['model'] = $model;

    return $isInValidField;
  }

  public function updateCourse($input, $id, $request)
  {
    // DB::beginTransaction();

    $is_published = false;
    if (isset($input['is_published']) && $input['is_published']) {
      $is_published = $input['is_published'];
    }

    $query = $this->model->newQuery();

    $model = $query->findOrFail($id);

    $input['course_target'] = json_encode($input['course_target'], JSON_UNESCAPED_UNICODE);

    $input['course_version'] = $model->course_version + 1;

    if (
      empty($input[MEDIA_COLLECTION['COURSE_FEATURE_IMAGE']]) ||
      $input[MEDIA_COLLECTION['COURSE_FEATURE_IMAGE']] == 'null'
    ) {
      $input['course_feature_image'] = $model->course_feature_image;
    }

    if (
      empty($input[MEDIA_COLLECTION['CERTIFICATE_IMAGE']]) ||
      $input[MEDIA_COLLECTION['CERTIFICATE_IMAGE']] == 'null'
    ) {
      $input['certificate_image'] = $model->certificate_image;
    }

    if (
      $model->published_at == null &&
      $model->is_published == 0 &&
      isset($input['is_published']) &&
      $input['is_published']
    ) {
      $now = date(Carbon::now());

      $input['published_at'] = $now;
    }

    $model->fill($input);

    if (
      $input[MEDIA_COLLECTION['COURSE_FEATURE_IMAGE']] != null &&
      $input[MEDIA_COLLECTION['COURSE_FEATURE_IMAGE']] != 'null'
    ) {
      $model->handleMedia($request, MEDIA_COLLECTION['COURSE_FEATURE_IMAGE'], $model);
    }

    if (
      isset($input[MEDIA_COLLECTION['CERTIFICATE_IMAGE']]) &&
      $input['certificate_image'] != null &&
      $input['certificate_image'] != 'null'
    ) {
      $model->handleMedia($request, MEDIA_COLLECTION['CERTIFICATE_IMAGE'], $model);
    }

    $model->save();

    DB::beginTransaction();

    if (isset($request->leader_ids) && $model->course_type == 'Group') {
      // $leader_ids = $request->leader_ids;
      $leader_ids = [];
      $deleteLeaders = [];

      if (isset($request->leader_ids)) {
        $leader_ids = explode(',', $request->leader_ids);
      }

      $currentLeaders = $model->leaders->toArray();

      if (count($leader_ids)) {
        $deleteLeaders = array_filter($currentLeaders, function ($var) use ($leader_ids) {
          return !in_array($var['id'], $leader_ids);
        });
      } else {
        $deleteLeaders = $currentLeaders;
      }
      $events = $model->events;
      if (count($deleteLeaders)) {
        foreach ($deleteLeaders as $key => $leader) {
          CourseLeader::where('student_id', $leader['id'])
            ->where('course_id', $model->course_id)
            ->delete();

          // $deleteLeader = User::find($leader["id"]);
          if (!$leader->hasPermissionTo(PERMISSION['MANAGE_COURSES'])) {
            $leader->permissions()->detach();
            // $leader->revokePermissionTo(PERMISSION["VIEW_COURSE"]);
            // $leader->revokePermissionTo(PERMISSION["MANAGE_STUDENTS"]);
          }
          $job = new RemoveMemberOutEvent($leader['id'], $model);

          dispatch($job);
        }
      }

      if (count($leader_ids)) {
        foreach ($leader_ids as $key => $leader_id) {
          $courses_leaders = CourseLeader::updateOrInsert([
            'course_id' => $model->course_id,
            'leader_id' => $leader_id,
          ]);

          $leader = User::find($leader_id);
          if (!$leader->hasPermissionTo(PERMISSION['MANAGE_COURSES'])) {
            $leader->givePermissionTo(PERMISSION['VIEW_COURSE'], PERMISSION['MANAGE_STUDENTS']);
            $leader->save();
          }

          $job = new AddMemberIntoEvent($leader_id, $events);

          dispatch($job);
        }
      }
    }

    // if (isset($request->student_ids) && $model->course_type == "Group") {
    if ($model->course_type == 'Group') {
      $student_ids = [];
      $student_new = [];
      $deleteStudents = [];
      $events = $model->events;

      if (isset($request->student_ids)) {
        $student_ids = explode(',', $request->student_ids);
      }

      $currentStudents = $model->students->toArray();

      $currentStudentIds = $model->students->pluck('id')->toArray(); // th cu

      if (count($student_ids)) {
        $student_new = array_filter($student_ids, function ($var) use ($currentStudentIds) {
          return !in_array($var, $currentStudentIds);
        });

        $deleteStudents = array_filter($currentStudents, function ($var) use ($student_ids) {
          return !in_array($var['id'], $student_ids);
        });
      } else {
        $deleteStudents = $currentStudents;
      }

      if (count($deleteStudents)) {
        foreach ($deleteStudents as $key => $student) {
          CourseStudent::where('student_id', $student['id'])
            ->where('course_id', $model->course_id)
            ->delete();
          $job = new RemoveMemberOutEvent($student['id'], $model);

          dispatch($job);
        }
      }

      if (count($student_ids)) {
        foreach ($student_ids as $key => $student_id) {
          $courses_students = CourseStudent::updateOrInsert([
            'course_id' => $model->course_id,
            'student_id' => $student_id,
          ]);
          $job = new AddMemberIntoEvent($student_id, $events);

          dispatch($job);
        }
      }
    }

    $courses_resources = $input['courses_resources'];
    $isInValidField = $this->checkValidCourseResources($courses_resources, $is_published);

    if (
      !$isInValidField['validJson'] ||
      count($isInValidField['isRequiredField']) ||
      count($isInValidField['isNotFoundField']['chapters']) ||
      count($isInValidField['isNotFoundField']['lessons']) ||
      count($isInValidField['isNotFoundField']['surveys'])
    ) {
      DB::rollBack();
      return $isInValidField;
    } else {
      $this->insertOrUpdateCourseResouces($courses_resources, $model, $id, $request);
    }

    DB::commit();

    $isInValidField['model'] = $model;
    $isInValidField['student_new'] = isset($student_new) ? $student_new : [];

    return $isInValidField;
  }

  public function checkValidCourseResources($courses_resources, $is_published = false)
  {
    $isInValidField = [
      'validJson' => true,

      'isRequiredField' => [],

      'isNotFoundField' => [
        'chapters' => [],
        'lessons' => [],
        'surveys' => [],
      ],

      'model' => null,
    ];

    if ($courses_resources) {
      if (!$courses_resources['chapters'] && $is_published == true) {
        $isInValidField['validJson'] = false;
        return $isInValidField;
      }

      $chapters = $courses_resources['chapters'];

      foreach ($chapters as $key => $chapter) {
        if (!empty($chapter['chapter_id'])) {
          $valid_chapter = Chapter::find($chapter['chapter_id']);

          if (empty($valid_chapter)) {
            array_push($isInValidField['isNotFoundField']['chapters'], $chapter['chapter_id']);
          }
        }

        if (empty($chapter['chapter_name'])) {
          if (!in_array('chapter_name', $isInValidField['isRequiredField'])) {
            array_push($isInValidField['isRequiredField'], 'chapter_name');
          }
        }

        if (
          isset($chapter['lessons']) &&
          !count($chapter['lessons']) &&
          $chapter['survey'] == null &&
          $is_published == true
        ) {
          array_push($isInValidField['isRequiredField'], 'lesson_or_survey');
        }
        // if ((!empty($chapter->lessons) && !count($chapter->lessons)) || empty($chapter->lessons)) {
        if (isset($chapter['lessons']) && count($chapter['lessons'])) {
          //     if (!in_array('lessons', $isInValidField["isRequiredField"])) {
          //         array_push($isInValidField["isRequiredField"], 'lessons');
          //     }
          // }else {

          $chapters_lessons = $chapter['lessons'];

          foreach ($chapters_lessons as $key => $chapter_lesson) {
            if (empty($chapter_lesson['lesson_id'])) {
              if (!in_array('lesson_id', $isInValidField['isRequiredField'])) {
                array_push($isInValidField['isRequiredField'], 'lesson_id');
              }
            }

            $checkLesson = Lesson::find($chapter_lesson['lesson_id']);

            if (empty($checkLesson)) {
              array_push($isInValidField['isNotFoundField']['lessons'], $chapter_lesson['lesson_id']);
            }
          }
        }

        // if (empty($chapter->survey)) {
        if (isset($chapter['survey']) && $chapter['survey'] != null) {
          //     if (!in_array('surveys', $isInValidField["isRequiredField"])) {
          //         array_push($isInValidField["isRequiredField"], 'surveys');
          //     }
          // }else {
          $chapter_survey = $chapter['survey'];

          // if (empty($chapter_survey)) {
          //     if (!in_array('surveys', $isInValidField["isRequiredField"])) {
          //         array_push($isInValidField["isRequiredField"], 'surveys');
          //     }
          // }else {
          $exist_survey = Survey::find($chapter_survey['survey_id']);

          if (empty($exist_survey)) {
            array_push($isInValidField['isNotFoundField']['surveys'], $chapter_survey['survey_id']);
          }
          // }
        }
      }

      return $isInValidField;
    }
  }

  function insertOrUpdateCourseResouces($courses_resources, $model, $id, $request = null)
  {
    $course_version = 1;

    $currentChapters = $model->chapters->pluck('chapter_id')->toArray();
    $chapters = $courses_resources['chapters'];
    $chapterIds = [];
    foreach ($chapters as $key => $chapter) {
      if (isset($chapter['chapter_id']) && $chapter['chapter_id'] != null) {
        array_push($chapterIds, $chapter['chapter_id']);
      }
    }

    $student_ids = [];

    if (isset($request->student_ids)) {
      $student_ids = explode(',', $request->student_ids);
    }

    // var_dump($student_ids);

    $deleteChapters = [];

    $deleteChapters = array_diff($currentChapters, $chapterIds);

    $deleteLessonProcess = [];

    $deleteSurveyProcess = [];

    if (count($deleteChapters)) {
      foreach ($deleteChapters as $key => $chapter) {
        $lessons = ChapterLesson::where('chapter_id', $chapter)->pluck('lesson_id');

        $deleteLessonProcess = array_merge($deleteLessonProcess, $lessons->toArray());

        $findChapter = Chapter::where('chapter_id', $chapter)->first();
        array_push($deleteSurveyProcess, $findChapter['survey_id']);

        Chapter::find($chapter)->delete();
      }
    }

    if (count($deleteLessonProcess)) {
      LearningProcess::whereIn('lesson_id', $deleteLessonProcess)
        ->where('course_id', $model->course_id)
        ->delete();
    }

    if (count($deleteSurveyProcess)) {
      LearningProcess::whereIn('survey_id', $deleteSurveyProcess)
        ->where('course_id', $model->course_id)
        ->delete();
    }

    $currentChapters = $model->chapters->toArray();
    // var_dump(count($chapters));
    foreach ($chapters as $key => $chapter) {
      if (isset($chapter['chapter_id']) && $chapter['chapter_id'] != null) {
        $newChapter = Chapter::where('chapter_id', $chapter['chapter_id'])->first();

        $newChapter->chapter_name = $chapter['chapter_name'];
        $newChapter->number_order = $chapter['number_order'] ? $chapter['number_order'] : 0;
        $newChapter->course_version = $newChapter->course_version + 1;
        $newChapter->save();
      } else {
        $newChapter = Chapter::create([
          'chapter_name' => $chapter['chapter_name'],
          'course_id' => $model->course_id,
          'course_version' => $course_version,
          'number_order' => $chapter['number_order'] ? $chapter['number_order'] : 0,
        ]);
      }

      $chapters_lessons = $chapter['lessons'];

      ChapterLesson::where('chapter_id', $newChapter->chapter_id)->delete();

      foreach ($chapters_lessons as $key => $lesson) {
        ChapterLesson::create([
          'chapter_id' => $newChapter->chapter_id,
          'lesson_id' => $lesson['lesson_id'],
          'number_order' => $lesson['number_order'] ? $lesson['number_order'] : 0,
        ]);

        if (count($student_ids)) {
          foreach ($student_ids as $key => $student_id) {
            $newLessonProcess = LearningProcess::where('course_id', $model->course_id)
              ->where('lesson_id', $lesson['lesson_id'])
              ->where('student_id', $student_id)
              ->first();

            if (!$newLessonProcess) {
              $newLessonProcess = LearningProcess::create([
                'course_id' => $model->course_id,
                'lesson_id' => $lesson['lesson_id'],
                'student_id' => $student_id,
              ]);
            }
          }
        }
      }

      $chapter_survey = $chapter['survey'];
      // var_dump($chapter_survey["survey_id"]);

      if (isset($chapter_survey['survey_id']) && $chapter_survey['survey_id'] != null) {
        if (
          (isset($newChapter->survey_id) &&
            $newChapter->survey_id != null &&
            $chapter_survey['survey_id'] != $newChapter->survey_id) ||
          !isset($newChapter->survey_id)
        ) {
          $newChapter->survey_id = $chapter_survey['survey_id'];
          $newChapter->save();
          // var_dump($newChapter->survey_id);
        }

        if (count($student_ids)) {
          foreach ($student_ids as $key => $student_id) {
            $process_survey = LearningProcess::where('course_id', $model->course_id)
              ->where('survey_id', $chapter_survey['survey_id'])
              ->where('student_id', $student_id)
              ->first();
            if (!$process_survey) {
              LearningProcess::create([
                'course_id' => $model->course_id,
                'survey_id' => $newChapter->survey_id,
                'student_id' => $student_id,
              ]);
            }
          }
        }
      }

      if ($id) {
        if ($chapter['delete_survey_id'] != null) {
          $delete_survey_id = $chapter['delete_survey_id'];

          if (isset($delete_survey_id) && $delete_survey_id != null) {
            $survey = Survey::where('survey_id', $delete_survey_id)->first();

            // dd($survey);

            if ($survey) {
              $chapter = $survey->chapter;

              // dd($chapter);

              if ($chapter) {
                $chapter->survey_id = null;

                $chapter->save();
              }

              // $survey->delete();
            }
          }
        }
      }
    }
    // dd(2);
  }

  public function allCourses($params = null)
  {
    $query = $this->model->newQuery()
            ->with('category')
            ->with('chapters', function($q) {
              $q->get()->map(function($chapter) {
                $data = collect($chapter)->all();
                $chapterLessons = ChapterLesson::where('chapter_id', $data['chapter_id'])->get();
                $data['lessons'] =  $chapterLessons;
                return $data; 
              });
            });


          

    $user = auth()->user();
    if ($user->hasRole('employee') && $user->hasPermissionTo(PERMISSION['MANAGE_COURSES'])) {
      $query = $query->where(function ($q) use ($user) {
        $q->orwhere('teacher_id', '=', $user->id)
          ->orwhere('course_created_by', '=', $user->id)
          ->orwhereHas('leaders', function ($q) use ($user) {
            $q->where('users.id', '=', $user->id);
          });
      });
    } elseif (
      $user->hasRole('employee') &&
      !$user->hasPermissionTo(PERMISSION['MANAGE_COURSES']) &&
      $user->hasPermissionTo(PERMISSION['VIEW_COURSE'])
    ) {
      $query = $query->whereHas('leaders', function ($q) use ($user) {
        $q->where('users.id', '=', $user->id);
      });
    }

    if (!empty($params['keyword'])) {
      $query = CommonBusiness::searchInCollection(
        $query,
        $this->fieldSearchable,
        $params['keyword'],
        $this->relationSearchable,
        $this->relations
      );
    }



    if (isset($params['course_types']) && $params['course_types'] != null) {
      $course_types = explode(',', $params['course_types']);
      $query = $query->whereIn('course_type', $course_types);
    }

    

    if (isset($params['course_categories']) && $params['course_categories'] != null) {
      $course_categories = explode(',', $params['course_categories']);
      $query = $query->whereIn('category_id', $course_categories);
    }

    if (isset($params['status']) && $params['status'] != null) {
      $status = explode(',', $params['status']);
      $all = [0, 1];
      $excludeStatus = implode(',', array_diff($all, $status));

      if (count($status) == count($all) && array_diff($status, $all) == array_diff($all, $status)) {
        $query = $query;
      } elseif ($excludeStatus == '0') {
        $query = $query->where(function ($q) {
          $q->where('is_published', 1)->where('isDraft', 0);
        });
      } elseif ($excludeStatus == '1') {
        $query = $query->where(function ($q) {
          $q->orWhere(function ($oq) {
            $oq->where('is_published', 1)->where('isDraft', 1);
          })
            ->orWhere(function ($oq) {
              $oq->where('is_published', 0)->where('isDraft', 1);
            })
            ->orWhere(function ($oq) {
              $oq->where('is_published', 0)->where('isDraft', 0);
            });
        });
      }
    }

    if (isset($params['teachers']) && $params['teachers'] != null) {
      $teachers = explode(',', $params['teachers']);
      $query = $query->whereIn('teacher_id', $teachers);
    }

    if (isset($params['created_at']) && $params['created_at'] != null) {
      $created_at = $params['created_at'];
      $query = $query->whereDate('created_at', '>=', $created_at);
    }

    if (
      isset($params['sortFields']) &&
      $params['sortFields'] != null &&
      isset($params['sortValue']) &&
      $params['sortValue'] != null
    ) {
      // dd(2);
      $sortFields = explode(',', $params['sortFields']);
      $sortValue = explode(',', $params['sortValue']);

      for ($i = 0; $i < count($sortFields); $i++) {
        if ($sortFields[$i] == 'teacher') {
          $query = $query->with([
            'teacher' => function ($query) use ($sortValue, $i) {
              $query->orderBy('name', "$sortValue[$i]");
            },
          ]);
        } elseif ($sortFields[$i] == 'category') {
          $query = $query->with([
            'category' => function ($query) use ($sortValue, $i) {
              $query->orderBy('category_name', "$sortValue[$i]");
            },
          ]);
        } elseif ($sortFields[$i] == 'status') {
          $query = $query->orderBy('is_published', "$sortValue[$i]")->orderBy('isDraft', "$sortValue[$i]");
        } elseif ($sortFields[$i] != 'teacher' && $sortFields[$i] != 'category' && $sortFields[$i] != 'status') {
          $query = $query->orderBy("$sortFields[$i]", "$sortValue[$i]");
        }
      }

      if (in_array('teacher', $sortFields) && !in_array('category', $sortFields)) {
        $query = $query->with('category');
      } elseif (!in_array('teacher', $sortFields) && in_array('category', $sortFields)) {
        $query = $query->with('teacher');
      } elseif (!in_array('teacher', $sortFields) && !in_array('category', $sortFields)) {
        $query = $query->with('teacher')->with('category');
      }
    } else {
      $query = $query->with('teacher')->orderBy('created_at', 'desc');
    }

    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {
        $perpage = $params['perpage'];

        $model = $query->paginate($perpage);
      } else {
        $model = $query->paginate(PERPAGE);
      }
    } else {
      $model = $query->get();
    }

    return $model;
  }

  public function getAllCourse($params = null, $category_id = null)
  {
    $course = Course::join('users', 'teacher_id', '=', 'users.id')
      ->select('courses.*', 'users.name as teacher_name')
      ->get();
    //        $query = $this->model->newQuery()->withAvg(['studentResultRatingAvg as student_result_avg_rating'], 'rating');
    //        if ($category_id) {
    //            $model = $query->where('category_id', $category_id);
    //        }
    //        $model = $query->where('course_type', 'Business')
    //        ->where('is_published', 1)
    //        ->where('isDraft', 0);
    // ->orderBy('created_at', 'DESC');

    //        if (isset($params['price']) && $params['price'] != null) {
    //
    //            $price = $params['price'];
    //
    //            if ($price == 'price-1') {
    //                $model = $model->where('course_price', '>=', 0)->where('course_price', '<=', 300000);
    //
    //            }else if ($price == 'price-2') {
    //                $model = $model->where('course_price', '>=', 300000)->where('course_price', '<=', 500000);
    //
    //            }else if ($price == 'price-3') {
    //                $model = $model->where('course_price', '>=', 500000)->where('course_price', '<=', 1000000);
    //
    //            }else if ($price == 'price-4') {
    //                $model = $model->where('course_price', '>=', 1000000);
    //
    //            }
    //        }
    //
    //        if (isset($params['category']) && $params['category'] != null) {
    //
    //            $category = $params['category'];
    //
    //            $query = $query->where('category_id', '=', $category);
    //        }
    //
    //        if (isset($params['rate']) && $params['rate'] != null) {
    //
    //            $rate = $params['rate'];
    //
    //            if ($rate == 'rate-1') {
    //                $query = $query->having('student_result_avg_rating', '=', 5);
    //
    //            } else if ($rate == 'rate-2') {
    //                $query = $query->having('student_result_avg_rating', '>=', 4)->having('student_result_avg_rating', '<=', 5);
    //
    //            } else if ($rate == 'rate-3') {
    //                $query = $query->having('student_result_avg_rating', '>=', 3)->having('student_result_avg_rating', '<=', 4);
    //
    //            } else if ($rate == 'rate-4') {
    //                $query = $query->having('student_result_avg_rating', '>=', 2)->having('student_result_avg_rating', '<=', 3);
    //
    //            } else if ($rate == 'rate-5') {
    //                $query = $query->having('student_result_avg_rating', '>=', 1)->having('student_result_avg_rating', '<=', 2);
    //
    //            }
    //        }
    //
    //        if (isset($params['policy']) && $params['policy'] != null) {
    //
    //            $policy = $params['policy'];
    //
    //            if ($policy == 'policy-1') {
    //                $model = $model->where('course_price', '>', 0)->where('course_type', 'Business');
    //
    //            } else if ($policy == 'policy-2') {
    //                $model = $model->where('course_price', '=', 0)->where('course_type', 'Business');
    //
    //            } else if ($policy == 'policy-3') {
    //                $model = $model->where('course_type', '=', 'Local');
    //
    //            }
    //        }
    //
    //        if (isset($params['sort']) && $params['sort'] != null) {
    //
    //            $sort = $params['sort'];
    //
    //            if ($sort == 'sort1') {
    //                $query = $query->orderBy('published_at', 'desc');
    //
    //            } else if ($sort == 'sort2') {
    //                $query = $query->orderBy('count_viewer', 'desc');
    //
    //            } else if ($sort == 'sort3') {
    //                $query = $query->orderBy('student_result_avg_rating', 'desc');
    //
    //            } else if ($sort == 'sort4') {
    //                $query = $query->orderBy('student_result_avg_rating', 'asc');
    //
    //            }else if ($sort == 'sort5') {
    //                $query = $query->orderBy('course_price', 'desc');
    //
    //            } else if ($sort == 'sort6') {
    //                $query = $query->orderBy('course_price', 'asc');
    //
    //            }
    //        } else {
    //            $query = $query->orderBy("published_at", 'desc');
    //        }
    //
    //
    //        if (!empty($params['keyword'])) {
    //            $model = CommonBusiness::searchInCollection($model, $this->fieldSearchable, $params['keyword']);
    //        }

    //        $model = $course->get();

    return $course;
  }

  public function getCoursesByCondition($params)
  {
    $user = auth()->user();
    $table = $this->model->getTable();

    $mainRole = $this->checkRoleForUser($user);

    if ($mainRole == 'admin') {
      $model = [];
      return $model;
    }

    $query = $this->model
      ->newQuery()
      ->with('studentResults')
      ->with('category', function ($q) {
        $q->select('category_id', 'category_name');
      })
      ->with('teacher', function ($q) {
        $q->select('id', 'name', 'email', 'avatar');
      })
      ->orderBy("$table.created_at", 'desc');

    if (isset($params['course_status']) && $params['course_status'] != null) {
      $course_status = $params['course_status'];
      $now = date(Carbon::now());

      if ($course_status == 'learning') {
        // $query = $query->where(function ($q) use ($now, $user) {
        //     $q->orwhere(function ($w) use ($now) {
        //         $w->whereNotNull('startTime')
        //             ->whereNotNull('endTime')
        //             ->where('startTime', '<', $now)
        //             ->where('endTime', '>', $now);
        //     })
        //         ->orwhere(function ($w) use ($now) {
        //             $w->whereNotNull('deadline')
        //                 ->where('deadline', '>', $now);
        //         });
        // });
        $query = $query->where(function ($where) use ($user) {
          $where->whereHas('studentsLearning', function ($q) use ($user) {
            $q->where('users.id', '=', $user->id);
          });
        });
      } elseif ($course_status = 'finished') {
        $query = $query->where(function ($q) use ($now, $user) {
          // $q->orwhere(function ($w) use ($now) {
          //     $w->whereNotNull('endTime')
          //         ->where('endTime', '<', $now);
          // })
          // ->orwhere(function ($w) use ($now) {
          //     $w->whereNotNull('deadline')
          //         ->where('deadline', '<', $now);
          // })
          $q->orwhere(function ($where) use ($user) {
            $where = $where->whereHas('studentsFinish', function ($q) use ($user) {
              $q->where('users.id', '=', $user->id);
            });
          });
        });
        // $query = $query->where(function ($where) use ($user) {
        //     $where = $where->whereHas('studentsFinish', function ($q) use ($user) {
        //         $q->where('users.id', '=', $user->id);
        //     });
        // });
      }
    } else {
      $query = $query->where(function ($where) use ($user, $mainRole) {
        $where
          ->orwhere(function ($q) use ($user) {
            $q->where('course_type', 'Business')
              ->where('course_price', 0)
              ->where(function ($w) use ($user) {
                $w->orwhere(function ($ow) {
                  $ow->where('is_published', 1)->where('isDraft', 0);
                })->orwhereHas('students', function ($q) use ($user) {
                  $q->where('users.id', '=', $user->id);
                });
              });
          })
          ->orwhere(function ($q) use ($user) {
            $q->where('course_type', 'Group')->where(function ($w) use ($user) {
              $w->orwhere(function ($ow) use ($user) {
                $ow
                  ->where('is_published', 1)
                  ->where('isDraft', 0)
                  ->whereHas('students', function ($q) use ($user) {
                    $q->where('users.id', '=', $user->id);
                  });
              })->orwhere(function ($ow) use ($user) {
                $ow
                  ->where('is_published', 1)
                  ->where('isDraft', 1)
                  ->whereHas('students', function ($q) use ($user) {
                    $q->where('users.id', '=', $user->id);
                  });
              });
            });
          });
        if ($mainRole != 'user') {
          $where = $where->orwhere('course_type', 'Local')->where(function ($w) use ($user) {
            $w->orwhere(function ($ow) use ($user) {
              $ow
                ->where('is_published', 1)
                ->where('isDraft', 0)
                ->where(function ($q) use ($user) {
                  $q->orwhereHas('students', function ($q) use ($user) {
                    $q->where('users.id', '!=', $user->id);
                  })->orWhereDoesntHave('students');
                });
              // ->whereHas('students', function ($q) use ($user) {
              //     $q->where('users.id', '=', $user->id);
              // });
            })->orwhere(function ($ow) use ($user) {
              $ow
                ->where('is_published', 1)
                ->where('isDraft', 1)
                ->whereHas('students', function ($q) use ($user) {
                  $q->where('users.id', '=', $user->id);
                });
            });
          });
        }
      });

      // $query = $query->with(['studentResult' => function ($query) use ($user) {
      //         $query->where(['student_id' => $user->id]);
      //     }]) ;

      // $query = $query->with('avgRating');
    }

    $query = $query->with([
      'studentResult' => function ($query) use ($user) {
        $query->where(['student_id' => $user->id]);
      },
    ]);

    $query = $query->withAvg(['studentResultRatingAvg as student_result_avg_rating'], 'rating');

    if (!empty($params['keyword'])) {
      $query = CommonBusiness::searchInCollection(
        $query,
        $this->fieldSearchable,
        $params['keyword'],
        ['name'],
        ['teacher']
      );
    }

    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {
        $perpage = $params['perpage'];

        $model = $query->paginate($perpage);
      } else {
        $model = $query->paginate(PERPAGE);
      }
    } else {
      $model = $query->get();
    }
    // foreach($model as $course){
    //     $get_list_rating = CourseStudent::where('course_id', $course->course_id)->whereNotNull('rating')->selectRaw('AVG(rating) as rating_course')->first();
    //     $course->avg_rating_course = $get_list_rating->rating_course ? $get_list_rating->rating_course : 0;
    // }

    return $model;
  }

  public function getFreeCourses($params)
  {
    $user = auth()->user();

    $table = $this->model->getTable();

    $mainRole = $this->checkRoleForUser($user);

    if ($mainRole == 'admin') {
      $model = [];
      return $model;
    }

    $query = $this->model
      ->newQuery()
      ->with('category')
      ->with('studentResult')
      ->withAvg(['studentResultRatingAvg as student_result_avg_rating'], 'rating')
      ->with('students')
      ->with('teacher')
      ->orderBy("$table.created_at", 'desc');

    if ($mainRole == 'user') {
      $query = $query->where(function ($where) use ($user) {
        $where->orwhere(function ($q) use ($user) {
          $q->where('course_type', 'Business')
            ->where('course_price', 0)
            ->where(function ($query) use ($user) {
              $query->orwhere(function ($ow) {
                $ow->where('is_published', 1)->where('isDraft', 0);
              });
            })
            ->where(function ($q) use ($user) {
              $q->orWhereDoesntHave('students', function ($q) use ($user) {
                $q->where('users.id', '=', $user->id);
              })->orWhereDoesntHave('students');
            });
        });
        // ->orwhere(function ($q) use ($user) {
        //     $q->where('course_type', 'Group')
        //     ->whereHas('students', function ($q) use ($user) {
        //         $q->where('users.id', '=', $user->id);
        //     });
        // });
      });
      // }else if ($mainRole != "admin") {
    } else {
      $query = $query->where(function ($where) use ($user) {
        $where
          ->orwhere(function ($q) use ($user) {
            $q->where('course_type', 'Business')
              ->where('course_price', 0)
              ->where(function ($query) use ($user) {
                $query->orwhere(function ($ow) {
                  $ow->where('is_published', 1)->where('isDraft', 0);
                });
              })
              ->where(function ($q) use ($user) {
                $q->orWhereDoesntHave('students', function ($q) use ($user) {
                  $q->where('users.id', '=', $user->id);
                })->orWhereDoesntHave('students');
              });
          })
          // ->orwhere(function ($q) use ($user) {
          //     $q->where('course_type', 'Group')
          //         ->whereHas('students', function ($q) use ($user) {
          //             $q->where('users.id', '=', $user->id);
          //         });
          // })
          ->orwhere(function ($q) use ($user) {
            $q->where('course_type', 'Local')
              ->where(function ($query) use ($user) {
                $query->orwhere(function ($ow) {
                  $ow->where('is_published', 1)->where('isDraft', 0);
                });
              })
              ->where(function ($q) use ($user) {
                $q->orWhereDoesntHave('students', function ($q) use ($user) {
                  $q->where('users.id', '=', $user->id);
                })->orWhereDoesntHave('students');
              });
          });
      });
    }

    if (!empty($params['keyword'])) {
      $query = CommonBusiness::searchInCollection(
        $query,
        $this->fieldSearchable,
        $params['keyword'],
        ['name'],
        ['teacher']
      );
    }

    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {
        $perpage = $params['perpage'];

        $model = $query->paginate($perpage);
      } else {
        $model = $query->paginate(PERPAGE);
      }
    } else {
      $model = $query->get();
    }

    // $model = $query->paginate(PERPAGE);

    return $model;
  }

  public function cloneCouse($course_id)
  {
    $user = auth()->user();
    // clone course
    $course_old = Course::where('course_id', $course_id)->first();
    $new_course = new Course();
    $new_course = $course_old->replicate();
    $new_course->course_name = $course_old->course_name . '_copy';
    $new_course->is_published = 0;
    $new_course->isDraft = 1;
    $new_course->course_created_by = $user->id;
    $new_course->course_version = 1;
    $new_course->count_viewer = 0;
    $new_course->save();
    // clone chapter
    $chapters = Chapter::where('course_id', $course_id)->get();
    // dd($chapters);
    if (count($chapters) > 0) {
      foreach ($chapters as $chapter) {
        $chapter_new = new Chapter();
        $chapter_new = $chapter->replicate();
        $chapter_new->course_id = $new_course->course_id;
        // clone and save new survey
        if ($chapter->survey_id != null) {
          $survey_id = $this->cloneSurvey($chapter->survey_id);
          // dd($survey_id);
          $chapter_new->survey_id = $survey_id;
        }
        // dd($chapter_new);
        $chapter_new->save();
        //clone chapter and lessson;
        $chapters_lessons = ChapterLesson::where('chapter_id', $chapter->chapter_id)->get();
        if (count($chapters_lessons) > 0) {
          foreach ($chapters_lessons as $lesson) {
            $chapter_lesson = new ChapterLesson();
            $chapter_lesson = $lesson->replicate();
            $lesson_id = $this->cloneLesson($lesson->lesson_id, $user->id);
            $chapter_lesson->chapter_id = $chapter_new->chapter_id;
            $chapter_lesson->lesson_id = $lesson_id;
            $chapter_lesson->save();
          }
        }
      }
    }
    // dd($new_course);
    return $new_course;
  }

  public function cloneLesson($lesson_id, $user_id)
  {
    $lesson_old = Lesson::where('lesson_id', $lesson_id)->first();
    $new_lesson = new Lesson();
    $new_lesson = $lesson_old->replicate();
    // dd($new_lesson);
    $new_lesson->lesson_author = $user_id;
    $new_lesson->total_views = 0;
    $new_lesson->save();
    // clone media
    $this->cloneMediaLesson($new_lesson->lesson_id, $lesson_id);
    return $new_lesson->lesson_id;
  }

  public function cloneSurvey($survey_id)
  {
    $survey_old = Survey::where('survey_id', $survey_id)->first();
    $new_survey = new Survey();
    $new_survey = $survey_old->replicate();
    $new_survey->save();
    $question_survey = Question::where('survey_id', $survey_id)->get();
    if (count($question_survey) > 0) {
      foreach ($question_survey as $question) {
        $new_question = new Question();
        $new_question = $question->replicate();
        $new_question->survey_id = $new_survey->survey_id;
        $new_question->save();

        $this->cloneMediaQuestion($new_question->question_id, $question->question_id);
        $list_option = QuestionOption::where('question_id', $question->question_id)->get();
        if (count($list_option) > 0) {
          foreach ($list_option as $question_option) {
            $new_quesion_option = new QuestionOption();
            $new_quesion_option = $question_option->replicate();
            $new_quesion_option->question_id = $new_question->question_id;
            $new_quesion_option->save();
            $this->cloneMediaQuestionOptions($new_quesion_option->option_id, $question_option->option_id);
          }
        }
      }
    }
    $new_survey->save();
    return $new_survey->survey_id;
  }

  public function commentAndRatingCourse($course_id, $user_id, $data)
  {
    $course_student = CourseStudent::where('course_id', $course_id)
      ->where('student_id', $user_id)
      ->first();
    $course_student->rating = $data['rating'];
    $course_student->comment = $data['comment'];
    $course_student->save();
    return $course_student;
  }

  public function getFeaturedCategory()
  {
    $course_category = CourseCategory::join('courses', 'courses.category_id', 'course_categories.category_id')
      ->groupBy('courses.category_id')
      ->selectRaw(
        'course_categories.category_id, course_categories.category_name,course_categories.course_category_attachment, count(courses.category_id) as count_course'
      )
      ->orderBy('count_course', 'DESC')
      ->limit(16)
      ->get();
    return $course_category;
  }

  public function getListRankCourse()
  {
    $user = auth()->user();
    $course_category = CourseStudent::join('courses', 'courses.course_id', 'courses_students.course_id')
      ->with('courseName.teacherName')
      ->where('rating', '>=', 3)
      ->where('is_published', '=', 1)
      ->where('isDraft', '=', 0)
      ->groupBy('courses_students.course_id')
      ->selectRaw(
        "courses.course_sale_price,
                                            courses.course_id,
                                            courses.is_published,
                                            courses.isDraft,
                                            courses_students.course_id,
                                            courses_students.student_id,
                                            courses.course_price,
                                            courses.course_name,
                                            courses.course_feature_image,
                                            courses.course_type,
                                            count(case when rating > 0 then 1 else null end) as count_rater,
                                            sum(courses_students.rating) as count_rating"
      )
      ->orderBy('count_rating', 'DESC')
      ->limit(16)
      ->get();
    foreach ($course_category as $key => $course) {
      # code...
      $course_student = CourseStudent::where('course_id', $course->course_id)
        ->where('student_id', $user->id)
        ->first();
      if ($course_student) {
        $course->isPassedForCurUser = $course_student->isPassed;
      } else {
        $course->isPassedForCurUser = false;
      }
    }
    return $course_category;
  }

  public function changePublishCourse($course_id, $isDraft)
  {
    $query = $this->model->newQuery();

    $model = $query->findOrFail($course_id);

    $model->isDraft = $isDraft;

    $model->save();

    return $model;
  }

  public function checkCoursesEnded()
  {
    $now = date(Carbon::now()->toDateTimeString());
    // dd($now);
    // if ($now < '2021-11-19 20:29') {
    //     dd('1111111');
    // } else {
    //     dd('2222222');
    // }

    $query = $this->model->newQuery();

    $query = $query
      ->whereNotNull('endTime')
      ->where('endTime', '<', $now)
      // ->whereDate('endTime', '<', $now)
      ->where('isDraft', 0)
      ->where('is_published', 1)
      ->update(['isDraft' => 1]);

    return $query;
  }

  public function SearchallCourses($params = null)
  {
    $query = $this->model->newQuery();

    $user = auth()->user();
    if ($user->hasRole('employee') && $user->hasPermissionTo(PERMISSION['MANAGE_COURSES'])) {
      $query = $query->where(function ($q) use ($user) {
        $q->orwhere('teacher_id', '=', $user->id)->orwhere('course_created_by', '=', $user->id);
      });
    } elseif (
      $user->hasRole('employee') &&
      !$user->hasPermissionTo(PERMISSION['MANAGE_COURSES']) &&
      $user->hasPermissionTo(PERMISSION['VIEW_COURSE'])
    ) {
      $query = $query->whereHas('leaders', function ($q) use ($user) {
        $q->where('users.id', '=', $user->id);
      });
    }

    if (!empty($params['keyword'])) {
      $query = CommonBusiness::searchInCollection(
        $query,
        $this->fieldSearchable,
        $params['keyword'],
        $this->relationSearchable,
        $this->relations
      );
    }

    $model = $query
      ->select('course_name', 'course_id', 'course_feature_image')
      ->orderBy('created_at', 'desc')
      ->get();

    return $model;
  }

  public function cloneMediaLesson($lesson_new, $lesson_old)
  {
    $get_list_media = Media::where('model_id', $lesson_old)
      ->where('collection_name', MEDIA_COLLECTION['LESSON_ATTACHMENT'])
      ->get();
    if (count($get_list_media) > 0) {
      $max_order_column = 1;
      $max_order_number = Media::select('order_column')
        ->orderBy('order_column', 'desc')
        ->first();
      if ($max_order_number) {
        $max_order_column = $max_order_number->order_column + 1;
      }
      foreach ($get_list_media as $media) {
        $new_media = new Media();
        $uuid = Str::uuid()->toString();
        $new_media = $media->replicate();
        $new_media->model_id = $lesson_new;
        $new_media->uuid = $uuid;
        $new_media->order_column = $max_order_column;
        $new_media->save();
        $max_order_column += 1;
      }
    }
  }

  public function cloneMediaQuestion($question_id_new, $question_id_old)
  {
    $get_list_media = Media::where('model_id', $question_id_old)
      ->where('collection_name', MEDIA_COLLECTION['QUESTION_ATTACHMENTS'])
      ->get();
    if (count($get_list_media) > 0) {
      $max_order_column = 1;
      $max_order_number = Media::select('order_column')
        ->orderBy('order_column', 'desc')
        ->first();
      if ($max_order_number) {
        $max_order_column = $max_order_number->order_column + 1;
      }
      foreach ($get_list_media as $media) {
        $new_media = new Media();
        $uuid = Str::uuid()->toString();
        $new_media = $media->replicate();
        $new_media->model_id = $question_id_new;
        $new_media->uuid = $uuid;
        $new_media->order_column = $max_order_column;
        $new_media->save();
        $max_order_column += 1;
      }
    }
  }

  public function cloneMediaQuestionOptions($option_id_new, $option_id_old)
  {
    $get_list_media = Media::where('model_id', $option_id_old)
      ->where('collection_name', MEDIA_COLLECTION['OPTION_ATTACHMENTS'])
      ->get();
    if (count($get_list_media) > 0) {
      $max_order_column = 1;
      $max_order_number = Media::select('order_column')
        ->orderBy('order_column', 'desc')
        ->first();
      if ($max_order_number) {
        $max_order_column = $max_order_number->order_column + 1;
      }
      foreach ($get_list_media as $media) {
        $new_media = new Media();
        $uuid = Str::uuid()->toString();
        $new_media = $media->replicate();
        $new_media->model_id = $option_id_new;
        $new_media->uuid = $uuid;
        $new_media->order_column = $max_order_column;
        $new_media->save();
        $max_order_column += 1;
      }
    }
  }

  public function getDetail($course_id)
  {
    $course = $this->model
      ->newQuery()
      ->with('teacher')
      ->with('category')
      ->with('chapters')
      ->with('chapters.lessons')
      ->withAvg(['studentResultRatingAvg as student_result_avg_rating'], 'rating')
      ->where('course_id', $course_id)
      ->first();

      if ($course) {
      if ($course->course_target != null) {
        $course_target = json_decode($course->course_target);
        if (count($course_target->course_target) > 0) {
          $course_target_new = [];
          foreach ($course_target->course_target as $line) {
            $course_target_new[] = $line->value;
          }
          $course->course_target_new = $course_target_new;
        }
      }
    }
    // dd($course);
    return $course;
  }

  public function checkToPublished($course)
  {
    $chapters = $course->chapters;
    if (count($chapters)) {
      foreach ($chapters as $key => $chapter) {
        $lessons = $chapter->lessons;
        $survey = $chapter->survey;
        if (!count($lessons) && !$survey) {
          return false;
        }
      }

      return true;
    } else {
      return false;
    }
  }

  public function getRelatedCourses($course)
  {
    $user = auth()->user();

    $course_related = $this->model
      ->newQuery()
      ->where('course_id', '!=', $course->course_id)
      ->with('teacher:id,name,email,avatar')
      ->withAvg(['studentResultRatingAvg as student_result_avg_rating'], 'rating')
      ->where('is_published', 1)
      ->where('isDraft', 0);

    if ($user) {
      $role = $this->checkRoleForUser($user);

      if ($role == 'user') {
        $course_related->where(function ($q) use ($course, $user) {
          $q->where('course_type', '=', $course->course_type)
            ->where('category_id', '=', $course->category_id)
            ->where('course_type', '!=', 'Local');
          if ($course->course_type == 'Group') {
            $q->whereHas('students', function ($q) use ($user) {
              $q->where('users.id', '=', $user->id);
            });
          }
        });
      } else {
        $course_related->where(function ($q) use ($course) {
          $q->where('course_type', '=', $course->course_type)->where('category_id', '=', $course->category_id);
        });
      }
    } else {
      $course_related->where(function ($q) use ($course) {
        $q->where('course_type', '=', $course->course_type)
          ->where('course_type', '!=', 'Group')
          ->where('category_id', '=', $course->category_id);
      });
    }

    $course_related = $course_related
      ->limit(4)
      ->orderBy('created_at', 'DESC')
      ->get();

    return $course_related;
  }

  public function updateViewCount($course)
  {
    $course->count_viewer = $course->count_viewer + 1;
    $course->save();
    return $course;
  }

  public function featureCourses($params = null)
  {
    $month = $this->getCurPrevMonth();
    $query = $this->model->newQuery();

    // $model = $query->with('getTotalViewer')->get();
    // dd($model);
    $model = $query
      ->select('course_id', 'course_feature_image', 'course_name')
      ->totalViewer()
      ->totalViewerMonth($month['currentMonth'], $month['previousMonth'], 'count_viewer_month')
      ->totalViewerMonth($month['previousMonth'], $month['prevPreviousMonth'], 'count_viewer_preMonth')
      ->withCount('students')
      ->withCount('studentsOfMonth')
      ->withCount('studentsOfPrevMonth')
      ->orderBy('count_viewer', 'desc')
      ->orderBy('students_count', 'desc')
      ->groupBy('course_id')
      ->limit(10)
      ->get();

    foreach ($model as $key => $course) {
      $current_quantity = $course->count_viewer_month + $course->students_of_month_count;
      $prev_quantity = $course->count_viewer_preMonth + $course->students_of_prev_month_count;
      $course->rate = $prev_quantity ? round((($current_quantity - $prev_quantity) * 100) / $prev_quantity, 2) : 100;
    }

    return $model;
  }

  public function reportBusinessCourses($params)
  {
    $query = $this->model
      ->newQuery()
      // ->select('course_id', 'course_name', 'course_price', 'teacher_id', 'category_id')
      ->selectRaw(
        'courses.course_id, course_name, courses.course_price, teacher_id, category_id,
        round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) as grandTotalBFee, orders.status,
        sum(order_items.course_price) as grandTotalAFee'
      )
      ->where('course_type', 'Business')
      ->where('courses.course_price', '>', 0)
      ->orderBy('courses.created_at', 'desc')
      ->orderBy('courses.updated_at', 'desc')
      ->orderBy('transactions_count', 'desc')
      ->groupBy('order_items.course_id');
    $query = $query
      ->withCount([
        'transactions as transactions_count' => function ($q) {
          $q->where('transactions.status', 'approved');
        },
      ])
      ->with('category', function ($q) {
        $q->select('category_id', 'category_name');
      })
      ->with('teacher', function ($q) {
        $q->select('id', 'name', 'email');
      })
      ->join('order_items', 'order_items.course_id', 'courses.course_id')
      ->join('orders', 'orders.order_id', 'order_items.order_id')
      ->whereHas('transactions', function ($q) {
        $q->where('transactions.status', 'approved');
      })
      ->where('orders.status', 'paid');

    if (!empty($params['keyword'])) {
      $query = CommonBusiness::searchInCollection(
        $query,
        $this->fieldSearchable,
        $params['keyword'],
        $this->relationSearchable,
        $this->relations
      );
    }

    if (isset($params['teachers']) && $params['teachers'] != null) {
      $teachers = explode(',', $params['teachers']);
      $query = $query->whereIn('teacher_id', $teachers);
    }

    if (isset($params['categories']) && $params['categories'] != null) {
      $categories = explode(',', $params['categories']);
      $query = $query->whereIn('category_id', $categories);
    }

    if (isset($params['rangeTotalAFee']) && $params['rangeTotalAFee'] != null) {
      $rangeTotalAFee = explode(',', $params['rangeTotalAFee']);
      $all = [1, 2, 3, 4];
      $excludeRange = implode(',', array_diff($all, $rangeTotalAFee));

      if (
        count($rangeTotalAFee) == count($all) &&
        array_diff($rangeTotalAFee, $all) == array_diff($all, $rangeTotalAFee)
      ) {
        $query = $query->havingRaw('sum(order_items.course_price) >= 0');
      } elseif ($excludeRange == '1') {
        $query = $query->havingRaw('sum(order_items.course_price) >= 1000000');
      } elseif ($excludeRange == '2') {
        $query = $query->havingRaw('(sum(order_items.course_price) <= 1000000) OR
                (sum(order_items.course_price) >= 3000000)');
      } elseif ($excludeRange == '3') {
        $query = $query->havingRaw('(sum(order_items.course_price) <= 3000000) OR
                (sum(order_items.course_price) >= 5000000)');
      } elseif ($excludeRange == '4') {
        $query = $query->havingRaw('sum(order_items.course_price) <= 5000000');
      } elseif ($excludeRange == '1,2') {
        $query = $query->havingRaw('sum(order_items.course_price) >= 3000000');
      } elseif ($excludeRange == '2,3') {
        $query = $query->havingRaw(
          'sum(order_items.course_price) <= 1000000 AND sum(order_items.course_price) >= 5000000'
        );
      } elseif ($excludeRange == '3,4') {
        $query = $query->havingRaw('sum(order_items.course_price) <= 3000000');
      } elseif ($excludeRange == '2,4') {
        $query = $query->havingRaw(
          '(sum(order_items.course_price) <= 1000000) OR (sum(order_items.course_price) >= 3000000 AND sum(order_items.course_price) <= 5000000)'
        );
      } elseif ($excludeRange == '1,4') {
        $query = $query->havingRaw(
          'sum(order_items.course_price) >= 1000000 AND sum(order_items.course_price) <= 5000000'
        );
      } elseif ($excludeRange == '1,3') {
        $query = $query->havingRaw(
          '(sum(order_items.course_price) >= 1000000) OR (sum(order_items.course_price) >= 1000000 AND sum(order_items.course_price) <= 3000000)'
        );
      } elseif ($excludeRange == '1,2,4') {
        $query = $query->havingRaw(
          'sum(order_items.course_price) >= 3000000 AND sum(order_items.course_price) <= 5000000'
        );
      } elseif ($excludeRange == '1,2,3') {
        $query = $query->havingRaw('sum(order_items.course_price) >= 5000000');
      } elseif ($excludeRange == '2,3,4') {
        $query = $query->havingRaw('sum(order_items.course_price) <= 1000000');
      } elseif ($excludeRange == '1,3,4') {
        $query = $query->havingRaw(
          'sum(order_items.course_price) >= 1000000 AND sum(order_items.course_price) <= 3000000'
        );
      }
    }

    if (isset($params['rangeTotalBFee']) && $params['rangeTotalBFee'] != null) {
      $rangeTotalBFee = explode(',', $params['rangeTotalBFee']);
      $all = [1, 2, 3, 4];
      $excludeRange = implode(',', array_diff($all, $rangeTotalBFee));

      if (
        count($rangeTotalBFee) == count($all) &&
        array_diff($rangeTotalBFee, $all) == array_diff($all, $rangeTotalBFee)
      ) {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 0'
        );
      } elseif ($excludeRange == '1') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 1000000'
        );
      } elseif ($excludeRange == '2') {
        $query = $query->havingRaw('(round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 1000000) OR
                (round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 3000000)');
      } elseif ($excludeRange == '3') {
        $query = $query->havingRaw('(round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0)) <= 3000000) OR
                (round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 5000000)');
      } elseif ($excludeRange == '4') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 5000000'
        );
      } elseif ($excludeRange == '1,2') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 3000000'
        );
      } elseif ($excludeRange == '2,3') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 1000000 AND round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 5000000'
        );
      } elseif ($excludeRange == '3,4') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 3000000'
        );
      } elseif ($excludeRange == '2,4') {
        $query = $query->havingRaw('(round((sum(order_items.course_price) + (round((sum(order_items.course_price) - (sum(order_items.course_price) * 10 /100)),0) * 10 /100)),0) <= 1000000) OR
                 (round((sum(order_items.course_price) - (sum(order_items.course_price) * 10 /100)),0) >= 3000000 AND round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 5000000)');
      } elseif ($excludeRange == '1,4') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 1000000 AND round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 5000000'
        );
      } elseif ($excludeRange == '1,3') {
        $query = $query->havingRaw('(round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0 >= 1000000)
                 OR (round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0 >= 1000000 AND round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0 <= 3000000)');
      } elseif ($excludeRange == '1,2,4') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 3000000 AND round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 5000000'
        );
      } elseif ($excludeRange == '1,2,3') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 5000000'
        );
      } elseif ($excludeRange == '2,3,4') {
        $query = $query->havingRaw(
          'round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 1000000'
        );
      } elseif ($excludeRange == '1,3,4') {
        $query = $query->havingRaw('round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) >= 1000000
                AND round((sum(order_items.course_price) + (sum(order_items.course_price) * 10 /100)),0) <= 3000000');
      }
    }

    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {
        $perpage = $params['perpage'];

        $model = $query->paginate($perpage);
      } else {
        $model = $query->paginate(PERPAGE);
      }
    } else {
      $model = $query->get();
    }

    return $model;
  }

  public function getDataTeacher($params)
  {
    $dataTeacher = Users::where('menuroles', ENUM_POSITION_TEACHER)->get();
    return $dataTeacher;
  }

  public function reportDetailCourses($params)
  {
    $query = $this->model
      ->newQuery()
      ->select('course_id', 'course_name', 'course_type', 'count_viewer', 'teacher_id', 'category_id')
      ->orderBy('student_results_avg_rating', 'desc')
      ->orderBy('student_results_count', 'desc')
      ->orderBy('students_finish_count', 'desc')
      ->orderBy('count_viewer', 'desc')
      ->orderBy('courses.created_at', 'desc')
      ->orderBy('courses.updated_at', 'desc')
      ->groupBy('courses.course_id');
    $query = $query
      ->withCount(['studentsLearning', 'studentsFinish', 'studentResults'])
      ->withAvg(['studentResultRatingAvg as student_results_avg_rating'], 'rating')
      ->with('category', function ($q) {
        $q->select('category_id', 'category_name');
      })
      ->with('teacher', function ($q) {
        $q->select('id', 'name', 'email');
      })
      ->where('courses.is_published', 1);

    if (!empty($params['keyword'])) {
      $query = CommonBusiness::searchInCollection(
        $query,
        $this->fieldSearchable,
        $params['keyword'],
        $this->relationSearchable,
        $this->relations
      );
    }

    if (isset($params['teachers']) && $params['teachers'] != null) {
      $teachers = explode(',', $params['teachers']);
      $query = $query->whereIn('teacher_id', $teachers);
    }

    if (isset($params['categories']) && $params['categories'] != null) {
      $categories = explode(',', $params['categories']);
      $query = $query->whereIn('category_id', $categories);
    }

    if (isset($params['course_types']) && $params['course_types'] != null) {
      $course_types = explode(',', $params['course_types']);
      $query = $query->whereIn('course_type', $course_types);
    }

    if (isset($params['results']) && $params['results'] != null) {
      $results = explode(',', $params['results']);
      $all = [1, 2, 3, 4];
      $excludeRange = implode(',', array_diff($all, $results));
      // dd($excludeRange);
      // < 50 , 50 - 100 , 100 - 300 ,>= 300
      if (count($results) == count($all) && array_diff($results, $all) == array_diff($all, $results)) {
        $query = $query->having('students_finish_count', '>=', 0);
      } elseif ($excludeRange == '1') {
        $query = $query->having('students_finish_count', '>=', 50);
      } elseif ($excludeRange == '2') {
        $query = $query->havingRaw('(students_finish_count <= 50) OR (students_finish_count >= 100)');
      } elseif ($excludeRange == '3') {
        $query = $query->havingRaw('(students_finish_count <= 100) OR (students_finish_count >= 300)');
      } elseif ($excludeRange == '4') {
        $query = $query->having('students_finish_count', '<=', 300);
      } elseif ($excludeRange == '1,2') {
        $query = $query->having('students_finish_count', '>=', 100);
      } elseif ($excludeRange == '2,3') {
        $query = $query->havingRaw('(students_finish_count <= 50) OR (students_finish_count >= 300)');
      } elseif ($excludeRange == '3,4') {
        $query = $query->having('students_finish_count', '<=', 100);
      } elseif ($excludeRange == '2,4') {
        $query = $query->havingRaw(
          '(students_finish_count <= 50) OR (students_finish_count >= 100 AND students_finish_count <= 300)'
        );
      } elseif ($excludeRange == '1,4') {
        $query = $query->havingRaw('students_finish_count >= 50 AND students_finish_count <= 300');
      } elseif ($excludeRange == '1,3') {
        $query = $query->havingRaw(
          '(students_finish_count >= 50 AND students_finish_count <= 100) OR (students_finish_count >= 300)'
        );
      } elseif ($excludeRange == '1,2,3') {
        $query = $query->having('students_finish_count', '>=', 300);
      } elseif ($excludeRange == '1,2,4') {
        $query = $query->havingRaw('(students_finish_count >= 100) AND (students_finish_count <= 300)');
      } elseif ($excludeRange == '2,3,4') {
        $query = $query->having('students_finish_count', '<=', 50);
      } elseif ($excludeRange == '1,3,4') {
        $query = $query->having('students_finish_count', '>=', 50)->having('students_finish_count', '<=', 100);
      }
    }

    if (isset($params['ratings']) && $params['ratings'] != null) {
      $ratings = explode(',', $params['ratings']);
      $all = [1, 2, 3];
      $excludeRange = implode(',', array_diff($all, $ratings));
      // dd($excludeRange);
      // < 1, 1 - 3 , 3 - 5
      if (count($ratings) == count($all) && array_diff($ratings, $all) == array_diff($all, $ratings)) {
        $query = $query->havingRaw('student_results_avg_rating > 0 OR student_results_avg_rating is null');
      } elseif ($excludeRange == '1') {
        $query = $query->havingRaw('student_results_avg_rating >= 1 AND student_results_avg_rating <= 5');
      } elseif ($excludeRange == '2') {
        $query = $query->havingRaw(
          '(student_results_avg_rating <= 1) OR student_results_avg_rating is null OR (student_results_avg_rating >= 3 AND student_results_avg_rating <= 5)'
        );
      } elseif ($excludeRange == '3') {
        $query = $query->havingRaw('student_results_avg_rating <= 3 OR student_results_avg_rating is null');
      } elseif ($excludeRange == '1,2') {
        $query = $query->having('student_results_avg_rating', '>=', 3);
      } elseif ($excludeRange == '2,3') {
        $query = $query->havingRaw('(student_results_avg_rating <= 1) OR student_results_avg_rating is null');
        // var_dump($query->toSql());
      } elseif ($excludeRange == '1,3') {
        $query = $query->havingRaw('student_results_avg_rating >= 1 AND student_results_avg_rating <= 3');
      }
    }

    if (isset($params['paging']) && $params['paging'] == true) {
      if (isset($params['perpage']) && $params['perpage'] != null) {
        $perpage = $params['perpage'];

        $model = $query->paginate($perpage);
      } else {
        $model = $query->paginate(PERPAGE);
      }
    } else {
      $model = $query->get();
    }

    return $model;
  }

  public function getBussinessCourses()
  {
    $model = $this->model->newQuery();

    $model = $model
      ->where('course_type', 'business')
      ->where('course_price', '>', 0)
      ->where('is_published', 1)
      ->where('isDraft', 0)
      ->get()
      ->pluck('course_id');

    return $model;
  }

  public function statisticalCourses($params = null)
  {
    $query = $this->model->newQuery();
    $now = Carbon::now();
    $month = 0;
    $year = Carbon::now()->year;

    if (isset($params['year']) && $params['year'] != null && isset($params['month']) && $params['month'] != null) {
      $month = $params['month'];
      $year = $params['year'];
      $subQuery = $query
        ->selectRaw('DATE_FORMAT(published_at, "%d") AS date, COUNT(course_id) as total')
        ->whereRaw('MONTH(published_at) = ' . $month . ' AND YEAR(published_at) = ' . $year)
        ->groupByRaw('DATE_FORMAT(published_at, "%d-%m-%Y")');

      // dd($subQuery->toSql());
    } elseif (isset($params['year']) && $params['year'] != null) {
      $year = $params['year'];
      $subQuery = $query
        ->selectRaw('DATE_FORMAT(published_at, "%c") AS date, COUNT(course_id) as total')
        ->whereRaw('YEAR(published_at) = ' . $year)
        ->groupByRaw('DATE_FORMAT(published_at, "%m-%Y")');
    } elseif (isset($params['month']) && $params['month'] != null) {
      $month = $params['month'];
      $subQuery = $query
        ->selectRaw('DATE_FORMAT(published_at, "%d") AS date, COUNT(course_id) as total')
        ->whereRaw('MONTH(published_at) = ' . $month)
        ->groupByRaw('DATE_FORMAT(published_at, "%d-%m-%Y")');
    } else {
      $subQuery = $query
        ->selectRaw('DATE_FORMAT(published_at, "%c") AS date, COUNT(course_id) as total')
        ->whereRaw('published_at < "' . $now . '" and published_at >= Date_add("' . $now . '",interval - 12 month)')
        ->groupByRaw('DATE_FORMAT(published_at, "%m-%Y")');
    }

    $str = '';
    $rangeDayInMonth = range(1, 12);

    if ($month) {
      $countDayInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
      $rangeDayInMonth = range(1, $countDayInMonth);
    }

    // dd($rangeDayInMonth);

    foreach ($rangeDayInMonth as $key => $day) {
      $str = $str . 'SUM(IF(date = ' . $day . ", total, 0)) AS '" . $day . "'";
      if ($key < count($rangeDayInMonth) - 1) {
        $str = $str . ',';
      }
    }

    $model = DB::query()
      ->from($subQuery, 'sub')
      ->selectRaw($str);

    $model = $model->first();

    $businessModel = $this->statisticalBusiness($params);

    $response = [];
    // dd($businessModel);
    foreach ($model as $key => $item) {
      $month = new \stdClass();
      $month->month_or_day = $key;
      $month->published_courses = $item;
      $month->business_courses = $businessModel->$key;
      array_push($response, $month);
    }
    // dd((array)$model);

    return $response;
  }

  public function statisticalBusiness($params = null)
  {
    $month = 0;
    $now = Carbon::now();
    $year = Carbon::now()->year;

    $subQuery = Transaction::where('status', 'approved')->join(
      'order_items',
      'order_items.order_id',
      '=',
      'transactions.order_id'
    );

    if (isset($params['year']) && $params['year'] != null && isset($params['month']) && $params['month'] != null) {
      $month = $params['month'];
      $year = $params['year'];
      $subQuery = $subQuery
        ->selectRaw('DATE_FORMAT(transactions.confirmed_at, "%d") AS date, COUNT(order_items.order_item_id) as total')
        ->whereRaw('MONTH(transactions.confirmed_at) = ' . $month . ' AND YEAR(transactions.confirmed_at) = ' . $year)
        ->groupByRaw('DATE_FORMAT(transactions.confirmed_at, "%d-%m-%Y")');

      // dd($subQuery->toSql());
    } elseif (isset($params['year']) && $params['year'] != null) {
      $year = $params['year'];

      $subQuery = $subQuery
        ->selectRaw('DATE_FORMAT(transactions.confirmed_at, "%c") AS date, COUNT(order_items.order_item_id) as total')
        ->whereRaw('YEAR(transactions.confirmed_at) = ' . $year)
        ->groupByRaw('DATE_FORMAT(transactions.confirmed_at, "%m-%Y")');
    } elseif (isset($params['month']) && $params['month'] != null) {
      $month = $params['month'];

      $subQuery = $subQuery
        ->selectRaw('DATE_FORMAT(transactions.confirmed_at, "%d") AS date, COUNT(order_items.order_item_id) as total')
        ->whereRaw('MONTH(transactions.confirmed_at) = ' . $month)
        ->groupByRaw('DATE_FORMAT(transactions.confirmed_at, "%d-%m-%Y")');
    } else {
      $subQuery = $subQuery
        ->selectRaw('DATE_FORMAT(transactions.confirmed_at, "%c") AS date, COUNT(order_items.order_item_id) as total')
        ->whereRaw(
          'transactions.confirmed_at < "' .
            $now .
            '" and transactions.confirmed_at >= Date_add("' .
            $now .
            '",interval - 12 month)'
        )
        ->groupByRaw('DATE_FORMAT(transactions.confirmed_at, "%m-%Y")');
    }

    // $subQuery = Transaction::where('status', 'approved')
    // ->join('order_items', 'order_items.order_id', '=', 'transactions.order_id')
    // ->selectRaw('DATE_FORMAT(transactions.updated_at, "%d") AS date, COUNT(order_items.order_item_id) as total')
    // ->whereRaw('transactions.updated_at < "'.$now.'" and transactions.updated_at >= Date_add("'.$now.'",interval - 12 month)')
    // ->groupByRaw('DATE_FORMAT(transactions.updated_at, "%m-%Y")');
    // dd($subQuery->get());
    $str = '';
    $rangeDayInMonth = range(1, 12);
    if ($month) {
      $countDayInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
      $rangeDayInMonth = range(1, $countDayInMonth);
    }

    foreach ($rangeDayInMonth as $key => $day) {
      $str = $str . 'SUM(IF(date = ' . $day . ", total, 0)) AS '" . $day . "'";
      if ($key < count($rangeDayInMonth) - 1) {
        $str = $str . ',';
      }
    }
    $model = DB::query()
      ->from($subQuery, 'sub')
      ->selectRaw($str);
    // $model = DB::query()->from($subQuery, 'sub')->selectRaw("
    //     SUM(IF(month = 'Jan', total, 0)) AS '1',
    //     SUM(IF(month = 'Feb', total, 0)) AS '2',
    //     SUM(IF(month = 'Mar', total, 0)) AS '3',
    //     SUM(IF(month = 'Apr', total, 0)) AS '4',
    //     SUM(IF(month = 'May', total, 0)) AS '5',
    //     SUM(IF(month = 'Jun', total, 0)) AS '6',
    //     SUM(IF(month = 'Jul', total, 0)) AS '7',
    //     SUM(IF(month = 'Aug', total, 0)) AS '8',
    //     SUM(IF(month = 'Sep', total, 0)) AS '9',
    //     SUM(IF(month = 'Oct', total, 0)) AS '10',
    //     SUM(IF(month = 'Nov', total, 0)) AS '11',
    //     SUM(IF(month = 'Dec', total, 0)) AS '12'
    //     ");

    $model = $model->first();

    return $model;
  }

  public function getAllCourseForLogged($user, $params = null, $category_id = null)
  {
    $table = $this->model->getTable();

    $mainRole = $this->checkRole($user);

    $query = $this->model->newQuery()->with('teacher', function ($q) {
      $q->select('id', 'name', 'email', 'avatar');
    });
    // ->orderBy("$table.published_at", 'desc');

    $query = $query->withAvg(['studentResultRatingAvg as student_result_avg_rating'], 'rating');

    if ($category_id) {
      $query = $query->where('category_id', $category_id);
    }

    $query = $query->where(function ($where) use ($user, $mainRole) {
      $where->orwhere(function ($q) use ($user) {
        $q->where('course_type', 'Business')->where(function ($w) use ($user) {
          $w->orwhere(function ($ow) {
            $ow->where('is_published', 1)->where('isDraft', 0);
          });
        });
      });
      if ($mainRole == 'localStudent') {
        $where = $where->orwhere('course_type', 'Local')->where(function ($w) use ($user) {
          $w->orwhere(function ($ow) use ($user) {
            $ow
              ->where('is_published', 1)
              ->where('isDraft', 0)
              ->where(function ($q) use ($user) {
                $q->orwhereHas('students', function ($q) use ($user) {
                  $q->where('users.id', '!=', $user->id);
                })->orWhereDoesntHave('students');
              });
          });
        });
      }
    });

    if (isset($params['price']) && $params['price'] != null) {
      $price = $params['price'];

      if ($price == 'price-1') {
        $query = $query->where('course_price', '>=', 0)->where('course_price', '<=', 300000);
      } elseif ($price == 'price-2') {
        $query = $query->where('course_price', '>=', 300000)->where('course_price', '<=', 500000);
      } elseif ($price == 'price-3') {
        $query = $query->where('course_price', '>=', 500000)->where('course_price', '<=', 1000000);
      } elseif ($price == 'price-4') {
        $query = $query->where('course_price', '>=', 1000000);
      }
    }

    if (isset($params['category']) && $params['category'] != null) {
      $category = $params['category'];

      $query = $query->where('category_id', '=', $category);
    }

    if (isset($params['rate']) && $params['rate'] != null) {
      $rate = $params['rate'];

      if ($rate == 'rate-1') {
        $query = $query->having('student_result_avg_rating', '=', 5);
      } elseif ($rate == 'rate-2') {
        $query = $query->having('student_result_avg_rating', '>=', 4)->having('student_result_avg_rating', '<=', 5);
      } elseif ($rate == 'rate-3') {
        $query = $query->having('student_result_avg_rating', '>=', 3)->having('student_result_avg_rating', '<=', 4);
      } elseif ($rate == 'rate-4') {
        $query = $query->having('student_result_avg_rating', '>=', 2)->having('student_result_avg_rating', '<=', 3);
      } elseif ($rate == 'rate-5') {
        $query = $query->having('student_result_avg_rating', '>=', 1)->having('student_result_avg_rating', '<=', 2);
      }
    }

    if (isset($params['policy']) && $params['policy'] != null) {
      $policy = $params['policy'];

      if ($policy == 'policy-1') {
        $query = $query->where('course_price', '>', 0)->where('course_type', 'Business');
      } elseif ($policy == 'policy-2') {
        $query = $query->where('course_price', '=', 0)->where('course_type', 'Business');
      } elseif ($policy == 'policy-3') {
        $query = $query->where('course_type', '=', 'Local');
      }
    }

    if (isset($params['sort']) && $params['sort'] != null) {
      $sort = $params['sort'];
      // dd($sort);

      if ($sort == 'sort1') {
        $query = $query->orderBy('published_at', 'desc');
      } elseif ($sort == 'sort2') {
        $query = $query->orderBy('count_viewer', 'desc');
      } elseif ($sort == 'sort3') {
        $query = $query->orderBy('student_result_avg_rating', 'desc');
      } elseif ($sort == 'sort4') {
        $query = $query->orderBy('student_result_avg_rating', 'asc');
      } elseif ($sort == 'sort5') {
        $query = $query->orderBy('course_price', 'desc');
      } elseif ($sort == 'sort6') {
        $query = $query->orderBy('course_price', 'asc');
      }
    } else {
      $query = $query->orderBy('published_at', 'desc');
    }

    // dd($query->toSql());

    if (!empty($params['keyword'])) {
      $query = CommonBusiness::searchInCollection(
        $query,
        $this->fieldSearchable,
        $params['keyword'],
        ['name'],
        ['teacher']
      );
    }

    $model = $query->get();
    // dd($model);
    return $model;
  }

  public function getCourseInfoForLandingPage($course_id)
  {
    $query = $this->model
      ->newQuery()
      ->select(
        'course_id',
        'course_name',
        'teacher_id',
        'course_feature_image',
        'course_description',
        'count_viewer',
        'category_id',
        'course_price',
        'course_type',
        'is_published',
        'isDraft',
        'startTime',
        'endTime',
        'course_target',
        'certificate_image',
        'unit_currency',
        'published_at'
      );

    $model = $query
      ->with('chapters', function ($q) {
        $q->orderBy('number_order', 'asc');
      })
      ->with('chapters.lessons', function ($q) {
        $q->select(
          'lessons.lesson_id',
          'lesson_duration',
          'lesson_name',
          'lesson_author',
          'chapters_lessons.number_order'
        )->orderBy('chapters_lessons.number_order', 'asc');
      })
      ->with('chapters.survey:survey_id,survey_title')
      ->with('teacher:id,name,email,avatar,about')
      ->withCount('students')
      ->withAvg(['studentResultRatingAvg as student_result_avg_rating'], 'rating')
      ->withCount(['studentResultRatingAvg as rating_round'])
      ->where('course_id', $course_id)
      ->first();

    // $model->course_pricse = $model->getFormattedPriceAttribute();

    return $model;
  }

  public function checkValidCourse($user, $course)
  {
    $role = $this->checkRoleForUser($user);

    if ($role == 'user') {
      if ($course->course_type != 'Business') {
        return false;
      }
    } else {
      if ($course->course_type == 'Group') {
        return false;
      }
    }
    return true;
  }

  public function checkCouseForMe($request)
  {
    $course =  Course::where('slug_course', $request->get('slug'))->first();
    return CourseStudent::where('course_id', '=', $course['course_id'])->first();
  }

  public function getDetailCourseBySlug($slug)
  {
    return Course::where('slug_course', $slug)->firstOrFail();
  }
}

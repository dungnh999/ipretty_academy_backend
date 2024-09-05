<?php

namespace App\Repositories;

use App\Contract\CommonBusiness;
use App\Http\Resources\CourseCategoryResource;
use App\Models\CourseCategory;
use App\Repositories\BaseRepository;

/**
 * Class CourseCategoryRepository
 * @package App\Repositories
 * @version September 7, 2021, 5:18 pm UTC
*/

class CourseCategoryRepository extends BaseRepository
{
    use CommonBusiness;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'category_name',
        'category_description',
        'category_code',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    protected $relations = ['createdBy'];

    protected $relationSearchable = [
        'name'
    ];
    /**
     * Configure the Model
     **/
    public function model()
    {
        return CourseCategory::class;
    }

    public function createCourseCategory($input,$request)
    {
        $model = $this->model->newInstance($input);

        $user = auth()->user();
        $model->created_by = $user->id;
        $model->save();

      $model->handleMedia($request);


      return $model;
    }

    public function updateCourseCategory($input,$id,$request)
    {
        $query = $this->model->newQuery();
        $model = $query->findOrFail($id);

        if (empty($input[MEDIA_COLLECTION["COURSE_CATEGORY_ATTACHMENT"]]) || $input[MEDIA_COLLECTION["COURSE_CATEGORY_ATTACHMENT"]] == "null") {
            $input[MEDIA_COLLECTION["COURSE_CATEGORY_ATTACHMENT"]] = $model[MEDIA_COLLECTION["COURSE_CATEGORY_ATTACHMENT"]];
        }
        $model->fill($input);
        // $model->handleMedia($request);
        $model->save();
        if ($input[MEDIA_COLLECTION["COURSE_CATEGORY_ATTACHMENT"]] != NULL && $input[MEDIA_COLLECTION["COURSE_CATEGORY_ATTACHMENT"]] != "null") {
            $model->handleMedia($request);
        }
        return $model;
    }


    public function allCategories ($params = null) {

        $query = $this->model->newQuery()
        ->with('createdBy', function($q) {
            $q->select('name', 'email', 'id');
        })
        // ->with('categoryType', function($q) {
        //     $q->select('category_type_name', 'category_type_description' , 'id');
        // })
        ->orderBy('created_at', 'desc');

        if (isset($params['status']) && $params['status'] != null) {
            $status = explode(',', $params['status']);
            $query = $query->whereIn('isPublished', $status);
        }

        if (isset($params['created_at']) && $params['created_at'] != null) {
            $created_at = $params['created_at'];
            $query = $query->whereDate('created_at', '>=', $created_at);
        }

        if (isset($params['updated_at']) && $params['updated_at'] != null) {
            $updated_at = $params['updated_at'];
            $query = $query->whereDate('updated_at', '>=', $updated_at);
        }

        if (!empty($params['keyword'])) {
            $query = CommonBusiness::searchInCollection($query, $this->fieldSearchable, $params['keyword'], $this->relationSearchable, $this->relations);
        }

        if (isset($params['paging']) && $params['paging']) {
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

    public function allCourseCategory($params = null, $user = null){

        $mainRole = null;

        if ($user) {

            $mainRole = $this->checkRole($user);

        }

        $query = $this->model->newQuery();
        $model = $query->where('isPublished', '1');

        if ($mainRole && $mainRole == 'localStudent') {
            $model = $model->whereHas('courses', function ($q) {
                $q->where('is_published', 1)
                    ->where('isDraft', 0)
                    ->where(function ($oq) {
                        $oq->orwhere('course_type', 'Business')
                            ->orwhere('course_type', 'Local');
                    });
            });
        }else {
            $model = $model->whereHas('courses', function ($q) {
                $q->where('is_published', 1)
                    ->where('isDraft', 0)
                    ->where('course_type', 'Business');
            });
        }

        $model = $model->withCount('courses')
        ->orderBy('courses_count', 'DESC')
        ->limit(12);

        if (!empty($params['keyword'])) {
            $model = CommonBusiness::searchInCollection($model, $this->fieldSearchable, $params['keyword']);
        }

        $model = $model->get();

        return $model;

    }

    public function getFeaturedCourseCategory($params = null, $user = null)
    {

        $mainRole = null;

        if ($user) {

            $mainRole = $this->checkRole($user);
        }

        $query = $this->model->newQuery();

        $model = $query->where('isPublished', '1');

        if ($mainRole && $mainRole == 'localStudent') {
            $model = $model->whereHas('courses', function ($q) {
                $q->where('is_published', 1)
                    ->where('isDraft', 0)
                    ->where(function ($oq) {
                        $oq->orwhere('course_type', 'Business')
                        ->orwhere('course_type', 'Local');
                    });
            });
        } else {
            $model = $model->whereHas('courses', function ($q) {
                $q->where('is_published', 1)
                    ->where('isDraft', 0)
                    ->where('course_type', 'Business');
            });
        }

        $model = $model->withCount('courses')
        ->orderBy('courses_count', 'DESC');

        if (isset($params) && isset($params['limit']) && $params['limit'] != null) {
            $model = $model->limit($params['limit']);

        }

        if (!empty($params['keyword'])) {
            $model = CommonBusiness::searchInCollection($model, $this->fieldSearchable, $params['keyword']);
        }

        $model = $model->get();

        return $model;
    }


    public function publishedCategory($category_id, $isPublished)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($category_id);

        if ($model) {
            $model->isPublished = $isPublished;
            $model->save();
        }
        return $model;
    }

    public function feature_course_categories () {

        $query = $this->model->newQuery();

        $model = $query->select('category_name', 'category_id', 'course_category_attachment')
                ->withCount(['coursesWithStudents'])
                ->withCount(['studentsCurrentMonth'])
                ->withCount(['studentsPrevMonth'])
                ->withSum('courses', 'count_viewer')
                ->withSum('coursesOfMonth', 'count_viewer')
                ->withSum('coursesOfPrevMonth', 'count_viewer')
                ->orderBy('courses_with_students_count', 'desc')
                ->orderBy('courses_sum_count_viewer', 'desc')
                ->limit(10)
                ->get();
        foreach ($model as $key => $item) {
            $current_quantity = $item->students_current_month_count + $item->courses_of_month_sum_count_viewer;
            $prev_quantity = $item->students_prev_month_count + $item->courses_of_prev_month_sum_count_viewer;
            $item->rate = $prev_quantity ? round(($current_quantity - $prev_quantity) * 100 / $prev_quantity, 2) : 100;
            # code...
        }
        // ->with('coursesWithViewers')->get()->map(function ($course) {
        //     $course->setRelation('coursesWithViewers', $course->coursesWithViewers->take(10));
        //     return $course;
        // });
        return $model;
    }
}

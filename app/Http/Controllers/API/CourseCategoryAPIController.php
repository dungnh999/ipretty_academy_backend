<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCourseCategoryAPIRequest;
use App\Http\Requests\API\UpdateCourseCategoryAPIRequest;
use App\Http\Resources\CourseCategoryTypesResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCategoryTypes;
use App\Repositories\CourseCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CourseCategoryResource;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CourseCategoryController
 * @package App\Http\Controllers\API
 */

class CourseCategoryAPIController extends AppBaseController
{
    /** @var  CourseCategoryRepository */
    private $courseCategoryRepository;

    public function __construct(CourseCategoryRepository $courseCategoryRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->courseCategoryRepository = $courseCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/courseCategories",
     *      summary="Get a listing of the CourseCategories.",
     *      tags={"CourseCategory"},
     *      description="Get all CourseCategories",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/CourseCategory")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {

        $params = request()->query();

        $courseCategories = $this->courseCategoryRepository->allCategories($params);


        if (isset($params["paging"]) && $params['paging'] != null) {
            return $this->sendResponse(
                $courseCategories,
                __('messages.retrieved', ['model' => __('models/courseCategories.plural')])
            );
        }else {
            return $this->sendResponse(
                CourseCategoryResource::collection($courseCategories),
                __('messages.retrieved', ['model' => __('models/courseCategories.plural')])
            );
        }

    }

    /**
     * @param CreateCourseCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/courseCategories",
     *      summary="Store a newly created CourseCategory in storage",
     *      tags={"CourseCategory"},
     *      description="Store CourseCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CourseCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CourseCategory")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CourseCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCourseCategoryAPIRequest $request)
    {
        $input = $request->all();

        $courseCategory = $this->courseCategoryRepository->createCourseCategory($input,$request);

        return $this->sendResponse(
            new CourseCategoryResource($courseCategory),
            __('messages.saved', ['model' => __('models/courseCategories.singular')])
        );
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/courseCategories/{id}",
     *      summary="Display the specified CourseCategory",
     *      tags={"CourseCategory"},
     *      description="Get CourseCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CourseCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CourseCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var CourseCategory $courseCategory */
        $courseCategory = $this->courseCategoryRepository->find($id);

        if (empty($courseCategory)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseCategories.singular')])
            );
        }

        return $this->sendResponse(
            new CourseCategoryResource($courseCategory),
            __('messages.retrieved', ['model' => __('models/courseCategories.singular')])
        );
    }

    public function getCourseCategoryType(Request $request){
        $slug_name = $request->get('slug_name');
        $category_type = CourseCategoryTypes::where('slug', $slug_name)->first();
        if(!isset($category_type)){
          return false;
        }
        $courses = DB::table('courses')
                   ->select('courses.*', 'course_categories_types.*')
                   ->join('course_categories', 'courses.category_id', '=', 'course_categories.category_id')
                   ->join('course_categories_types', 'course_categories.category_id', '=', 'course_categories_types.id')
                   ->where('course_categories_types.id', $category_type['id'])
                   ->get();
        return $this->sendResponse(
          $courses,
          __('messages.retrieved', ['model' => __('models/courses.plural')])
        );
    }

    public function getCategoryMenu(Request $request){
      $data = DB::table('course_categories_types')
              ->join('course_categories', 'course_categories_types.id', '=', 'course_categories.category_type_id')
              ->select('course_categories_types.category_type_name AS label', 'course_categories_types.slug AS value' , DB::raw('@row_number := @row_number + 1 AS row_number') ,DB::raw('CONCAT(\'[\', GROUP_CONCAT(CONCAT(\'{"value": "\', course_categories.category_code, \'", "label": "\', course_categories.category_name, \'"}\')), \']\') AS children'))
              ->crossJoin(DB::raw('(SELECT @row_number := 0) AS row_number'))
              ->groupBy('category_type_name')
              ->get();

      $dataCategory = json_decode($data, true);
      foreach ($dataCategory as $key => $item) {
        $dataCategory[$key]['children'] = json_decode($item['children'], true);
      }

      return $this->sendResponse(
        $dataCategory,
        __('messages.retrieved', ['model' => __('models/courseCategories.singular')])
      );
    }

    /**
     * @param int $id
     * @param UpdateCourseCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/courseCategories/{id}",
     *      summary="Update the specified CourseCategory in storage",
     *      tags={"CourseCategory"},
     *      description="Update CourseCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CourseCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CourseCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CourseCategory")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CourseCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCourseCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var CourseCategory $courseCategory */
        $courseCategory = $this->courseCategoryRepository->find($id);

        if (empty($courseCategory)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseCategories.singular')])
            );
        }
        $courseCategory = $this->courseCategoryRepository->updateCourseCategory($input, $id, $request);

        return $this->sendResponse(
            new CourseCategoryResource($courseCategory),
            __('messages.updated', ['model' => __('models/courseCategories.singular')])
        );
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/courseCategories/{id}",
     *      summary="Remove the specified CourseCategory from storage",
     *      tags={"CourseCategory"},
     *      description="Delete CourseCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CourseCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var CourseCategory $courseCategory */
        $courseCategory = $this->courseCategoryRepository->find($id);

        if (empty($courseCategory)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseCategories.singular')])
            );
        }

        $courseCategory->delete();

        $courseCategories = $this->courseCategoryRepository->all();

        return $this->sendResponse(
            CourseCategoryResource::collection($courseCategories),
            __('messages.deleted', ['model' => __('models/courseCategories.singular')])
        );
    }

    public function changePublished ($category_id, Request $request)
    {
        $courseCategory = $this->courseCategoryRepository->find($category_id);

        if (empty($courseCategory)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/courseCategories.singular')])
            );
        }

        $published_category = $this->courseCategoryRepository->publishedCategory($category_id, $request->isPublished);

        $message = 'messages.unpublished';

        if ($request->isPublished) {
            $message = 'messages.published';
        }

        return $this->sendResponse(
            new CourseCategoryResource($published_category),
            __($message, ['model' => __('models/courseCategories.singular')])
        );
    }

    public function feature_course_categories() {
        $courseCategory = $this->courseCategoryRepository->feature_course_categories();

        return $this->sendResponse(
            $courseCategory,
            __('messages.retrieved', ['model' => __('models/courseCategories.plural')])
        );
    }
}
